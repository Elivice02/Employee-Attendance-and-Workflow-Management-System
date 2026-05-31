<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceNotification;
use App\Models\AttendanceSetting;
use App\Models\Notification;
use App\Models\User;
use App\Services\AttendanceNetwork;
use App\Services\LateAttendanceLetterGenerator;
use App\Services\ReferenceNumberGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function __construct(
        private AttendanceNetwork $attendanceNetwork,
        private ReferenceNumberGenerator $referenceNumberGenerator,
        private LateAttendanceLetterGenerator $letterGenerator
    ) {}

    public function checkIn(Request $request)
    {
        $user = Auth::user();
        $settings = AttendanceSetting::current();
        $ip = $request->ip();

        if (! $this->canMarkAttendance($user)) {
            abort(403);
        }

        if (! $this->attendanceNetwork->isAllowed($ip, $settings->networkList())) {
            return back()->with('error', 'Attendance can only be marked from the organization network.');
        }

        $attendance = Attendance::query()
            ->where('user_id', $user->id)
            ->whereDate('attendance_date', today())
            ->first();

        if ($attendance?->check_in_at) {
            return back()->with('error', 'You have already checked in today.');
        }

        $isLate = now()->gt(today()->setTimeFromTimeString($settings->work_start_time)->addMinutes($settings->late_grace_minutes));

        if ($isLate) {
            $request->session()->put('late_check_in_detected_at', now()->toISOString());

            return redirect()
                ->route('attendance.late.create')
                ->with('warning', 'You are late. Provide your explanation and evidence before check-in can be completed.');
        }

        // Use firstOrCreate to prevent race condition
        Attendance::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'attendance_date' => today(),
            ],
            [
                'check_in_at' => now(),
                'check_in_ip' => $ip,
                'status' => 'present',
            ]
        );

        return back()->with('success', 'Checked in successfully.');
    }

    public function lateCheckInForm(Request $request)
    {
        $user = Auth::user();

        if (! $this->canMarkAttendance($user)) {
            abort(403);
        }

        $settings = AttendanceSetting::current();

        if (! $request->session()->has('late_check_in_detected_at') || ! $this->isLateNow($settings)) {
            return $this->redirectToDashboard($user)
                ->with('error', 'Use the Check In button first so the system can confirm whether you are late.');
        }

        $attendance = Attendance::query()
            ->where('user_id', $user->id)
            ->whereDate('attendance_date', today())
            ->first();

        if ($attendance?->check_in_at) {
            return $this->redirectToDashboard($user)->with('error', 'You have already checked in today.');
        }

        return view('attendance.late-check-in', [
            'attendanceSetting' => $settings,
            'backRoute' => $this->dashboardRouteFor($user),
            'companyName' => config('attendance.company_name', 'ABC Company Ltd'),
            'companyAddress' => config('attendance.company_address', 'P.O. Box 123, Morogoro'),
            'referencePreview' => $this->referenceCodeFor($user)
                ? 'LE/' . now()->format('Y') . '/' . $this->referenceCodeFor($user) . '/###'
                : 'Generated after submission',
            'openingParagraph' => $this->defaultOpeningParagraph(today()),
            'closingParagraph' => $this->defaultClosingParagraph(),
            'draftPayload' => $request->session()->get('late_check_in_payload', []),
        ]);
    }

    public function previewLateCheckIn(Request $request)
    {
        $user = Auth::user();
        $settings = AttendanceSetting::current();
        $ip = $request->ip();

        if (! $this->canMarkAttendance($user)) {
            abort(403);
        }

        if (! $request->session()->has('late_check_in_detected_at')) {
            return $this->redirectToDashboard($user)
                ->with('error', 'Use the Check In button first so the system can detect your attendance status.');
        }

        if (! $this->attendanceNetwork->isAllowed($ip, $settings->networkList())) {
            return back()->with('error', 'Attendance can only be marked from the organization network.');
        }

        $existingAttendance = Attendance::query()
            ->where('user_id', $user->id)
            ->whereDate('attendance_date', today())
            ->first();

        if ($existingAttendance?->check_in_at) {
            return $this->redirectToDashboard($user)->with('error', 'You have already checked in today.');
        }

        if (! $this->isLateNow($settings)) {
            $request->session()->forget('late_check_in_detected_at');

            return $this->redirectToDashboard($user)
                ->with('error', 'You are no longer detected as late. Please use the Check In button again.');
        }

        $validated = $request->validate([
            'late_opening_paragraph' => ['required', 'string', 'min:20', 'max:1000'],
            'late_explanation' => ['required', 'string', 'min:20', 'max:2000'],
            'late_closing_paragraph' => ['required', 'string', 'min:20', 'max:1000'],
            'late_signature_name' => ['required', 'string', 'max:255'],
            'late_evidence' => [
                $settings->late_evidence_required ? 'required' : 'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx',
                'max:4096',
            ],
        ]);

        $evidencePath = $request->hasFile('late_evidence')
            ? $request->file('late_evidence')->store('attendance-evidence/drafts', 'public')
            : null;

        unset($validated['late_evidence']);

        $request->session()->put('late_check_in_payload', [
            ...$validated,
            'late_evidence_path' => $evidencePath,
            'check_in_ip' => $ip,
        ]);

        return view('attendance.late-preview', [
            'payload' => $request->session()->get('late_check_in_payload'),
            'companyName' => config('attendance.company_name', 'ABC Company Ltd'),
            'companyAddress' => config('attendance.company_address', 'P.O. Box 123, Morogoro'),
            'referencePreview' => 'LE/' . now()->format('Y') . '/' . $this->referenceCodeFor($user) . '/###',
            'attendanceDate' => today(),
            'backRoute' => $this->dashboardRouteFor($user),
            'user' => $user,
        ]);
    }

    public function submitLateCheckIn(Request $request)
    {
        $user = Auth::user();
        $settings = AttendanceSetting::current();
        $ip = $request->ip();

        if (! $this->canMarkAttendance($user)) {
            abort(403);
        }

        if (! $request->session()->has('late_check_in_detected_at') || ! $request->session()->has('late_check_in_payload')) {
            return $this->redirectToDashboard($user)
                ->with('error', 'Preview your late explanation letter before submitting it to HR.');
        }

        if (! $this->attendanceNetwork->isAllowed($ip, $settings->networkList())) {
            return back()->with('error', 'Attendance can only be marked from the organization network.');
        }

        $existingAttendance = Attendance::query()
            ->where('user_id', $user->id)
            ->whereDate('attendance_date', today())
            ->first();

        if ($existingAttendance?->check_in_at) {
            return $this->redirectToDashboard($user)->with('error', 'You have already checked in today.');
        }

        if (! $this->isLateNow($settings)) {
            $request->session()->forget(['late_check_in_detected_at', 'late_check_in_payload']);

            return $this->redirectToDashboard($user)
                ->with('error', 'You are no longer detected as late. Please use the Check In button again.');
        }

        $payload = $request->session()->get('late_check_in_payload');
        $evidencePath = $payload['late_evidence_path'] ?? null;

        $referenceCode = $this->referenceCodeFor($user);

        // Use firstOrCreate to prevent race condition
        $attendance = Attendance::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'attendance_date' => today(),
            ],
            [
                'check_in_at' => now(),
                'check_in_ip' => $payload['check_in_ip'] ?? $ip,
                'status' => 'late_pending_review',
                'late_opening_paragraph' => $payload['late_opening_paragraph'],
                'late_explanation' => $payload['late_explanation'] ?? null,
                'late_closing_paragraph' => $payload['late_closing_paragraph'],
                'late_signature_name' => $payload['late_signature_name'],
                'late_evidence_path' => $evidencePath,
                'late_letter_reference' => $this->referenceNumberGenerator->lateExplanation($referenceCode),
                'late_submitted_at' => now(),
                'late_review_status' => 'pending',
            ]
        );

        if (! $attendance->late_letter_draft_path) {
            $attendance->update([
                'late_letter_draft_path' => $this->letterGenerator->generateDraft($attendance),
            ]);
        }

        $request->session()->forget(['late_check_in_detected_at', 'late_check_in_payload']);
        $this->notifyLateCheckInReviewers($attendance);

        return $this->redirectToDashboard($user)
            ->with('success', 'Late check-in submitted. Your official draft letter is ready and HR has been notified.');
    }

    public function checkOut(Request $request)
    {
        $settings = AttendanceSetting::current();
        $ip = $request->ip();

        if (! $this->canMarkAttendance(Auth::user())) {
            abort(403);
        }

        if (! $this->attendanceNetwork->isAllowed($ip, $settings->networkList())) {
            return back()->with('error', 'Attendance can only be marked from the organization network.');
        }

        $attendance = Attendance::query()
            ->where('user_id', Auth::id())
            ->whereDate('attendance_date', today())
            ->first();

        if (! $attendance?->check_in_at) {
            return back()->with('error', 'You need to check in before checking out.');
        }

        if ($attendance->check_out_at) {
            return back()->with('error', 'You have already checked out today.');
        }

        // Validate that check_out time is after check_in time
        $checkOutTime = now();
        if ($checkOutTime->lte($attendance->check_in_at)) {
            return back()->with('error', 'Check-out time must be after check-in time.');
        }

        $attendance->update([
            'check_out_at' => $checkOutTime,
            'check_out_ip' => $ip,
        ]);

        return back()->with('success', 'Checked out successfully.');
    }

    public function editLateEvidence(Attendance $attendance)
    {
        $user = Auth::user();

        if ($attendance->user_id !== $user->id) {
            abort(403);
        }

        if ($attendance->late_review_status !== 'needs_more_evidence') {
            return $this->redirectToDashboard($user)
                ->with('error', 'This attendance record is not waiting for more evidence.');
        }

        return view('attendance.evidence', [
            'attendance' => $attendance,
            'backRoute' => $this->dashboardRouteFor($user),
        ]);
    }

    public function updateLateEvidence(Request $request, Attendance $attendance)
    {
        $user = Auth::user();

        if ($attendance->user_id !== $user->id) {
            abort(403);
        }

        if ($attendance->late_review_status !== 'needs_more_evidence') {
            return $this->redirectToDashboard($user)
                ->with('error', 'This attendance record is not waiting for more evidence.');
        }

        $validated = $request->validate([
            'late_opening_paragraph' => ['required', 'string', 'min:20', 'max:1000'],
            'late_explanation' => ['required', 'string', 'min:20', 'max:2000'],
            'late_closing_paragraph' => ['required', 'string', 'min:20', 'max:1000'],
            'late_signature_name' => ['required', 'string', 'max:255'],
            'late_evidence' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:4096'],
        ]);

        if ($attendance->late_evidence_path) {
            Storage::disk('public')->delete($attendance->late_evidence_path);
        }

        $attendance->update([
            'late_opening_paragraph' => $validated['late_opening_paragraph'],
            'late_explanation' => $validated['late_explanation'],
            'late_closing_paragraph' => $validated['late_closing_paragraph'],
            'late_signature_name' => $validated['late_signature_name'],
            'late_evidence_path' => $request->file('late_evidence')->store('attendance-evidence', 'public'),
            'late_letter_final_path' => null,
            'late_review_status' => 'pending',
            'late_review_note' => null,
            'late_reviewed_by' => null,
            'late_reviewed_at' => null,
            'status' => 'late_pending_review',
        ]);

        $attendance->update([
            'late_letter_draft_path' => $this->letterGenerator->generateDraft($attendance),
        ]);

        AttendanceNotification::query()
            ->where('attendance_id', $attendance->id)
            ->where('recipient_id', $user->id)
            ->where('type', 'late_review_result')
            ->update(['read_at' => now()]);

        $this->notifyLateCheckInReviewers($attendance);

        return $this->redirectToDashboard($user)
            ->with('success', 'Updated evidence submitted. Your draft letter was regenerated and HR has been notified.');
    }

    public function viewLateLetter(Attendance $attendance, string $type = 'draft')
    {
        $user = Auth::user();

        if (! $this->canViewLateLetter($attendance, $user)) {
            abort(403);
        }

        if (! in_array($type, ['draft', 'final'], true)) {
            abort(404);
        }

        $path = $type === 'final'
            ? $attendance->late_letter_final_path
            : $attendance->late_letter_draft_path;

        if (! $path || ! Storage::disk('public')->exists($path)) {
            return back()->with('error', 'The requested letter has not been generated yet.');
        }

        return Storage::disk('public')->response($path);
    }

    public function employeeRecords()
    {
        $records = Attendance::query()
            ->where('user_id', Auth::id())
            ->latest('attendance_date')
            ->paginate(15);

        return view('employee.dashboard.attendance', compact('records'));
    }

    public function hrIndex()
    {
        $records = Attendance::query()
            ->with('user.department')
            ->latest('attendance_date')
            ->latest('check_in_at')
            ->paginate(20);

        $lateNotifications = $this->notificationsFor(Auth::user());

        return view('attendance.manage', [
            'records' => $records,
            'lateNotifications' => $lateNotifications,
            'reviewRoute' => 'hr.attendance.late-review',
            'canReviewLate' => true,
            'title' => 'Attendance Management',
            'backRoute' => 'hr.dashboard',
        ]);
    }

    public function supervisorIndex()
    {
        $supervisor = Auth::user();
        $teamIds = $supervisor->employees()->pluck('id');

        $records = Attendance::query()
            ->with('user.department')
            ->whereIn('user_id', $teamIds)
            ->latest('attendance_date')
            ->latest('check_in_at')
            ->paginate(20);

        $lateNotifications = $this->notificationsFor($supervisor);

        return view('attendance.manage', [
            'records' => $records,
            'lateNotifications' => $lateNotifications,
            'reviewRoute' => null,
            'canReviewLate' => false,
            'title' => 'Team Attendance Review',
            'backRoute' => 'supervisor.dashboard',
        ]);
    }

    public function reviewLate(Request $request, Attendance $attendance)
    {
        $reviewer = Auth::user();

        if ($reviewer->role !== 'hr') {
            abort(403);
        }

        $validated = $request->validate([
            'late_review_status' => ['required', Rule::in(['approved', 'rejected', 'needs_more_evidence'])],
            'late_review_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $status = match ($validated['late_review_status']) {
            'approved' => 'present',  // Approved late attendance = present
            'rejected' => 'late_rejected',
            default => 'late_pending_evidence',
        };

        $attendance->update([
            'late_review_status' => $validated['late_review_status'],
            'late_review_note' => $validated['late_review_note'] ?? null,
            'late_reviewed_by' => $reviewer->id,
            'late_reviewed_at' => now(),
            'status' => $status,
        ]);

        if (in_array($validated['late_review_status'], ['approved', 'rejected'], true)) {
            $attendance->update([
                'late_letter_final_path' => $this->letterGenerator->generateFinal($attendance),
            ]);
        }

        // Mark reviewer's notification as read
        AttendanceNotification::query()
            ->where('attendance_id', $attendance->id)
            ->where('recipient_id', $reviewer->id)
            ->update(['read_at' => now()]);

        // Notify the employee of the review outcome
        AttendanceNotification::query()->firstOrCreate(
            [
                'attendance_id' => $attendance->id,
                'recipient_id' => $attendance->user_id,
                'type' => 'late_review_result',
            ],
            [
                'read_at' => null,
            ]
        );

        Notification::query()->create([
            'user_id' => $attendance->user_id,
            'type' => 'late_review_result',
            'title' => 'Late attendance review updated',
            'message' => 'Your late attendance explanation was marked ' . str_replace('_', ' ', $validated['late_review_status']) . '.',
            'notifiable_type' => Attendance::class,
            'notifiable_id' => $attendance->id,
            'action_url' => route('employee.attendance'),
            'icon' => 'bell',
            'color' => $validated['late_review_status'] === 'approved' ? 'green' : 'yellow',
        ]);

        return back()->with('success', 'Late explanation reviewed.');
    }

    public static function dashboardDataFor(User $user): array
    {
        $todayAttendance = Attendance::query()
            ->where('user_id', $user->id)
            ->whereDate('attendance_date', today())
            ->first();

        $setting = AttendanceSetting::current();

        return [
            'todayAttendance' => $todayAttendance,
            'attendanceSetting' => $setting,
        ];
    }

    public static function notificationsFor(User $user)
    {
        return AttendanceNotification::query()
            ->with('attendance.user.department')
            ->where('recipient_id', $user->id)
            ->where('created_at', '>=', now()->subDay())
            ->latest()
            ->take(8)
            ->get();
    }

    private function notifyLateCheckInReviewers(Attendance $attendance): void
    {
        $recipients = User::query()
            ->where('role', 'hr')
            ->pluck('id');

        if ($attendance->user->supervisor_id) {
            $recipients->push($attendance->user->supervisor_id);
        }

        $recipients->unique()->each(function ($recipientId) use ($attendance) {
            AttendanceNotification::query()->firstOrCreate([
                'attendance_id' => $attendance->id,
                'recipient_id' => $recipientId,
                'type' => 'late_check_in',
            ]);

            $recipient = User::query()->find($recipientId);

            Notification::query()->firstOrCreate(
                [
                    'user_id' => $recipientId,
                    'type' => 'late_attendance',
                    'notifiable_type' => Attendance::class,
                    'notifiable_id' => $attendance->id,
                ],
                [
                    'title' => 'Late attendance submitted',
                    'message' => $attendance->user->name . ' submitted a late attendance explanation.',
                    'action_url' => $recipient?->role === 'supervisor'
                        ? route('supervisor.attendance.index')
                        : route('hr.attendance.index'),
                    'icon' => 'bell',
                    'color' => 'yellow',
                ]
            );
        });
    }

    private function referenceCodeFor(User $user): string
    {
        $department = $user->department;

        if (! $department) {
            return match ($user->role) {
                'hr' => 'HR',
                'supervisor' => 'SUP',
                default => 'EMP',
            };
        }

        if ($department->code) {
            return strtoupper($department->code);
        }

        $code = $this->uniqueDepartmentCodeFromName($department->name);
        $department->update(['code' => $code]);

        return $code;
    }

    private function defaultOpeningParagraph($date): string
    {
        return 'I hereby write this letter to explain my late arrival to work on ' . $date->format('jS F Y') . '.';
    }

    private function defaultClosingParagraph(): string
    {
        return 'I sincerely apologize for the inconvenience caused and assure management that I will take appropriate measures to avoid similar incidents in future.';
    }

    private function uniqueDepartmentCodeFromName(string $name): string
    {
        $words = preg_split('/\s+/', trim($name));
        $base = collect($words)
            ->filter()
            ->map(fn ($word) => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $word), 0, 1)))
            ->join('');

        if (strlen($base) < 2) {
            $base = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 3));
        }

        $base = substr($base ?: 'DEP', 0, 8);
        $code = $base;
        $suffix = 1;

        while (\App\Models\Department::query()->where('code', $code)->exists()) {
            $code = substr($base, 0, 8) . $suffix;
            $suffix++;
        }

        return $code;
    }

    private function canViewLateLetter(Attendance $attendance, User $user): bool
    {
        if ($attendance->user_id === $user->id) {
            return true;
        }

        if ($user->role === 'hr') {
            return true;
        }

        return $user->role === 'supervisor' && $attendance->user->supervisor_id === $user->id;
    }

    private function isLateNow(AttendanceSetting $settings): bool
    {
        return now()->gt(today()->setTimeFromTimeString($settings->work_start_time)->addMinutes($settings->late_grace_minutes));
    }

    private function canMarkAttendance(User $user): bool
    {
        return in_array($user->role, ['employee', 'hr', 'supervisor'], true);
    }

    private function dashboardRouteFor(User $user): string
    {
        return match ($user->role) {
            'hr' => 'hr.dashboard',
            'supervisor' => 'supervisor.dashboard',
            default => 'employee.dashboard',
        };
    }

    private function redirectToDashboard(User $user)
    {
        return redirect()->route($this->dashboardRouteFor($user));
    }
}

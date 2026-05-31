<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Notification;
use App\Models\User;
use App\Services\LeavePdfGenerator;
use App\Services\LeaveReferenceNumberGenerator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LeaveController extends Controller
{
    public function employeeIndex()
    {
        $leaves = Auth::user()->leaveRequests()
            ->latest()
            ->get();

        return view('employee.dashboard.leave-requests', compact('leaves'));
    }

    public function create()
    {
        return view('employee.dashboard.request-leave', [
            'types' => config('leave.types', []),
            'employee' => Auth::user()->loadMissing('department'),
        ]);
    }

    public function preview(Request $request)
    {
        $payload = $this->validateSectionA($request);
        $employee = Auth::user()->loadMissing('department');
        $type = $this->leaveType((int) $payload['leave_type_number']);
        $payload['total_days'] = $this->totalDays($payload['start_date'], $payload['end_date']);

        if ($request->hasFile('attachment_path')) {
            $payload['attachment_path'] = $request->file('attachment_path')->store('leave-attachments/drafts', 'public');
            $payload['attachment_original_name'] = $request->file('attachment_path')->getClientOriginalName();
        }

        $request->session()->put('leave_request_preview', $payload);

        return view('leave.preview', [
            'payload' => $payload,
            'employee' => $employee,
            'type' => $type,
            'types' => config('leave.types', []),
        ]);
    }

    public function store(Request $request, LeaveReferenceNumberGenerator $referenceNumbers)
    {
        $payload = $request->session()->get('leave_request_preview');

        if (! $payload) {
            return redirect()
                ->route('employee.leave.create')
                ->with('error', 'Please review the leave application before submitting it.');
        }

        $employee = Auth::user()->loadMissing('department', 'supervisor');
        $type = $this->leaveType((int) $payload['leave_type_number']);
        $departmentCode = $employee->department?->code ?: $this->roleDepartmentCode($employee);
        $refNo = $referenceNumbers->generate($departmentCode);
        $totalDays = (int) $payload['total_days'];

        $leave = Leave::create([
            'ref_no' => $refNo,
            'user_id' => $employee->id,
            'leave_type' => $this->legacyLeaveType((int) $payload['leave_type_number']),
            'leave_type_number' => (int) $payload['leave_type_number'],
            'leave_type_name' => $type['name'],
            'leave_type_standing_order' => $type['standing_order'],
            'start_date' => $payload['start_date'],
            'end_date' => $payload['end_date'],
            'total_days' => $totalDays,
            'reason' => $payload['reason'],
            'attachment_path' => $payload['attachment_path'] ?? null,
            'status' => 'pending',
            'current_stage' => 'pending_hr_review',
            'section_a' => $payload,
            'submitted_at' => now(),
            'supervisor_id' => $employee->supervisor?->id,
        ]);

        $request->session()->forget('leave_request_preview');
        $this->notifyUsers(
            User::query()->where('role', 'hr')->get(),
            $leave,
            'New leave application requires HR review',
            "{$employee->name} submitted {$leave->leave_type_name} ({$leave->ref_no}).",
            'hr.leaves.show'
        );

        return redirect()
            ->route('employee.leave.show', $leave)
            ->with('success', 'Leave application submitted to HR for Section B review.');
    }

    public function employeeShow(Leave $leave)
    {
        abort_unless($leave->user_id === Auth::id(), 403);

        return view('leave.show', [
            'leave' => $leave->loadMissing(['employee.department', 'supervisor', 'hrReviewer']),
            'roleView' => 'employee',
            'types' => config('leave.types', []),
        ]);
    }

    public function hrIndex()
    {
        $leaves = Leave::with(['employee.department', 'supervisor'])
            ->latest()
            ->get();

        return view('leave.hr-index', compact('leaves'));
    }

    public function hrShow(Leave $leave)
    {
        return view('leave.show', [
            'leave' => $leave->loadMissing(['employee.department', 'supervisor', 'hrReviewer']),
            'roleView' => 'hr',
            'types' => config('leave.types', []),
        ]);
    }

    public function hrVerify(Request $request, Leave $leave)
    {
        abort_unless($leave->current_stage === 'pending_hr_review', 403);

        $validated = $request->validate([
            'last_leave_start' => ['nullable', 'date'],
            'last_leave_end' => ['nullable', 'date', 'after_or_equal:last_leave_start'],
            'days_taken' => ['nullable', 'integer', 'min:0'],
            'previous_outstanding_days' => ['nullable', 'integer', 'min:0'],
            'current_outstanding_days' => ['nullable', 'integer', 'min:0'],
            'transport_allowance_status' => ['nullable', Rule::in(['paid', 'not_paid'])],
            'transport_paid_amount' => ['nullable', 'numeric', 'min:0'],
            'transport_debt_amount' => ['nullable', 'numeric', 'min:0'],
            'hr_review_remarks' => ['nullable', 'string', 'max:1000'],
            'signature_name' => ['required', 'string', 'max:255'],
        ]);

        $nextStage = $leave->supervisor_id ? 'pending_supervisor_recommendation' : 'pending_final_approval';

        $leave->update([
            'section_b' => array_merge($validated, ['reviewed_at' => now()->toDateString()]),
            'hr_id' => Auth::id(),
            'hr_comment' => $validated['hr_review_remarks'] ?? null,
            'current_stage' => $nextStage,
            'status' => $leave->supervisor_id ? 'pending' : 'supervisor_approved',
        ]);

        if ($leave->supervisor_id) {
            $this->notifyUsers(
                User::query()->whereKey($leave->supervisor_id)->get(),
                $leave,
                'Leave application requires your recommendation',
                "{$leave->employee->name}'s leave application {$leave->ref_no} has been reviewed by HR.",
                'supervisor.leaves.show'
            );
        }

        return back()->with('success', 'Section B reviewed. The leave application moved to the next stage.');
    }

    public function supervisorIndex()
    {
        $leaves = Leave::with(['employee.department', 'hrReviewer'])
            ->where('supervisor_id', Auth::id())
            ->latest()
            ->get();

        return view('leave.supervisor-index', compact('leaves'));
    }

    public function supervisorShow(Leave $leave)
    {
        abort_unless($leave->supervisor_id === Auth::id(), 403);

        return view('leave.show', [
            'leave' => $leave->loadMissing(['employee.department', 'supervisor', 'hrReviewer']),
            'roleView' => 'supervisor',
            'types' => config('leave.types', []),
        ]);
    }

    public function supervisorRecommend(Request $request, Leave $leave)
    {
        abort_unless($leave->supervisor_id === Auth::id(), 403);
        abort_unless($leave->current_stage === 'pending_supervisor_recommendation', 403);

        $validated = $request->validate([
            'recommendation' => ['required', Rule::in(['recommended', 'not_recommended'])],
            'reason' => ['required', 'string', 'max:1000'],
            'signature_name' => ['required', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
        ]);

        $leave->update([
            'section_c' => array_merge($validated, ['reviewed_at' => now()->toDateString()]),
            'supervisor_comment' => $validated['reason'],
            'supervisor_reviewed_at' => now(),
            'status' => 'supervisor_approved',
            'current_stage' => 'pending_final_approval',
        ]);

        $this->notifyUsers(
            User::query()->where('role', 'hr')->get(),
            $leave,
            'Leave application is ready for final decision',
            "{$leave->employee->name}'s leave application {$leave->ref_no} has a supervisor recommendation.",
            'hr.leaves.show'
        );

        return back()->with('success', 'Section C recommendation submitted to HR for final decision.');
    }

    public function hrFinalReview(Request $request, Leave $leave, LeavePdfGenerator $pdfGenerator)
    {
        abort_unless($leave->current_stage === 'pending_final_approval', 403);

        $validated = $request->validate([
            'decision' => ['required', Rule::in(['approved', 'denied'])],
            'transport_allowance_decision' => ['nullable', Rule::in(['with', 'without'])],
            'transport_allowance_deserved' => ['nullable', Rule::in(['deserve', 'not_deserve'])],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'signature_name' => ['required', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
        ]);

        $leave->update([
            'section_d' => array_merge($validated, ['reviewed_at' => now()->toDateString()]),
            'hr_id' => Auth::id(),
            'hr_comment' => $validated['remarks'] ?? null,
            'hr_reviewed_at' => now(),
            'status' => $validated['decision'] === 'approved' ? 'hr_approved' : 'rejected',
            'current_stage' => 'closed',
        ]);

        $leave->update(['pdf_path' => $pdfGenerator->generate($leave->fresh())]);

        $this->notifyUsers(
            collect([$leave->employee]),
            $leave,
            'Leave application decision issued',
            "Your leave application {$leave->ref_no} has been {$validated['decision']}.",
            'employee.leave.show'
        );

        return back()->with('success', 'Final decision saved and official PDF generated.');
    }

    public function pdf(Leave $leave)
    {
        $user = Auth::user();
        abort_unless(
            $leave->user_id === $user->id
            || $leave->supervisor_id === $user->id
            || in_array($user->role, ['hr', 'admin'], true),
            403
        );

        abort_unless($leave->pdf_path && Storage::disk('public')->exists($leave->pdf_path), 404);

        return response()->file(Storage::disk('public')->path($leave->pdf_path));
    }

    private function validateSectionA(Request $request): array
    {
        return $request->validate([
            'last_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'first_name' => ['required', 'string', 'max:100'],
            'personal_file_no' => ['nullable', 'string', 'max:100'],
            'check_no' => ['nullable', 'string', 'max:100'],
            'tsd_no' => ['nullable', 'string', 'max:100'],
            'designation' => ['nullable', 'string', 'max:150'],
            'station' => ['nullable', 'string', 'max:150'],
            'institution' => ['nullable', 'string', 'max:150'],
            'division_department' => ['nullable', 'string', 'max:150'],
            'first_appointment_date' => ['nullable', 'date'],
            'leave_type_number' => ['required', 'integer', Rule::in(array_keys(config('leave.types', [])))],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
            'travel_destination' => ['nullable', 'string', 'max:150'],
            'travel_days' => ['nullable', 'integer', 'min:0'],
            'transport_assistance' => ['nullable', Rule::in(['entitled', 'not_entitled'])],
            'spouse_name' => ['nullable', 'string', 'max:150'],
            'child_1_name' => ['nullable', 'string', 'max:150'],
            'child_1_dob' => ['nullable', 'date'],
            'child_2_name' => ['nullable', 'string', 'max:150'],
            'child_2_dob' => ['nullable', 'date'],
            'child_3_name' => ['nullable', 'string', 'max:150'],
            'child_3_dob' => ['nullable', 'date'],
            'child_4_name' => ['nullable', 'string', 'max:150'],
            'child_4_dob' => ['nullable', 'date'],
            'po_box' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'signature_name' => ['required', 'string', 'max:255'],
            'attachment_path' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
        ]);
    }

    private function leaveType(int $number): array
    {
        return config("leave.types.$number") ?? config('leave.types.1');
    }

    private function totalDays(string $startDate, string $endDate): int
    {
        return Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
    }

    private function legacyLeaveType(int $number): string
    {
        return match ($number) {
            4 => 'maternity',
            5 => 'paternity',
            7 => 'unpaid',
            9, 10 => 'sick',
            default => 'annual',
        };
    }

    private function roleDepartmentCode(User $user): string
    {
        return strtoupper(match ($user->role) {
            'hr' => 'HR',
            'supervisor' => 'SUP',
            default => 'EMP',
        });
    }

    private function notifyUsers($users, Leave $leave, string $title, string $message, string $routeName): void
    {
        foreach ($users as $user) {
            if (! $user) {
                continue;
            }

            Notification::create([
                'user_id' => $user->id,
                'type' => 'leave',
                'title' => $title,
                'message' => $message,
                'notifiable_type' => Leave::class,
                'notifiable_id' => $leave->id,
                'action_url' => route($routeName, $leave),
                'icon' => 'file-text',
                'color' => 'blue',
            ]);
        }
    }
}

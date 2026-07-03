<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHRRequest;
use App\Models\AttendanceSetting;
use App\Services\HRService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminController extends Controller
{
    public function __construct(
        private HRService $hrService
    ) {}

    /**
     * ADMIN DASHBOARD
     */
    public function dashboard()
    {
        $settings = AttendanceSetting::current();

        return view('admin.dashboard', [
            'totalUsers' => User::count(),
            'totalHR' => User::where('role', 'hr')->count(),
            'totalEmployees' => User::where('role', 'employee')->count(),
            'recentUsers' => User::latest()->take(5)->get(),
            'settings' => $settings,
        ]);
    }

    public function settings()
    {
        return view('admin.settings', [
            'settings' => AttendanceSetting::current(),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'work_start_time' => ['required', 'date_format:H:i'],
            'work_end_time' => ['required', 'date_format:H:i', 'after:work_start_time'],
            'late_grace_minutes' => ['required', 'integer', 'min:0', 'max:240'],
            'allowed_networks' => ['nullable', 'string', 'max:2000'],
            'late_evidence_required' => ['nullable', 'boolean'],
            'password_expiry_days' => ['required', 'integer', 'min:30', 'max:365'],
            'default_annual_leave_days' => ['required', 'integer', 'min:0', 'max:60'],
        ]);

        $validated['late_evidence_required'] = $request->boolean('late_evidence_required');
        $validated['work_start_time'] .= ':00';
        $validated['work_end_time'] .= ':00';

        AttendanceSetting::current()->update($validated);

        return redirect()
            ->route('admin.settings')
            ->with('success', 'System settings updated successfully.');
    }


    /**
     * LIST HR
     */
    public function listHR()
    {
        $hrs = User::where('role', 'hr')->latest()->get();

        return view('admin.hr.index', compact('hrs'));
    }

    /**
     * SHOW CREATE FORM
     */
    public function createHR()
    {
        return view('admin.hr.create');
    }

    /**
     * STORE HR (uses service)
     */
    public function storeHR(StoreHRRequest $request)
    {
        $this->hrService->create(
            $request->validated(),
            $request->file('profile_picture'),
            Auth::id()
        );

        return redirect()
            ->route('admin.hr.index')
            ->with('success', 'HR created successfully');
    }

    /**
     * SHOW EDIT FORM
     */
    public function editHR(User $hr)
    {
        if ($hr->role !== 'hr') {
            abort(404);
        }

        return view('admin.hr.edit', compact('hr'));
    }

    /**
     * UPDATE HR
     */
    public function updateHR(StoreHRRequest $request, User $hr)
    {
        if ($hr->role !== 'hr') {
            abort(404);
        }

        $this->hrService->update(
            $hr,
            $request->validated(),
            $request->file('profile_picture')
        );

        return redirect()
            ->route('admin.hr.index')
            ->with('success', 'HR updated successfully');
    }

    /**
     * DELETE HR
     */
    public function destroyHR(User $hr)
    {
        if ($hr->role !== 'hr') {
            abort(404);
        }

        $this->hrService->delete($hr);

        return redirect()
            ->route('admin.hr.index')
            ->with('success', 'HR deleted successfully');
    }
}

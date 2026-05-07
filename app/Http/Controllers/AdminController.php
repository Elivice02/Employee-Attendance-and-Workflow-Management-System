<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHRRequest;
use App\Services\HRService;
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
        return view('admin.dashboard', [
            'totalUsers' => User::count(),
            'totalHR' => User::where('role', 'hr')->count(),
            'totalEmployees' => User::where('role', 'employee')->count(),
            'recentUsers' => User::latest()->take(5)->get(),
        ]);
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
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Department;
use App\Events\EmployeeCreated;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::whereIn('role', ['employee', 'supervisor'])
            ->with('department')
            ->latest()
            ->get();

        return view('hr.employees.index', compact('employees'));
    }

    public function create()
    {
        $departments = Department::all();
        $supervisors = User::where('role', 'supervisor')->orderBy('name')->get();

        return view('hr.employees.create', compact('departments', 'supervisors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'date_of_birth' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(['employee', 'supervisor'])],
            'department_id' => ['nullable', 'exists:departments,id'],
            'supervisor_id' => ['nullable', 'exists:users,id'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $tempPassword = Str::random(10);

        if ($request->hasFile('profile_picture')) {
            $validated['profile_picture'] = $request->file('profile_picture')->store('profiles', 'public');
        }

        $validated['password'] = Hash::make($tempPassword);
        $validated['must_change_password'] = true;

        if (($validated['role'] ?? null) === 'supervisor') {
            $validated['supervisor_id'] = null;
        }

        $employee = User::create($validated);

        EmployeeCreated::dispatch($employee, $tempPassword);

        return redirect()->route('hr.employees.index')
            ->with('success', ucfirst($employee->role) . ' created');
    }

    public function edit(User $employee)
    {
        if (! in_array($employee->role, ['employee', 'supervisor'], true)) {
            abort(404);
        }

        $departments = Department::all();
        $supervisors = User::where('role', 'supervisor')
            ->whereKeyNot($employee->id)
            ->orderBy('name')
            ->get();

        return view('hr.employees.edit', compact('employee', 'departments', 'supervisors'));
    }

    public function update(Request $request, User $employee)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($employee->id)],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'date_of_birth' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(['employee', 'supervisor'])],
            'department_id' => ['nullable', 'exists:departments,id'],
            'supervisor_id' => ['nullable', 'exists:users,id'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($validated['role'] === 'supervisor') {
            $validated['supervisor_id'] = null;
        }

        if ($request->hasFile('profile_picture')) {
            if ($employee->profile_picture) {
                Storage::disk('public')->delete($employee->profile_picture);
            }

            $validated['profile_picture'] = $request->file('profile_picture')->store('profiles', 'public');
        }

        $employee->update($validated);

        return redirect()->route('hr.employees.index')
            ->with('success', ucfirst($employee->role) . ' updated');
    }

    public function destroy(User $employee)
    {
        if (! in_array($employee->role, ['employee', 'supervisor'], true)) {
            abort(404);
        }

        $employee->delete();

        return back()->with('success', 'User removed');
    }
}

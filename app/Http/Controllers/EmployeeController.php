<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Department;
use App\Events\EmployeeCreated;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::where('role', 'employee')
            ->with('department')
            ->latest()
            ->get();

        return view('hr.employees.index', compact('employees'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('hr.employees.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $tempPassword = Str::random(10);

        $employee = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($tempPassword),
            'role' => 'employee',
            'department_id' => $request->department_id,
            'must_change_password' => true,
        ]);

        EmployeeCreated::dispatch($employee, $tempPassword);

        return redirect()->route('hr.employees.index')
            ->with('success', 'Employee created');
    }

    public function edit(User $employee)
    {
        $departments = Department::all();
        return view('hr.employees.edit', compact('employee', 'departments'));
    }

    public function update(Request $request, User $employee)
    {
        $employee->update([
            'name' => $request->name,
            'email' => $request->email,
            'department_id' => $request->department_id,
        ]);

        return redirect()->route('hr.employees.index')
            ->with('success', 'Employee updated');
    }

    public function destroy(User $employee)
    {
        $employee->delete();

        return back()->with('success', 'Employee removed');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::latest()->get();

        return view('admin.departments.index', compact('departments'));
    }

    /**
     * SHOW CREATE FORM
     */
    public function create()
    {
        $employees = User::whereIn('role', ['supervisor'])->get();
        return view('admin.departments.create', compact('employees'));
    }

    /**
     * STORE DEPARTMENT
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:departments,name',
            'description' => 'nullable|string',
            'head_id' => 'nullable|exists:users,id',
        ], [
            'name.unique' => 'Department name already exists',
            'head_id.exists' => 'Selected department head does not exist',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'active';

        Department::create($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully');
    }

    /**
     * SHOW EDIT FORM
     */
    public function edit(Department $department)
    {
        $employees = User::whereIn('role', ['supervisor', 'employee'])->get();
        return view('admin.departments.edit', compact('department', 'employees'));
    }

    /**
     * UPDATE DEPARTMENT
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
            'head_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully');
    }

    /**
     * DELETE DEPARTMENT
     */
    public function destroy(Department $department)
    {
        // Check if department has employees
        if ($department->employees()->count() > 0) {
            return back()->with('error', 'Cannot delete department with assigned employees');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    /**
     * LIST DEPARTMENTS (HR only)
     */
    public function index()
    {
        $departments = Department::where('created_by', Auth::id())
            ->orWhere('status', 'active')
            ->latest()
            ->get();

        return view('hr.departments.index', compact('departments'));
    }

    /**
     * SHOW CREATE FORM
     */
    public function create()
    {
        $employees = User::where('role', 'employee')->get();
        return view('hr.departments.create', compact('employees'));
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

        return redirect()->route('hr.departments.index')
            ->with('success', 'Department created successfully');
    }

    /**
     * SHOW EDIT FORM
     */
    public function edit(Department $department)
    {
        // Only creator can edit
        if ($department->created_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $employees = User::where('role', 'employee')->get();
        return view('hr.departments.edit', compact('department', 'employees'));
    }

    /**
     * UPDATE DEPARTMENT
     */
    public function update(Request $request, Department $department)
    {
        // Only creator can update
        if ($department->created_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
            'head_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        $department->update($validated);

        return redirect()->route('hr.departments.index')
            ->with('success', 'Department updated successfully');
    }

    /**
     * DELETE DEPARTMENT
     */
    public function destroy(Department $department)
    {
        // Only creator can delete
        if ($department->created_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check if department has employees
        if ($department->employees()->count() > 0) {
            return back()->with('error', 'Cannot delete department with assigned employees');
        }

        $department->delete();

        return redirect()->route('hr.departments.index')
            ->with('success', 'Department deleted successfully');
    }
}

@extends('layouts.hr')

@section('content')
<h1 class="text-2xl font-bold mb-4">Employees</h1>

<a href="{{ route('hr.employees.create') }}"
   class="bg-teal-600 text-white px-4 py-2 rounded mb-4 inline-block">
   + Add Employee
</a>

<table class="w-full border">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-3">Name</th>
            <th class="p-3">Email</th>
            <th class="p-3">Department</th>
            <th class="p-3">Actions</th>
        </tr>
    </thead>

    <tbody>
        @foreach($employees as $employee)
        <tr class="border-t">
            <td class="p-3">{{ $employee->name }}</td>
            <td class="p-3">{{ $employee->email }}</td>
            <td class="p-3">{{ $employee->department->name ?? 'N/A' }}</td>

            <td class="p-3 flex gap-2">

                <!-- Promote -->
                <a href="{{ route('hr.employees.promote.form', $employee->id) }}"
                   class="bg-green-500 text-white px-3 py-1 rounded text-sm">
                    Promote
                </a>

                <!-- Edit -->
                <a href="{{ route('hr.employees.edit', $employee->id) }}"
                   class="bg-blue-500 text-white px-3 py-1 rounded text-sm">
                    Edit
                </a>

                <!-- Delete -->
                <form action="{{ route('hr.employees.destroy', $employee->id) }}"
                      method="POST"
                      onsubmit="return confirm('Delete this employee?')">
                    @csrf
                    @method('DELETE')
                    <button class="bg-red-500 text-white px-3 py-1 rounded text-sm">
                        Delete
                    </button>
                </form>

            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
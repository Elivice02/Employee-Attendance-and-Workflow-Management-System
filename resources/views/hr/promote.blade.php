@extends('layouts.hr')

@section('content')
<div class="max-w-3xl mx-auto">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Promote Employee</h1>
        <p class="text-gray-500 mt-1">
            Change employee role and update access permissions.
        </p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">

        <!-- Employee Info -->
        <div class="mb-6 p-4 rounded-xl bg-gray-50 border">
            <p class="text-gray-700 mb-2">
                <span class="font-semibold">Employee:</span> {{ $user->name }}
            </p>

            <p class="text-gray-700">
                <span class="font-semibold">Current Role:</span>
                <span class="inline-block px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-full">
                    {{ ucfirst($user->role) }}
                </span>
            </p>
        </div>

        <!-- Warning -->
        <div class="mb-6 p-4 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
            This action will immediately update this user's permissions and dashboard access.
        </div>

        <!-- Form -->
        <form action="{{ route('hr.employees.promote', $user->id) }}" method="POST" class="space-y-6">
            @csrf

            <!-- Role -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    New Role
                </label>

                <select
                    name="role"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                >
                    <option value="supervisor">Supervisor</option>
                    <option value="hr">HR</option>
                </select>
            </div>

            <!-- Reason -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Promotion Reason
                </label>

                <textarea
                    name="reason"
                    rows="4"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="Explain why this employee is being promoted..."
                ></textarea>
            </div>

            <!-- Button -->
            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-xl shadow"
                >
                    Confirm Promotion
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
<head>
    <meta charset="UTF-8">
    <title>Create Department</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<div class="max-w-4xl mx-auto mt-10">

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6">
            <h2 class="text-2xl font-bold text-white">Create Department</h2>
            <p class="text-blue-100 text-sm mt-1">
                Add a new department to your organization
            </p>
        </div>

        <!-- Body -->
        <div class="p-8">

            <x-alert />

            <form method="POST" action="{{ route('admin.departments.store') }}" class="space-y-8">
                @csrf

                <!-- BASIC INFO -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Department Information</h3>

                    <!-- Name -->
                    <div class="mb-5">
                        <label class="text-sm font-medium text-gray-600">Department Name *</label>
                        <input name="name" value="{{ old('name') }}" placeholder="e.g., Human Resources, Finance"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition @error('name') border-red-500 @enderror">

                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-5">
                        <label class="text-sm font-medium text-gray-600">Description</label>
                        <textarea name="description" placeholder="Briefly describe the department's role and responsibilities"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition" rows="4">{{ old('description') }}</textarea>

                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Department Head -->
                    <div>
                        <label class="text-sm font-medium text-gray-600">Department Head (Optional)</label>
                        <select name="head_id"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition @error('head_id') border-red-500 @enderror">
                            <option value="">Select a department head</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('head_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }} ({{ $employee->email }})
                                </option>
                            @endforeach
                        </select>

                        @error('head_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- BUTTON -->
                <div class="pt-4 flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-lg font-semibold shadow-md hover:shadow-lg hover:scale-[1.01] transition">
                        Create Department
                    </button>

                    <a href="{{ route('admin.departments.index') }}"
                        class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg font-semibold text-center shadow-md hover:shadow-lg hover:scale-[1.01] transition">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

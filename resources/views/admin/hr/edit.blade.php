<head>
    <meta charset="UTF-8">
    <title>Edit HR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<div class="max-w-4xl mx-auto mt-10">

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6">
            <h2 class="text-2xl font-bold text-white">Edit HR Manager</h2>
            <p class="text-blue-100 text-sm mt-1">
                Update HR manager information
            </p>
        </div>

        <!-- Body -->
        <div class="p-8">

            <x-alert />

            <form method="POST" action="{{ route('admin.hr.update', $hr->id) }}" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- BASIC INFO -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <!-- Name -->
                        <div>
                            <label class="text-sm font-medium text-gray-600">Full Name</label>
                            <input name="name" value="{{ old('name', $hr->name) }}"
                                class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition">

                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div>
                            <label class="text-sm font-medium text-gray-600">Gender</label>
                            <select name="gender"
                                class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $hr->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $hr->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            </select>

                            @error('gender')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <!-- DOB -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $hr->date_of_birth) }}"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition">

                        @error('date_of_birth')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-600">Phone Number</label>
                        <input name="phone" value="{{ old('phone', $hr->phone) }}" placeholder="0712345678 or +255712345678"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        <p class="text-xs text-gray-500 mt-1">Use a Tanzania mobile number. It will be saved as +255 format for SMS.</p>

                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    </div>
                </div>

                <!-- ACCOUNT -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Information</h3>

                    <div>
                        <label class="text-sm font-medium text-gray-600">Email Address</label>
                        <input name="email" value="{{ old('email', $hr->email) }}"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition">

                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- PROFILE -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Profile Picture</h3>

                    @if($hr->profile_picture)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Current Picture:</p>
                            <img src="{{ Storage::url($hr->profile_picture) }}" alt="{{ $hr->name }}" class="h-24 w-24 rounded-lg object-cover">
                        </div>
                    @endif

                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition">
                        <input type="file" name="profile_picture" class="mx-auto">

                        <p class="text-sm text-gray-500 mt-2">
                            Upload JPG or PNG (Max 2MB)
                        </p>
                    </div>

                    @error('profile_picture')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- BUTTON -->
                <div class="pt-4 flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-lg font-semibold shadow-md hover:shadow-lg hover:scale-[1.01] transition">
                        Update HR Manager
                    </button>
                    
                    <a href="{{ route('admin.hr.index') }}"
                        class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg font-semibold text-center shadow-md hover:shadow-lg hover:scale-[1.01] transition">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

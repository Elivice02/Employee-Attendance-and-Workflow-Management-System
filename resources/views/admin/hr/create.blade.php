<head>
    <meta charset="UTF-8">
    <title>Create HR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<div class="max-w-4xl mx-auto mt-10">

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6">
            <h2 class="text-2xl font-bold text-white">Create HR Manager</h2>
            <p class="text-blue-100 text-sm mt-1">
                Add a new HR manager to your system
            </p>
        </div>

        <!-- Body -->
        <div class="p-8">

            <x-alert />

            <form method="POST" action="{{ route('admin.hr.store') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- BASIC INFO -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <!-- Name -->
                        <div>
                            <label class="text-sm font-medium text-gray-600">Full Name</label>
                            <input name="name" value="{{ old('name') }}"
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
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
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
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition">

                        @error('date_of_birth')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-600">Phone Number</label>
                        <input name="phone" value="{{ old('phone') }}"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition">

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
                        <input name="email" value="{{ old('email') }}"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition">

                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <p class="text-sm text-gray-500 mt-2">
                        A temporary password will be automatically generated and sent via email.
                    </p>
                </div>

                <!-- PROFILE -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Profile Picture</h3>

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
                <div class="pt-4">
                    <button
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-lg font-semibold shadow-md hover:shadow-lg hover:scale-[1.01] transition">
                        Create HR Manager
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

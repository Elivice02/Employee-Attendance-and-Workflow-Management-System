<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="min-h-screen py-10 px-4">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Profile</h1>
                <p class="text-sm text-gray-500 capitalize">{{ $user->role }} account</p>
            </div>

            <a href="{{ url('/' . $user->role . '/dashboard') }}" class="text-gray-600 hover:text-gray-900">
                Back
            </a>
        </div>

        <x-alert />

        @if ($errors->any())
            <div class="mb-6 rounded border border-red-200 bg-red-50 p-4 text-red-700">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border p-2 rounded" required>
                    @error('name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1 font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border p-2 rounded" required>
                    @error('email')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1 font-medium text-gray-700">Gender</label>
                    <select name="gender" class="w-full border p-2 rounded">
                        <option value="">Select gender</option>
                        <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1 font-medium text-gray-700">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}" class="w-full border p-2 rounded">
                    @error('date_of_birth')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1 font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="0712345678 or +255712345678" class="w-full border p-2 rounded">
                    <p class="text-xs text-gray-500 mt-1">Use a Tanzania mobile number. It will be saved as +255 format for SMS.</p>
                    @error('phone')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1 font-medium text-gray-700">Profile Picture</label>
                    @if($user->profile_picture)
                        <img src="{{ Storage::url($user->profile_picture) }}" alt="{{ $user->name }}" class="h-20 w-20 rounded object-cover mb-3">
                    @endif
                    <input type="file" name="profile_picture" class="w-full border p-2 rounded">
                    @error('profile_picture')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="rounded bg-gray-50 border p-4 text-sm text-gray-600">
                <p><span class="font-medium">Role:</span> {{ ucfirst($user->role) }}</p>
                <p><span class="font-medium">Department:</span> {{ $user->department?->name ?? 'N/A' }}</p>
                <p><span class="font-medium">Supervisor:</span> {{ $user->supervisor?->name ?? 'N/A' }}</p>
            </div>

            <div class="flex justify-end">
                <button class="bg-teal-600 text-white px-6 py-2 rounded hover:bg-teal-700 transition">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>

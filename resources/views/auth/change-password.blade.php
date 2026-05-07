<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6">
            <h2 class="text-2xl font-bold text-white">
                Change Your Password
            </h2>
            <p class="text-blue-100 text-sm mt-1">
                Create a strong password to secure your account
            </p>
        </div>

        <!-- Body -->
        <div class="p-8">
            <x-alert />

            <form method="POST" action="/change-password" class="space-y-5">
                @csrf

                <!-- Current Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Current Password
                    </label>
                    <input type="password" name="current_password" placeholder="Enter your current password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition @error('current_password') border-red-500 @enderror"
                        required>

                    @error('current_password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        New Password
                    </label>
                    <input type="password" name="password" placeholder="Enter new password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition @error('password') border-red-500 @enderror"
                        required>

                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm Password
                    </label>
                    <input type="password" name="password_confirmation" placeholder="Confirm new password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                        required>
                </div>

                <!-- Password Policy Requirements -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm font-semibold text-gray-800 mb-3">Password must contain:</p>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li class="flex items-center">
                            <span class="w-4 h-4 bg-blue-300 rounded-full mr-2"></span>
                            At least 8 characters
                        </li>
                        <li class="flex items-center">
                            <span class="w-4 h-4 bg-blue-300 rounded-full mr-2"></span>
                            At least one uppercase letter (A-Z)
                        </li>
                        <li class="flex items-center">
                            <span class="w-4 h-4 bg-blue-300 rounded-full mr-2"></span>
                            At least one lowercase letter (a-z)
                        </li>
                        <li class="flex items-center">
                            <span class="w-4 h-4 bg-blue-300 rounded-full mr-2"></span>
                            At least one number (0-9)
                        </li>
                        <li class="flex items-center">
                            <span class="w-4 h-4 bg-blue-300 rounded-full mr-2"></span>
                            At least one special character (!@#$%^&*...)
                        </li>
                    </ul>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-3 rounded-lg hover:shadow-lg hover:scale-[1.02] transition mt-6">
                    Update Password
                </button>
            </form>
        </div>
    </div>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --primary: #0f766e;
            --primary-dark: #115e59;
            --accent: #f59e0b;
            --background: #f3f4f6;
            --input-bg: #f9fafb;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --danger: #dc2626;
        }
    </style>
</head>
<body class="bg-[var(--background)] flex items-center justify-center min-h-screen">

    <div class="bg-white w-[430px] rounded-lg shadow-lg p-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-semibold text-[var(--primary)]">
                Reset Password
            </h2>

            <a href="{{ route('login') }}"
               class="bg-[var(--primary)] text-[var(--accent)] w-8 h-8 rounded flex items-center justify-center font-bold">
                ×
            </a>
        </div>

        <!-- Divider -->
        <div class="border-b-2 border-[var(--primary)] mb-6"></div>

        <p class="text-sm text-[var(--text-muted)] mb-6">
            Enter your new password below to reset your account access.
        </p>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email -->
            <div class="mb-4">
                <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    required
                    class="w-full p-4 rounded bg-[var(--input-bg)] border border-gray-200 outline-none focus:ring-2 focus:ring-[var(--primary)]"
                >
                @error('email')
                    <p class="text-[var(--danger)] text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div class="mb-4">
                <input
                    type="password"
                    name="password"
                    placeholder="New Password"
                    required
                    class="w-full p-4 rounded bg-[var(--input-bg)] border border-gray-200 outline-none focus:ring-2 focus:ring-[var(--primary)]"
                >
                @error('password')
                    <p class="text-[var(--danger)] text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-5">
                <input
                    type="password"
                    name="password_confirmation"
                    placeholder="Confirm Password"
                    required
                    class="w-full p-4 rounded bg-[var(--input-bg)] border border-gray-200 outline-none focus:ring-2 focus:ring-[var(--primary)]"
                >
            </div>

            <!-- Submit -->
            <button
                type="submit"
                class="w-full bg-[var(--primary)] hover:bg-[var(--primary-dark)] text-white font-semibold py-4 rounded transition duration-200"
            >
                Reset Password
            </button>
        </form>

        <div class="mt-5 text-center">
            <a href="{{ route('login') }}"
               class="text-[var(--primary)] hover:underline text-sm">
                Back to Login
            </a>
        </div>
    </div>

</body>
</html>
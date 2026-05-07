<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>

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
        }
    </style>
</head>
<body class="bg-[var(--background)] flex items-center justify-center min-h-screen">

    <div class="bg-white w-[420px] p-8 rounded-lg shadow-lg">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-semibold text-[var(--primary)]">
                Forgot Password
            </h2>

            <a href="{{ route('login') }}"
               class="bg-[var(--primary)] text-[var(--accent)] w-8 h-8 flex items-center justify-center font-bold rounded">
                ×
            </a>
        </div>

        <div class="border-b-2 border-[var(--primary)] mb-6"></div>

        <p class="text-sm text-[var(--text-muted)] mb-6">
            Enter your email address and we'll send you a password reset link.
        </p>

        @if(session('status'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-700 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <input
                type="email"
                name="email"
                placeholder="Enter email"
                required
                class="w-full p-4 rounded bg-[var(--input-bg)] border border-gray-200 outline-none mb-5 focus:ring-2 focus:ring-[var(--primary)]"
            >

            @error('email')
                <p class="text-red-500 text-sm mb-4">{{ $message }}</p>
            @enderror

            <button
                type="submit"
                class="w-full bg-[var(--primary)] hover:bg-[var(--primary-dark)] text-white font-semibold py-4 rounded transition duration-200"
            >
                Send Reset Link
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
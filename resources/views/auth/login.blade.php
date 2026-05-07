<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-gray-200 flex items-center justify-center min-h-screen">

    <div class="bg-white w-[430px] rounded shadow-md p-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-semibold text-cyan-700">Login</h2>

        </div>

        <!-- Divider -->
        <div class="border-b-2 border-cyan-700 mb-8"></div>

        <x-alert />

        <form method="POST" action="/login">
            @csrf

            <!-- Email -->
            <div class="relative mb-5">
                <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    required
                    class="w-full bg-gray-100 p-4 pr-12 rounded outline-none text-lg placeholder-gray-500"
                >
                <i class="fa-solid fa-envelope absolute right-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
            </div>

            <!-- Password -->
            <div class="relative mb-6">
                <input
                    type="password"
                    name="password"
                    placeholder="Password"
                    required
                    class="w-full bg-gray-100 p-4 pr-12 rounded outline-none text-lg placeholder-gray-500"
                >
                <i class="fa-solid fa-eye absolute right-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
            </div>

            <!-- Login button -->
            <button
                type="submit"
                class="w-full bg-cyan-700 hover:bg-cyan-800 text-white font-semibold py-4 rounded text-lg transition"
            >
                Login
            </button>

            <!-- Footer options -->
            <div class="flex justify-between items-center mt-5 text-sm">
                <a href="{{ route('password.request') }}"
                   class="text-blue-500 hover:underline">
                    Forgot Password?
                </a>

                <label class="flex items-center gap-2 text-gray-600">
                    <input type="checkbox" name="remember" class="w-4 h-4">
                    Remember Me
                </label>
            </div>
        </form>
    </div>

    <script>
        const passwordInput = document.querySelector('input[name="password"]');
        const eyeIcon = document.querySelector('.fa-eye');

        eyeIcon.addEventListener('click', () => {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    </script>

</body>
</html>
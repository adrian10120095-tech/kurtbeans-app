<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KurtBeans Coffee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Playfair Display', serif; }

        .panel-left {
            background-color: #F5EFE0;
        }

        .input-field {
            background-color: #FDF8F0;
            border: 1.5px solid #E8DFC8;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .input-field:focus {
            outline: none;
            border-color: #2C1A0E;
            box-shadow: 0 0 0 3px rgba(44, 26, 14, 0.08);
        }

        .btn-signin {
            background-color: #2C1A0E;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }
        .btn-signin:hover {
            background-color: #3D2516;
        }
        .btn-signin:active {
            transform: scale(0.99);
        }

        .checkbox-custom {
            accent-color: #2C1A0E;
            width: 15px;
            height: 15px;
        }

        .logo-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .decorative-icon {
            font-size: 22px;
            opacity: 0.55;
        }
    </style>
</head>
<body class="bg-[#1E3030] flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-3xl bg-white rounded-2xl overflow-hidden flex shadow-2xl" style="min-height: 380px;">

        <!-- Panel Kiri — Cream / Brand -->
        <div class="panel-left w-5/12 flex flex-col items-center justify-between p-10 py-14">
            <!-- Logo -->
            <div class="logo-wrapper mt-10">
                <img src="{{ asset('images/logo2.png') }}" alt="KurtBeans Logo" class="w-40 h-40 object-contain">
            </div>

        </div>

        <!-- Panel Kanan — Form -->
        <div class="w-7/12 bg-white flex flex-col justify-center px-10 py-12">

            <h1 class="font-display text-2xl font-semibold text-[#1C1210] mb-8 tracking-tight">Welcome back</h1>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Username -->
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#9E8B78]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </span>
                    <input
                        id="username"
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Username"
                        class="input-field w-full pl-10 pr-4 py-3 rounded-xl text-sm text-[#2C1A0E] placeholder-[#B8A898]"
                    >
                    <x-input-error :messages="$errors->get('username')" class="mt-1.5 text-red-500 text-xs" />
                </div>

                <!-- Password -->
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#9E8B78]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </span>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Password"
                        class="input-field w-full pl-10 pr-11 py-3 rounded-xl text-sm text-[#2C1A0E] placeholder-[#B8A898]"
                    >
                    <button type="button" onclick="togglePassword()" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[#9E8B78] hover:text-[#5C4030] focus:outline-none transition-colors">
                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-red-500 text-xs" />
                </div>

                <!-- Remember me + Forgot password -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="checkbox-custom rounded">
                        <span class="text-xs text-[#7A6A5A] font-light">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs text-[#A07850] hover:text-[#2C1A0E] transition-colors font-medium">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Tombol Sign In -->
                <div class="pt-2">
                    <button type="submit" class="btn-signin w-full text-white text-sm font-medium py-3.5 rounded-full flex items-center justify-center gap-2">
                        Sign In
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </div>

            </form>
        </div>

    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />';
            }
        }
    </script>
</body>
</html>
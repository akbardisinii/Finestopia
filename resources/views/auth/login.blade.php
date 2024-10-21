<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PFMS') }} - Login</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    
    <link rel="shortcut icon" href="{{ asset('logo.png') }}" type="image/x-icon" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        .text-customGreen {
            color: #2ca444;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        vibrantGreen: '#FBEEA0',
                        customRed: '#634969',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        };

        // Function to toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</head>
<body class="bg-vibrantGreen bg-opacity-20 font-sans">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-lg p-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Finestopia</h2>
            
            <form method="POST" action="{{ route('login') }}" id="logIn">
                @csrf
                
                <!-- Email -->
                <div class="mb-4">
                    <input id="email" type="email" class="w-full px-4 py-3 rounded-lg bg-gray-100 border-transparent focus:border-vibrantGreen focus:bg-white focus:ring-0 text-sm" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email" autofocus>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6 relative">
                    <input id="password" type="password" class="w-full px-4 py-3 rounded-lg bg-gray-100 border-transparent focus:border-vibrantGreen focus:bg-white focus:ring-0 text-sm pr-10" name="password" required autocomplete="current-password" placeholder="Password">
                    <i id="eye-icon" class="fa-regular fa-eye absolute right-3 top-3 cursor-pointer" onclick="togglePassword()" style="font-size: 20px;"></i>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <button type="submit" class="w-full bg-customRed text-white p-3 rounded-lg font-semibold hover:bg-purple-700 transition duration-200">
                        Login
                    </button>
                </div>
            </form>
            
            <p class="text-center text-sm text-gray-500">
                Tidak memiliki akun? 
                <a href="{{ route('register') }}" class="text-customRed hover:underline font-semibold">Daftar</a>
            </p>
        </div>
    </div>
</body>
</html>

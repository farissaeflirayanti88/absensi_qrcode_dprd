<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sistem Absensi QR Code DPRD Kota Batam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .login-bg {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #60a5fa 100%);
        }
        .shake-error {
            animation: shake 0.5s ease-in-out;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6 text-center">
                <!-- Logo Placeholder -->
                <div class="mb-4 flex justify-center">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center p-2">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-landmark text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-white">Sistem Absensi QR Code</h1>
                <p class="text-blue-100 text-sm mt-1">DPRD Kota Batam</p>
            </div>

            <!-- Form Section -->
            <div class="p-6">
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 shake-error">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    </div>
                @endif

                @if (session('status'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="csrfToken">
                    
                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-blue-500"></i>
                            Username
                        </label>
                        <input type="text" id="username" name="username" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                               required autofocus
                               placeholder="Masukkan username"
                               value="{{ old('username') }}">
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 text-blue-500"></i>
                            Password
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 pr-10"
                                   required
                                   placeholder="Masukkan password">
                            <button type="button" id="togglePassword" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-blue-600">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" id="loginBtn"
                            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-200 font-medium flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        <span id="btnText">Masuk</span>
                        <div id="loadingSpinner" class="hidden ml-2">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </button>

                    <!-- Footer Info -->
                    <div class="mt-6 pt-4 border-t border-gray-200 text-center">
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-shield-alt mr-1"></i>
                            Sistem Absensi QR Code DPRD Kota Batam &copy; {{ date('Y') }}
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        // Form submission handling
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            const spinner = document.getElementById('loadingSpinner');
            
            // Show loading state
            btn.disabled = true;
            btnText.textContent = 'Memproses...';
            spinner.classList.remove('hidden');
        });

        // Auto-focus username field
        document.getElementById('username').focus();
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Safety Permit INKA Madiun</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logok3.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="min-h-screen flex flex-col selection:bg-inka-navy selection:text-white">

    <main class="relative flex-1 flex items-center justify-center p-4">
        
        {{-- Tombol Kembali --}}
        <a href="{{ url('/') }}" class="absolute top-6 left-6 md:top-8 md:left-8 flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-gray-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Beranda
        </a>

        <div class="w-full max-w-[400px]">
            
            {{-- Logo Area --}}
            <div class="flex justify-center mb-8">
                <a href="{{ url('/') }}" class="inline-block transition-transform hover:scale-105">
                    <img src="{{ asset('assets/images/logoinka.svg') }}" alt="Logo PT INKA" class="h-12 w-auto">
                </a>
            </div>

            {{-- Login Card --}}
            <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-6 sm:p-8">
                
                {{-- Header --}}
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Masuk ke Akun</h1>
                    <p class="text-sm text-gray-500">Sistem Monitoring K3 Kontraktor</p>
                </div>

                {{-- Error Alert (Server & Client) --}}
                <div id="error-alert" class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 p-4 rounded-xl transition-opacity duration-300 {{ $errors->any() ? '' : 'hidden' }}">
                    <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div id="error-message" class="text-sm font-medium text-red-800">
                        {{ $errors->any() ? 'Username atau kata sandi salah.' : '' }}
                    </div>
                </div>

                {{-- Form --}}
                <form method="POST" action="{{ route('login') }}" id="login-form" class="space-y-5" novalidate>
                    @csrf

                    {{-- Username --}}
                    <div>
                        <label for="username" class="block text-sm font-semibold text-gray-700 mb-1.5">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username"
                                class="login-input block w-full pl-11 pr-4 py-2.5 bg-gray-50 border {{ $errors->any() ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20' : 'border-gray-200 focus:border-inka-navy focus:ring-inka-navy/20' }} rounded-xl text-sm transition-all duration-200 focus:bg-white focus:outline-none focus:ring-4 placeholder-gray-400" 
                                placeholder="Masukkan username Anda">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Kata Sandi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="login-input block w-full pl-11 pr-12 py-2.5 bg-gray-50 border {{ $errors->any() ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20' : 'border-gray-200 focus:border-inka-navy focus:ring-inka-navy/20' }} rounded-xl text-sm transition-all duration-200 focus:bg-white focus:outline-none focus:ring-4 placeholder-gray-400" 
                                placeholder="••••••••">
                            
                            {{-- Toggle Password Visibility --}}
                            <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
                                <svg id="eye-icon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="eye-off-icon" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-2">
                        <button type="submit" id="submit-btn" class="w-full flex items-center justify-center gap-2 py-3 px-4 border border-transparent rounded-xl text-sm font-bold text-white bg-inka-navy hover:bg-opacity-90 focus:outline-none focus:ring-4 focus:ring-inka-navy/30 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed">
                            <span id="btn-text">Masuk</span>
                            <svg id="btn-spinner" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white hidden" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="py-6 text-center text-xs text-gray-400">
        <p>&copy; {{ date('Y') }} PT INKA (Persero) Madiun. All rights reserved.</p>
    </footer>

    {{-- Interactivity Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('login-form');
            const submitBtn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const btnSpinner = document.getElementById('btn-spinner');
            const errorAlert = document.getElementById('error-alert');
            const inputs = document.querySelectorAll('.login-input');
            const togglePasswordBtn = document.getElementById('toggle-password');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');

            // 1. Password Visibility Toggle
            togglePasswordBtn.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle icons
                eyeIcon.classList.toggle('hidden');
                eyeOffIcon.classList.toggle('hidden');
                
                // Preserve cursor
                const val = passwordInput.value;
                passwordInput.value = '';
                passwordInput.value = val;
                passwordInput.focus();
            });

            // 2. Hide error alert on input or after 5 seconds
            if (errorAlert) {
                // Auto hide after 5 seconds
                let hideTimer = setTimeout(() => {
                    errorAlert.style.opacity = '0';
                    setTimeout(() => errorAlert.classList.add('hidden'), 300);
                }, 5000);

                // Hide on typing
                inputs.forEach(input => {
                    input.addEventListener('input', () => {
                        errorAlert.style.opacity = '0';
                        setTimeout(() => errorAlert.classList.add('hidden'), 300);
                        clearTimeout(hideTimer);
                        
                        // Remove red borders
                        inputs.forEach(i => {
                            i.classList.remove('border-red-300', 'focus:border-red-500', 'focus:ring-red-500/20');
                            i.classList.add('border-gray-200', 'focus:border-inka-navy', 'focus:ring-inka-navy/20');
                        });
                    });
                });
            }

            // 3. Form Submit State and Validation
            form.addEventListener('submit', function(e) {
                const usernameVal = document.getElementById('username').value.trim();
                const passwordVal = passwordInput.value.trim();

                // Client-side validation for empty fields
                if (!usernameVal || !passwordVal) {
                    e.preventDefault(); // Stop submission
                    
                    let errorMessage = 'Mohon lengkapi username dan kata sandi.';
                    if (!usernameVal && passwordVal) {
                        errorMessage = 'Mohon isi username Anda.';
                    } else if (usernameVal && !passwordVal) {
                        errorMessage = 'Mohon isi kata sandi Anda.';
                    }

                    // Update error message and show alert
                    document.getElementById('error-message').textContent = errorMessage;
                    errorAlert.classList.remove('hidden');
                    errorAlert.style.opacity = '1';
                    
                    // Highlight empty fields
                    inputs.forEach(input => {
                        if (!input.value.trim()) {
                            input.classList.remove('border-gray-200', 'focus:border-inka-navy', 'focus:ring-inka-navy/20');
                            input.classList.add('border-red-300', 'focus:border-red-500', 'focus:ring-red-500/20');
                        }
                    });

                    // Set auto hide timer
                    setTimeout(() => {
                        errorAlert.style.opacity = '0';
                        setTimeout(() => errorAlert.classList.add('hidden'), 300);
                    }, 5000);
                    
                    return;
                }

                // If valid, show loading state
                submitBtn.disabled = true;
                btnText.textContent = 'Masuk...';
                btnSpinner.classList.remove('hidden');
                btnText.classList.add('ml-2');
            });

            // 4. Enter to submit
            inputs.forEach(input => {
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>

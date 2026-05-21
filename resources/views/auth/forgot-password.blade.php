<x-guest-layout>
    <x-auth-card>
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">Forgot Password</h2>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <!-- Rate Limit Timer -->
        <div id="rateLimitTimer" class="hidden mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
            <p class="text-sm text-yellow-800 dark:text-yellow-200 text-center">
                Silakan tunggu <span id="countdown" class="font-bold"></span> detik sebelum mengirim email lagi.
            </p>
        </div>

        <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
            @csrf

            <div class="grid gap-6">
                <!-- Email Address -->
                <div class="space-y-2">
                    <x-form.label
                        for="email"
                        :value="__('Email')"
                        class="text-gray-700 dark:text-gray-300 font-medium"
                    />

                    <x-form.input-with-icon-wrapper>
                        <x-slot name="icon">
                            <x-heroicon-o-mail aria-hidden="true" class="w-5 h-5 text-green-600" />
                        </x-slot>

                        <x-form.input
                            withicon
                            id="email"
                            class="block w-full border-gray-300 dark:border-gray-600 focus:border-green-500 focus:ring-green-500 rounded-lg"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            placeholder="{{ __('Email') }}"
                        />
                    </x-form.input-with-icon-wrapper>
                </div>

                <div>
                    <button type="submit" id="submitBtn" class="w-full py-3 px-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-lg shadow-lg transform transition-all duration-200 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                        {{ __('Email Password Reset Link') }}
                    </button>
                </div>

                <p class="text-sm text-center text-gray-600 dark:text-gray-400">
                    <a href="{{ route('login') }}" class="text-green-600 hover:text-green-700 font-semibold hover:underline">
                        {{ __('Back to Login') }}
                    </a>
                </p>
            </div>
        </form>

        <script>
            let countdownInterval;
            const email = document.getElementById('email');
            const submitBtn = document.getElementById('submitBtn');
            const rateLimitTimer = document.getElementById('rateLimitTimer');
            const countdownSpan = document.getElementById('countdown');

            function checkRateLimit() {
                const key = 'password_reset_' + email.value;
                const expiry = localStorage.getItem(key);
                
                if (expiry) {
                    const now = Math.floor(Date.now() / 1000);
                    const remaining = expiry - now;
                    
                    if (remaining > 0) {
                        startCountdown(remaining);
                        return true;
                    } else {
                        localStorage.removeItem(key);
                    }
                }
                return false;
            }

            function startCountdown(seconds) {
                rateLimitTimer.classList.remove('hidden');
                submitBtn.disabled = true;
                
                countdownSpan.textContent = seconds;
                
                clearInterval(countdownInterval);
                countdownInterval = setInterval(() => {
                    seconds--;
                    countdownSpan.textContent = seconds;
                    
                    if (seconds <= 0) {
                        clearInterval(countdownInterval);
                        rateLimitTimer.classList.add('hidden');
                        submitBtn.disabled = false;
                        localStorage.removeItem('password_reset_' + email.value);
                    }
                }, 1000);
            }

            email.addEventListener('input', () => {
                if (email.value) {
                    checkRateLimit();
                }
            });

            document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
                if (checkRateLimit()) {
                    e.preventDefault();
                    return false;
                }
            });

            // Check on page load if there's an error with seconds
            @if($errors->has('email'))
                const errorMsg = "{{ $errors->first('email') }}";
                const match = errorMsg.match(/(\d+)\s+detik/);
                if (match) {
                    const seconds = parseInt(match[1]);
                    const key = 'password_reset_' + email.value;
                    const expiry = Math.floor(Date.now() / 1000) + seconds;
                    localStorage.setItem(key, expiry);
                    startCountdown(seconds);
                }
            @endif

            // Set rate limit after successful submission
            @if(session('status'))
                const key = 'password_reset_' + email.value;
                const expiry = Math.floor(Date.now() / 1000) + 120;
                localStorage.setItem(key, expiry);
                startCountdown(120);
            @endif
        </script>
    </x-auth-card>
</x-guest-layout>

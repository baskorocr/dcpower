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

        <form method="POST" action="{{ route('password.email') }}">
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
                    <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-lg shadow-lg transform transition-all duration-200 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
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
    </x-auth-card>
</x-guest-layout>

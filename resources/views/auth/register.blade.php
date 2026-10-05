<x-guest-layout>
    <x-auth-card>
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register') }}" x-data="{
            step: 1,
            showPassword: false,
            showPasswordConfirmation: false,
            name: '{{ old('name') }}',
            email: '{{ old('email') }}',
            password: '',
            password_confirmation: '',
            survey_q1: '{{ old('survey_q1') }}',
            survey_q2: '{{ old('survey_q2') }}',
            survey_q3: '{{ old('survey_q3') }}',
            nextStep() {
                if (!this.name || !this.email || !this.password || !this.password_confirmation) {
                    alert('Please fill in all fields.');
                    return;
                }
                if (this.password !== this.password_confirmation) {
                    alert('Passwords do not match.');
                    return;
                }
                this.step = 2;
            }
        }">
            @csrf

            <!-- Step Indicator -->
            <div class="flex items-center justify-center gap-3 mb-6">
                <div class="flex items-center gap-2">
                    <div :class="step >= 1 ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-500'" class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-colors">1</div>
                    <span class="text-xs font-medium" :class="step >= 1 ? 'text-green-600' : 'text-gray-400'">Account</span>
                </div>
                <div class="w-8 h-px" :class="step >= 2 ? 'bg-green-600' : 'bg-gray-300'"></div>
                <div class="flex items-center gap-2">
                    <div :class="step >= 2 ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-500'" class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-colors">2</div>
                    <span class="text-xs font-medium" :class="step >= 2 ? 'text-green-600' : 'text-gray-400'">Survey</span>
                </div>
            </div>

            <!-- STEP 1: Account Info -->
            <div x-show="step === 1" x-transition>
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">Create Account</h2>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Join us today</p>
                </div>

                <div class="grid gap-6">
                    <div class="space-y-2">
                        <x-form.label for="name" :value="__('Name')" class="text-gray-700 dark:text-gray-300 font-medium" />
                        <x-form.input-with-icon-wrapper>
                            <x-slot name="icon">
                                <x-heroicon-o-user aria-hidden="true" class="w-5 h-5 text-green-600" />
                            </x-slot>
                            <x-form.input withicon id="name" class="block w-full border-gray-300 dark:border-gray-600 focus:border-green-500 focus:ring-green-500 rounded-lg" type="text" name="name" x-model="name" required autofocus placeholder="{{ __('Name') }}" />
                        </x-form.input-with-icon-wrapper>
                    </div>

                    <div class="space-y-2">
                        <x-form.label for="email" :value="__('Email')" class="text-gray-700 dark:text-gray-300 font-medium" />
                        <x-form.input-with-icon-wrapper>
                            <x-slot name="icon">
                                <x-heroicon-o-mail aria-hidden="true" class="w-5 h-5 text-green-600" />
                            </x-slot>
                            <x-form.input withicon id="email" class="block w-full border-gray-300 dark:border-gray-600 focus:border-green-500 focus:ring-green-500 rounded-lg" type="email" name="email" x-model="email" required placeholder="{{ __('Email') }}" />
                        </x-form.input-with-icon-wrapper>
                    </div>

                    <div class="space-y-2">
                        <x-form.label for="password" :value="__('Password')" class="text-gray-700 dark:text-gray-300 font-medium" />
                        <div class="relative">
                            <x-form.input-with-icon-wrapper>
                                <x-slot name="icon">
                                    <x-heroicon-o-lock-closed aria-hidden="true" class="w-5 h-5 text-green-600" />
                                </x-slot>
                                <x-form.input withicon id="password" class="block w-full pr-12 border-gray-300 dark:border-gray-600 focus:border-green-500 focus:ring-green-500 rounded-lg" x-bind:type="showPassword ? 'text' : 'password'" name="password" x-model="password" required autocomplete="new-password" placeholder="{{ __('Password') }}" />
                            </x-form.input-with-icon-wrapper>
                            <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-green-600 transition-colors duration-200">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <x-form.label for="password_confirmation" :value="__('Confirm Password')" class="text-gray-700 dark:text-gray-300 font-medium" />
                        <div class="relative">
                            <x-form.input-with-icon-wrapper>
                                <x-slot name="icon">
                                    <x-heroicon-o-lock-closed aria-hidden="true" class="w-5 h-5 text-green-600" />
                                </x-slot>
                                <x-form.input withicon id="password_confirmation" class="block w-full pr-12 border-gray-300 dark:border-gray-600 focus:border-green-500 focus:ring-green-500 rounded-lg" x-bind:type="showPasswordConfirmation ? 'text' : 'password'" name="password_confirmation" x-model="password_confirmation" required placeholder="{{ __('Confirm Password') }}" />
                            </x-form.input-with-icon-wrapper>
                            <button type="button" @click="showPasswordConfirmation = !showPasswordConfirmation" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-green-600 transition-colors duration-200">
                                <svg x-show="!showPasswordConfirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPasswordConfirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="button" @click="nextStep()" class="w-full py-3 px-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-lg shadow-lg transform transition-all duration-200 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        <span class="flex items-center justify-center gap-2">
                            Next
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    </button>

                    <p class="text-sm text-center text-gray-600 dark:text-gray-400">
                        {{ __('Already registered?') }}
                        <a href="{{ route('login') }}" class="text-green-600 hover:text-green-700 font-semibold hover:underline">{{ __('Login') }}</a>
                    </p>
                </div>
            </div>

            <!-- STEP 2: Survey -->
            <div x-show="step === 2" x-transition>
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">Kuesioner</h2>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Bantu kami mengenal Anda lebih baik</p>
                </div>

                <div class="grid gap-6">
                    <!-- Q1 -->
                    <div class="space-y-2">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">1. Sudah berapa lama Anda menggunakan sepeda motor (secara keseluruhan)?</p>
                        <div class="space-y-2">
                            @foreach([
                                'A' => 'Less than 1 year (< 1 tahun)',
                                'B' => '1 – 3 tahun',
                                'C' => '4 – 6 tahun',
                                'D' => 'More than 6 years (> 6 tahun)',
                            ] as $val => $label)
                            <label class="flex items-center gap-3 p-3 rounded-lg border-2 border-gray-200 dark:border-gray-700 hover:border-green-400 dark:hover:border-green-600 cursor-pointer transition-colors" :class="survey_q1 === '{{ $val }}' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : ''">
                                <input type="radio" name="survey_q1" value="{{ $val }}" x-model="survey_q1" class="text-green-600 focus:ring-green-500" required>
                                <span class="text-sm text-gray-700 dark:text-gray-300"><span class="font-bold">{{ $val }}.</span> {{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('survey_q1')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Q2 -->
                    <div class="space-y-2">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">2. Dalam satu kali siklus penggantian, berapa rata-rata umur/masa pakai aki hingga harus diganti kembali?</p>
                        <div class="space-y-2">
                            @foreach([
                                'A' => 'Kurang dari 6 bulan',
                                'B' => '6 – 12 bulan (½ - 1 tahun)',
                                'C' => '13 – 24 bulan (1 - 2 tahun)',
                                'D' => '25 – 36 bulan (2 - 3 tahun)',
                                'E' => 'Lebih dari 36 bulan (> 3 tahun)',
                            ] as $val => $label)
                            <label class="flex items-center gap-3 p-3 rounded-lg border-2 border-gray-200 dark:border-gray-700 hover:border-green-400 dark:hover:border-green-600 cursor-pointer transition-colors" :class="survey_q2 === '{{ $val }}' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : ''">
                                <input type="radio" name="survey_q2" value="{{ $val }}" x-model="survey_q2" class="text-green-600 focus:ring-green-500" required>
                                <span class="text-sm text-gray-700 dark:text-gray-300"><span class="font-bold">{{ $val }}.</span> {{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('survey_q2')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Q3 -->
                    <div class="space-y-2">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">3. Apa alasan utama Anda melakukan penggantian aki? <span class="font-normal text-gray-500">(Pilih 1 jawaban utama)</span></p>
                        <div class="space-y-2">
                            @foreach([
                                'A' => 'Aki tekor/soak/mati total (Motor tidak bisa distarter sama sekali)',
                                'B' => 'Performa kelistrikan menurun (Starter terasa berat, klakson redup, atau lampu meredup)',
                                'C' => 'Kerusakan fisik pada aki (Aki kembung, bocor, atau berkarat)',
                                'D' => 'Perawatan rutin / Pencegahan (Mengganti sesuai rekomendasi usia pakai/servis berkala sebelum mogok)',
                                'E' => 'Rekomendasi dari bengkel / teknisi saat melakukan servis rutin',
                            ] as $val => $label)
                            <label class="flex items-center gap-3 p-3 rounded-lg border-2 border-gray-200 dark:border-gray-700 hover:border-green-400 dark:hover:border-green-600 cursor-pointer transition-colors" :class="survey_q3 === '{{ $val }}' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : ''">
                                <input type="radio" name="survey_q3" value="{{ $val }}" x-model="survey_q3" class="text-green-600 focus:ring-green-500" required>
                                <span class="text-sm text-gray-700 dark:text-gray-300"><span class="font-bold">{{ $val }}.</span> {{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('survey_q3')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex gap-3">
                        <button type="button" @click="step = 1" class="flex-1 py-3 px-4 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-lg transition-colors">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                Back
                            </span>
                        </button>
                        <button type="submit" class="flex-1 py-3 px-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-lg shadow-lg transform transition-all duration-200 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            <span class="flex items-center justify-center gap-2">
                                <x-heroicon-o-user-add class="w-5 h-5" aria-hidden="true" />
                                {{ __('Register') }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </x-auth-card>
</x-guest-layout>

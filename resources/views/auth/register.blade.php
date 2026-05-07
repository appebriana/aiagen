<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" x-data="registerForm()" @submit.prevent="submitForm($event)">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Username -->
        <div class="mt-4">
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autocomplete="username" placeholder="Contoh: johndoe"
                x-model="username"
                @input.debounce.500ms="checkUsername()"
                ::class="{
                    'border-red-500 focus:border-red-500 focus:ring-red-500': usernameStatus === 'taken',
                    'border-green-500 focus:border-green-500 focus:ring-green-500': usernameStatus === 'available'
                }" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
            <p x-show="usernameStatus === 'taken'" x-cloak class="mt-1 text-xs text-red-600" x-text="usernameMessage"></p>
            <p x-show="usernameStatus === 'available'" x-cloak class="mt-1 text-xs text-green-600" x-text="usernameMessage"></p>
            <p x-show="usernameStatus === 'checking'" x-cloak class="mt-1 text-xs text-gray-500">
                <svg class="inline w-3 h-3 animate-spin mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                Mengecek...
            </p>
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="email"
                x-model="email"
                @input.debounce.500ms="checkEmail()"
                ::class="{
                    'border-red-500 focus:border-red-500 focus:ring-red-500': emailStatus === 'taken',
                    'border-green-500 focus:border-green-500 focus:ring-green-500': emailStatus === 'available'
                }" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <p x-show="emailStatus === 'taken'" x-cloak class="mt-1 text-xs text-red-600" x-text="emailMessage"></p>
            <p x-show="emailStatus === 'available'" x-cloak class="mt-1 text-xs text-green-600" x-text="emailMessage"></p>
            <p x-show="emailStatus === 'checking'" x-cloak class="mt-1 text-xs text-gray-500">
                <svg class="inline w-3 h-3 animate-spin mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                Mengecek...
            </p>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative mt-1">
                <x-text-input id="password" class="block w-full pr-10"
                                ::type="showPw ? 'text' : 'password'"
                                name="password"
                                required autocomplete="new-password"
                                x-model="password"
                                @input="validatePassword()" />
                <button type="button" @click="showPw = !showPw" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 focus:outline-none" tabindex="-1">
                    <svg x-show="!showPw" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <svg x-show="showPw" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />

            <!-- Password strength rules -->
            <div class="mt-2 space-y-1" x-show="password.length > 0" x-cloak>
                <p class="text-xs font-medium text-gray-600 mb-1">Persyaratan password:</p>
                <div class="grid grid-cols-2 gap-x-4 gap-y-1">
                    <p class="text-xs flex items-center gap-1.5" :class="rules.minLength ? 'text-green-600' : 'text-red-500'">
                        <svg x-show="rules.minLength" class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <svg x-show="!rules.minLength" class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        Min. 8 karakter
                    </p>
                    <p class="text-xs flex items-center gap-1.5" :class="rules.maxLength ? 'text-green-600' : 'text-red-500'">
                        <svg x-show="rules.maxLength" class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <svg x-show="!rules.maxLength" class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        Maks. 18 karakter
                    </p>
                    <p class="text-xs flex items-center gap-1.5" :class="rules.lowercase ? 'text-green-600' : 'text-red-500'">
                        <svg x-show="rules.lowercase" class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <svg x-show="!rules.lowercase" class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        Huruf kecil (a-z)
                    </p>
                    <p class="text-xs flex items-center gap-1.5" :class="rules.uppercase ? 'text-green-600' : 'text-red-500'">
                        <svg x-show="rules.uppercase" class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <svg x-show="!rules.uppercase" class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        Huruf kapital (A-Z)
                    </p>
                    <p class="text-xs flex items-center gap-1.5" :class="rules.number ? 'text-green-600' : 'text-red-500'">
                        <svg x-show="rules.number" class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <svg x-show="!rules.number" class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        Angka (0-9)
                    </p>
                    <p class="text-xs flex items-center gap-1.5" :class="rules.symbol ? 'text-green-600' : 'text-red-500'">
                        <svg x-show="rules.symbol" class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <svg x-show="!rules.symbol" class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        Simbol (!@#$%...)
                    </p>
                </div>
                <!-- Strength bar -->
                <div class="mt-2">
                    <div class="flex gap-1">
                        <template x-for="i in 6" :key="i">
                            <div class="h-1.5 flex-1 rounded-full transition-all duration-300"
                                 :class="i <= strengthScore ? strengthColor : 'bg-gray-200'"></div>
                        </template>
                    </div>
                    <p class="text-xs mt-1" :class="strengthTextColor" x-text="strengthText"></p>
                </div>
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />

            <div class="relative mt-1">
                <x-text-input id="password_confirmation" class="block w-full pr-10"
                                ::type="showPwConfirm ? 'text' : 'password'"
                                name="password_confirmation" required autocomplete="new-password"
                                x-model="passwordConfirmation"
                                @input="validatePasswordMatch()"
                                ::class="{
                                    'border-red-500 focus:border-red-500 focus:ring-red-500': passwordConfirmation.length > 0 && !passwordMatch,
                                    'border-green-500 focus:border-green-500 focus:ring-green-500': passwordConfirmation.length > 0 && passwordMatch
                                }" />
                <button type="button" @click="showPwConfirm = !showPwConfirm" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 focus:outline-none" tabindex="-1">
                    <svg x-show="!showPwConfirm" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <svg x-show="showPwConfirm" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            <p x-show="passwordConfirmation.length > 0 && !passwordMatch" x-cloak class="mt-1 text-xs text-red-600">Password tidak cocok.</p>
            <p x-show="passwordConfirmation.length > 0 && passwordMatch" x-cloak class="mt-1 text-xs text-green-600">Password cocok!</p>
        </div>

        <div class="mt-8">
            <x-primary-button class="w-full justify-center" ::disabled="!canSubmit">
                {{ __('Buat Akun Sekarang') }}
            </x-primary-button>
        </div>

        <div class="mt-6 text-center text-sm">
            <p class="text-slate-500">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                    Masuk ke Akun
                </a>
            </p>
        </div>
    </form>

    <script>
        function registerForm() {
            return {
                username: '{{ old("username", "") }}',
                email: '{{ old("email", "") }}',
                password: '',
                passwordConfirmation: '',
                showPw: false,
                showPwConfirm: false,
                usernameStatus: '', // '', 'checking', 'available', 'taken'
                usernameMessage: '',
                emailStatus: '',
                emailMessage: '',
                passwordMatch: false,
                rules: {
                    minLength: false,
                    maxLength: true,
                    lowercase: false,
                    uppercase: false,
                    number: false,
                    symbol: false,
                },

                get strengthScore() {
                    let score = 0;
                    if (this.rules.minLength) score++;
                    if (this.rules.maxLength) score++;
                    if (this.rules.lowercase) score++;
                    if (this.rules.uppercase) score++;
                    if (this.rules.number) score++;
                    if (this.rules.symbol) score++;
                    return score;
                },

                get strengthColor() {
                    if (this.strengthScore <= 2) return 'bg-red-500';
                    if (this.strengthScore <= 4) return 'bg-yellow-500';
                    return 'bg-green-500';
                },

                get strengthTextColor() {
                    if (this.strengthScore <= 2) return 'text-red-500';
                    if (this.strengthScore <= 4) return 'text-yellow-500';
                    return 'text-green-500';
                },

                get strengthText() {
                    if (this.password.length === 0) return '';
                    if (this.strengthScore <= 2) return 'Lemah';
                    if (this.strengthScore <= 4) return 'Sedang';
                    if (this.strengthScore <= 5) return 'Kuat';
                    return 'Sangat Kuat';
                },

                get allRulesPass() {
                    return this.rules.minLength && this.rules.maxLength && this.rules.lowercase && this.rules.uppercase && this.rules.number && this.rules.symbol;
                },

                get canSubmit() {
                    return this.usernameStatus !== 'taken' && this.emailStatus !== 'taken';
                },

                validatePassword() {
                    this.rules.minLength = this.password.length >= 8;
                    this.rules.maxLength = this.password.length <= 18;
                    this.rules.lowercase = /[a-z]/.test(this.password);
                    this.rules.uppercase = /[A-Z]/.test(this.password);
                    this.rules.number = /[0-9]/.test(this.password);
                    this.rules.symbol = /[^a-zA-Z0-9]/.test(this.password);
                    this.validatePasswordMatch();
                },

                validatePasswordMatch() {
                    this.passwordMatch = this.password.length > 0 && this.password === this.passwordConfirmation;
                },

                async checkUsername() {
                    if (this.username.length < 3) {
                        this.usernameStatus = '';
                        this.usernameMessage = '';
                        return;
                    }
                    this.usernameStatus = 'checking';
                    try {
                        const res = await fetch('{{ route("api.check-username") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ username: this.username })
                        });
                        const data = await res.json();
                        this.usernameStatus = data.available ? 'available' : 'taken';
                        this.usernameMessage = data.message;
                    } catch (e) {
                        this.usernameStatus = '';
                    }
                },

                async checkEmail() {
                    if (this.email.length < 5 || !this.email.includes('@')) {
                        this.emailStatus = '';
                        this.emailMessage = '';
                        return;
                    }
                    this.emailStatus = 'checking';
                    try {
                        const res = await fetch('{{ route("api.check-email") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ email: this.email })
                        });
                        const data = await res.json();
                        this.emailStatus = data.available ? 'available' : 'taken';
                        this.emailMessage = data.message;
                    } catch (e) {
                        this.emailStatus = '';
                    }
                },

                submitForm(event) {
                    if (this.usernameStatus === 'taken' || this.emailStatus === 'taken') {
                        return;
                    }
                    event.target.submit();
                }
            }
        }
    </script>
</x-guest-layout>

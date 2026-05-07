<x-pengaturan-layout>
    <x-slot name="header">Edit Pengguna: {{ $user->name }}</x-slot>

    <div class="max-w-2xl mx-auto" x-data="userForm()">
        <div class="mb-6">
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm text-secondary-500 hover:text-primary-600 transition-colors font-medium">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Daftar
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden">
            <div class="p-6 border-b border-secondary-200 bg-secondary-50/50">
                <h3 class="text-lg font-bold text-secondary-900">Informasi Pengguna</h3>
                <p class="text-sm text-secondary-500">Perbarui detail akun atau ubah peran pengguna.</p>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6 space-y-6" @submit.prevent="submitForm($event)">
                @csrf
                @method('PATCH')

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Nama Lengkap')" class="font-bold text-secondary-700" />
                    <x-text-input id="name" class="block mt-1 w-full border-secondary-300 focus:border-primary-500 focus:ring-primary-500 rounded-xl" type="text" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Username -->
                    <div>
                        <x-input-label for="username" :value="__('Username')" class="font-bold text-secondary-700" />
                        <x-text-input id="username" class="block mt-1 w-full border-secondary-300 focus:border-primary-500 focus:ring-primary-500 rounded-xl" type="text" name="username" :value="old('username', $user->username)" required
                            x-model="username"
                            @input.debounce.500ms="checkUsername()"
                            ::class="{
                                'border-red-500 focus:border-red-500 focus:ring-red-500': usernameStatus === 'taken',
                                'border-green-500 focus:border-green-500 focus:ring-green-500': usernameStatus === 'available'
                            }" />
                        <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        <p x-show="usernameStatus === 'taken'" x-cloak class="mt-1 text-xs text-red-600" x-text="usernameMessage"></p>
                        <p x-show="usernameStatus === 'available'" x-cloak class="mt-1 text-xs text-green-600" x-text="usernameMessage"></p>
                        <p x-show="usernameStatus === 'checking'" x-cloak class="mt-1 text-xs text-secondary-500 italic">Mengecek...</p>
                    </div>

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Alamat Email')" class="font-bold text-secondary-700" />
                        <x-text-input id="email" class="block mt-1 w-full border-secondary-300 focus:border-primary-500 focus:ring-primary-500 rounded-xl" type="email" name="email" :value="old('email', $user->email)" required
                            x-model="email"
                            @input.debounce.500ms="checkEmail()"
                            ::class="{
                                'border-red-500 focus:border-red-500 focus:ring-red-500': emailStatus === 'taken',
                                'border-green-500 focus:border-green-500 focus:ring-green-500': emailStatus === 'available'
                            }" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        <p x-show="emailStatus === 'taken'" x-cloak class="mt-1 text-xs text-red-600" x-text="emailMessage"></p>
                        <p x-show="emailStatus === 'available'" x-cloak class="mt-1 text-xs text-green-600" x-text="emailMessage"></p>
                        <p x-show="emailStatus === 'checking'" x-cloak class="mt-1 text-xs text-secondary-500 italic">Mengecek...</p>
                    </div>
                </div>

                <!-- Role -->
                <div>
                    <x-input-label for="role" :value="__('Peran (Role)')" class="font-bold text-secondary-700" />
                    <select id="role" name="role" class="block mt-1 w-full border-secondary-300 focus:border-primary-500 focus:ring-primary-500 rounded-xl text-secondary-700">
                        <option value="pengguna" {{ old('role', $user->role) == 'pengguna' ? 'selected' : '' }}>Pengguna Biasa</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <hr class="border-secondary-100">

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <x-input-label for="password" :value="__('Ubah Kata Sandi')" class="font-bold text-secondary-700" />
                        <span class="text-[10px] text-secondary-400 font-medium italic">Kosongkan jika tidak ingin mengubah</span>
                    </div>
                    <div class="relative mt-1">
                        <x-text-input id="password" class="block w-full border-secondary-300 focus:border-primary-500 focus:ring-primary-500 rounded-xl pr-10"
                                        ::type="showPw ? 'text' : 'password'"
                                        name="password"
                                        autocomplete="new-password" placeholder="••••••••" 
                                        x-model="password"
                                        @input="validatePassword()" />
                        <button type="button" @click="showPw = !showPw" class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary-400 hover:text-secondary-600 focus:outline-none">
                            <svg x-show="!showPw" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showPw" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    
                    {{-- Password Strength --}}
                    <div class="mt-3 space-y-2" x-show="password.length > 0" x-cloak>
                        <div class="flex gap-1.5">
                            <template x-for="i in 6" :key="i">
                                <div class="h-1.5 flex-1 rounded-full transition-all duration-300"
                                     :class="i <= strengthScore ? strengthColor : 'bg-secondary-200'"></div>
                            </template>
                        </div>
                        <div class="flex justify-between items-center">
                            <p class="text-[10px] font-bold uppercase tracking-wider" :class="strengthTextColor" x-text="strengthText"></p>
                            <div class="flex flex-wrap gap-x-3 gap-y-1">
                                <span class="text-[10px] flex items-center gap-1" :class="rules.minLength ? 'text-green-600' : 'text-secondary-400'">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> 8+
                                </span>
                                <span class="text-[10px] flex items-center gap-1" :class="rules.lowercase && rules.uppercase ? 'text-green-600' : 'text-secondary-400'">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Aa
                                </span>
                                <span class="text-[10px] flex items-center gap-1" :class="rules.number ? 'text-green-600' : 'text-secondary-400'">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> 0-9
                                </span>
                                <span class="text-[10px] flex items-center gap-1" :class="rules.symbol ? 'text-green-600' : 'text-secondary-400'">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> !@#
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div x-show="password.length > 0" x-cloak class="relative">
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi Baru')" class="font-bold text-secondary-700" />
                    <div class="relative mt-1">
                        <x-text-input id="password_confirmation" class="block w-full border-secondary-300 focus:border-primary-500 focus:ring-primary-500 rounded-xl pr-10"
                                        ::type="showPwConfirm ? 'text' : 'password'"
                                        name="password_confirmation" placeholder="••••••••" 
                                        x-model="passwordConfirmation"
                                        @input="validatePasswordMatch()"
                                        ::class="{
                                            'border-red-500 focus:border-red-500 focus:ring-red-500': passwordConfirmation.length > 0 && !passwordMatch,
                                            'border-green-500 focus:border-green-500 focus:ring-green-500': passwordConfirmation.length > 0 && passwordMatch
                                        }" />
                        <button type="button" @click="showPwConfirm = !showPwConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary-400 hover:text-secondary-600 focus:outline-none">
                            <svg x-show="!showPwConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showPwConfirm" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                        </button>
                    </div>
                    <p x-show="passwordConfirmation.length > 0 && !passwordMatch" x-cloak class="mt-1 text-xs text-red-600 italic">Konfirmasi password tidak cocok.</p>
                </div>

                <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-secondary-100">
                    <a href="{{ route('admin.users.index') }}" class="text-sm font-bold text-secondary-500 hover:text-secondary-700 transition-colors">Batal</a>
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-primary-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-primary-500/30"
                        ::disabled="usernameStatus === 'taken' || emailStatus === 'taken'">
                        Perbarui Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function userForm() {
            return {
                userId: {{ $user->id }},
                username: '{{ old("username", $user->username) }}',
                email: '{{ old("email", $user->email) }}',
                password: '',
                passwordConfirmation: '',
                showPw: false,
                showPwConfirm: false,
                usernameStatus: '',
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
                            body: JSON.stringify({ username: this.username, ignore_id: this.userId })
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
                            body: JSON.stringify({ email: this.email, ignore_id: this.userId })
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
                    if (this.password.length > 0 && !this.passwordMatch) {
                        return;
                    }
                    event.target.submit();
                }
            }
        }
    </script>
</x-pengaturan-layout>

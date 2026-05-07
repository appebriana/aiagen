<section x-data="profileForm({{ $user->id }}, '{{ $user->username }}', '{{ $user->email }}')">
    <header>
        <h2 class="text-lg font-bold text-secondary-900">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-secondary-500">
            {{ __("Perbarui informasi profil dan alamat email Anda.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route(auth()->user()->isAdmin() ? 'admin.profile.update' : 'pengguna.profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="profile_username" :value="__('Username')" />
            <x-text-input id="profile_username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $user->username)" required autocomplete="username"
                x-model="username"
                @input.debounce.500ms="checkUsername()"
                ::class="{
                    'border-red-500 focus:border-red-500 focus:ring-red-500': usernameStatus === 'taken',
                    'border-green-500 focus:border-green-500 focus:ring-green-500': usernameStatus === 'available'
                }" />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
            <p x-show="usernameStatus === 'taken'" x-cloak class="mt-1 text-xs text-red-600" x-text="usernameMessage"></p>
            <p x-show="usernameStatus === 'available'" x-cloak class="mt-1 text-xs text-green-600" x-text="usernameMessage"></p>
            <p x-show="usernameStatus === 'checking'" x-cloak class="mt-1 text-xs text-secondary-500">
                <svg class="inline w-3 h-3 animate-spin mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                Mengecek...
            </p>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="email"
                x-model="email"
                @input.debounce.500ms="checkEmail()"
                ::class="{
                    'border-red-500 focus:border-red-500 focus:ring-red-500': emailStatus === 'taken',
                    'border-green-500 focus:border-green-500 focus:ring-green-500': emailStatus === 'available'
                }" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
            <p x-show="emailStatus === 'taken'" x-cloak class="mt-1 text-xs text-red-600" x-text="emailMessage"></p>
            <p x-show="emailStatus === 'available'" x-cloak class="mt-1 text-xs text-green-600" x-text="emailMessage"></p>
            <p x-show="emailStatus === 'checking'" x-cloak class="mt-1 text-xs text-secondary-500">
                <svg class="inline w-3 h-3 animate-spin mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                Mengecek...
            </p>

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-secondary-800">
                        {{ __('Email Anda belum diverifikasi.') }}

                        <button form="send-verification" class="underline text-sm text-secondary-500 hover:text-secondary-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('Link verifikasi baru telah dikirim ke email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-secondary-500 font-medium"
                >
                    <svg class="inline w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ __('Tersimpan.') }}
                </p>
            @endif
        </div>
    </form>

    <script>
        function profileForm(userId, initialUsername, initialEmail) {
            return {
                username: initialUsername,
                email: initialEmail,
                originalUsername: initialUsername,
                originalEmail: initialEmail,
                userId: userId,
                usernameStatus: '',
                usernameMessage: '',
                emailStatus: '',
                emailMessage: '',

                async checkUsername() {
                    if (this.username === this.originalUsername) {
                        this.usernameStatus = '';
                        this.usernameMessage = '';
                        return;
                    }
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
                    if (this.email === this.originalEmail) {
                        this.emailStatus = '';
                        this.emailMessage = '';
                        return;
                    }
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
                }
            }
        }
    </script>
</section>

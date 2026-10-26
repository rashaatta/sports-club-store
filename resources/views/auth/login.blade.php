<x-guest-layout>
    <div class="rounded-2xl border border-line/70 bg-white p-6 shadow-panel sm:p-8">
        <div class="mb-6">
            <p class="eyebrow-latin">{{ __('admin.brand.panel') }}</p>
            <h2 class="mt-2 text-2xl font-black">{{ __('auth.login_title') }}</h2>
            <p class="mt-2 text-sm leading-6 text-muted">{{ __('auth.login_intro') }}</p>
        </div>

        <x-auth-session-status class="mb-4 rounded-lg bg-success-soft px-4 py-3 text-sm font-semibold text-success" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="field-label">{{ __('auth.email') }}</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    dir="ltr"
                    placeholder="{{ __('auth.email_placeholder') }}"
                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                    class="field"
                >
                @error('email')
                    <p id="email-error" class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="flex items-center justify-between gap-3">
                    <label for="password" class="field-label">{{ __('auth.password_label') }}</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="mb-1.5 text-xs font-bold text-muted underline decoration-ink/20 underline-offset-4 transition hover:text-ink">
                            {{ __('auth.forgot') }}
                        </a>
                    @endif
                </div>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    dir="ltr"
                    placeholder="{{ __('auth.password_placeholder') }}"
                    @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                    class="field"
                >
                @error('password')
                    <p id="password-error" class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <label for="remember_me" class="flex cursor-pointer items-center gap-2.5 pt-1">
                <input id="remember_me" type="checkbox" name="remember" class="size-4 rounded border-ink/25 text-ink focus:ring-0">
                <span class="text-sm font-semibold text-muted">{{ __('auth.remember') }}</span>
            </label>

            <button type="submit" class="btn-primary w-full">
                <x-store.icon name="key" class="size-4" />
                {{ __('auth.login') }}
            </button>
        </form>

        <p class="mt-5 flex items-center justify-center gap-2 text-center text-[11px] leading-5 text-muted">
            <x-store.icon name="shield" class="size-3.5 shrink-0" />
            {{ __('auth.secure_note') }}
        </p>
    </div>
</x-guest-layout>

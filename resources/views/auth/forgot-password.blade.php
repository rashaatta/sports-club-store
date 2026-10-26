<x-guest-layout>
    <div class="rounded-2xl border border-line/70 bg-white p-6 shadow-panel sm:p-8">
        <div class="mb-6">
            <p class="eyebrow-latin">{{ __('admin.brand.panel') }}</p>
            <h2 class="mt-2 text-2xl font-black">{{ __('auth.forgot_title') }}</h2>
            <p class="mt-2 text-sm leading-6 text-muted">{{ __('auth.forgot_intro') }}</p>
        </div>

        <x-auth-session-status class="mb-4 rounded-lg bg-success-soft px-4 py-3 text-sm font-semibold text-success" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
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

            <button type="submit" class="btn-primary w-full">
                <x-store.icon name="mail" class="size-4" />
                {{ __('auth.email_reset_link') }}
            </button>
        </form>

        <a href="{{ route('login') }}" class="mt-5 flex items-center justify-center gap-2 text-xs font-bold text-muted transition hover:text-ink">
            <x-store.icon name="arrow-right" class="size-3.5 ltr:-scale-x-100" />
            {{ __('auth.login_title') }}
        </a>
    </div>
</x-guest-layout>

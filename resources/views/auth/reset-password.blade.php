<x-guest-layout>
    <div class="rounded-2xl border border-line/70 bg-white p-6 shadow-panel sm:p-8">
        <div class="mb-6">
            <p class="eyebrow-latin">{{ __('admin.brand.panel') }}</p>
            <h2 class="mt-2 text-2xl font-black">{{ __('auth.reset_title') }}</h2>
            <p class="mt-2 text-sm leading-6 text-muted">{{ __('auth.reset_intro') }}</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label for="email" class="field-label">{{ __('auth.email') }}</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $request->email) }}"
                    required
                    autofocus
                    autocomplete="username"
                    dir="ltr"
                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                    class="field"
                >
                @error('email')
                    <p id="email-error" class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="field-label">{{ __('auth.new_password') }}</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    dir="ltr"
                    @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                    class="field"
                >
                @error('password')
                    <p id="password-error" class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="field-label">{{ __('auth.confirm_password') }}</label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    dir="ltr"
                    class="field"
                >
            </div>

            <button type="submit" class="btn-primary w-full">
                <x-store.icon name="key" class="size-4" />
                {{ __('auth.reset_action') }}
            </button>
        </form>
    </div>
</x-guest-layout>

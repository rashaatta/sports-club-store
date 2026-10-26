<x-layouts.admin :title="__('admin.profile.title')">
    <x-admin.page-header :title="__('admin.profile.title')" :subtitle="__('admin.profile.subtitle')">
        <x-slot:actions>
            <span class="badge-outline">{{ $user->role?->label() }}</span>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 xl:grid-cols-2">
        <section class="surface-card p-5 sm:p-6" aria-labelledby="profile-details-title">
            <div class="mb-5 flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-full bg-ink text-sm font-black text-white">{{ mb_substr($user->name, 0, 1) }}</span>
                <div>
                    <h2 id="profile-details-title" class="text-base font-black">{{ __('admin.profile.section_profile') }}</h2>
                    <p class="mt-0.5 text-[11px] text-muted">{{ __('admin.profile.save_hint') }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.profile.update') }}" class="grid gap-4">
                @csrf
                @method('PATCH')

                <x-admin.field name="name" :label="__('admin.profile.field_name')" :value="$user->name" maxlength="120" required />
                <x-admin.field
                    name="email"
                    type="email"
                    :label="__('admin.profile.field_email')"
                    :value="$user->email"
                    :help="__('admin.profile.email_hint')"
                    maxlength="190"
                    dir="ltr"
                    required
                />

                <div class="rounded-xl bg-smoke/60 px-4 py-3 text-xs leading-5 text-muted">
                    {{ __('admin.profile.danger_note') }}
                </div>

                <div class="flex justify-end border-t border-line pt-4">
                    <button type="submit" class="btn-primary">
                        <x-store.icon name="check" class="size-4" />
                        {{ __('admin.profile.save_profile') }}
                    </button>
                </div>
            </form>
        </section>

        <section class="surface-card p-5 sm:p-6" aria-labelledby="profile-password-title">
            <div class="mb-5 flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-full bg-smoke text-ink">
                    <x-store.icon name="key" class="size-5" />
                </span>
                <div>
                    <h2 id="profile-password-title" class="text-base font-black">{{ __('admin.profile.section_password') }}</h2>
                    <p class="mt-0.5 text-[11px] text-muted">{{ __('admin.profile.password_hint') }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.profile.password') }}" class="grid gap-4">
                @csrf
                @method('PUT')

                <x-admin.field
                    name="current_password"
                    type="password"
                    :label="__('admin.profile.field_current_password')"
                    autocomplete="current-password"
                    required
                />
                <x-admin.field
                    name="password"
                    type="password"
                    :label="__('admin.profile.field_password')"
                    :help="__('admin.profile.password_hint')"
                    autocomplete="new-password"
                    required
                />
                <x-admin.field
                    name="password_confirmation"
                    type="password"
                    :label="__('admin.profile.field_password_confirmation')"
                    autocomplete="new-password"
                    required
                />

                <div class="flex justify-end border-t border-line pt-4">
                    <button type="submit" class="btn-primary">
                        <x-store.icon name="key" class="size-4" />
                        {{ __('admin.profile.save_password') }}
                    </button>
                </div>
            </form>
        </section>
    </div>
</x-layouts.admin>

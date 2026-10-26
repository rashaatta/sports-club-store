@php($isEdit = $user !== null)

<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
    <section class="surface-card p-5 sm:p-6" aria-labelledby="user-details-title">
        <div class="mb-5 flex items-center gap-3">
            <span class="flex size-9 items-center justify-center rounded-xl bg-volt text-ink">
                <x-store.icon name="users" class="size-4" />
            </span>
            <h2 id="user-details-title" class="text-base font-black">{{ __('admin.users.title') }}</h2>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <x-admin.field name="name" :label="__('admin.users.field_name')" :value="$user?->name" maxlength="120" required />
            <x-admin.field name="email" type="email" :label="__('admin.users.field_email')" :value="$user?->email" maxlength="190" dir="ltr" required />
            <div class="sm:col-span-2">
                <x-admin.select
                    name="role"
                    :label="__('admin.users.field_role')"
                    :options="$roles"
                    :selected="$user?->role?->value"
                    :help="__('admin.users.role_hint')"
                    required
                />
            </div>
            <x-admin.field
                name="password"
                type="password"
                :label="__('admin.users.field_password')"
                :help="__('admin.users.password_hint')"
                autocomplete="new-password"
                :required="! $isEdit"
            />
            <x-admin.field
                name="password_confirmation"
                type="password"
                :label="__('admin.users.field_password_confirmation')"
                autocomplete="new-password"
                :required="! $isEdit"
            />
        </div>
    </section>

    <div class="space-y-6">
        <section class="surface-card p-5">
            <div class="flex items-center gap-2 text-xs font-bold text-muted">
                <x-store.icon name="shield" class="size-4" />
                {{ __('admin.users.role_permissions') }}
            </div>
            <p class="mt-2 text-xs leading-6 text-muted">{{ __('admin.users.permissions_hint') }}</p>
            <p class="mt-3 text-xs leading-6 text-muted">{{ __('admin.users.password_note') }}</p>

            <div class="mt-5 grid gap-2 border-t border-line pt-5">
                <button type="submit" form="user-form" class="btn-primary w-full">
                    <x-store.icon name="check" class="size-4" />
                    {{ __('admin.common.save') }}
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn-ghost w-full">{{ __('admin.common.cancel') }}</a>
            </div>
        </section>

        @if ($isEdit && auth()->id() === $user->id)
            <div class="rounded-2xl bg-ink p-5 text-white">
                <p class="text-xs leading-6 text-white/70">{{ __('admin.users.cannot_demote_self') }}</p>
            </div>
        @endif
    </div>
</div>

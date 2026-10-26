<x-layouts.admin :title="__('admin.users.edit_title')">
    <x-admin.page-header :title="__('admin.users.edit_title')" :subtitle="$user->name">
        <x-slot:actions>
            <a href="{{ route('admin.users.index') }}" class="btn-ghost btn-sm">{{ __('admin.common.back_list') }}</a>
            <button type="submit" form="user-form" class="btn-primary btn-sm">
                <x-store.icon name="check" class="size-4" />
                {{ __('admin.common.save') }}
            </button>
        </x-slot:actions>
    </x-admin.page-header>

    <form id="user-form" method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')
        @include('admin.users._form')
    </form>

    @if (auth()->id() !== $user->id)
        <section class="mt-6 rounded-2xl border border-danger/20 bg-danger-soft/60 p-5 sm:p-6" aria-labelledby="user-danger-title">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 id="user-danger-title" class="text-base font-black text-danger">{{ __('admin.common.danger_zone') }}</h2>
                    <p class="mt-1 text-xs leading-5 text-danger/80">{{ __('admin.users.delete_confirm') }}</p>
                </div>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('{{ __('admin.users.delete_confirm') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">
                        <x-store.icon name="trash" class="size-4" />
                        {{ __('admin.common.delete') }}
                    </button>
                </form>
            </div>
        </section>
    @endif
</x-layouts.admin>

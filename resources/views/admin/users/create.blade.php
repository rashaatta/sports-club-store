<x-layouts.admin :title="__('admin.users.create_title')">
    <x-admin.page-header :title="__('admin.users.create_title')" :subtitle="__('admin.users.subtitle')">
        <x-slot:actions>
            <a href="{{ route('admin.users.index') }}" class="btn-ghost btn-sm">{{ __('admin.common.back_list') }}</a>
            <button type="submit" form="user-form" class="btn-primary btn-sm">
                <x-store.icon name="check" class="size-4" />
                {{ __('admin.common.save') }}
            </button>
        </x-slot:actions>
    </x-admin.page-header>

    <form id="user-form" method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        @include('admin.users._form', ['user' => null])
    </form>
</x-layouts.admin>

<x-layouts.admin :title="__('admin.pages.create_title')">
    <x-admin.page-header :title="__('admin.pages.create_title')" :subtitle="__('admin.pages.subtitle')">
        <x-slot:actions>
            <a href="{{ route('admin.pages.index') }}" class="btn-ghost btn-sm">{{ __('admin.common.back_list') }}</a>
            <button type="submit" form="page-form" class="btn-primary btn-sm">
                <x-store.icon name="check" class="size-4" />
                {{ __('admin.common.save') }}
            </button>
        </x-slot:actions>
    </x-admin.page-header>

    <form id="page-form" method="POST" action="{{ route('admin.pages.store') }}">
        @csrf
        @include('admin.pages._form', ['page' => null])
    </form>
</x-layouts.admin>

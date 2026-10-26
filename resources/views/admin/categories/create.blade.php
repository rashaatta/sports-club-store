<x-layouts.admin :title="__('admin.categories.create_title')">
    <x-admin.page-header :title="__('admin.categories.create_title')" :subtitle="__('admin.categories.subtitle')">
        <x-slot:actions>
            <a href="{{ route('admin.categories.index') }}" class="btn-ghost btn-sm">{{ __('admin.common.back_list') }}</a>
            <button type="submit" form="category-form" class="btn-primary btn-sm">
                <x-store.icon name="check" class="size-4" />
                {{ __('admin.common.save') }}
            </button>
        </x-slot:actions>
    </x-admin.page-header>

    <form id="category-form" method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.categories._form', ['category' => null])
    </form>
</x-layouts.admin>

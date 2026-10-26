<x-layouts.admin :title="__('admin.banners.create_title')">
    <x-admin.page-header :title="__('admin.banners.create_title')" :subtitle="__('admin.banners.subtitle')">
        <x-slot:actions>
            <a href="{{ route('admin.banners.index') }}" class="btn-ghost btn-sm">{{ __('admin.common.back_list') }}</a>
            <button type="submit" form="banner-form" class="btn-primary btn-sm">
                <x-store.icon name="check" class="size-4" />
                {{ __('admin.common.save') }}
            </button>
        </x-slot:actions>
    </x-admin.page-header>

    <form id="banner-form" method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.banners._form', ['banner' => null])
    </form>
</x-layouts.admin>

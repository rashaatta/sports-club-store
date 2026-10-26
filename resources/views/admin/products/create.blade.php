<x-layouts.admin :title="__('admin.products.create_title')">
    <x-admin.page-header :title="__('admin.products.create_title')" :subtitle="__('admin.products.subtitle')">
        <x-slot:actions>
            <a href="{{ route('admin.products.index') }}" class="btn-ghost btn-sm">
                <x-store.icon name="chevron-start" class="size-4" />
                {{ __('admin.products.back_to_products') }}
            </a>
            <button type="submit" form="product-form" class="btn-primary btn-sm">
                <x-store.icon name="check" class="size-4" />
                {{ __('admin.common.save') }}
            </button>
        </x-slot:actions>
    </x-admin.page-header>

    <form id="product-form" method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.products._fields', ['product' => null])
    </form>
</x-layouts.admin>

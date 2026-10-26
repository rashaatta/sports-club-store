<x-layouts.admin :title="__('admin.faqs.create_title')">
    <x-admin.page-header :title="__('admin.faqs.create_title')" :subtitle="__('admin.faqs.subtitle')">
        <x-slot:actions>
            <a href="{{ route('admin.faqs.index') }}" class="btn-ghost btn-sm">{{ __('admin.common.back_list') }}</a>
            <button type="submit" form="faq-form" class="btn-primary btn-sm">
                <x-store.icon name="check" class="size-4" />
                {{ __('admin.common.save') }}
            </button>
        </x-slot:actions>
    </x-admin.page-header>

    <form id="faq-form" method="POST" action="{{ route('admin.faqs.store') }}">
        @csrf
        @include('admin.faqs._form', ['faq' => null])
    </form>
</x-layouts.admin>

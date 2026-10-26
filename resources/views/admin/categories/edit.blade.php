<x-layouts.admin :title="__('admin.categories.edit_title')">
    <x-admin.page-header :title="__('admin.categories.edit_title')" :subtitle="$category->name">
        <x-slot:actions>
            @if ($category->is_active)
                <a href="{{ route('categories.show', $category) }}" target="_blank" rel="noopener" class="btn-outline btn-sm">
                    <x-store.icon name="external" class="size-4" />
                    {{ __('admin.common.view_store') }}
                </a>
            @endif
            <a href="{{ route('admin.categories.index') }}" class="btn-ghost btn-sm">{{ __('admin.common.back_list') }}</a>
            <button type="submit" form="category-form" class="btn-primary btn-sm">
                <x-store.icon name="check" class="size-4" />
                {{ __('admin.common.save') }}
            </button>
        </x-slot:actions>
    </x-admin.page-header>

    <form id="category-form" method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.categories._form')
    </form>

    <section class="mt-6 rounded-2xl border border-danger/20 bg-danger-soft/60 p-5 sm:p-6" aria-labelledby="category-danger-title">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 id="category-danger-title" class="text-base font-black text-danger">{{ __('admin.common.danger_zone') }}</h2>
                <p class="mt-1 text-xs leading-5 text-danger/80">{{ __('admin.categories.delete_confirm') }}</p>
            </div>
            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('{{ __('admin.categories.delete_confirm') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">
                    <x-store.icon name="trash" class="size-4" />
                    {{ __('admin.common.delete') }}
                </button>
            </form>
        </div>
    </section>
</x-layouts.admin>

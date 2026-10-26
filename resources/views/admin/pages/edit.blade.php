<x-layouts.admin :title="__('admin.pages.edit_title')">
    <x-admin.page-header :title="__('admin.pages.edit_title')" :subtitle="$page->title">
        <x-slot:actions>
            <a href="{{ route('admin.pages.index') }}" class="btn-ghost btn-sm">{{ __('admin.common.back_list') }}</a>
            <button type="submit" form="page-form" class="btn-primary btn-sm">
                <x-store.icon name="check" class="size-4" />
                {{ __('admin.common.save') }}
            </button>
        </x-slot:actions>
    </x-admin.page-header>

    <form id="page-form" method="POST" action="{{ route('admin.pages.update', $page) }}">
        @csrf
        @method('PUT')
        @include('admin.pages._form')
    </form>

    @if ($page->slug !== 'about-us')
        <section class="mt-6 rounded-2xl border border-danger/20 bg-danger-soft/60 p-5 sm:p-6" aria-labelledby="page-danger-title">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 id="page-danger-title" class="text-base font-black text-danger">{{ __('admin.common.danger_zone') }}</h2>
                    <p class="mt-1 text-xs leading-5 text-danger/80">{{ __('admin.pages.delete_confirm') }}</p>
                </div>
                <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" onsubmit="return confirm('{{ __('admin.pages.delete_confirm') }}')">
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

<x-layouts.admin :title="__('admin.categories.title')">
    <x-admin.page-header :title="__('admin.categories.title')" :subtitle="__('admin.categories.subtitle')">
        <x-slot:actions>
            <a href="{{ route('admin.categories.create') }}" class="btn-primary btn-sm">
                <x-store.icon name="plus" class="size-4" />
                {{ __('admin.categories.add') }}
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="surface-card mb-5 p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-col gap-3 sm:flex-row">
            <div class="flex-1">
                <label for="category-search" class="sr-only">{{ __('admin.common.search') }}</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-muted">
                        <x-store.icon name="search" class="size-4" />
                    </span>
                    <input id="category-search" type="search" name="q" value="{{ $search }}" placeholder="{{ __('admin.categories.search_placeholder') }}" class="field ps-10">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-primary btn-sm">{{ __('admin.common.search') }}</button>
                @if ($search !== '')
                    <a href="{{ route('admin.categories.index') }}" class="btn-ghost btn-sm">{{ __('admin.common.reset') }}</a>
                @endif
            </div>
        </form>
    </div>

    <div class="surface-card overflow-hidden">
        @if ($categories->isEmpty())
            <x-admin.empty-state :title="__('admin.categories.empty')" icon="tags">
                <a href="{{ route('admin.categories.create') }}" class="btn-primary btn-sm">{{ __('admin.categories.add') }}</a>
            </x-admin.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="table-admin min-w-[820px]">
                    <thead>
                        <tr>
                            <th>{{ __('admin.categories.table_name') }}</th>
                            <th>{{ __('admin.categories.table_parent') }}</th>
                            <th>{{ __('admin.categories.table_products') }}</th>
                            <th>{{ __('admin.categories.table_order') }}</th>
                            <th>{{ __('admin.categories.table_status') }}</th>
                            <th class="text-end!">{{ __('admin.common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr class="transition hover:bg-smoke/40">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-smoke">
                                            @if ($category->image_path)
                                                <img src="{{ media_url($category->image_path) }}" alt="" class="size-full object-cover" width="48" height="48" loading="lazy">
                                            @else
                                                <x-store.icon name="tags" class="size-5 text-muted" />
                                            @endif
                                        </span>
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.categories.edit', $category) }}" class="block max-w-[260px] truncate text-sm font-bold hover:underline">{{ $category->name }}</a>
                                            <span class="mt-0.5 block max-w-[240px] truncate text-[11px] text-muted" dir="ltr">{{ $category->slug }}</span>
                                            @if ($category->is_featured)
                                                <span class="mt-1 badge-volt">{{ __('admin.common.featured') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($category->parent)
                                        <span class="badge-outline">{{ $category->parent->name }}</span>
                                    @else
                                        <span class="badge-muted">{{ __('admin.categories.parent_badge') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-sm font-bold">{{ $category->products_count }}</span>
                                </td>
                                <td>
                                    <span class="text-sm text-muted">{{ $category->display_order }}</span>
                                </td>
                                <td>
                                    @if ($category->is_active)
                                        <span class="badge-success">{{ __('admin.common.active') }}</span>
                                    @else
                                        <span class="badge-muted">{{ __('admin.common.inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        @if ($category->is_active)
                                            <a href="{{ route('categories.show', $category) }}" target="_blank" rel="noopener" class="icon-btn size-9 text-muted hover:bg-smoke hover:text-ink" aria-label="{{ __('admin.common.view_store') }}">
                                                <x-store.icon name="external" class="size-4" />
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="icon-btn size-9 text-muted hover:bg-smoke hover:text-ink" aria-label="{{ __('admin.common.edit') }}">
                                            <x-store.icon name="edit" class="size-4" />
                                        </a>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('{{ __('admin.categories.delete_confirm') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn size-9 text-muted hover:bg-danger-soft hover:text-danger" aria-label="{{ __('admin.common.delete') }}">
                                                <x-store.icon name="trash" class="size-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-line px-4 py-3">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>

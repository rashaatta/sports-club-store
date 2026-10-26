<x-layouts.admin :title="__('admin.products.title')">
    <x-admin.page-header :title="__('admin.products.title')" :subtitle="__('admin.products.subtitle')">
        <x-slot:actions>
            <a href="{{ route('admin.products.create') }}" class="btn-primary btn-sm">
                <x-store.icon name="plus" class="size-4" />
                {{ __('admin.products.add') }}
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="surface-card mb-5 p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.products.index') }}" class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_220px_200px_auto]">
            <div>
                <label for="product-search" class="sr-only">{{ __('admin.common.search') }}</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-muted">
                        <x-store.icon name="search" class="size-4" />
                    </span>
                    <input id="product-search" type="search" name="q" value="{{ $search }}" placeholder="{{ __('admin.products.search_placeholder') }}" class="field ps-10">
                </div>
            </div>

            <div>
                <label for="product-category" class="sr-only">{{ __('admin.products.field_category') }}</label>
                <select id="product-category" name="category" class="field min-h-11">
                    <option value="0">{{ __('admin.products.filter_category') }}</option>
                    @foreach ($categories as $id => $name)
                        <option value="{{ $id }}" @selected((int) $categoryId === (int) $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="product-status" class="sr-only">{{ __('admin.common.status') }}</label>
                <select id="product-status" name="status" class="field min-h-11">
                    <option value="all" @selected($status === 'all')>{{ __('admin.products.status_all') }}</option>
                    <option value="active" @selected($status === 'active')>{{ __('admin.products.status_active') }}</option>
                    <option value="inactive" @selected($status === 'inactive')>{{ __('admin.products.status_inactive') }}</option>
                    <option value="featured" @selected($status === 'featured')>{{ __('admin.products.status_featured') }}</option>
                    <option value="new" @selected($status === 'new')>{{ __('admin.products.status_new') }}</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="btn-primary btn-sm flex-1 lg:flex-none">
                    <x-store.icon name="filter" class="size-4" />
                    {{ __('admin.common.apply') }}
                </button>
                @if ($search !== '' || $categoryId > 0 || $status !== 'all')
                    <a href="{{ route('admin.products.index') }}" class="btn-ghost btn-sm">{{ __('admin.common.reset') }}</a>
                @endif
            </div>
        </form>
    </div>

    <div class="surface-card overflow-hidden">
        @if ($products->isEmpty())
            <x-admin.empty-state :title="__('admin.products.empty')" icon="bag">
                <a href="{{ route('admin.products.create') }}" class="btn-primary btn-sm">{{ __('admin.products.add') }}</a>
            </x-admin.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="table-admin min-w-[900px]">
                    <thead>
                        <tr>
                            <th>{{ __('admin.products.table_product') }}</th>
                            <th>{{ __('admin.products.table_category') }}</th>
                            <th>{{ __('admin.products.table_price') }}</th>
                            <th>{{ __('admin.products.table_stock') }}</th>
                            <th>{{ __('admin.products.table_status') }}</th>
                            <th class="text-end!">{{ __('admin.common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr class="transition hover:bg-smoke/40">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-14 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-smoke">
                                            @if ($product->primary_image_path)
                                                <img src="{{ media_url($product->primary_image_path) }}" alt="" class="size-full object-cover" width="56" height="56" loading="lazy">
                                            @else
                                                <x-store.icon name="image" class="size-5 text-muted" />
                                            @endif
                                        </span>
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.products.edit', $product) }}" class="block max-w-[260px] truncate text-sm font-bold hover:underline">{{ $product->name }}</a>
                                            <span class="mt-0.5 block text-[11px] text-muted" dir="ltr">{{ $product->sku }}</span>
                                            <span class="mt-1 flex flex-wrap gap-1">
                                                @if ($product->is_featured)
                                                    <span class="badge-volt">{{ __('admin.common.featured') }}</span>
                                                @endif
                                                @if ($product->is_new_arrival)
                                                    <span class="badge-outline">{{ __('admin.common.new') }}</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-sm">{{ $product->category?->name ?? __('admin.common.none') }}</span>
                                </td>
                                <td>
                                    <span class="block text-sm font-black">{{ $product->price_formatted }}</span>
                                    @if ($product->compare_at_price)
                                        <span class="block text-[11px] text-muted line-through">{{ $product->compare_at_formatted }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <span @class([
                                            'text-sm font-black',
                                            'text-danger' => ! $product->is_in_stock,
                                            'text-ink' => $product->is_in_stock,
                                        ])>{{ $product->total_stock }}</span>
                                        @if (! $product->is_in_stock)
                                            <span class="badge-danger">{{ __('admin.products.out_stock_badge') }}</span>
                                        @elseif ($product->is_low_stock)
                                            <span class="badge-warning">{{ __('admin.products.low_stock_badge') }}</span>
                                        @endif
                                    </div>
                                    <span class="mt-0.5 block text-[11px] text-muted">
                                        @if ($product->usesVariants())
                                            {{ __('admin.products.variant_count', ['count' => $product->variants_count]) }}
                                        @else
                                            {{ __('admin.products.simple_product') }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @if ($product->is_active)
                                        <span class="badge-success">{{ __('admin.common.active') }}</span>
                                    @else
                                        <span class="badge-muted">{{ __('admin.common.inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        @if ($product->is_active)
                                            <a href="{{ route('products.show', $product) }}" target="_blank" rel="noopener" class="icon-btn size-9 text-muted hover:bg-smoke hover:text-ink" aria-label="{{ __('admin.products.actions_view') }}">
                                                <x-store.icon name="external" class="size-4" />
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.inventory.show', $product) }}" class="icon-btn size-9 text-muted hover:bg-smoke hover:text-ink" aria-label="{{ __('admin.products.actions_inventory') }}">
                                            <x-store.icon name="inventory" class="size-4" />
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}" class="icon-btn size-9 text-muted hover:bg-smoke hover:text-ink" aria-label="{{ __('admin.products.actions_edit') }}">
                                            <x-store.icon name="edit" class="size-4" />
                                        </a>
                                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('{{ __('admin.products.delete_confirm') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn size-9 text-muted hover:bg-danger-soft hover:text-danger" aria-label="{{ __('admin.products.actions_delete') }}">
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
                {{ $products->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>

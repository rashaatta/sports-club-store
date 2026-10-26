<x-layouts.admin :title="__('admin.pages.title')">
    <x-admin.page-header :title="__('admin.pages.title')" :subtitle="__('admin.pages.subtitle')">
        <x-slot:actions>
            <a href="{{ route('admin.pages.create') }}" class="btn-primary btn-sm">
                <x-store.icon name="plus" class="size-4" />
                {{ __('admin.pages.add') }}
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="surface-card mb-5 p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.pages.index') }}" class="flex flex-col gap-3 sm:flex-row">
            <div class="flex-1">
                <label for="page-search" class="sr-only">{{ __('admin.common.search') }}</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-muted">
                        <x-store.icon name="search" class="size-4" />
                    </span>
                    <input id="page-search" type="search" name="q" value="{{ $search }}" placeholder="{{ __('admin.common.search_placeholder') }}" class="field ps-10">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-primary btn-sm">{{ __('admin.common.search') }}</button>
                @if ($search !== '')
                    <a href="{{ route('admin.pages.index') }}" class="btn-ghost btn-sm">{{ __('admin.common.reset') }}</a>
                @endif
            </div>
        </form>
    </div>

    <div class="surface-card overflow-hidden">
        @if ($pages->isEmpty())
            <x-admin.empty-state :title="__('admin.pages.empty')" icon="page">
                <a href="{{ route('admin.pages.create') }}" class="btn-primary btn-sm">{{ __('admin.pages.add') }}</a>
            </x-admin.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="table-admin min-w-[780px]">
                    <thead>
                        <tr>
                            <th>{{ __('admin.pages.table_title') }}</th>
                            <th>{{ __('admin.pages.table_slug') }}</th>
                            <th>{{ __('admin.pages.table_footer') }}</th>
                            <th>{{ __('admin.pages.table_status') }}</th>
                            <th class="text-end!">{{ __('admin.common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pages as $page)
                            <tr class="transition hover:bg-smoke/40">
                                <td>
                                    <a href="{{ route('admin.pages.edit', $page) }}" class="block max-w-[300px] truncate text-sm font-bold hover:underline">{{ $page->title }}</a>
                                    <span class="mt-0.5 block text-[11px] text-muted">{{ __('admin.common.updated_at') }}: {{ $page->updated_at?->diffForHumans() }}</span>
                                </td>
                                <td>
                                    <span class="text-xs text-muted" dir="ltr">/{{ $page->slug === 'about-us' ? 'about' : 'pages/'.$page->slug }}</span>
                                </td>
                                <td>
                                    @if ($page->show_in_footer)
                                        <span class="badge-outline">{{ __('admin.pages.footer_badge') }}</span>
                                    @else
                                        <span class="text-xs text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($page->is_active)
                                        <span class="badge-success">{{ __('admin.common.active') }}</span>
                                    @else
                                        <span class="badge-muted">{{ __('admin.common.inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        @if ($page->is_active)
                                            <a href="{{ $page->slug === 'about-us' ? route('about') : route('pages.show', $page) }}" target="_blank" rel="noopener" class="icon-btn size-9 text-muted hover:bg-smoke hover:text-ink" aria-label="{{ __('admin.common.view_store') }}">
                                                <x-store.icon name="external" class="size-4" />
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.pages.edit', $page) }}" class="icon-btn size-9 text-muted hover:bg-smoke hover:text-ink" aria-label="{{ __('admin.common.edit') }}">
                                            <x-store.icon name="edit" class="size-4" />
                                        </a>
                                        @if ($page->slug !== 'about-us')
                                            <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" onsubmit="return confirm('{{ __('admin.pages.delete_confirm') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="icon-btn size-9 text-muted hover:bg-danger-soft hover:text-danger" aria-label="{{ __('admin.common.delete') }}">
                                                    <x-store.icon name="trash" class="size-4" />
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-line px-4 py-3">
                {{ $pages->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>

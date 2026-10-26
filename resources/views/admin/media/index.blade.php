<x-layouts.admin :title="__('admin.media.title')">
    <x-admin.page-header :title="__('admin.media.title')" :subtitle="__('admin.media.subtitle')">
        <x-slot:actions>
            <span class="badge-muted">{{ __('admin.media.files_count', ['count' => $media->total()]) }}</span>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_360px]">
        <div class="surface-card p-4 sm:p-5">
            <form method="GET" action="{{ route('admin.media.index') }}" class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_200px_auto]">
                <div>
                    <label for="media-search" class="sr-only">{{ __('admin.common.search') }}</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-muted">
                            <x-store.icon name="search" class="size-4" />
                        </span>
                        <input id="media-search" type="search" name="q" value="{{ $search }}" placeholder="{{ __('admin.media.search_placeholder') }}" class="field ps-10">
                    </div>
                </div>
                <div>
                    <label for="media-folder" class="sr-only">{{ __('admin.media.folder') }}</label>
                    <select id="media-folder" name="folder" class="field min-h-11">
                        <option value="">{{ __('admin.media.filter_folder') }}</option>
                        @foreach ($folders as $availableFolder)
                            <option value="{{ $availableFolder }}" @selected($folder === $availableFolder)>{{ $availableFolder }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="btn-primary btn-sm">{{ __('admin.common.apply') }}</button>
                    @if ($search !== '' || $folder !== '')
                        <a href="{{ route('admin.media.index') }}" class="btn-ghost btn-sm">{{ __('admin.common.reset') }}</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="surface-card p-4 sm:p-5">
            <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="flex flex-col gap-3 sm:flex-row lg:flex-col">
                @csrf
                <div class="flex-1">
                    <label for="media-upload" class="field-label">{{ __('admin.media.upload_new') }}</label>
                    <input id="media-upload" type="file" name="image" accept="image/jpeg,image/png,image/webp" class="block w-full text-xs text-muted file:me-3 file:cursor-pointer file:rounded-full file:border-0 file:bg-ink file:px-4 file:py-2 file:text-xs file:font-bold file:text-white" required>
                    <p class="mt-1.5 text-[11px] leading-5 text-muted">{{ __('admin.media.upload_hint') }}</p>
                    @error('image')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn-primary btn-sm">
                    <x-store.icon name="upload" class="size-4" />
                    {{ __('admin.media.upload') }}
                </button>
            </form>
        </div>
    </div>

    <div class="mt-6">
        @if ($media->isEmpty())
            <div class="surface-card">
                <x-admin.empty-state :title="__('admin.media.empty')" icon="image" />
            </div>
        @else
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-4">
                @foreach ($media as $item)
                    @php
                        $size = $item['size'] >= 1048576
                            ? number_format($item['size'] / 1048576, 1).' MB'
                            : number_format($item['size'] / 1024, 0).' KB';
                    @endphp
                    <article class="surface-card group overflow-hidden">
                        <a href="{{ $item['url'] }}" target="_blank" rel="noopener" class="relative block aspect-square overflow-hidden bg-smoke">
                            <img src="{{ $item['url'] }}" alt="{{ $item['name'] }}" class="size-full object-cover transition duration-300 group-hover:scale-[1.03]" loading="lazy">
                            <span class="absolute start-2 top-2 badge-ink">{{ $item['folder'] }}</span>
                        </a>
                        <div class="p-3.5">
                            <p class="truncate text-xs font-bold" title="{{ $item['name'] }}" dir="ltr">{{ $item['name'] }}</p>
                            <p class="mt-1 flex items-center justify-between text-[10px] text-muted">
                                <span>{{ $size }}</span>
                                <span>{{ \Illuminate\Support\Carbon::createFromTimestamp($item['modified'])->format('Y-m-d') }}</span>
                            </p>
                            <div class="mt-3 flex items-center justify-between gap-1 border-t border-line/70 pt-2.5">
                                <div x-data="{ copied: false }">
                                    <button
                                        type="button"
                                        class="btn-ghost btn-sm px-2"
                                        @click="navigator.clipboard.writeText(@js($item['url'])).then(() => { copied = true; setTimeout(() => copied = false, 1500) })"
                                    >
                                        <x-store.icon name="copy" class="size-3.5" />
                                        <span x-show="! copied">{{ __('admin.media.copy_url') }}</span>
                                        <span x-show="copied" x-cloak>{{ __('admin.common.copied') }}</span>
                                    </button>
                                </div>
                                <div class="flex items-center gap-1">
                                    <a href="{{ $item['url'] }}" target="_blank" rel="noopener" class="icon-btn size-8 text-muted hover:bg-smoke hover:text-ink" aria-label="{{ __('admin.media.open') }}">
                                        <x-store.icon name="external" class="size-3.5" />
                                    </a>
                                    <form method="POST" action="{{ route('admin.media.destroy') }}" onsubmit="return confirm('{{ __('admin.media.delete_confirm') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="path" value="{{ $item['path'] }}">
                                        <button type="submit" class="icon-btn size-8 text-muted hover:bg-danger-soft hover:text-danger" aria-label="{{ __('admin.media.delete_file') }}">
                                            <x-store.icon name="trash" class="size-3.5" />
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-5">{{ $media->links() }}</div>
        @endif

        <p class="mt-4 text-xs text-muted">{{ __('admin.media.note') }}</p>
    </div>
</x-layouts.admin>

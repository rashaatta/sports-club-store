<x-layouts.admin :title="__('admin.banners.title')">
    <x-admin.page-header :title="__('admin.banners.title')" :subtitle="__('admin.banners.subtitle')">
        <x-slot:actions>
            <a href="{{ route('admin.banners.create') }}" class="btn-primary btn-sm">
                <x-store.icon name="plus" class="size-4" />
                {{ __('admin.banners.add') }}
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    @if ($banners->isEmpty())
        <div class="surface-card">
            <x-admin.empty-state :title="__('admin.banners.empty')" icon="image">
                <a href="{{ route('admin.banners.create') }}" class="btn-primary btn-sm">{{ __('admin.banners.add') }}</a>
            </x-admin.empty-state>
        </div>
    @else
        <div class="grid gap-5 md:grid-cols-2">
            @foreach ($banners as $banner)
                <article class="surface-card overflow-hidden">
                    <div class="relative aspect-[16/7] bg-ink">
                        @if ($banner->image_path)
                            <img src="{{ media_url($banner->image_path) }}" alt="" class="size-full object-cover" loading="lazy">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-ink/80 via-ink/10 to-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 p-5 text-white">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-volt">{{ __('admin.banners.placement_home_promo') }}</p>
                            <h2 class="mt-1 line-clamp-1 text-lg font-black">{{ $banner->title }}</h2>
                            @if ($banner->subtitle)
                                <p class="mt-1 line-clamp-1 text-xs text-white/70">{{ $banner->subtitle }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 px-5 py-4">
                        @if ($banner->is_active)
                            <span class="badge-success">{{ __('admin.common.active') }}</span>
                        @else
                            <span class="badge-muted">{{ __('admin.common.inactive') }}</span>
                        @endif
                        <span class="badge-outline">{{ __('admin.common.sort_order') }}: {{ $banner->sort_order }}</span>
                        <span class="text-[11px] text-muted">
                            @if ($banner->starts_at || $banner->ends_at)
                                {{ $banner->starts_at?->format('Y-m-d') ?? '—' }} ← {{ $banner->ends_at?->format('Y-m-d') ?? '—' }}
                            @else
                                {{ __('admin.banners.always') }}
                            @endif
                        </span>
                        <div class="ms-auto flex items-center gap-1">
                            <a href="{{ route('admin.banners.edit', $banner) }}" class="icon-btn size-9 text-muted hover:bg-smoke hover:text-ink" aria-label="{{ __('admin.common.edit') }}">
                                <x-store.icon name="edit" class="size-4" />
                            </a>
                            <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" onsubmit="return confirm('{{ __('admin.banners.delete_confirm') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="icon-btn size-9 text-muted hover:bg-danger-soft hover:text-danger" aria-label="{{ __('admin.common.delete') }}">
                                    <x-store.icon name="trash" class="size-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-4">{{ $banners->links() }}</div>
    @endif
</x-layouts.admin>

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('storefront.pagination.label') }}" class="mt-10 flex flex-col items-center gap-4 sm:flex-row sm:justify-between">
        <p class="text-xs font-semibold text-muted sm:hidden">
            {{ __('storefront.common.page_of', ['current' => $paginator->currentPage(), 'last' => $paginator->lastPage()]) }}
        </p>

        <div class="hidden items-center gap-1 sm:flex">
            @if ($paginator->onFirstPage())
                <span class="inline-flex size-10 items-center justify-center rounded-full border border-line text-muted/40" aria-disabled="true">
                    <x-store.icon name="chevron-start" class="size-4" />
                    <span class="sr-only">{{ __('storefront.pagination.previous') }}</span>
                </span>
            @else
                <a
                    href="{{ $paginator->previousPageUrl() }}"
                    rel="prev"
                    class="inline-flex size-10 items-center justify-center rounded-full border border-line text-ink transition hover:border-ink hover:bg-ink hover:text-white"
                    aria-label="{{ __('storefront.pagination.previous') }}"
                >
                    <x-store.icon name="chevron-start" class="size-4" />
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex size-10 items-center justify-center text-sm text-muted">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex size-10 items-center justify-center rounded-full bg-ink text-sm font-bold text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex size-10 items-center justify-center rounded-full text-sm font-semibold text-ink transition hover:bg-smoke">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a
                    href="{{ $paginator->nextPageUrl() }}"
                    rel="next"
                    class="inline-flex size-10 items-center justify-center rounded-full border border-line text-ink transition hover:border-ink hover:bg-ink hover:text-white"
                    aria-label="{{ __('storefront.pagination.next') }}"
                >
                    <x-store.icon name="chevron-end" class="size-4" />
                </a>
            @else
                <span class="inline-flex size-10 items-center justify-center rounded-full border border-line text-muted/40" aria-disabled="true">
                    <x-store.icon name="chevron-end" class="size-4" />
                    <span class="sr-only">{{ __('storefront.pagination.next') }}</span>
                </span>
            @endif
        </div>

        <div class="flex w-full items-center justify-between gap-3 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="btn-outline btn-sm flex-1 border-line text-muted/40">{{ __('storefront.pagination.previous') }}</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn-outline btn-sm flex-1">{{ __('storefront.pagination.previous') }}</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn-primary btn-sm flex-1">{{ __('storefront.pagination.next') }}</a>
            @else
                <span class="btn-primary btn-sm flex-1 opacity-30">{{ __('storefront.pagination.next') }}</span>
            @endif
        </div>

        @if ($paginator->total() > 0)
            <p class="hidden text-xs text-muted sm:block">
                {{ __('storefront.catalog.showing', ['from' => $paginator->firstItem(), 'to' => $paginator->lastItem(), 'total' => $paginator->total()]) }}
            </p>
        @endif
    </nav>
@endif

@props(['items' => []])

@php
    $trail = collect([['label' => __('storefront.common.home'), 'url' => route('home')]])
        ->concat(collect($items)->map(fn ($item) => [
            'label' => $item['label'] ?? '',
            'url' => $item['url'] ?? null,
        ]))
        ->values();

    $structured = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $trail->map(fn ($item, $index) => array_filter([
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $item['label'],
            'item' => $item['url'] ?? url()->current(),
        ]))->all(),
    ];
@endphp

<nav aria-label="{{ __('storefront.common.breadcrumb') }}" class="text-xs text-muted">
    <ol class="flex flex-wrap items-center gap-2">
        @foreach ($trail as $item)
            <li class="flex items-center gap-2">
                @if ($loop->last)
                    <span aria-current="page" class="font-semibold text-ink">{{ $item['label'] }}</span>
                @else
                    <a href="{{ $item['url'] }}" class="transition hover:text-ink">{{ $item['label'] }}</a>
                    <span class="text-line" aria-hidden="true">/</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>

<script type="application/ld+json">{!! json_encode($structured, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>

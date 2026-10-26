@props([
    'title' => null,
    'description' => null,
    'canonical' => null,
    'robots' => 'index,follow',
    'ogImage' => null,
    'ogType' => 'website',
    'schema' => [],
    'preloadImage' => null,
    'bodyClass' => '',
])

@php
    $storeName = store_setting('store_name');
    $pageTitle = filled($title) ? $title : store_setting('seo_title');
    $metaDescription = filled($description) ? $description : store_setting('seo_description');
    $pageCanonical = filled($canonical) ? $canonical : url()->current();
    $faviconPath = store_setting('favicon');
    $favicon = $faviconPath !== '' ? media_url($faviconPath) : asset('favicon.svg');
    $faviconType = str_ends_with((string) parse_url($favicon, PHP_URL_PATH), '.svg') ? 'image/svg+xml' : null;
    $ogImageUrl = filled($ogImage)
        ? (str_starts_with($ogImage, 'http') ? $ogImage : url($ogImage))
        : null;
    $preloadSrcset = filled($preloadImage) ? media_srcset($preloadImage) : null;
    $preloadUrl = filled($preloadImage) ? media_url($preloadImage) : null;
    $jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ store_dir() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0b0b0c">

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ Str::limit(strip_tags($metaDescription), 158) }}">
    <link rel="canonical" href="{{ $pageCanonical }}">
    <meta name="robots" content="{{ $robots }}">

    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:site_name" content="{{ $storeName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($metaDescription), 158) }}">
    <meta property="og:url" content="{{ $pageCanonical }}">
    <meta property="og:locale" content="ar_SA">
    @if ($ogImageUrl)
        <meta property="og:image" content="{{ $ogImageUrl }}">
        <meta name="twitter:image" content="{{ $ogImageUrl }}">
    @endif
    <meta name="twitter:card" content="{{ $ogImageUrl ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($metaDescription), 158) }}">

    <link rel="icon" href="{{ $favicon }}" @if ($faviconType) type="{{ $faviconType }}" @endif>
    <link rel="apple-touch-icon" href="{{ $favicon }}">

    @if ($preloadUrl)
        <link
            rel="preload"
            as="image"
            href="{{ $preloadUrl }}"
            @if ($preloadSrcset) imagesrcset="{{ $preloadSrcset }}" imagesizes="100vw" @endif
        >
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @foreach ($schema as $data)
        <script type="application/ld+json">{!! json_encode($data, $jsonFlags) !!}</script>
    @endforeach

    @stack('head')
</head>
<body
    class="{{ $bodyClass }} min-h-screen bg-paper text-ink"
    x-data
    x-effect="document.body.style.overflow = ($store.ui.menuOpen || $store.ui.searchOpen) ? 'hidden' : ''"
>
    <a
        href="#main-content"
        class="sr-only z-[100] focus:not-sr-only focus:fixed focus:start-4 focus:top-4 focus:rounded-full focus:bg-volt focus:px-5 focus:py-3 focus:text-sm focus:font-bold focus:text-ink"
    >
        {{ __('storefront.common.skip_to_content') }}
    </a>

    @include('storefront.partials.header')

    <main id="main-content" tabindex="-1">
        {{ $slot }}
    </main>

    @include('storefront.partials.footer')
    @include('storefront.partials.flash')

    @stack('scripts')
</body>
</html>

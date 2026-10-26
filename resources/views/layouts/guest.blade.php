@php
    $storeName = store_setting('store_name');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ store_dir() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ __('admin.brand.panel') }} | {{ $storeName }}</title>
    <link rel="icon" href="{{ media_url(store_setting('favicon')) ?? asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-ink font-sans text-ink antialiased">
    <div class="min-h-screen lg:grid lg:grid-cols-[1.05fr_1fr]">
        <section class="relative hidden overflow-hidden bg-ink p-10 text-white lg:flex lg:flex-col lg:justify-between xl:p-16">
            <div class="pointer-events-none absolute -end-32 -top-32 size-[28rem] rounded-full border border-white/10"></div>
            <div class="pointer-events-none absolute -bottom-40 -start-24 size-[34rem] rounded-full border border-volt/20"></div>
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_70%_20%,rgba(215,254,58,0.12),transparent_45%)]"></div>

            <div class="relative flex items-center gap-3">
                <span class="flex size-11 items-center justify-center rounded-xl bg-volt font-display text-2xl leading-none text-ink">S</span>
                <span class="font-display text-3xl leading-none tracking-wide">{{ $storeName }}</span>
            </div>

            <div class="relative max-w-lg py-20">
                <p class="eyebrow-latin text-volt">{{ __('admin.brand.panel') }}</p>
                <h1 class="mt-4 text-4xl font-black leading-[1.15] xl:text-5xl">{{ __('admin.login.aside_title') }}</h1>
                <p class="mt-5 text-base leading-8 text-white/55">{{ __('admin.login.aside_text') }}</p>
                <ul class="mt-8 space-y-3.5 text-sm text-white/70">
                    @foreach ([__('admin.login.aside_point_1'), __('admin.login.aside_point_2'), __('admin.login.aside_point_3')] as $point)
                        <li class="flex items-center gap-3">
                            <span class="flex size-5 items-center justify-center rounded-full bg-volt text-ink">
                                <x-store.icon name="check" class="size-3" />
                            </span>
                            {{ $point }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <p class="relative text-[11px] text-white/30">{{ $storeName }} · {{ now()->format('Y') }}</p>
        </section>

        <section class="flex min-h-screen flex-col bg-smoke">
            <div class="flex items-center justify-between gap-3 p-5">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 lg:hidden">
                    <span class="flex size-9 items-center justify-center rounded-lg bg-ink font-display text-xl leading-none text-volt">S</span>
                    <span class="font-display text-2xl leading-none">{{ $storeName }}</span>
                </a>
                <div class="ms-auto flex items-center gap-3">
                    <span class="hidden text-[11px] font-bold text-muted lg:inline">{{ __('admin.brand.panel') }}</span>
                    <x-language-switcher />
                </div>
            </div>

            <div class="flex flex-1 items-center justify-center px-4 py-10 sm:px-8">
                <div class="w-full max-w-[430px]">
                    {{ $slot }}
                </div>
            </div>

            <div class="p-5 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold text-muted transition hover:text-ink">
                    <x-store.icon name="arrow-right" class="size-3.5 ltr:-scale-x-100" />
                    {{ __('auth.back_store') }}
                </a>
            </div>
        </section>
    </div>
</body>
</html>

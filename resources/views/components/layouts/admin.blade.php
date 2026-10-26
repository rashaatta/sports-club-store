@props(['title' => null])
@php
    $storeName = store_setting('store_name');
    $user = auth()->user();
    $pageTitle = $title ? $title.' | '.__('admin.brand.panel') : __('admin.brand.panel').' | '.$storeName;
    $navItems = [
        [
            'label' => __('admin.nav.dashboard'),
            'route' => 'admin.dashboard',
            'active' => 'admin.dashboard',
            'icon' => 'dashboard',
            'permission' => 'view_dashboard',
        ],
        [
            'label' => __('admin.nav.analytics'),
            'route' => 'admin.analytics.index',
            'active' => 'admin.analytics.*',
            'icon' => 'chart',
            'permission' => 'view_analytics',
        ],
        [
            'label' => __('admin.nav.products'),
            'route' => 'admin.products.index',
            'active' => 'admin.products.*',
            'icon' => 'bag',
            'permission' => 'manage_products',
        ],
        [
            'label' => __('admin.nav.categories'),
            'route' => 'admin.categories.index',
            'active' => 'admin.categories.*',
            'icon' => 'tags',
            'permission' => 'manage_categories',
        ],
        [
            'label' => __('admin.nav.inventory'),
            'route' => 'admin.inventory.index',
            'active' => 'admin.inventory.*',
            'icon' => 'inventory',
            'permission' => 'manage_inventory',
        ],
        [
            'label' => __('admin.nav.pages'),
            'route' => 'admin.pages.index',
            'active' => 'admin.pages.*',
            'icon' => 'page',
            'permission' => 'manage_content',
        ],
        [
            'label' => __('admin.nav.banners'),
            'route' => 'admin.banners.index',
            'active' => 'admin.banners.*',
            'icon' => 'image',
            'permission' => 'manage_content',
        ],
        [
            'label' => __('admin.nav.faqs'),
            'route' => 'admin.faqs.index',
            'active' => 'admin.faqs.*',
            'icon' => 'faq',
            'permission' => 'manage_content',
        ],
        [
            'label' => __('admin.nav.messages'),
            'route' => 'admin.messages.index',
            'active' => 'admin.messages.*',
            'icon' => 'message',
            'permission' => 'view_messages',
        ],
        [
            'label' => __('admin.nav.users'),
            'route' => 'admin.users.index',
            'active' => 'admin.users.*',
            'icon' => 'users',
            'permission' => 'manage_users',
        ],
        [
            'label' => __('admin.nav.media'),
            'route' => 'admin.media.index',
            'active' => 'admin.media.*',
            'icon' => 'upload',
            'permission' => null,
        ],
        [
            'label' => __('admin.nav.settings'),
            'route' => 'admin.settings.edit',
            'active' => 'admin.settings.*',
            'icon' => 'settings',
            'permission' => null,
        ],
    ];
    $canMedia = $user?->can('manage_products') || $user?->can('manage_content');
    $canSettings = $user?->can('manage_settings') || $user?->can('manage_content');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ store_dir() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ $pageTitle }}</title>
    <link rel="icon" href="{{ media_url(store_setting('favicon')) ?? asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-smoke font-sans text-ink antialiased">
    <a href="#admin-main" class="sr-only focus:not-sr-only focus:fixed focus:start-4 focus:top-4 focus:z-[100] focus:rounded-full focus:bg-ink focus:px-5 focus:py-3 focus:text-sm focus:font-bold focus:text-white">
        {{ __('storefront.common.skip_to_content') }}
    </a>

    <div
        x-data="{ sidebarOpen: false }"
        x-effect="document.body.style.overflow = sidebarOpen && window.innerWidth < 1024 ? 'hidden' : ''"
        @keydown.escape.window="sidebarOpen = false"
    >
        <div
            x-cloak
            x-show="sidebarOpen"
            x-transition.opacity
            class="fixed inset-0 z-40 bg-ink/50 backdrop-blur-sm lg:hidden"
            @click="sidebarOpen = false"
            aria-hidden="true"
        ></div>

        <aside
            class="fixed inset-y-0 start-0 z-50 flex w-[280px] translate-x-full flex-col bg-ink text-white transition-transform duration-300 lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0!' : ''"
            aria-label="{{ __('admin.brand.panel') }}"
        >
            <div class="flex h-20 shrink-0 items-center justify-between border-b border-white/10 px-5">
                <a href="{{ route('admin.dashboard') }}" class="group flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-volt font-display text-xl leading-none text-ink">S</span>
                    <span class="min-w-0">
                        <span class="block truncate font-display text-2xl leading-none tracking-wide">{{ $storeName }}</span>
                        <span class="mt-1 block text-[10px] font-bold uppercase tracking-[0.22em] text-white/40">{{ __('admin.brand.panel') }}</span>
                    </span>
                </a>
                <button type="button" class="icon-btn text-white/60 hover:bg-white/10 hover:text-white lg:hidden" @click="sidebarOpen = false" aria-label="{{ __('admin.nav.close_menu') }}">
                    <x-store.icon name="close" class="size-5" />
                </button>
            </div>

            <nav class="admin-scrollbar flex-1 overflow-y-auto px-3 py-5" aria-label="{{ __('admin.nav.menu') }}">
                <p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.22em] text-white/30">{{ __('admin.brand.store_label') }}</p>
                <ul class="space-y-1">
                    @foreach ($navItems as $item)
                        @php
                            $allowed = $item['permission'] === null
                                ? ($item['route'] === 'admin.media.index' ? $canMedia : $canSettings)
                                : $user?->can($item['permission']);
                        @endphp
                        @if ($allowed)
                            <li>
                                <a
                                    href="{{ route($item['route']) }}"
                                    @class([
                                        'group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition',
                                        'bg-white/10 text-white' => request()->routeIs($item['active']),
                                        'text-white/55 hover:bg-white/5 hover:text-white' => ! request()->routeIs($item['active']),
                                    ])
                                    @if (request()->routeIs($item['active'])) aria-current="page" @endif
                                >
                                    <span @class([
                                        'flex size-8 items-center justify-center rounded-lg transition',
                                        'bg-volt text-ink' => request()->routeIs($item['active']),
                                        'bg-white/5 text-white/55 group-hover:text-white' => ! request()->routeIs($item['active']),
                                    ])>
                                        <x-store.icon :name="$item['icon']" class="size-4" />
                                    </span>
                                    <span class="flex-1">{{ $item['label'] }}</span>
                                    @if ($item['route'] === 'admin.messages.index' && ($unreadMessages ?? 0) > 0)
                                        <span class="rounded-full bg-volt px-2 py-0.5 text-[10px] font-bold text-ink">{{ $unreadMessages }}</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </nav>

            <div class="shrink-0 border-t border-white/10 p-4">
                <div class="flex items-center gap-3 rounded-xl bg-white/5 p-3">
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-volt text-sm font-black text-ink">{{ mb_substr($user?->name ?? '', 0, 1) }}</span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-bold">{{ $user?->name }}</span>
                        <span class="block truncate text-[11px] text-white/45">{{ $user?->role?->label() }}</span>
                    </span>
                    <a href="{{ route('admin.profile.edit') }}" class="rounded-lg p-2 text-white/50 transition hover:bg-white/10 hover:text-white" aria-label="{{ __('admin.nav.profile') }}">
                        <x-store.icon name="settings" class="size-4" />
                    </a>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    <a href="{{ route('home') }}" class="flex flex-1 items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-[11px] font-bold text-white/50 transition hover:bg-white/5 hover:text-white" target="_blank" rel="noopener">
                        <x-store.icon name="external" class="size-3.5" />
                        {{ __('admin.nav.view_store') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-[11px] font-bold text-white/50 transition hover:bg-white/5 hover:text-white">
                            <x-store.icon name="logout" class="size-3.5" />
                            {{ __('admin.nav.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex min-h-screen flex-col lg:ps-[280px]">
            <header class="sticky top-0 z-30 border-b border-line/70 bg-white/90 backdrop-blur">
                <div class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8">
                    <button type="button" class="icon-btn -ms-2 text-ink hover:bg-smoke lg:hidden" @click="sidebarOpen = true" aria-label="{{ __('admin.nav.open_menu') }}" :aria-expanded="sidebarOpen">
                        <x-store.icon name="menu" class="size-5" />
                    </button>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-bold">{{ $storeName }} <span class="font-normal text-muted">/ {{ __('admin.brand.store_label') }}</span></p>
                    </div>
                    <a href="{{ route('home') }}" class="hidden items-center gap-1.5 rounded-full border border-ink/10 px-4 py-2 text-xs font-bold text-ink transition hover:border-ink hover:bg-ink hover:text-white sm:inline-flex" target="_blank" rel="noopener">
                        <x-store.icon name="external" class="size-3.5" />
                        {{ __('admin.nav.view_store') }}
                    </a>
                    @can('view_messages')
                        <a href="{{ route('admin.messages.index') }}" class="relative icon-btn text-ink hover:bg-smoke" aria-label="{{ __('admin.nav.messages') }}">
                            <x-store.icon name="message" class="size-5" />
                            @if (($unreadMessages ?? 0) > 0)
                                <span class="absolute end-1.5 top-1.5 flex size-4 items-center justify-center rounded-full bg-danger text-[9px] font-bold text-white">{{ min(9, $unreadMessages) }}{{ $unreadMessages > 9 ? '+' : '' }}</span>
                            @endif
                        </a>
                    @endcan
                </div>
            </header>

            <main id="admin-main" class="flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                <div class="mx-auto w-full max-w-[1320px]">
                    @include('admin.partials.flash')
                    {{ $slot }}
                </div>
            </main>

            <footer class="px-4 pb-6 text-center text-[11px] text-muted sm:px-6 lg:px-8">
                {{ $storeName }} — {{ __('admin.brand.panel') }} · {{ now()->format('Y') }}
            </footer>
        </div>
    </div>
</body>
</html>

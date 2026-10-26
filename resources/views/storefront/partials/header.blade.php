@php
    $storeName = store_setting('store_name');
    $tagline = store_setting('store_tagline');
    $logo = store_setting('logo');
    $announcement = store_setting('announcement_text');
    $showAnnouncement = store_boolean('show_announcement') && filled($announcement);
@endphp

@if ($showAnnouncement)
    <div
        x-data="{ visible: true }"
        x-show="visible"
        class="relative z-30 bg-ink text-white"
        role="region"
        aria-label="{{ __('storefront.header.announcement_aria') }}"
    >
        <div class="container-page flex min-h-9 items-center justify-center gap-3 py-2">
            <p class="text-center text-[11px] font-semibold tracking-wide text-white/85 sm:text-xs">{{ $announcement }}</p>
            <button
                type="button"
                class="absolute end-3 top-1/2 -translate-y-1/2 rounded-full p-1.5 text-white/50 transition hover:bg-white/10 hover:text-white"
                @click="visible = false"
                aria-label="{{ __('storefront.header.dismiss_announcement') }}"
            >
                <x-store.icon name="close" class="size-4" />
            </button>
        </div>
    </div>
@endif

<header class="sticky top-0 z-40 border-b border-line/90 bg-paper">
    <div class="container-page flex h-[70px] items-center justify-between gap-4 sm:h-[78px]">
        <div class="flex items-center gap-3 lg:gap-9">
            <button
                type="button"
                class="icon-btn -ms-2 text-ink lg:hidden"
                @click="$store.ui.menuOpen = true"
                aria-label="{{ __('storefront.nav.open_menu') }}"
                :aria-expanded="$store.ui.menuOpen.toString()"
                aria-controls="store-menu-dialog"
            >
                <x-store.icon name="menu" class="size-6" />
            </button>

            <a
                href="{{ route('home') }}"
                class="group flex shrink-0 items-center gap-2.5"
                aria-label="{{ __('storefront.aria.home_logo', ['store' => $storeName]) }}"
            >
                @if ($logo !== '')
                    <img src="{{ media_url($logo) }}" alt="{{ $storeName }}" class="h-9 w-auto sm:h-10" width="160" height="44">
                @else
                    <span class="display-latin text-[26px] text-ink transition group-hover:text-carbon sm:text-[30px]">{{ $storeName }}<span class="text-volt-dark">.</span></span>
                    <span class="hidden max-w-[130px] border-s border-line ps-2.5 text-[9px] font-bold leading-4 text-muted sm:block">{{ $tagline }}</span>
                @endif
            </a>

            <nav class="hidden items-center gap-6 lg:flex xl:gap-7" aria-label="{{ __('storefront.nav.aria_label') }}">
                <a href="{{ route('home') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('home')])>
                    {{ __('storefront.nav.home') }}
                </a>
                <a href="{{ route('products.index') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('products.index') && ! request()->boolean('on_sale')])>
                    {{ __('storefront.nav.shop') }}
                </a>

                <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                    <button
                        type="button"
                        class="nav-link gap-1.5"
                        @click="open = ! open"
                        :aria-expanded="open.toString()"
                        aria-haspopup="true"
                        aria-controls="category-menu"
                    >
                        {{ __('storefront.nav.categories') }}
                        <x-store.icon name="chevron-down" class="size-3.5 transition" x-bind:class="open ? 'rotate-180' : ''" />
                    </button>

                    <div
                        id="category-menu"
                        x-show="open"
                        x-cloak
                        @click.outside="open = false"
                        x-transition.origin.top
                        class="absolute start-0 top-full z-50 mt-3 w-[560px] overflow-hidden rounded-2xl border border-line bg-paper p-3 shadow-lift"
                    >
                        @if ($navCategories->isNotEmpty())
                            <div class="grid grid-cols-2 gap-1.5">
                                @foreach ($navCategories as $category)
                                    <div class="rounded-xl p-3 transition hover:bg-smoke">
                                        <a href="{{ route('categories.show', $category) }}" class="flex items-start justify-between gap-2 text-sm font-extrabold text-ink">
                                            {{ $category->name }}
                                            <x-store.icon name="arrow-left" class="mt-0.5 size-4 shrink-0 text-muted ltr:-scale-x-100" />
                                        </a>
                                        @if ($category->children->isNotEmpty())
                                            <ul class="mt-2 space-y-1">
                                                @foreach ($category->children as $child)
                                                    <li>
                                                        <a href="{{ route('categories.show', $child) }}" class="block rounded-md py-0.5 text-xs font-semibold text-muted transition hover:text-ink">
                                                            {{ $child->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <a href="{{ route('products.index') }}" class="mt-3 flex items-center justify-center gap-2 rounded-xl bg-smoke px-4 py-3 text-xs font-bold text-ink transition hover:bg-line/70">
                                {{ __('storefront.catalog.all_products') }}
                                <x-store.icon name="arrow-left" class="size-4 ltr:-scale-x-100" />
                            </a>
                        @else
                            <p class="p-4 text-sm text-muted">{{ __('storefront.home.categories_empty') }}</p>
                        @endif
                    </div>
                </div>

                <a href="{{ route('products.index', ['on_sale' => 1]) }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('products.index') && request()->boolean('on_sale')])>
                    {{ __('storefront.nav.offers') }}
                </a>
                <a href="{{ route('about') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('about')])>
                    {{ __('storefront.nav.about') }}
                </a>
                <a href="{{ route('contact') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('contact')])>
                    {{ __('storefront.nav.contact') }}
                </a>
            </nav>
        </div>

        <div class="flex items-center gap-1.5 sm:gap-2">
            <span class="hidden sm:block">
                <x-language-switcher />
            </span>

            <button
                type="button"
                class="icon-btn text-ink hover:bg-smoke"
                @click="$store.ui.searchOpen = true"
                aria-label="{{ __('storefront.header.search_title') }}"
                :aria-expanded="$store.ui.searchOpen.toString()"
                aria-controls="store-search-dialog"
            >
                <x-store.icon name="search" class="size-[21px]" />
            </button>

            @auth
                <a href="{{ route('admin.dashboard') }}" class="icon-btn hidden text-ink hover:bg-smoke sm:inline-flex" aria-label="{{ __('storefront.nav.admin') }}">
                    <x-store.icon name="dashboard" class="size-5" />
                </a>
            @endauth

            @if ($whatsappUrl)
                <a
                    href="{{ $whatsappUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn-primary btn-sm ms-1 hidden sm:inline-flex"
                    aria-label="{{ __('storefront.header.whatsapp_aria') }}"
                >
                    <x-store.icon name="whatsapp" class="size-4 text-volt" />
                    {{ __('storefront.nav.whatsapp') }}
                </a>
            @endif
        </div>
    </div>

    <div
        id="store-search-dialog"
        x-show="$store.ui.searchOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('storefront.header.search_title') }}"
        x-data="headerOverlay('searchOpen', '#header-search')"
        @keydown.escape.window="close()"
        @keydown.tab="trapFocus"
    >
        <div class="absolute inset-0 bg-ink/60 backdrop-blur-sm" @click="close()"></div>
        <div
            class="relative mx-auto mt-[8vh] w-[calc(100%-2rem)] max-w-2xl rounded-2xl border border-line bg-paper p-5 shadow-lift sm:mt-[12vh] sm:p-7"
            x-data="{ q: '' }"
        >
            <div class="mb-5 flex items-center justify-between gap-4">
                <div>
                    <p class="eyebrow-latin">SEARCH</p>
                    <h2 class="mt-1 text-xl font-extrabold">{{ __('storefront.header.search_title') }}</h2>
                </div>
                <button type="button" class="icon-btn hover:bg-smoke" @click="close()" aria-label="{{ __('storefront.header.search_close') }}">
                    <x-store.icon name="close" class="size-5" />
                </button>
            </div>

            <form method="GET" action="{{ route('products.index') }}" role="search" class="flex gap-2">
                <label for="header-search" class="sr-only">{{ __('storefront.header.search_title') }}</label>
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 start-4 flex items-center text-muted">
                        <x-store.icon name="search" class="size-5" />
                    </span>
                    <input
                        id="header-search"
                        name="q"
                        type="search"
                        x-model="q"
                        placeholder="{{ __('storefront.header.search_placeholder') }}"
                        class="field h-13 ps-12 text-base"
                        autocomplete="off"
                        @keydown.enter="$el.form?.submit()"
                    >
                </div>
                <button type="submit" class="btn-primary px-5">{{ __('storefront.header.search_submit') }}</button>
            </form>
            <p class="mt-2.5 text-[11px] text-muted">{{ __('storefront.header.search_hint') }}</p>

            @if ($navCategories->isNotEmpty())
                <div class="mt-5 border-t border-line pt-4">
                    <p class="mb-2 text-xs font-bold text-muted">{{ __('storefront.search.popular_categories') }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($navCategories as $category)
                            <a href="{{ route('categories.show', $category) }}" class="chip">{{ $category->name }}</a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div
        id="store-menu-dialog"
        x-show="$store.ui.menuOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 lg:hidden"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('storefront.header.menu_title') }}"
        x-data="headerOverlay('menuOpen', '[data-menu-close]')"
        @keydown.escape.window="close()"
        @keydown.tab="trapFocus"
    >
        <div class="absolute inset-0 bg-ink/60 backdrop-blur-sm" @click="close()"></div>
        <div class="absolute inset-y-0 start-0 flex w-[88%] max-w-sm flex-col bg-paper shadow-lift">
            <div class="flex h-[70px] items-center justify-between border-b border-line px-5">
                <a href="{{ route('home') }}" class="display-latin text-2xl">{{ $storeName }}<span class="text-volt-dark">.</span></a>
                <button type="button" class="icon-btn hover:bg-smoke" data-menu-close @click="close()" aria-label="{{ __('storefront.nav.close_menu') }}">
                    <x-store.icon name="close" class="size-5" />
                </button>
            </div>

            <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-5">
                <nav aria-label="{{ __('storefront.nav.aria_label') }}">
                    <div class="space-y-1">
                        <a href="{{ route('home') }}" class="flex items-center justify-between rounded-xl px-3 py-3 text-base font-bold hover:bg-smoke">
                            {{ __('storefront.nav.home') }}
                            <x-store.icon name="chevron-start" class="size-4 text-muted ltr:-scale-x-100" />
                        </a>
                        <a href="{{ route('products.index') }}" class="flex items-center justify-between rounded-xl px-3 py-3 text-base font-bold hover:bg-smoke">
                            {{ __('storefront.nav.shop') }}
                            <x-store.icon name="chevron-start" class="size-4 text-muted ltr:-scale-x-100" />
                        </a>
                        <a href="{{ route('products.index', ['on_sale' => 1]) }}" class="flex items-center justify-between rounded-xl px-3 py-3 text-base font-bold hover:bg-smoke">
                            {{ __('storefront.nav.offers') }}
                            <x-store.icon name="chevron-start" class="size-4 text-muted ltr:-scale-x-100" />
                        </a>
                    </div>

                    @if ($navCategories->isNotEmpty())
                        <div class="mt-6">
                            <p class="px-3 pb-2 text-[11px] font-bold text-muted">{{ __('storefront.nav.categories') }}</p>
                            <div class="divide-y divide-line/70 border-y border-line/70">
                                @foreach ($navCategories as $category)
                                    <div x-data="{ open: false }">
                                        <div class="flex items-center">
                                            <a href="{{ route('categories.show', $category) }}" class="flex-1 py-3.5 text-sm font-bold">{{ $category->name }}</a>
                                            @if ($category->children->isNotEmpty())
                                                <button
                                                    type="button"
                                                    class="icon-btn size-10 text-muted"
                                                    @click="open = ! open"
                                                    :aria-expanded="open.toString()"
                                                    aria-label="{{ $category->name }}"
                                                >
                                                    <x-store.icon name="chevron-down" class="size-4 transition" x-bind:class="open ? 'rotate-180' : ''" />
                                                </button>
                                            @endif
                                        </div>
                                        @if ($category->children->isNotEmpty())
                                            <ul x-show="open" x-cloak class="space-y-1 pb-3 ps-3">
                                                @foreach ($category->children as $child)
                                                    <li>
                                                        <a href="{{ route('categories.show', $child) }}" class="block rounded-lg px-3 py-2 text-sm text-muted hover:bg-smoke hover:text-ink">
                                                            {{ $child->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mt-6 space-y-1">
                        <a href="{{ route('about') }}" class="flex items-center justify-between rounded-xl px-3 py-3 text-base font-bold hover:bg-smoke">
                            {{ __('storefront.nav.about') }}
                            <x-store.icon name="chevron-start" class="size-4 text-muted ltr:-scale-x-100" />
                        </a>
                        <a href="{{ route('contact') }}" class="flex items-center justify-between rounded-xl px-3 py-3 text-base font-bold hover:bg-smoke">
                            {{ __('storefront.nav.contact') }}
                            <x-store.icon name="chevron-start" class="size-4 text-muted ltr:-scale-x-100" />
                        </a>
                        <a href="{{ route('faq') }}" class="flex items-center justify-between rounded-xl px-3 py-3 text-base font-bold hover:bg-smoke">
                            {{ __('storefront.nav.faq') }}
                            <x-store.icon name="chevron-start" class="size-4 text-muted ltr:-scale-x-100" />
                        </a>
                        @auth
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between rounded-xl px-3 py-3 text-base font-bold hover:bg-smoke">
                                {{ __('storefront.nav.admin') }}
                                <x-store.icon name="chevron-start" class="size-4 text-muted ltr:-scale-x-100" />
                            </a>
                        @endauth
                    </div>
                </nav>

                <div class="mt-6 flex items-center justify-between gap-3 rounded-xl border border-line px-3 py-2">
                    <span class="text-sm font-bold">{{ __('ui.language.label') }}</span>
                    <x-language-switcher />
                </div>

                <div class="mt-7 rounded-2xl bg-smoke p-5">
                    <p class="text-sm font-extrabold">{{ __('storefront.header.menu_title') }}</p>
                    <p class="mt-1 text-xs leading-6 text-muted">{{ __('storefront.header.menu_hint') }}</p>
                    @if ($whatsappUrl)
                        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn-primary mt-4 w-full">
                            <x-store.icon name="whatsapp" class="size-5 text-volt" />
                            {{ __('storefront.nav.whatsapp') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</header>

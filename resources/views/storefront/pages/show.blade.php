@php
    $otherPages = $footerPages->where('id', '!=', $page->id)->values();
    $pageSchema = [
        [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $page->title,
            'url' => $canonical,
            'description' => $page->meta_description ?: str($page->safe_content)->stripTags()->limit(155)->toString(),
            'dateModified' => $page->updated_at?->toIso8601String(),
        ],
    ];
@endphp

<x-layouts.storefront
    :title="$pageTitle"
    :description="$metaDescription"
    :canonical="$canonical"
    :schema="$pageSchema"
>
    <div class="border-b border-line bg-smoke/60">
        <div class="container-page py-6 sm:py-8">
            <x-store.breadcrumbs :items="[['label' => $page->title]]" />
        </div>
    </div>

    <section class="container-page section-space">
        <div class="grid gap-12 lg:grid-cols-[minmax(0,1.8fr)_minmax(0,1fr)] lg:gap-16">
            <article>
                <p class="eyebrow flex items-center gap-2.5">
                    <span class="h-px w-7 bg-volt-dark" aria-hidden="true"></span>
                    {{ __('storefront.page.policies_eyebrow') }}
                </p>
                <h1 class="mt-3 text-3xl font-extrabold leading-tight sm:text-4xl">{{ $page->title }}</h1>
                <p class="mt-3 text-[11px] text-muted">
                    {{ __('storefront.page.updated_at', ['date' => $page->updated_at?->isoFormat('D MMMM Y') ?? '']) }}
                </p>

                <div class="prose-ar mt-8 max-w-3xl">
                    {!! $page->safe_content !!}
                </div>

                <p class="mt-8 rounded-xl bg-smoke px-4 py-3 text-xs leading-6 text-muted">
                    {{ __('storefront.page.policies_note') }}
                </p>
            </article>

            <aside class="space-y-5 lg:sticky lg:top-24 lg:self-start">
                <div class="surface-card p-6">
                    <h2 class="text-sm font-extrabold">{{ __('storefront.footer.policies_title') }}</h2>
                    <ul class="mt-3 divide-y divide-line/70">
                        @foreach ($otherPages as $otherPage)
                            <li>
                                <a href="{{ route('pages.show', $otherPage) }}" class="flex items-center justify-between gap-3 py-2.5 text-xs font-semibold text-muted transition hover:text-ink">
                                    {{ $otherPage->title }}
                                    <x-store.icon name="chevron-start" class="size-3.5 shrink-0" />
                                </a>
                            </li>
                        @endforeach
                        <li>
                            <a href="{{ route('faq') }}" class="flex items-center justify-between gap-3 py-2.5 text-xs font-semibold text-muted transition hover:text-ink">
                                {{ __('storefront.nav.faq') }}
                                <x-store.icon name="chevron-start" class="size-3.5 shrink-0" />
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="surface-card p-6">
                    <h2 class="text-sm font-extrabold">{{ __('storefront.contact.title') }}</h2>
                    <p class="mt-2 text-xs leading-6 text-muted">{{ __('storefront.contact.intro') }}</p>
                    <div class="mt-4 flex flex-col gap-2">
                        @if ($whatsappUrl)
                            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn-primary btn-sm w-full">
                                <x-store.icon name="whatsapp" class="size-4 text-volt" />
                                {{ __('storefront.contact.whatsapp_cta') }}
                            </a>
                        @endif
                        <a href="{{ route('contact') }}" class="btn-outline btn-sm w-full">{{ __('storefront.nav.contact') }}</a>
                    </div>
                </div>
            </aside>
        </div>
    </section>
</x-layouts.storefront>

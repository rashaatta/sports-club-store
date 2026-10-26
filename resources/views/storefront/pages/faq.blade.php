@php
    $pageSchema = [];

    if ($faqs->isNotEmpty()) {
        $pageSchema[] = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faqs->map(fn ($faq) => [
                '@type' => 'Question',
                'name' => $faq->question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq->answer,
                ],
            ])->all(),
        ];
    }
@endphp

<x-layouts.storefront
    :title="$pageTitle"
    :description="$metaDescription"
    :canonical="$canonical"
    :schema="$pageSchema"
>
    <div class="border-b border-line bg-smoke/60">
        <div class="container-page py-6 sm:py-8">
            <x-store.breadcrumbs :items="[['label' => __('storefront.faq.title')]]" />
        </div>
    </div>

    <section class="container-page section-space">
        <div class="mx-auto max-w-3xl text-center">
            <p class="eyebrow inline-flex items-center gap-2.5">
                <span class="h-px w-7 bg-volt-dark" aria-hidden="true"></span>
                {{ __('storefront.faq.eyebrow') }}
                <span class="h-px w-7 bg-volt-dark" aria-hidden="true"></span>
            </p>
            <h1 class="mt-3 text-3xl font-extrabold leading-tight sm:text-4xl">{{ __('storefront.faq.title') }}</h1>
            <p class="mt-3 text-sm leading-7 text-muted">{{ __('storefront.faq.intro') }}</p>
        </div>

        @if ($faqs->isNotEmpty())
            <div class="mx-auto mt-10 max-w-3xl space-y-3">
                @foreach ($faqs as $faq)
                    <details class="faq-item surface-card group overflow-hidden" @if ($loop->first) open @endif>
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 text-start sm:px-6">
                            <span class="text-sm font-extrabold leading-6">{{ $faq->question }}</span>
                            <span class="faq-icon flex size-8 shrink-0 items-center justify-center rounded-full bg-smoke text-ink">
                                <x-store.icon name="plus" class="size-4" />
                            </span>
                        </summary>
                        <div class="border-t border-line px-5 py-4 text-sm leading-7 text-muted sm:px-6">
                            {{ $faq->answer }}
                        </div>
                    </details>
                @endforeach
            </div>
        @else
            <div class="mx-auto mt-10 max-w-3xl rounded-2xl border border-dashed border-line bg-smoke/40 px-6 py-14 text-center">
                <span class="mx-auto flex size-12 items-center justify-center rounded-full bg-white text-muted shadow-sm">
                    <x-store.icon name="faq" class="size-5" />
                </span>
                <h2 class="mt-4 text-lg font-extrabold">{{ __('storefront.faq.empty_title') }}</h2>
                <p class="mt-2 text-sm text-muted">{{ __('storefront.faq.empty_text') }}</p>
            </div>
        @endif

        <div class="mx-auto mt-10 max-w-3xl rounded-2xl bg-ink p-6 text-white sm:p-8">
            <div class="flex flex-col items-start gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-extrabold">{{ __('storefront.faq.more_questions_title') }}</h2>
                    <p class="mt-1.5 max-w-md text-xs leading-6 text-white/55">{{ __('storefront.faq.more_questions_text') }}</p>
                </div>
                <div class="flex shrink-0 flex-wrap gap-2">
                    @if ($whatsappUrl)
                        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn-volt btn-sm">
                            <x-store.icon name="whatsapp" class="size-4" />
                            {{ __('storefront.faq.whatsapp_cta') }}
                        </a>
                    @endif
                    <a href="{{ route('contact') }}" class="btn btn-sm border border-white/20 text-white hover:border-white hover:bg-white hover:text-ink">
                        {{ __('storefront.faq.contact_cta') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.storefront>

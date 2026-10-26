@props([
    'eyebrow' => null,
    'title',
    'subtitle' => null,
    'actionUrl' => null,
    'actionLabel' => null,
])

<div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
    <div class="max-w-2xl">
        @if (filled($eyebrow))
            <p class="eyebrow flex items-center gap-2.5">
                <span class="h-px w-7 bg-volt-dark" aria-hidden="true"></span>
                {{ $eyebrow }}
            </p>
        @endif
        <h2 class="section-title mt-3">{{ $title }}</h2>
        @if (filled($subtitle))
            <p class="mt-3 text-sm leading-7 text-muted">{{ $subtitle }}</p>
        @endif
    </div>

    @if ($actionUrl && $actionLabel)
        <a href="{{ $actionUrl }}" class="btn-outline btn-sm shrink-0 self-start sm:self-auto">
            {{ $actionLabel }}
            <x-store.icon name="arrow-left" class="size-4 ltr:-scale-x-100" />
        </a>
    @endif
</div>

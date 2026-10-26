@props(['label', 'value', 'hint' => null, 'icon' => 'chart', 'tone' => 'default', 'href' => null])

@php
    $tag = $href ? 'a' : 'div';
    $tones = [
        'default' => 'bg-white text-ink',
        'volt' => 'bg-volt text-ink',
        'ink' => 'bg-ink text-white',
        'danger' => 'bg-danger-soft text-danger',
    ];
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => 'group flex min-h-[132px] flex-col justify-between rounded-2xl border border-line/70 p-5 shadow-panel transition '.$tones[$tone]]) }}
>
    <div class="flex items-start justify-between gap-3">
        <p class="text-xs font-bold {{ $tone === 'ink' ? 'text-white/55' : 'text-muted' }}">{{ $label }}</p>
        <span class="flex size-9 items-center justify-center rounded-xl {{ $tone === 'ink' ? 'bg-white/10 text-white' : 'bg-smoke text-ink' }}">
            <x-store.icon :name="$icon" class="size-4" />
        </span>
    </div>
    <div class="mt-4">
        <p class="text-3xl font-black leading-none tracking-tight">{{ $value }}</p>
        @if ($hint)
            <p class="mt-1.5 text-[11px] leading-4 {{ $tone === 'ink' ? 'text-white/45' : 'text-muted' }}">{{ $hint }}</p>
        @endif
    </div>
</{{ $tag }}>

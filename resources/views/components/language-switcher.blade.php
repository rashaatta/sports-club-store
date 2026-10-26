@props(['variant' => 'light'])

@php
    $current = app()->getLocale();
    $target = $current === 'ar' ? 'en' : 'ar';
    $targetLocale = config('store.locales.'.$target, []);
    $currentLocale = config('store.locales.'.$current, []);
    $targetName = $targetLocale['name'] ?? strtoupper($target);
    $currentName = $currentLocale['name'] ?? strtoupper($current);
    $styles = $variant === 'dark'
        ? 'border-white/15 text-white/60 hover:border-white/40 hover:text-white'
        : 'border-line text-muted hover:border-ink hover:text-ink';
@endphp

<a
    href="{{ route('locale.switch', ['locale' => $target, 'redirect' => request()->getRequestUri()]) }}"
    {{ $attributes->merge([
        'class' => 'inline-flex min-h-9 items-center gap-1.5 rounded-full border px-3 text-[11px] font-bold transition '.$styles,
        'title' => __('ui.language.switch_to', ['language' => $targetName]),
        'aria-label' => __('ui.language.switch_to', ['language' => $targetName]),
    ]) }}
>
    <x-store.icon name="globe" class="size-3.5 shrink-0" />
    <span lang="{{ $target }}" dir="{{ $targetLocale['dir'] ?? 'ltr' }}">{{ $targetName }}</span>
    <span class="sr-only">{{ __('ui.language.current', ['language' => $currentName]) }}</span>
</a>

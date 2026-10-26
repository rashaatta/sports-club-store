@props(['name', 'label', 'checked' => false, 'help' => null])

@php
    $id = $attributes->get('id') ?? 'field-'.str_replace(['[', ']', '.'], '-', $name);
    $isChecked = (bool) old($name, $checked);
@endphp

<div {{ $attributes->only('class') }}>
    <input type="hidden" name="{{ $name }}" value="0">
    <label for="{{ $id }}" class="flex cursor-pointer items-start gap-3 rounded-xl border border-line bg-smoke/60 p-3.5 transition hover:border-ink/25">
        <input
            id="{{ $id }}"
            type="checkbox"
            name="{{ $name }}"
            value="1"
            @checked($isChecked)
            class="mt-0.5 size-4 rounded border-ink/25 text-ink focus:ring-0"
        >
        <span class="min-w-0">
            <span class="block text-sm font-bold">{{ $label }}</span>
            @if ($help)
                <span class="mt-0.5 block text-xs leading-5 text-muted">{{ $help }}</span>
            @endif
        </span>
    </label>
    @error($name)
        <p class="field-error">{{ $message }}</p>
    @enderror
</div>

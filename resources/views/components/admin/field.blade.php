@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'help' => null,
    'required' => false,
    'placeholder' => null,
])

@php
    $id = $attributes->get('id') ?? 'field-'.str_replace(['[', ']', '.'], '-', $name);
    $current = old($name, $value);
    $hasError = $errors->has($name);
    $describedBy = trim(($help ? $id.'-help ' : '').($hasError ? $id.'-error' : ''));
@endphp

<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $id }}" class="field-label">
            {{ $label }}
            @if ($required)<span class="text-danger" aria-hidden="true">*</span>@else<span class="text-[10px] font-normal text-muted">({{ __('admin.common.optional') }})</span>@endif
        </label>
    @endif

    @if ($type === 'textarea')
        <textarea
            id="{{ $id }}"
            name="{{ $name }}"
            rows="{{ $attributes->get('rows', 4) }}"
            placeholder="{{ $placeholder }}"
            dir="auto"
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @if ($hasError) aria-invalid="true" @endif
            @required($required)
            {{ $attributes->except(['class', 'id', 'rows'])->merge(['class' => 'field min-h-28 resize-y']) }}
        >{{ $current }}</textarea>
    @elseif ($type === 'password')
        <input
            id="{{ $id }}"
            type="password"
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @if ($hasError) aria-invalid="true" @endif
            @if ($required) required @endif
            {{ $attributes->except(['class', 'id', 'type'])->merge(['class' => 'field']) }}
        >
    @else
        <input
            id="{{ $id }}"
            type="{{ $type }}"
            name="{{ $name }}"
            value="{{ $current }}"
            placeholder="{{ $placeholder }}"
            dir="{{ in_array($type, ['email', 'url', 'password', 'number', 'date', 'datetime-local'], true) ? 'ltr' : 'auto' }}"
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @if ($hasError) aria-invalid="true" @endif
            @if ($required) required @endif
            {{ $attributes->except(['class', 'id', 'type'])->merge(['class' => 'field']) }}
        >
    @endif

    @if ($help)
        <p id="{{ $id }}-help" class="mt-1.5 text-xs leading-5 text-muted">{{ $help }}</p>
    @endif
    @error($name)
        <p id="{{ $id }}-error" class="field-error">{{ $message }}</p>
    @enderror
</div>

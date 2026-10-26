@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'help' => null,
    'required' => false,
])

@php
    $id = $attributes->get('id') ?? 'field-'.str_replace(['[', ']', '.'], '-', $name);
    $current = (string) old($name, $selected ?? '');
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
    <select
        id="{{ $id }}"
        name="{{ $name }}"
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        @if ($hasError) aria-invalid="true" @endif
        @required($required)
        {{ $attributes->except(['class', 'id'])->merge(['class' => 'field min-h-11']) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected($current === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @if ($help)
        <p id="{{ $id }}-help" class="mt-1.5 text-xs leading-5 text-muted">{{ $help }}</p>
    @endif
    @error($name)
        <p id="{{ $id }}-error" class="field-error">{{ $message }}</p>
    @enderror
</div>

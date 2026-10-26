@props(['title', 'description' => null, 'icon' => 'info', 'compact' => false])

<div @class(['flex flex-col items-center justify-center px-5 text-center', 'py-10' => ! $compact, 'py-7' => $compact])>
    <span class="flex size-11 items-center justify-center rounded-full bg-smoke text-muted">
        <x-store.icon :name="$icon" class="size-5" />
    </span>
    <p class="mt-3 text-sm font-bold">{{ $title }}</p>
    @if ($description)
        <p class="mt-1 max-w-sm text-xs leading-5 text-muted">{{ $description }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>

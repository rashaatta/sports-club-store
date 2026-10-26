@props(['name', 'class' => 'size-5'])

<svg
    {{ $attributes->merge(['class' => $class, 'aria-hidden' => 'true', 'focusable' => 'false', 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round']) }}
>
    @switch($name)
        @case('menu')
            <path d="M4 7h16M4 12h16M4 17h10" />
            @break
        @case('close')
        @case('x')
            <path d="m6 6 12 12M18 6 6 18" />
            @break
        @case('search')
            <circle cx="11" cy="11" r="7" />
            <path d="m20 20-3.5-3.5" />
            @break
        @case('whatsapp')
            <path fill="currentColor" stroke="none" d="M12.04 2C6.6 2 2.2 6.4 2.2 11.84c0 1.73.45 3.42 1.32 4.91L2 22l5.38-1.41a9.87 9.87 0 0 0 4.66 1.18h.01c5.43 0 9.84-4.4 9.84-9.84 0-2.63-1.02-5.1-2.88-6.95A9.78 9.78 0 0 0 12.04 2Zm0 18.02h-.01a8.2 8.2 0 0 1-4.17-1.14l-.3-.18-3.1.81.83-3.02-.2-.31a8.18 8.18 0 0 1-1.25-4.34c0-4.52 3.68-8.2 8.21-8.2 2.2 0 4.25.86 5.8 2.41a8.16 8.16 0 0 1 2.4 5.8c0 4.52-3.68 8.17-8.21 8.17Zm4.5-6.12c-.24-.12-1.46-.72-1.68-.8-.23-.09-.4-.13-.56.12-.17.24-.65.8-.8.97-.14.16-.29.18-.53.06-.24-.12-1.04-.38-1.98-1.22-.73-.65-1.22-1.45-1.37-1.7-.14-.24-.01-.37.11-.49.11-.11.24-.29.36-.43.12-.14.16-.24.24-.41.08-.16.04-.31-.02-.43-.06-.12-.55-1.34-.76-1.83-.2-.48-.4-.42-.56-.43h-.48c-.16 0-.43.06-.65.3-.22.25-.85.83-.85 2.02 0 1.2.87 2.35 1 2.51.12.16 1.71 2.62 4.15 3.67.58.25 1.03.4 1.39.52.58.18 1.11.16 1.53.1.47-.07 1.46-.6 1.67-1.18.2-.58.2-1.07.14-1.18-.06-.1-.22-.16-.46-.28Z" />
            @break
        @case('arrow-right')
            <path d="M5 12h14m0 0-6-6m6 6-6 6" />
            @break
        @case('arrow-left')
            <path d="M19 12H5m0 0 6-6m-6 6 6 6" />
            @break
        @case('arrow-up')
            <path d="M12 19V5m0 0-6 6m6-6 6 6" />
            @break
        @case('chevron-down')
            <path d="m6 9 6 6 6-6" />
            @break
        @case('chevron-start')
            <path d="m15 6-6 6 6 6" />
            @break
        @case('chevron-end')
            <path d="m9 6 6 6-6 6" />
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14" />
            @break
        @case('minus')
            <path d="M5 12h14" />
            @break
        @case('check')
            <path d="m5 12 4.5 4.5L19 7" />
            @break
        @case('filter')
            <path d="M4 6h16M7 12h10M10 18h4" />
            @break
        @case('grid')
            <rect x="4" y="4" width="6" height="6" rx="1" />
            <rect x="14" y="4" width="6" height="6" rx="1" />
            <rect x="4" y="14" width="6" height="6" rx="1" />
            <rect x="14" y="14" width="6" height="6" rx="1" />
            @break
        @case('bag')
            <path d="M6 8h12l-1 12H7L6 8Z" />
            <path d="M9 8V6a3 3 0 0 1 6 0v2" />
            @break
        @case('truck')
            <path d="M3 6h11v10H3zM14 9h4l3 3v4h-7" />
            <circle cx="7" cy="18" r="1.6" />
            <circle cx="18" cy="18" r="1.6" />
            @break
        @case('shield')
            <path d="M12 3 5 6v5c0 4.4 3 8.1 7 10 4-1.9 7-5.6 7-10V6l-7-3Z" />
            <path d="m9 12 2 2 4-4" />
            @break
        @case('ruler')
            <path d="M4 17 17 4l3 3L7 20l-3-3Z" />
            <path d="m7 14 2 2m2-5 2 2m2-5 2 2" />
            @break
        @case('sparkles')
            <path d="m12 3 1.6 4.4L18 9l-4.4 1.6L12 15l-1.6-4.4L6 9l4.4-1.6L12 3Z" />
            <path d="m18 14 .8 2.2L21 17l-2.2.8L18 20l-.8-2.2L15 17l2.2-.8L18 14Z" />
            @break
        @case('share')
            <circle cx="18" cy="5" r="2.5" />
            <circle cx="6" cy="12" r="2.5" />
            <circle cx="18" cy="19" r="2.5" />
            <path d="m8.2 10.8 7.6-4.3M8.2 13.2l7.6 4.3" />
            @break
        @case('link')
            <path d="M10 13a5 5 0 0 0 7.1 0l2-2a5 5 0 0 0-7.1-7.1l-1.1 1.1" />
            <path d="M14 11a5 5 0 0 0-7.1 0l-2 2A5 5 0 0 0 12 20.1l1.1-1.1" />
            @break
        @case('instagram')
            <rect x="3" y="3" width="18" height="18" rx="5" />
            <circle cx="12" cy="12" r="4" />
            <circle cx="17.2" cy="6.8" r=".8" fill="currentColor" stroke="none" />
            @break
        @case('tiktok')
            <path fill="currentColor" stroke="none" d="M15.6 3c.3 2 1.6 3.4 3.6 3.7v2.5a6.6 6.6 0 0 1-3.6-1.1v6.2a5.9 5.9 0 1 1-5.9-5.9c.3 0 .6 0 .9.1v2.7a3.2 3.2 0 1 0 2.3 3.1V3h2.7Z" />
            @break
        @case('x-social')
        @case('twitter')
            <path fill="currentColor" stroke="none" d="M17.7 3H21l-7.2 8.2L22.2 21h-6.6l-5.2-6.2L4.5 21H1.2l7.7-8.8L1.8 3h6.8l4.7 5.6L17.7 3Zm-1.2 16h1.8L7.6 4.9H5.7L16.5 19Z" />
            @break
        @case('mail')
            <rect x="3" y="5" width="18" height="14" rx="2" />
            <path d="m3 7 9 6 9-6" />
            @break
        @case('phone')
            <path d="M5 4h3l2 5-2 1.5a12 12 0 0 0 5.5 5.5L15 14l5 2v3a2 2 0 0 1-2 2A15 15 0 0 1 3 6a2 2 0 0 1 2-2Z" />
            @break
        @case('pin')
            <path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11Z" />
            <circle cx="12" cy="10" r="2.5" />
            @break
        @case('clock')
            <circle cx="12" cy="12" r="9" />
            <path d="M12 7v5l3 2" />
            @break
        @case('alert')
            <path d="M12 3 2.5 20h19L12 3Z" />
            <path d="M12 9v5m0 3v.01" />
            @break
        @case('info')
            <circle cx="12" cy="12" r="9" />
            <path d="M12 11v5m0-8v.01" />
            @break
        @case('cube')
            <path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z" />
            <path d="m4 7.5 8 4.5 8-4.5M12 12v9" />
            @break
        @case('zoom')
            <circle cx="11" cy="11" r="7" />
            <path d="m20 20-3.5-3.5M11 8v6m-3-3h6" />
            @break
        @case('image')
            <rect x="3" y="4" width="18" height="16" rx="2" />
            <circle cx="9" cy="10" r="2" />
            <path d="m3 17 5-5 4 4 3-3 6 6" />
            @break
        @case('trash')
            <path d="M4 7h16M9 7V5h6v2m-8 0 1 13h8l1-13M10 11v5m4-5v5" />
            @break
        @case('upload')
            <path d="M12 16V4m0 0-4 4m4-4 4 4M5 15v4h14v-4" />
            @break
        @case('edit')
            <path d="M4 20h4l11-11-4-4L4 16v4Zm9-13 4 4" />
            @break
        @case('eye')
            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z" />
            <circle cx="12" cy="12" r="2.5" />
            @break
        @case('external')
            <path d="M14 4h6v6M20 4l-9 9M19 14v5H5V5h5" />
            @break
        @case('logout')
            <path d="M10 5H5v14h5M15 8l4 4-4 4m4-4H9" />
            @break
        @case('dashboard')
            <rect x="3" y="3" width="8" height="8" rx="2" />
            <rect x="13" y="3" width="8" height="5" rx="2" />
            <rect x="13" y="10" width="8" height="11" rx="2" />
            <rect x="3" y="13" width="8" height="8" rx="2" />
            @break
        @case('box')
            <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z" />
            <path d="M4 7.5 12 12l8-4.5M12 12v9" />
            @break
        @case('tags')
            <path d="M3 5h8l10 10-6 6L5 11V5Z" />
            <circle cx="8" cy="9" r="1" fill="currentColor" stroke="none" />
            @break
        @case('users')
            <circle cx="9" cy="8" r="3" />
            <path d="M3 20a6 6 0 0 1 12 0M16 5.5a3 3 0 0 1 0 5.8M17 14a5 5 0 0 1 4 5" />
            @break
        @case('settings')
            <circle cx="12" cy="12" r="3" />
            <path d="M19 12a7 7 0 0 0-.1-1.2l2-1.5-2-3.4-2.3 1a7 7 0 0 0-2-1.1L14.2 3h-4l-.4 2.7a7 7 0 0 0-2 1.1l-2.3-1-2 3.4 2 1.5a7 7 0 0 0 0 2.5l-2 1.5 2 3.4 2.3-1a7 7 0 0 0 2 1.1l.4 2.8h4l.4-2.8a7 7 0 0 0 2-1.1l2.3 1 2-3.4-2-1.5c.1-.4.1-.8.1-1.2Z" />
            @break
        @case('page')
            <path d="M6 3h8l4 4v14H6V3Z" />
            <path d="M14 3v4h4M9 12h6m-6 4h6" />
            @break
        @case('faq')
            <circle cx="12" cy="12" r="9" />
            <path d="M9.5 9a2.5 2.5 0 1 1 4 2c-1 .7-1.5 1.2-1.5 2.5M12 17v.01" />
            @break
        @case('message')
            <path d="M4 5h16v11H9l-5 4V5Z" />
            <path d="M8 9h8m-8 3h5" />
            @break
        @case('chart')
            <path d="M4 20V10m6 10V4m6 16v-7m4 7H2" />
            @break
        @case('inventory')
            <path d="M4 8 12 4l8 4-8 4-8-4Z" />
            <path d="M4 8v8l8 4 8-4V8M12 12v8" />
            <path d="m8 18 .01.01" />
            @break
        @case('sliders')
            <path d="M4 7h10M18 7h2M4 17h2M10 17h10" />
            <circle cx="16" cy="7" r="2" />
            <circle cx="8" cy="17" r="2" />
            @break
        @case('home')
            <path d="m3 11 9-8 9 8v9h-6v-6H9v6H3v-9Z" />
            @break
        @case('heart')
            <path d="M12 20s-8-4.6-8-10a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 5.4-8 10-8 10Z" />
            @break
        @case('copy')
            <rect x="8" y="8" width="12" height="12" rx="2" />
            <path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2" />
            @break
        @case('sort')
            <path d="M8 5v14m0 0-3-3m3 3 3-3M16 19V5m0 0-3 3m3-3 3 3" />
            @break
        @case('calendar')
            <rect x="3" y="5" width="18" height="16" rx="2" />
            <path d="M8 3v4m8-4v4M3 10h18" />
            @break
        @case('key')
            <circle cx="8" cy="15" r="4" />
            <path d="m11 12 9-9m-3 3 3 3" />
            @break
        @default
            <circle cx="12" cy="12" r="9" />
            <path d="M12 8v4m0 4v.01" />
    @endswitch
</svg>

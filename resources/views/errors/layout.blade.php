@php
    $locale = app()->getLocale();
    $dir = config('store.locales.'.$locale.'.dir', 'rtl');
    $storeName = config('app.name', 'SQUAD');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title') | {{ $storeName }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            background: #0c0d0b;
            color: #f7f7f2;
            font-family: "Segoe UI", Tahoma, "Noto Kufi Arabic", "Cairo", Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at 15% 0%, rgba(216, 255, 62, 0.12), transparent 42%),
                radial-gradient(circle at 85% 100%, rgba(216, 255, 62, 0.06), transparent 40%);
            pointer-events: none;
        }
        .shell { position: relative; width: 100%; max-width: 580px; }
        .card {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            background: rgba(22, 23, 20, 0.9);
            padding: clamp(28px, 6vw, 48px);
            box-shadow: 0 30px 90px rgba(0, 0, 0, 0.5);
        }
        .card::before {
            content: "";
            position: absolute;
            inset-block-start: 0;
            inset-inline: 0;
            height: 4px;
            background: linear-gradient(90deg, #d8ff3e, rgba(216, 255, 62, 0.08));
        }
        .brand { display: flex; align-items: center; gap: 10px; font-weight: 800; letter-spacing: 0.06em; }
        .mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #d8ff3e;
            color: #11120e;
            font-weight: 900;
            font-size: 18px;
        }
        .status {
            margin: 30px 0 6px;
            font-size: clamp(64px, 16vw, 108px);
            font-weight: 800;
            line-height: 0.9;
            letter-spacing: -0.05em;
            color: #d8ff3e;
        }
        h1 { margin: 0 0 12px; font-size: clamp(24px, 5vw, 34px); line-height: 1.3; }
        .lead { margin: 0; color: rgba(247, 247, 242, 0.66); font-size: 15px; line-height: 1.9; }
        .actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 28px; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 22px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 800;
            text-decoration: none;
            transition: transform 160ms ease, background-color 160ms ease, border-color 160ms ease;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn.primary { background: #d8ff3e; color: #11120e; }
        .btn.primary:hover { background: #e4ff6f; }
        .btn.ghost { border: 1px solid rgba(255, 255, 255, 0.2); color: #f7f7f2; }
        .btn.ghost:hover { border-color: rgba(255, 255, 255, 0.45); background: rgba(255, 255, 255, 0.05); }
        .btn:focus-visible, a:focus-visible { outline: 3px solid #d8ff3e; outline-offset: 3px; }
        .foot { margin: 18px 0 0; text-align: center; color: rgba(247, 247, 242, 0.38); font-size: 12px; line-height: 1.9; }
        @media (prefers-reduced-motion: reduce) {
            .btn { transition: none; }
            .btn:hover { transform: none; }
        }
    </style>
</head>
<body>
    <main class="shell">
        <div class="card">
            <div class="brand">
                <span class="mark" aria-hidden="true">S</span>
                <span>{{ $storeName }}</span>
            </div>
            <p class="status" aria-hidden="true">@yield('code')</p>
            <h1>@yield('title')</h1>
            <p class="lead">@yield('message')</p>
            <div class="actions">
                <a class="btn primary" href="{{ url('/') }}">{{ __('errors.actions.home') }}</a>
                <a class="btn ghost" href="{{ url('/products') }}">{{ __('errors.actions.products') }}</a>
                @yield('extra_actions')
            </div>
        </div>
        <p class="foot">{{ __('errors.footer') }}</p>
    </main>
</body>
</html>

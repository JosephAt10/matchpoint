@php
    $user = $page['user'];
    $toneClasses = [
        'amber' => 'bg-[#fff4e8] text-[#f08b20]',
        'emerald' => 'bg-[#eaf9ef] text-[#16a34a]',
        'indigo' => 'bg-[#eef0ff] text-[#4f46e5]',
    ];
    $iconSvg = function (string $name): \Illuminate\Support\HtmlString {
        $icons = [
            'bell' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M15 17H5l1.2-1.6A4 4 0 007 13V10a5 5 0 1110 0v3c0 .9.3 1.8.8 2.6L19 17h-4"/><path d="M10 18a2 2 0 004 0"/></svg>',
            'user' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M16 8a4 4 0 11-8 0 4 4 0 018 0z"/><path d="M5 20a7 7 0 0114 0"/></svg>',
            'chevron-down' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M7 10l5 5 5-5"/></svg>',
            'clock' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="8"/><path d="M12 8v4.5l3 2"/></svg>',
            'check-circle' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="8"/><path d="M9 12.5l2 2 4-4"/></svg>',
        ];
        return new \Illuminate\Support\HtmlString($icons[$name] ?? $icons['bell']);
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Notifications') }} - MatchPoint</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { colors: { ink: '#131a33', copy: '#60708f', line: '#edf1f7', indigoSoft: '#4f46e5', page: '#f7f8fc' }, boxShadow: { panel: '0 18px 42px rgba(34, 43, 84, 0.08)' }, fontFamily: { heading: ['Outfit', 'sans-serif'], body: ['DM Sans', 'sans-serif'] } } } }
    </script>
    <style>*{font-family:'DM Sans',sans-serif}h1,h2,h3,h4,h5,h6,.font-heading{font-family:'Outfit',sans-serif}.surface-card{border:1px solid #edf1f7;box-shadow:0 18px 42px rgba(34,43,84,.08)}.sidebar-shell{background:radial-gradient(circle at top left,rgba(99,102,241,.24),transparent 30%),linear-gradient(180deg,#0f172a 0%,#111827 52%,#0b1120 100%)}.sidebar-nav-link{color:rgba(226,232,240,.88)}.sidebar-nav-link:hover{background:rgba(255,255,255,.06);color:#fff}.sidebar-nav-link.is-active{background:linear-gradient(90deg,rgba(79,70,229,.32),rgba(99,102,241,.18));color:#fff;box-shadow:inset 0 0 0 1px rgba(129,140,248,.16)}</style>
</head>
<body class="min-h-screen bg-page text-ink">
    <header class="bg-white/96 shadow-[0_14px_34px_rgba(34,43,84,0.05)] backdrop-blur">
        <div class="flex items-center justify-between px-8 py-5 lg:px-12">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('landing/matchpoint-logo.png') }}" alt="{{ __('MatchPoint logo') }}" class="h-11 w-11 object-contain">
                <span class="font-heading text-[24px] font-bold tracking-[0.14em] text-[#1b2565]">{{ __('MATCHPOINT') }}</span>
            </a>
            @include('partials.locale-switcher')
        </div>
    </header>

    <div class="grid min-h-[calc(100vh-85px)] lg:grid-cols-[292px_minmax(0,1fr)]">
        <aside class="sidebar-shell border-r border-[#dde5f3] shadow-[1px_0_0_rgba(255,255,255,0.04)] px-6 py-8">
            <div class="mb-8 px-3">
                <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-indigo-200/70">{{ __('Player Space') }}</p>
                <h2 class="mt-3 font-heading text-[24px] font-bold text-white">{{ __('MatchPoint') }}</h2>
                <p class="mt-2 text-[14px] leading-6 text-slate-300">{{ __('Track your bookings, payments, and public match activity in one place.') }}</p>
            </div>
            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition"><span class="h-6 w-6">{!! $iconSvg('user') !!}</span><span>{{ __('Dashboard') }}</span></a>
                <a href="{{ route('fields.index') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition"><span class="h-6 w-6">{!! $iconSvg('check-circle') !!}</span><span>{{ __('Browse Fields') }}</span></a>
                <a href="{{ route('bookings.index') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition"><span class="h-6 w-6">{!! $iconSvg('clock') !!}</span><span>{{ __('My Bookings') }}</span></a>
                <a href="{{ route('matches.index') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition"><span class="h-6 w-6">{!! $iconSvg('check-circle') !!}</span><span>{{ __('Public Matches') }}</span></a>
                <a href="{{ route('matches.my') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition"><span class="h-6 w-6">{!! $iconSvg('check-circle') !!}</span><span>{{ __('My Matches') }}</span></a>
                <a href="{{ route('favorites.index') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition"><span class="h-6 w-6">{!! $iconSvg('check-circle') !!}</span><span>{{ __('Favorites') }}</span></a>
                <a href="{{ route('notifications.index') }}" class="sidebar-nav-link is-active flex items-center gap-4 rounded-2xl px-5 py-4 font-medium"><span class="h-6 w-6">{!! $iconSvg('bell') !!}</span><span>{{ __('Notifications') }}</span></a>
                <a href="{{ route('profile.edit') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition"><span class="h-6 w-6">{!! $iconSvg('user') !!}</span><span>{{ __('Profile') }}</span></a>
                <form action="{{ route('logout') }}" method="POST" class="pt-3">@csrf<button type="submit" class="sidebar-nav-link flex w-full items-center gap-4 rounded-2xl px-5 py-4 text-left font-medium transition"><span class="h-6 w-6">{!! $iconSvg('chevron-down') !!}</span><span>{{ __('Logout') }}</span></button></form>
            </nav>
        </aside>

        <main class="bg-[linear-gradient(180deg,#fbfcff_0%,#f7f8fc_100%)] px-8 py-8 lg:px-12">
        <section class="surface-card rounded-[28px] bg-white p-6">
            <p class="text-[14px] font-semibold uppercase tracking-[0.24em] text-[#4f46e5]">{{ __('Notifications') }}</p>
            <h1 class="mt-3 font-heading text-[42px] font-bold text-ink">{{ __('Recent updates for your account') }}</h1>
            <p class="mt-3 max-w-[720px] text-[18px] leading-8 text-copy">{{ __('Payment reviews, booking reminders, and account updates are shown here from your database notifications.') }}</p>
        </section>

        <section class="mt-6 space-y-4">
            @forelse ($page['notifications'] as $notification)
                <article class="surface-card flex items-start gap-5 rounded-[28px] bg-white p-6">
                    <span class="mt-1 flex h-14 w-14 shrink-0 items-center justify-center rounded-full {{ $toneClasses[$notification['tone']] ?? $toneClasses['indigo'] }}">
                        {!! $iconSvg($notification['tone'] === 'emerald' ? 'check-circle' : ($notification['tone'] === 'amber' ? 'clock' : 'bell')) !!}
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="font-heading text-[24px] font-bold text-ink">{{ __($notification['type']) }}</h2>
                            <span class="rounded-full bg-[#f4f6fb] px-3 py-1 text-[13px] font-semibold text-[#60708f]">{{ __($notification['status']) }}</span>
                        </div>
                        <p class="mt-3 text-[17px] leading-8 text-[#43506c]">{{ $notification['message'] }}</p>
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="text-[15px] font-semibold text-[#51607d]">{{ $notification['time_label'] }}</p>
                        <p class="mt-2 text-[14px] text-copy">{{ $notification['created_label'] }}</p>
                    </div>
                </article>
            @empty
                <div class="rounded-[28px] border border-dashed border-[#e7ecf4] bg-white px-6 py-10 text-center text-[18px] text-copy shadow-panel">{{ __('No notifications yet.') }}</div>
            @endforelse
        </section>
        </main>
    </div>
</body>
</html>

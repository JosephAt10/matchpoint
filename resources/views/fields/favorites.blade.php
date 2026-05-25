@php($favoritesUrl = route('favorites.index'))
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('My Favorites') }} - MatchPoint</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { colors: { ink: '#252b50', indigoSoft: '#6b63f6', indigoDeep: '#5446ea', copy: '#6d7296' }, boxShadow: { card: '0 18px 42px rgba(89, 83, 178, 0.10)' }, fontFamily: { heading: ['Outfit', 'sans-serif'], body: ['DM Sans', 'sans-serif'] } } } }
    </script>
    <style>* { font-family: 'DM Sans', sans-serif; } h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Outfit', sans-serif; }.sidebar-shell{background:radial-gradient(circle at top left,rgba(99,102,241,.24),transparent 30%),linear-gradient(180deg,#0f172a 0%,#111827 52%,#0b1120 100%)}.sidebar-nav-link{color:rgba(226,232,240,.88)}.sidebar-nav-link:hover{background:rgba(255,255,255,.06);color:#fff}.sidebar-nav-link.is-active{background:linear-gradient(90deg,rgba(79,70,229,.32),rgba(99,102,241,.18));color:#fff;box-shadow:inset 0 0 0 1px rgba(129,140,248,.16)}</style>
</head>
<body class="min-h-screen bg-[linear-gradient(180deg,#ffffff_0%,#f7f4ff_42%,#ffffff_100%)] text-ink">
    <header class="bg-white/96 shadow-[0_14px_34px_rgba(34,43,84,0.05)] backdrop-blur">
        <div class="mx-auto flex w-full max-w-[1820px] items-center justify-between px-8 py-4 lg:px-14 2xl:px-20">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('landing/matchpoint-logo.png') }}" alt="{{ __('MatchPoint logo') }}" class="h-11 w-11 object-contain">
                <span class="font-heading text-[28px] font-bold tracking-[0.16em] text-[#2f3273]">{{ __('MATCHPOINT') }}</span>
            </a>
            <div class="hidden md:block">@include('partials.locale-switcher')</div>
        </div>
        <div class="px-8 pb-4 md:hidden lg:px-14 2xl:px-20">@include('partials.locale-switcher')</div>
    </header>

    <div class="grid min-h-[calc(100vh-80px)] lg:grid-cols-[292px_minmax(0,1fr)]">
        <aside class="sidebar-shell border-r border-[#dde5f3] shadow-[1px_0_0_rgba(255,255,255,0.04)] px-6 py-8">
            <div class="mb-8 px-3">
                <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-indigo-200/70">{{ __('Player Space') }}</p>
                <h2 class="mt-3 font-heading text-[24px] font-bold text-white">{{ __('MatchPoint') }}</h2>
                <p class="mt-2 text-[14px] leading-6 text-slate-300">{{ __('Track your bookings, payments, and public match activity in one place.') }}</p>
            </div>
            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition"><span>{{ __('Dashboard') }}</span></a>
                <a href="{{ route('fields.index') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition"><span>{{ __('Browse Fields') }}</span></a>
                <a href="{{ route('bookings.index') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition"><span>{{ __('My Bookings') }}</span></a>
                <a href="{{ route('matches.create') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition"><span>{{ __('Public Matches') }}</span></a>
                <a href="{{ route('matches.my') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition"><span>{{ __('My Matches') }}</span></a>
                <a href="{{ $favoritesUrl }}" class="sidebar-nav-link is-active flex items-center gap-4 rounded-2xl px-5 py-4 font-medium"><span>{{ __('Favorites') }}</span></a>
                <a href="{{ route('notifications.index') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition"><span>{{ __('Notifications') }}</span></a>
                <a href="{{ route('profile.edit') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition"><span>{{ __('Profile') }}</span></a>
                <form action="{{ route('logout') }}" method="POST" class="pt-3">@csrf<button type="submit" class="sidebar-nav-link flex w-full items-center gap-4 rounded-2xl px-5 py-4 text-left font-medium transition"><span>{{ __('Logout') }}</span></button></form>
            </nav>
        </aside>

        <main class="mx-auto w-full max-w-[1820px] px-8 py-10 lg:px-14 2xl:px-20">
        @if (session('status'))<div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>@endif
        <section class="rounded-[2.2rem] border border-[#f0ecff] bg-[linear-gradient(180deg,#ffffff_0%,#f7f4ff_100%)] px-8 py-10 shadow-card">
            <p class="text-[15px] font-semibold uppercase tracking-[0.24em] text-[#ff9a1f]">{{ __('Favorites') }}</p>
            <h1 class="mt-3 font-heading text-[44px] font-bold leading-tight text-ink">{{ __('My Favorites') }}</h1>
            <p class="mt-3 max-w-[620px] text-[18px] leading-8 text-copy">{{ __('Keep the venues you like in one place, then come back anytime to review them before booking.') }}</p>
        </section>
        <section class="mt-10"><div class="grid gap-5 lg:grid-cols-3">@include('fields.partials.cards', ['fields' => $fields, 'favoriteIds' => $favoriteIds])</div></section>
        </main>
    </div>
</body>
</html>

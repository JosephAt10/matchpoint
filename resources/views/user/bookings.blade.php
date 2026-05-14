@php
    $user = $page['user'];
    $statusClasses = [
        'amber' => 'bg-[#fff4e8] text-[#f08b20]',
        'emerald' => 'bg-[#eaf9ef] text-[#16a34a]',
        'rose' => 'bg-[#fff1f2] text-[#e11d48]',
        'slate' => 'bg-[#edf2f8] text-[#64748b]',
        'indigo' => 'bg-[#eef0ff] text-[#4f46e5]',
    ];
    $iconSvg = function (string $name): \Illuminate\Support\HtmlString {
        $icons = [
            'calendar' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/></svg>',
            'bell' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M15 17H5l1.2-1.6A4 4 0 007 13V10a5 5 0 1110 0v3c0 .9.3 1.8.8 2.6L19 17h-4"/><path d="M10 18a2 2 0 004 0"/></svg>',
            'user' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M16 8a4 4 0 11-8 0 4 4 0 018 0z"/><path d="M5 20a7 7 0 0114 0"/></svg>',
            'chevron-down' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M7 10l5 5 5-5"/></svg>',
            'location' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 21s-6-4.35-6-10a6 6 0 1112 0c0 5.65-6 10-6 10z"/><circle cx="12" cy="11" r="2.5"/></svg>',
        ];
        return new \Illuminate\Support\HtmlString($icons[$name] ?? $icons['calendar']);
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('My Bookings') }} - MatchPoint</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { colors: { ink: '#131a33', copy: '#60708f', line: '#e9ebf5', indigoSoft: '#4f46e5', page: '#f7f8fc' }, boxShadow: { panel: '0 20px 44px rgba(34, 43, 84, 0.08)' }, fontFamily: { heading: ['Outfit', 'sans-serif'], body: ['DM Sans', 'sans-serif'] } } } }
    </script>
    <style>*{font-family:'DM Sans',sans-serif}h1,h2,h3,h4,h5,h6,.font-heading{font-family:'Outfit',sans-serif}</style>
</head>
<body class="min-h-screen bg-page text-ink">
    <header class="border-b border-line bg-white">
        <div class="flex w-full items-center justify-between px-8 py-5 lg:px-12 xl:px-16 2xl:px-20">
            <a href="{{ route('home') }}" class="font-heading text-[24px] font-bold tracking-[0.14em] text-[#1b2565]">{{ __('MATCHPOINT') }}</a>
            <div class="flex items-center gap-6 lg:gap-10">
                <nav class="flex items-center gap-3 text-[16px] font-medium text-[#52617f]">
                    <a href="{{ route('fields.index') }}" class="px-4 py-3 transition hover:text-indigoSoft">{{ __('Browse Fields') }}</a>
                    <a href="{{ route('dashboard') }}" class="px-4 py-3 transition hover:text-indigoSoft">{{ __('Dashboard') }}</a>
                    <a href="{{ route('favorites.index') }}" class="px-4 py-3 transition hover:text-indigoSoft">{{ __('Favorites') }}</a>
                    <a href="{{ route('profile.edit') }}" class="px-4 py-3 transition hover:text-indigoSoft">{{ __('Profile') }}</a>
                    <form action="{{ route('logout') }}" method="POST" class="inline-flex">@csrf<button type="submit" class="rounded-full bg-[#5f55dc] px-5 py-3 font-semibold text-white transition hover:bg-[#4d43cb]">{{ __('Logout') }}</button></form>
                </nav>
                <div class="shrink-0">@include('partials.locale-switcher')</div>
            </div>
        </div>
    </header>

    <main class="px-8 py-8 lg:px-12">
        <section class="rounded-[28px] border border-line bg-white p-6 shadow-panel">
            <p class="text-[14px] font-semibold uppercase tracking-[0.24em] text-[#4f46e5]">{{ __('Bookings') }}</p>
            <h1 class="mt-3 font-heading text-[42px] font-bold text-ink">{{ __('All your bookings in one place') }}</h1>
            <p class="mt-3 max-w-[760px] text-[18px] leading-8 text-copy">{{ __('Track upcoming sessions, review booking status, and open each booking for full payment and schedule details.') }}</p>

            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <article class="rounded-[22px] bg-[#fff4e8] p-5"><p class="text-[15px] text-copy">{{ __('Pending') }}</p><p class="mt-2 text-[34px] font-bold text-[#f08b20]">{{ $page['pending_count'] }}</p></article>
                <article class="rounded-[22px] bg-[#eaf9ef] p-5"><p class="text-[15px] text-copy">{{ __('Confirmed') }}</p><p class="mt-2 text-[34px] font-bold text-[#16a34a]">{{ $page['confirmed_count'] }}</p></article>
                <article class="rounded-[22px] bg-[#edf2f8] p-5"><p class="text-[15px] text-copy">{{ __('Completed') }}</p><p class="mt-2 text-[34px] font-bold text-[#64748b]">{{ $page['completed_count'] }}</p></article>
            </div>
        </section>

        <section class="mt-6 space-y-4">
            @forelse ($page['bookings'] as $booking)
                <article class="grid gap-5 rounded-[28px] border border-line bg-white p-6 shadow-panel lg:grid-cols-[160px_minmax(0,1fr)_240px] lg:items-center">
                    <div class="overflow-hidden rounded-[20px] bg-[#eef1f8]">
                        <img src="{{ $booking['image_url'] ? url($booking['image_url']) : asset('landing/football-stadium.jpg') }}" alt="{{ $booking['field_name'] }}" class="h-[120px] w-full object-cover">
                    </div>
                    <div>
                        <h2 class="font-heading text-[28px] font-bold text-ink">{{ $booking['field_name'] }}</h2>
                        <p class="mt-2 flex items-center gap-2 text-[17px] text-copy"><span class="h-5 w-5">{!! $iconSvg('location') !!}</span>{{ $booking['location'] }}</p>
                        <div class="mt-4 flex flex-wrap items-center gap-4 text-[17px] text-[#5c6988]">
                            <span class="inline-flex items-center gap-2"><span class="h-5 w-5">{!! $iconSvg('calendar') !!}</span>{{ $booking['date_label'] }}</span>
                            <span>&bull;</span>
                            <span>{{ $booking['time_label'] }}</span>
                            <span>&bull;</span>
                            <span>{{ $booking['amount'] }}</span>
                        </div>
                        @if ($booking['payment_deadline'])
                            <p class="mt-4 text-[15px] text-copy">{{ __('Payment deadline: :time', ['time' => $booking['payment_deadline']]) }}</p>
                        @endif
                    </div>
                    <div class="flex flex-col items-start gap-4 lg:items-end">
                        <span class="rounded-full px-4 py-2 text-[15px] font-semibold {{ $statusClasses[$booking['status_tone']] ?? $statusClasses['indigo'] }}">{{ __($booking['status_label']) }}</span>
                        @if ($booking['payment_status'])
                            <p class="text-[15px] text-[#5c6988]">{{ __('Payment: :status', ['status' => __($booking['payment_status'])]) }}</p>
                        @endif
                        <a href="{{ $booking['show_url'] }}" class="rounded-2xl border border-[#a89dff] px-4 py-2 text-[15px] font-semibold text-indigoSoft transition hover:bg-[#f7f5ff]">{{ __('View Booking') }}</a>
                    </div>
                </article>
            @empty
                <div class="rounded-[28px] border border-dashed border-line bg-white px-6 py-10 text-center text-[18px] text-copy shadow-panel">{{ __('No bookings found yet.') }}</div>
            @endforelse
        </section>
    </main>
</body>
</html>

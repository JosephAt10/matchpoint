@php
    $user = $page['user'];
    $statusClasses = [
        'amber' => 'bg-[#fff4e8] text-[#f08b20]',
        'emerald' => 'bg-[#eaf9ef] text-[#16a34a]',
        'rose' => 'bg-[#fff1f2] text-[#e11d48]',
    ];
    $iconSvg = function (string $name): \Illuminate\Support\HtmlString {
        $icons = [
            'wallet' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 7.5A2.5 2.5 0 016.5 5h10A2.5 2.5 0 0119 7.5V9H6.5A2.5 2.5 0 004 11.5v5A2.5 2.5 0 006.5 19H18a2 2 0 002-2v-5.5A2.5 2.5 0 0017.5 9H6.5A2.5 2.5 0 014 6.5z"/><path d="M16 14h4"/></svg>',
            'home' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M3 11.5L12 4l9 7.5"/><path d="M5 10.5V20h14v-9.5"/></svg>',
            'calendar' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/></svg>',
            'heart' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12.1 20.3l-.1.1-.11-.1C7.14 16.24 4 13.39 4 9.84 4 7.03 6.24 5 9.05 5c1.6 0 3.13.75 4.05 1.94A5.17 5.17 0 0117.15 5C19.96 5 22.2 7.03 22.2 9.84c0 3.55-3.14 6.4-8.99 10.46z"/></svg>',
            'bell' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M15 17H5l1.2-1.6A4 4 0 007 13V10a5 5 0 1110 0v3c0 .9.3 1.8.8 2.6L19 17h-4"/><path d="M10 18a2 2 0 004 0"/></svg>',
            'user' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M16 8a4 4 0 11-8 0 4 4 0 018 0z"/><path d="M5 20a7 7 0 0114 0"/></svg>',
            'chevron-down' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M7 10l5 5 5-5"/></svg>',
            'location' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 21s-6-4.35-6-10a6 6 0 1112 0c0 5.65-6 10-6 10z"/><circle cx="12" cy="11" r="2.5"/></svg>',
            'clock' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="8"/><path d="M12 8v4.5l3 2"/></svg>',
        ];
        return new \Illuminate\Support\HtmlString($icons[$name] ?? $icons['wallet']);
    };
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - MatchPoint</title>
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
        <div class="flex items-center justify-between px-8 py-5 lg:px-12">
            <a href="{{ route('home') }}" class="font-heading text-[24px] font-bold tracking-[0.14em] text-[#1b2565]">MATCHPOINT</a>
            <nav class="flex items-center gap-6 text-[16px] font-medium text-[#52617f]">
                <a href="{{ route('fields.index') }}" class="transition hover:text-indigoSoft">Browse Fields</a>
                <a href="{{ route('dashboard') }}" class="transition hover:text-indigoSoft">Dashboard</a>
                <a href="{{ route('notifications.index') }}" class="relative text-[#5b6785] transition hover:text-indigoSoft">
                    {!! $iconSvg('bell') !!}
                    @if ($user['unread_notifications'] > 0)
                        <span class="absolute -right-2 -top-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-indigoSoft px-1.5 text-[11px] font-semibold text-white">{{ $user['unread_notifications'] }}</span>
                    @endif
                </a>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-full bg-[#f4f6fb] px-3 py-2 text-ink">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#e6eaf5] text-[#5d6886]">{!! $iconSvg('user') !!}</span>
                    <span class="font-semibold">{{ $user['first_name'] }}</span>
                    <span class="text-[#7784a4]">{!! $iconSvg('chevron-down') !!}</span>
                </a>
            </nav>
        </div>
    </header>

    <main class="px-8 py-8 lg:px-12">
        <section class="rounded-[28px] border border-line bg-white p-6 shadow-panel">
            <p class="text-[14px] font-semibold uppercase tracking-[0.24em] text-[#f08b20]">Payments</p>
            <h1 class="mt-3 font-heading text-[42px] font-bold text-ink">Your payment activity</h1>
            <p class="mt-3 max-w-[720px] text-[18px] leading-8 text-copy">Review pending proofs, verified payments, and any rejected submissions from your bookings.</p>

            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <article class="rounded-[22px] bg-[#fff4e8] p-5">
                    <p class="text-[15px] text-copy">Pending</p>
                    <p class="mt-2 text-[34px] font-bold text-[#f08b20]">{{ $page['pending_count'] }}</p>
                </article>
                <article class="rounded-[22px] bg-[#eaf9ef] p-5">
                    <p class="text-[15px] text-copy">Verified</p>
                    <p class="mt-2 text-[34px] font-bold text-[#16a34a]">{{ $page['verified_count'] }}</p>
                </article>
                <article class="rounded-[22px] bg-[#fff1f2] p-5">
                    <p class="text-[15px] text-copy">Rejected</p>
                    <p class="mt-2 text-[34px] font-bold text-[#e11d48]">{{ $page['rejected_count'] }}</p>
                </article>
            </div>
        </section>

        <section class="mt-6 space-y-4">
            @forelse ($page['payments'] as $payment)
                <article class="grid gap-5 rounded-[28px] border border-line bg-white p-6 shadow-panel lg:grid-cols-[160px_minmax(0,1fr)_220px] lg:items-center">
                    <div class="overflow-hidden rounded-[20px] bg-[#eef1f8]">
                        <img src="{{ $payment['image_url'] ? url($payment['image_url']) : asset('landing/football-stadium.jpg') }}" alt="{{ $payment['field_name'] }}" class="h-[120px] w-full object-cover">
                    </div>
                    <div>
                        <h2 class="font-heading text-[28px] font-bold text-ink">{{ $payment['field_name'] }}</h2>
                        <p class="mt-2 flex items-center gap-2 text-[17px] text-copy"><span class="h-5 w-5">{!! $iconSvg('location') !!}</span>{{ $payment['location'] }}</p>
                        <div class="mt-4 flex flex-wrap items-center gap-4 text-[17px] text-[#5c6988]">
                            <span class="inline-flex items-center gap-2"><span class="h-5 w-5">{!! $iconSvg('calendar') !!}</span>{{ $payment['date_label'] }}</span>
                            <span>&bull;</span>
                            <span>{{ $payment['time_label'] }}</span>
                            <span>&bull;</span>
                            <span>Submitted {{ $payment['submitted_at'] }}</span>
                        </div>
                        @if ($payment['rejection_reason'])
                            <p class="mt-4 rounded-2xl bg-[#fff1f2] px-4 py-3 text-[15px] text-[#be123c]">Rejected reason: {{ $payment['rejection_reason'] }}</p>
                        @endif
                    </div>
                    <div class="flex flex-col items-start gap-4 lg:items-end">
                        <p class="text-[28px] font-bold text-ink">{{ $payment['amount'] }}</p>
                        <span class="rounded-full px-4 py-2 text-[15px] font-semibold {{ $statusClasses[$payment['status_tone']] ?? $statusClasses['amber'] }}">{{ $payment['status'] }}</span>
                        <div class="flex flex-wrap gap-3">
                            @if ($payment['proof_url'])
                                <a href="{{ $payment['proof_url'] }}" target="_blank" class="rounded-2xl border border-line px-4 py-2 text-[15px] font-semibold text-[#53627f] transition hover:bg-[#f8f9fd]">View Proof</a>
                            @endif
                            @if ($payment['booking_url'])
                                <a href="{{ $payment['booking_url'] }}" class="rounded-2xl border border-[#a89dff] px-4 py-2 text-[15px] font-semibold text-indigoSoft transition hover:bg-[#f7f5ff]">View Booking</a>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-[28px] border border-dashed border-line bg-white px-6 py-10 text-center text-[18px] text-copy shadow-panel">No booking payments found yet.</div>
            @endforelse
        </section>
    </main>
</body>
</html>

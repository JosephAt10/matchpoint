@php
    $user = $dashboard['user'];

    $iconSvg = function (string $name): \Illuminate\Support\HtmlString {
        $icons = [
            'home' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M3 11.5L12 4l9 7.5"/><path d="M5 10.5V20h14v-9.5"/></svg>',
            'calendar' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/></svg>',
            'heart' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12.1 20.3l-.1.1-.11-.1C7.14 16.24 4 13.39 4 9.84 4 7.03 6.24 5 9.05 5c1.6 0 3.13.75 4.05 1.94A5.17 5.17 0 0117.15 5C19.96 5 22.2 7.03 22.2 9.84c0 3.55-3.14 6.4-8.99 10.46z"/></svg>',
            'wallet' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 7.5A2.5 2.5 0 016.5 5h10A2.5 2.5 0 0119 7.5V9H6.5A2.5 2.5 0 004 11.5v5A2.5 2.5 0 006.5 19H18a2 2 0 002-2v-5.5A2.5 2.5 0 0017.5 9H6.5A2.5 2.5 0 014 6.5z"/><path d="M16 14h4"/></svg>',
            'bell' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M15 17H5l1.2-1.6A4 4 0 007 13V10a5 5 0 1110 0v3c0 .9.3 1.8.8 2.6L19 17h-4"/><path d="M10 18a2 2 0 004 0"/></svg>',
            'user' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M16 8a4 4 0 11-8 0 4 4 0 018 0z"/><path d="M5 20a7 7 0 0114 0"/></svg>',
            'clock' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="8"/><path d="M12 8v4.5l3 2"/></svg>',
            'check-circle' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="8"/><path d="M9 12.5l2 2 4-4"/></svg>',
            'search' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="11" cy="11" r="6"/><path d="M20 20l-4.2-4.2"/></svg>',
            'chevron-right' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M9 6l6 6-6 6"/></svg>',
            'location' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 21s-6-4.35-6-10a6 6 0 1112 0c0 5.65-6 10-6 10z"/><circle cx="12" cy="11" r="2.5"/></svg>',
            'gift' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 10h16v10H4z"/><path d="M12 10v10M4 14h16"/><path d="M12 10H7.5A2.5 2.5 0 117.5 5c2 0 4.5 5 4.5 5zm0 0h4.5A2.5 2.5 0 1016.5 5c-2 0-4.5 5-4.5 5z"/></svg>',
            'chevron-down' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M7 10l5 5 5-5"/></svg>',
        ];

        return new \Illuminate\Support\HtmlString($icons[$name] ?? $icons['home']);
    };

    $statusClasses = [
        'amber' => 'bg-[#fff4e8] text-[#f08b20]',
        'emerald' => 'bg-[#eaf9ef] text-[#16a34a]',
        'slate' => 'bg-[#edf2f8] text-[#64748b]',
        'rose' => 'bg-[#fff1f2] text-[#e11d48]',
        'indigo' => 'bg-[#eef0ff] text-[#4f46e5]',
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - MatchPoint</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#131a33',
                        copy: '#60708f',
                        line: '#e9ebf5',
                        panel: '#ffffff',
                        indigoSoft: '#4f46e5',
                        page: '#f7f8fc',
                    },
                    boxShadow: {
                        panel: '0 20px 44px rgba(34, 43, 84, 0.08)',
                    },
                    fontFamily: {
                        heading: ['Outfit', 'sans-serif'],
                        body: ['DM Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-page text-ink">
    <header class="border-b border-line bg-white">
        <div class="flex items-center justify-between px-8 py-5 lg:px-12">
            <a href="{{ route('home') }}" class="font-heading text-[24px] font-bold tracking-[0.14em] text-[#1b2565]">MATCHPOINT</a>

            <nav class="flex items-center gap-6 text-[16px] font-medium text-[#52617f]">
                <a href="{{ $dashboard['links']['browse_fields'] }}" class="transition hover:text-indigoSoft">Browse Fields</a>
                <a href="{{ $dashboard['links']['dashboard'] }}" class="border-b-2 border-indigoSoft pb-6 pt-6 text-indigoSoft">Dashboard</a>
                <a href="{{ $dashboard['links']['notifications_anchor'] }}" class="relative text-[#5b6785] transition hover:text-indigoSoft">
                    {!! $iconSvg('bell') !!}
                    @if ($dashboard['unread_notifications'] > 0)
                        <span class="absolute -right-2 -top-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-indigoSoft px-1.5 text-[11px] font-semibold text-white">{{ $dashboard['unread_notifications'] }}</span>
                    @endif
                </a>
                <a href="{{ $dashboard['links']['profile'] }}" class="flex items-center gap-3 rounded-full bg-[#f4f6fb] px-3 py-2 text-ink">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#e6eaf5] text-[#5d6886]">{!! $iconSvg('user') !!}</span>
                    <span class="font-semibold">{{ $user['first_name'] }}</span>
                    <span class="text-[#7784a4]">{!! $iconSvg('chevron-down') !!}</span>
                </a>
            </nav>
        </div>
    </header>

    <div class="grid min-h-[calc(100vh-85px)] lg:grid-cols-[260px_minmax(0,1fr)]">
        <aside class="border-r border-line bg-white px-6 py-8">
            <nav class="space-y-2">
                <a href="{{ $dashboard['links']['dashboard'] }}" class="flex items-center gap-4 rounded-2xl bg-[#f2f1ff] px-5 py-4 font-medium text-indigoSoft">
                    <span class="h-6 w-6">{!! $iconSvg('home') !!}</span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ $dashboard['links']['bookings_anchor'] }}" class="flex items-center gap-4 rounded-2xl px-5 py-4 font-medium text-[#485775] transition hover:bg-[#f8f9fd]">
                    <span class="h-6 w-6">{!! $iconSvg('calendar') !!}</span>
                    <span>My Bookings</span>
                </a>
                <a href="{{ $dashboard['links']['favorites'] }}" class="flex items-center gap-4 rounded-2xl px-5 py-4 font-medium text-[#485775] transition hover:bg-[#f8f9fd]">
                    <span class="h-6 w-6">{!! $iconSvg('heart') !!}</span>
                    <span>Favorites</span>
                </a>
                <a href="{{ $dashboard['links']['payments_anchor'] }}" class="flex items-center gap-4 rounded-2xl px-5 py-4 font-medium text-[#485775] transition hover:bg-[#f8f9fd]">
                    <span class="h-6 w-6">{!! $iconSvg('wallet') !!}</span>
                    <span>Payments</span>
                </a>
                <a href="{{ $dashboard['links']['notifications_anchor'] }}" class="flex items-center justify-between rounded-2xl px-5 py-4 font-medium text-[#485775] transition hover:bg-[#f8f9fd]">
                    <span class="flex items-center gap-4">
                        <span class="h-6 w-6">{!! $iconSvg('bell') !!}</span>
                        <span>Notifications</span>
                    </span>
                    @if ($dashboard['unread_notifications'] > 0)
                        <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-indigoSoft px-2 text-[12px] font-semibold text-white">{{ $dashboard['unread_notifications'] }}</span>
                    @endif
                </a>
                <a href="{{ $dashboard['links']['profile'] }}" class="flex items-center gap-4 rounded-2xl px-5 py-4 font-medium text-[#485775] transition hover:bg-[#f8f9fd]">
                    <span class="h-6 w-6">{!! $iconSvg('user') !!}</span>
                    <span>Profile</span>
                </a>
            </nav>

            <div class="mt-12 rounded-[28px] bg-[linear-gradient(180deg,#f4f2ff_0%,#f8f7ff_100%)] p-6 shadow-panel">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#f0edff] text-indigoSoft">
                    {!! $iconSvg('gift') !!}
                </div>
                <h3 class="mt-5 font-heading text-[24px] font-bold text-ink">Invite your friends</h3>
                <p class="mt-3 text-[16px] leading-7 text-copy">Get RM10 credit when your friend makes a booking!</p>
                <button type="button" class="mt-6 w-full rounded-2xl bg-[linear-gradient(90deg,#5046e5_0%,#695df5_100%)] px-5 py-3 text-[16px] font-semibold text-white shadow-[0_16px_28px_rgba(79,70,229,0.24)]">Invite Now</button>
            </div>
        </aside>

        <main class="px-8 py-8 lg:px-10 xl:px-12">
            <section>
                <h1 class="font-heading text-[48px] font-bold leading-tight text-ink">Welcome back, {{ $user['first_name'] }}! <span class="text-[40px]">👋</span></h1>
                <p class="mt-3 text-[24px] text-copy">Here's what's happening with your bookings.</p>
            </section>

            <section class="mt-8 grid gap-5 xl:grid-cols-4">
                @foreach ($dashboard['stats'] as $stat)
                    <article class="rounded-[28px] border border-line bg-white p-6 shadow-panel">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="flex h-16 w-16 items-center justify-center rounded-3xl
                                    {{ $stat['tone'] === 'indigo' ? 'bg-[#efecff] text-indigoSoft' : '' }}
                                    {{ $stat['tone'] === 'amber' ? 'bg-[#fff3e6] text-[#f08b20]' : '' }}
                                    {{ $stat['tone'] === 'emerald' ? 'bg-[#eaf9ef] text-[#16a34a]' : '' }}
                                    {{ $stat['tone'] === 'pink' ? 'bg-[#ffe8f1] text-[#ec4899]' : '' }}">
                                    {!! $iconSvg($stat['icon']) !!}
                                </div>
                                <div>
                                    <p class="text-[42px] font-bold text-ink">{{ $stat['value'] }}</p>
                                    <p class="font-heading text-[24px] font-semibold text-ink">{{ $stat['label'] }}</p>
                                    <p class="mt-1 text-[18px] text-copy">{{ $stat['hint'] }}</p>
                                </div>
                            </div>
                            <span class="mt-2 text-[#9aa4bf]">{!! $iconSvg('chevron-right') !!}</span>
                        </div>
                    </article>
                @endforeach
            </section>

            <section class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.8fr)_360px]">
                <div class="space-y-6">
                    <article id="upcoming-bookings" class="rounded-[30px] border border-line bg-white p-6 shadow-panel">
                        <div class="flex items-center justify-between gap-4 border-b border-line pb-5">
                            <h2 class="font-heading text-[32px] font-bold text-ink">Upcoming Bookings</h2>
                            <a href="{{ $dashboard['links']['bookings_anchor'] }}" class="inline-flex items-center gap-2 text-[18px] font-semibold text-indigoSoft">
                                View All Bookings
                                <span class="h-5 w-5">{!! $iconSvg('chevron-right') !!}</span>
                            </a>
                        </div>

                        <div class="divide-y divide-line">
                            @forelse ($dashboard['bookings'] as $booking)
                                <article class="grid gap-5 py-5 lg:grid-cols-[144px_minmax(0,1fr)_160px] lg:items-center">
                                    <a href="{{ $booking['view_url'] }}" class="overflow-hidden rounded-[22px] bg-[#eef1f8]">
                                        <img src="{{ $booking['image_url'] ? url($booking['image_url']) : asset('landing/football-stadium.jpg') }}" alt="{{ $booking['field_name'] }}" class="h-[110px] w-full object-cover">
                                    </a>

                                    <div class="min-w-0">
                                        <h3 class="font-heading text-[30px] font-bold text-ink">{{ $booking['field_name'] }}</h3>
                                        <p class="mt-2 flex items-center gap-2 text-[18px] text-copy">
                                            <span class="h-5 w-5 text-[#7b89a8]">{!! $iconSvg('location') !!}</span>
                                            <span>{{ $booking['location'] }}</span>
                                        </p>
                                        <div class="mt-4 flex flex-wrap items-center gap-4 text-[18px] text-[#5c6988]">
                                            <span class="inline-flex items-center gap-2">
                                                <span class="h-5 w-5 text-[#7b89a8]">{!! $iconSvg('calendar') !!}</span>
                                                {{ $booking['date_label'] }}
                                            </span>
                                            <span>&bull;</span>
                                            <span>{{ $booking['time_label'] }}</span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-start gap-4 lg:items-end">
                                        <span class="rounded-full px-4 py-2 text-[15px] font-semibold {{ $statusClasses[$booking['status_tone']] ?? $statusClasses['indigo'] }}">
                                            {{ $booking['status_label'] }}
                                        </span>
                                        <a href="{{ $booking['view_url'] }}" class="inline-flex min-w-[140px] items-center justify-center rounded-2xl border border-[#a89dff] px-5 py-3 text-[16px] font-semibold text-indigoSoft transition hover:bg-[#f7f5ff]">View Booking</a>
                                    </div>
                                </article>
                            @empty
                                <div class="py-10 text-center text-[18px] text-copy">
                                    No upcoming bookings yet. Browse a field and make your first booking.
                                </div>
                            @endforelse
                        </div>
                    </article>
                </div>

                <div class="space-y-6">
                    <article class="rounded-[30px] border border-line bg-white p-6 shadow-panel">
                        <h2 class="font-heading text-[30px] font-bold text-ink">Quick Actions</h2>
                        <div class="mt-5 space-y-3">
                            <a href="{{ $dashboard['links']['browse_fields'] }}" class="flex items-center gap-4 rounded-[22px] border border-line p-4 transition hover:bg-[#fafbff]">
                                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#f0edff] text-indigoSoft">{!! $iconSvg('search') !!}</span>
                                <span class="min-w-0 flex-1">
                                    <span class="block font-heading text-[22px] font-semibold text-ink">Browse Fields</span>
                                    <span class="mt-1 block text-[16px] text-copy">Find and book your next venue</span>
                                </span>
                                <span class="text-[#9aa4bf]">{!! $iconSvg('chevron-right') !!}</span>
                            </a>
                            <a href="{{ $dashboard['links']['favorites'] }}" class="flex items-center gap-4 rounded-[22px] border border-line p-4 transition hover:bg-[#fafbff]">
                                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#ffeaf2] text-[#ec4899]">{!! $iconSvg('heart') !!}</span>
                                <span class="min-w-0 flex-1">
                                    <span class="block font-heading text-[22px] font-semibold text-ink">My Favorites</span>
                                    <span class="mt-1 block text-[16px] text-copy">View your saved venues</span>
                                </span>
                                <span class="text-[#9aa4bf]">{!! $iconSvg('chevron-right') !!}</span>
                            </a>
                            <a href="{{ $dashboard['links']['bookings_anchor'] }}" class="flex items-center gap-4 rounded-[22px] border border-line p-4 transition hover:bg-[#fafbff]">
                                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#eef5ff] text-[#2563eb]">{!! $iconSvg('calendar') !!}</span>
                                <span class="min-w-0 flex-1">
                                    <span class="block font-heading text-[22px] font-semibold text-ink">Booking History</span>
                                    <span class="mt-1 block text-[16px] text-copy">View all your past bookings</span>
                                </span>
                                <span class="text-[#9aa4bf]">{!! $iconSvg('chevron-right') !!}</span>
                            </a>
                        </div>
                    </article>

                    <article class="rounded-[30px] border border-line bg-white p-6 shadow-panel">
                        <div class="flex items-center justify-between gap-4">
                            <h2 class="font-heading text-[30px] font-bold text-ink">Your Favorites</h2>
                            <a href="{{ $dashboard['links']['favorites'] }}" class="text-[17px] font-semibold text-indigoSoft">View All</a>
                        </div>

                        <div class="mt-5 space-y-4">
                            @forelse ($dashboard['favorites'] as $favorite)
                                <a href="{{ $favorite['show_url'] }}" class="block overflow-hidden rounded-[24px] border border-line transition hover:shadow-panel">
                                    <img src="{{ $favorite['image_url'] ? url($favorite['image_url']) : asset('landing/football-stadium.jpg') }}" alt="{{ $favorite['name'] }}" class="h-[128px] w-full object-cover">
                                    <div class="p-4">
                                        <h3 class="font-heading text-[22px] font-bold text-ink">{{ $favorite['name'] }}</h3>
                                        <p class="mt-1 text-[16px] text-copy">{{ $favorite['location'] }}</p>
                                        <p class="mt-3 text-[18px] font-semibold text-indigoSoft">{{ $favorite['price'] }} <span class="text-copy">/ slot</span></p>
                                    </div>
                                </a>
                            @empty
                                <div class="rounded-[24px] border border-dashed border-line px-5 py-8 text-center text-[17px] text-copy">
                                    No favorite venues saved yet.
                                </div>
                            @endforelse
                        </div>
                    </article>

                    <article id="recent-notifications" class="rounded-[30px] border border-line bg-white p-6 shadow-panel">
                        <div class="flex items-center justify-between gap-4">
                            <h2 class="font-heading text-[30px] font-bold text-ink">Recent Notifications</h2>
                            <a href="{{ $dashboard['links']['notifications_anchor'] }}" class="text-[17px] font-semibold text-indigoSoft">View All</a>
                        </div>

                        <div class="mt-5 space-y-4">
                            @forelse ($dashboard['recent_notifications'] as $notification)
                                <article class="flex items-start gap-4">
                                    <span class="mt-1 flex h-11 w-11 items-center justify-center rounded-full
                                        {{ $notification['tone'] === 'emerald' ? 'bg-[#eaf9ef] text-[#16a34a]' : '' }}
                                        {{ $notification['tone'] === 'amber' ? 'bg-[#fff3e6] text-[#f08b20]' : '' }}
                                        {{ $notification['tone'] === 'indigo' ? 'bg-[#eef0ff] text-indigoSoft' : '' }}">
                                        {!! $iconSvg($notification['tone'] === 'emerald' ? 'check-circle' : ($notification['tone'] === 'amber' ? 'clock' : 'bell')) !!}
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[16px] leading-7 text-[#3d4b67]">{{ $notification['message'] }}</p>
                                    </div>
                                    <span class="shrink-0 text-[14px] text-copy">{{ $notification['time_label'] }}</span>
                                </article>
                            @empty
                                <div class="rounded-[24px] border border-dashed border-line px-5 py-8 text-center text-[17px] text-copy">
                                    No recent notifications.
                                </div>
                            @endforelse
                        </div>
                    </article>
                </div>
            </section>
        </main>
    </div>
</body>
</html>

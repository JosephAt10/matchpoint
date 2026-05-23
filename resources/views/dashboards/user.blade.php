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
            'plus-square' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3.5" y="3.5" width="17" height="17" rx="2.5"/><path d="M12 8v8M8 12h8"/></svg>',
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
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('User Dashboard') }} - MatchPoint</title>
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
                        line: '#edf1f7',
                        panel: '#ffffff',
                        indigoSoft: '#4f46e5',
                        page: '#f7f8fc',
                    },
                    boxShadow: {
                        panel: '0 18px 42px rgba(34, 43, 84, 0.08)',
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
        .surface-card { border: 1px solid #edf1f7; box-shadow: 0 18px 42px rgba(34, 43, 84, 0.08); }
        .soft-card { border: 1px solid #edf1f7; box-shadow: 0 12px 28px rgba(34, 43, 84, 0.06); }
        .sidebar-shell {
            background:
                radial-gradient(circle at top left, rgba(99, 102, 241, 0.24), transparent 30%),
                linear-gradient(180deg, #0f172a 0%, #111827 52%, #0b1120 100%);
        }
        .sidebar-nav-link {
            color: rgba(226, 232, 240, 0.88);
        }
        .sidebar-nav-link:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #ffffff;
        }
        .sidebar-nav-link.is-active {
            background: linear-gradient(90deg, rgba(79, 70, 229, 0.32), rgba(99, 102, 241, 0.18));
            color: #ffffff;
            box-shadow: inset 0 0 0 1px rgba(129, 140, 248, 0.16);
        }
    </style>
</head>
<body class="min-h-screen bg-page text-ink">
    <header class="border-b border-line bg-white">
        <div class="flex w-full items-center justify-between px-8 py-5 lg:px-12 xl:px-16 2xl:px-20">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('landing/matchpoint-logo.png') }}" alt="{{ __('MatchPoint logo') }}" class="h-11 w-11 object-contain">
                <span class="font-heading text-[24px] font-bold tracking-[0.14em] text-[#1b2565]">{{ __('MATCHPOINT') }}</span>
            </a>
            <div class="shrink-0">@include('partials.locale-switcher')</div>
        </div>
    </header>

    <div class="grid min-h-[calc(100vh-85px)] lg:grid-cols-[260px_minmax(0,1fr)]">
        <aside class="sidebar-shell border-r border-white/5 px-6 py-8">
            <div class="mb-8 px-3">
                <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-indigo-200/70">{{ __('Player Space') }}</p>
                <h2 class="mt-3 font-heading text-[24px] font-bold text-white">{{ __('MatchPoint') }}</h2>
                <p class="mt-2 text-[14px] leading-6 text-slate-300">{{ __('Manage bookings, track updates, and open your next public match from one place.') }}</p>
            </div>
            <nav class="space-y-2">
                <a href="{{ $dashboard['links']['dashboard'] }}" class="sidebar-nav-link is-active flex items-center gap-4 rounded-2xl px-5 py-4 font-medium">
                    <span class="h-6 w-6">{!! $iconSvg('home') !!}</span>
                    <span>{{ __('Dashboard') }}</span>
                </a>
                <a href="{{ $dashboard['links']['browse_fields'] }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition">
                    <span class="h-6 w-6">{!! $iconSvg('search') !!}</span>
                    <span>{{ __('Browse Fields') }}</span>
                </a>
                <a href="{{ $dashboard['links']['bookings_anchor'] }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition">
                    <span class="h-6 w-6">{!! $iconSvg('calendar') !!}</span>
                    <span>{{ __('My Bookings') }}</span>
                </a>
                <a href="{{ $dashboard['links']['create_match'] }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition">
                    <span class="h-6 w-6">{!! $iconSvg('plus-square') !!}</span>
                    <span>{{ __('Public Matches') }}</span>
                </a>
                <a href="{{ $dashboard['links']['favorites'] }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition">
                    <span class="h-6 w-6">{!! $iconSvg('heart') !!}</span>
                    <span>{{ __('Favorites') }}</span>
                </a>
                <a href="{{ $dashboard['links']['notifications_anchor'] }}" class="sidebar-nav-link flex items-center justify-between rounded-2xl px-5 py-4 font-medium transition">
                    <span class="flex items-center gap-4">
                        <span class="h-6 w-6">{!! $iconSvg('bell') !!}</span>
                        <span>{{ __('Notifications') }}</span>
                    </span>
                    @if ($dashboard['unread_notifications'] > 0)
                        <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-emerald-400 px-2 text-[12px] font-semibold text-slate-950">{{ $dashboard['unread_notifications'] }}</span>
                    @endif
                </a>
                <a href="{{ $dashboard['links']['profile'] }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition">
                    <span class="h-6 w-6">{!! $iconSvg('user') !!}</span>
                    <span>{{ __('Profile') }}</span>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="pt-3">
                    @csrf
                    <button type="submit" class="sidebar-nav-link flex w-full items-center gap-4 rounded-2xl px-5 py-4 text-left font-medium transition">
                        <span class="h-6 w-6">{!! $iconSvg('chevron-right') !!}</span>
                        <span>{{ __('Logout') }}</span>
                    </button>
                </form>
            </nav>

            <div class="mt-12 rounded-[28px] border border-white/10 bg-white/5 p-6 shadow-[0_18px_38px_rgba(0,0,0,0.20)] backdrop-blur">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-400/16 text-emerald-300">
                    {!! $iconSvg('plus-square') !!}
                </div>
                <h3 class="mt-5 font-heading text-[24px] font-bold text-white">{{ __('Create Public Match') }}</h3>
                <p class="mt-3 text-[16px] leading-7 text-slate-300">{{ __('Turn a confirmed booking into a public match other players can discover and join.') }}</p>
                <a href="{{ $dashboard['links']['create_match'] }}" class="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-[linear-gradient(90deg,#22c55e_0%,#4ade80_100%)] px-5 py-3 text-[16px] font-semibold text-slate-950 shadow-[0_16px_28px_rgba(34,197,94,0.24)]">{{ __('Create Match') }}</a>
            </div>
        </aside>

        <main class="bg-[linear-gradient(180deg,#fbfcff_0%,#f7f8fc_100%)] px-8 py-8 lg:px-10 xl:px-12">
            <section>
                <h1 class="font-heading text-[48px] font-bold leading-tight text-ink">{{ __('Welcome back, :name!', ['name' => $user['first_name']]) }} <span class="text-[40px]">👋</span></h1>
                <p class="mt-3 text-[24px] text-copy">{{ __('Here\'s what\'s happening with your bookings.') }}</p>
            </section>

            <section class="mt-8 grid gap-5 xl:grid-cols-4">
                @foreach ($dashboard['stats'] as $stat)
                    <article class="surface-card rounded-[28px] bg-white p-6">
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
                                    <p class="font-heading text-[24px] font-semibold text-ink">{{ __($stat['label']) }}</p>
                                    <p class="mt-1 text-[18px] text-copy">{{ __($stat['hint']) }}</p>
                                </div>
                            </div>
                            <span class="mt-2 text-[#9aa4bf]">{!! $iconSvg('chevron-right') !!}</span>
                        </div>
                    </article>
                @endforeach
            </section>

            <section class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.8fr)_360px]">
                <div class="space-y-6">
                    <article id="upcoming-bookings" class="surface-card rounded-[30px] bg-white p-6">
                        <div class="flex items-center justify-between gap-4 border-b border-[#f1f4f9] pb-5">
                            <h2 class="font-heading text-[32px] font-bold text-ink">{{ __('Upcoming Bookings') }}</h2>
                            <a href="{{ $dashboard['links']['bookings_anchor'] }}" class="inline-flex items-center gap-2 text-[18px] font-semibold text-indigoSoft">
                                {{ __('View All Bookings') }}
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
                                            {{ __($booking['status_label']) }}
                                        </span>
                                        <a href="{{ $booking['view_url'] }}" class="inline-flex min-w-[140px] items-center justify-center rounded-2xl border border-[#a89dff] px-5 py-3 text-[16px] font-semibold text-indigoSoft transition hover:bg-[#f7f5ff]">{{ __('View Booking') }}</a>
                                    </div>
                                </article>
                            @empty
                                <div class="py-10 text-center text-[18px] text-copy">
                                    {{ __('No upcoming bookings yet. Browse a field and make your first booking.') }}
                                </div>
                            @endforelse
                        </div>
                    </article>
                </div>

                <div class="space-y-6">
                    <article class="surface-card rounded-[30px] bg-white p-6">
                        <h2 class="font-heading text-[30px] font-bold text-ink">{{ __('Quick Actions') }}</h2>
                        <div class="mt-5 space-y-3">
                            <a href="{{ $dashboard['links']['browse_fields'] }}" class="soft-card flex items-center gap-4 rounded-[22px] p-4 transition hover:-translate-y-0.5 hover:bg-[#fafbff]">
                                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#f0edff] text-indigoSoft">{!! $iconSvg('search') !!}</span>
                                <span class="min-w-0 flex-1">
                                    <span class="block font-heading text-[22px] font-semibold text-ink">{{ __('Browse Fields') }}</span>
                                    <span class="mt-1 block text-[16px] text-copy">{{ __('Find and book your next venue') }}</span>
                                </span>
                                <span class="text-[#9aa4bf]">{!! $iconSvg('chevron-right') !!}</span>
                            </a>
                            <a href="{{ $dashboard['links']['favorites'] }}" class="soft-card flex items-center gap-4 rounded-[22px] p-4 transition hover:-translate-y-0.5 hover:bg-[#fafbff]">
                                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#ffeaf2] text-[#ec4899]">{!! $iconSvg('heart') !!}</span>
                                <span class="min-w-0 flex-1">
                                    <span class="block font-heading text-[22px] font-semibold text-ink">{{ __('My Favorites') }}</span>
                                    <span class="mt-1 block text-[16px] text-copy">{{ __('View your saved venues') }}</span>
                                </span>
                                <span class="text-[#9aa4bf]">{!! $iconSvg('chevron-right') !!}</span>
                            </a>
                            <a href="{{ $dashboard['links']['bookings_anchor'] }}" class="soft-card flex items-center gap-4 rounded-[22px] p-4 transition hover:-translate-y-0.5 hover:bg-[#fafbff]">
                                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#eef5ff] text-[#2563eb]">{!! $iconSvg('calendar') !!}</span>
                                <span class="min-w-0 flex-1">
                                    <span class="block font-heading text-[22px] font-semibold text-ink">{{ __('Booking History') }}</span>
                                    <span class="mt-1 block text-[16px] text-copy">{{ __('View all your past bookings') }}</span>
                                </span>
                                <span class="text-[#9aa4bf]">{!! $iconSvg('chevron-right') !!}</span>
                            </a>
                            <a href="{{ $dashboard['links']['create_match'] }}" class="soft-card flex items-center gap-4 rounded-[22px] p-4 transition hover:-translate-y-0.5 hover:bg-[#fafbff]">
                                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#f0edff] text-indigoSoft">{!! $iconSvg('gift') !!}</span>
                                <span class="min-w-0 flex-1">
                                    <span class="block font-heading text-[22px] font-semibold text-ink">{{ __('Create Match') }}</span>
                                    <span class="mt-1 block text-[16px] text-copy">{{ __('Open a confirmed booking to public participants') }}</span>
                                </span>
                                <span class="text-[#9aa4bf]">{!! $iconSvg('chevron-right') !!}</span>
                            </a>
                        </div>
                    </article>

                    <article class="surface-card rounded-[30px] bg-white p-6">
                        <div class="flex items-center justify-between gap-4">
                            <h2 class="font-heading text-[30px] font-bold text-ink">{{ __('Your Favorites') }}</h2>
                            <a href="{{ $dashboard['links']['favorites'] }}" class="text-[17px] font-semibold text-indigoSoft">{{ __('View All') }}</a>
                        </div>

                        <div class="mt-5 space-y-4">
                            @forelse ($dashboard['favorites'] as $favorite)
                                <a href="{{ $favorite['show_url'] }}" class="block overflow-hidden rounded-[24px] border border-line transition hover:shadow-panel">
                                    <img src="{{ $favorite['image_url'] ? url($favorite['image_url']) : asset('landing/football-stadium.jpg') }}" alt="{{ $favorite['name'] }}" class="h-[128px] w-full object-cover">
                                    <div class="p-4">
                                        <h3 class="font-heading text-[22px] font-bold text-ink">{{ $favorite['name'] }}</h3>
                                        <p class="mt-1 text-[16px] text-copy">{{ $favorite['location'] }}</p>
                                        <p class="mt-3 text-[18px] font-semibold text-indigoSoft">{{ $favorite['price'] }} <span class="text-copy">/ {{ __('slot') }}</span></p>
                                    </div>
                                </a>
                            @empty
                                <div class="rounded-[24px] border border-dashed border-[#e7ecf4] px-5 py-8 text-center text-[17px] text-copy">
                                    {{ __('No favorite venues saved yet.') }}
                                </div>
                            @endforelse
                        </div>
                    </article>

                    <article id="recent-notifications" class="surface-card rounded-[30px] bg-white p-6">
                        <div class="flex items-center justify-between gap-4">
                            <h2 class="font-heading text-[30px] font-bold text-ink">{{ __('Recent Notifications') }}</h2>
                            <a href="{{ $dashboard['links']['notifications_anchor'] }}" class="text-[17px] font-semibold text-indigoSoft">{{ __('View All') }}</a>
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
                                <div class="rounded-[24px] border border-dashed border-[#e7ecf4] px-5 py-8 text-center text-[17px] text-copy">
                                    {{ __('No recent notifications.') }}
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

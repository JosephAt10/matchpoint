<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('MatchPoint') }} — {{ __('Field & Public Match Booking') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#162853',
                        indigo: '#7371df',
                        lavender: '#e8e8fb',
                        page: '#dfe2ff',
                        line: '#a9aff3',
                        textsoft: '#5e6480',
                    },
                    fontFamily: {
                        heading: ['Outfit', 'sans-serif'],
                        body: ['DM Sans', 'sans-serif'],
                    },
                    boxShadow: {
                        soft: '0 8px 20px rgba(78, 85, 168, 0.18)',
                        card: '0 6px 14px rgba(47, 56, 128, 0.22)',
                    },
                    borderRadius: {
                        '4xl': '2rem',
                    },
                }
            }
        };
    </script>

    <style>
        * { font-family: 'DM Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Outfit', sans-serif; }

        body {
            background:
                radial-gradient(circle at top center, rgba(255,255,255,0.72), transparent 28%),
                linear-gradient(180deg, #dfe2ff 0%, #d9ddff 100%);
        }

        .section-card {
            border: 1px solid rgba(126, 132, 225, 0.42);
            box-shadow: 0 8px 18px rgba(70, 78, 150, 0.22);
        }

        .primary-btn {
            background: linear-gradient(90deg, #4b57c5 0%, #7b74df 100%);
            transition: transform .18s ease, box-shadow .18s ease, opacity .18s ease;
        }

        .primary-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(91, 91, 214, 0.25);
        }

        .mini-icon svg {
            width: 18px;
            height: 18px;
        }

        .hero-field-image {
            transform: scale(1.05);
            transform-origin: center;
        }

        .hero-field-image.is-zooming {
            animation: heroFieldZoom 8.4s linear forwards;
        }

        .hero-team-layer {
            transition: opacity .42s ease, transform .42s ease;
        }

        .hero-team-layer.is-hidden {
            opacity: 0;
            transform: scale(.985);
        }

        .hero-details.is-leaving {
            animation: detailSweepDown .42s cubic-bezier(.45, 0, .7, .2) forwards;
        }

        .hero-details.is-entering {
            animation: detailRiseIn .54s cubic-bezier(.2, .9, .28, 1) both;
        }

        @keyframes heroFieldZoom {
            0% { transform: scale(1.05); }
            100% { transform: scale(1.19); }
        }

        @keyframes detailSweepDown {
            0% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(42px); }
        }

        @keyframes detailRiseIn {
            0% { opacity: 0; transform: translateY(-42px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen text-[#1f2740]">
    @php
        $initialsFor = function (?string $name): string {
            return collect(explode(' ', trim($name ?: 'MP')))
                ->filter()
                ->take(2)
                ->map(fn (string $part) => strtoupper(substr($part, 0, 1)))
                ->implode('') ?: 'MP';
        };

        $fallbackHeroMatch = [
            'title' => __('Friendly Match'),
            'date' => __('Saturday, April 18'),
            'time' => '3:30 WIB',
            'location' => __('Kanjuruhan Stadium Malang'),
            'slots' => '5/10',
            'home' => 'Gumbal FC',
            'away' => 'Ssunesia FC',
            'fieldImage' => asset('landing/football-stadium.jpg'),
            'homeLogo' => asset('landing/football-team-2.png'),
            'awayLogo' => asset('landing/football-team-1.png'),
            'homeInitials' => 'GF',
            'awayInitials' => 'SF',
            'centerLogo' => asset('landing/friendly-matche.png'),
            'detailsUrl' => route('matches.index'),
        ];

        $formatHeroMatch = function ($match) use ($initialsFor): array {
            $booking = $match->booking;
            $slots = $booking?->bookedSlots
                ?->pluck('timeSlot')
                ->filter()
                ->sortBy('start_time')
                ->values();

            return [
                'title' => $match->title ?: __('Friendly Match'),
                'date' => $booking?->date?->translatedFormat('l, F j') ?: __('Date'),
                'time' => $slots?->isNotEmpty()
                    ? substr($slots->first()->start_time, 0, 5) . ' - ' . substr($slots->last()->end_time, 0, 5)
                    : __('Time not available'),
                'location' => $booking?->field?->location ?: __('Location'),
                'slots' => $match->filled_slots . '/' . $match->max_participants,
                'home' => $match->team_a_name ?: __('Team A'),
                'away' => $match->team_b_name ?: __('Team B'),
                'fieldImage' => $booking?->field?->image_url ? url($booking->field->image_url) : asset('landing/football-stadium.jpg'),
                'homeLogo' => $match->team_a_logo ? Storage::url($match->team_a_logo) : null,
                'awayLogo' => $match->team_b_logo ? Storage::url($match->team_b_logo) : null,
                'homeInitials' => $initialsFor($match->team_a_name ?: __('Team A')),
                'awayInitials' => $initialsFor($match->team_b_name ?: __('Team B')),
                'centerLogo' => asset('landing/friendly-matche.png'),
                'detailsUrl' => route('matches.show', $match),
            ];
        };

        $heroSlides = $matches->isNotEmpty()
            ? $matches->map($formatHeroMatch)->values()->all()
            : [$fallbackHeroMatch];

        $heroMatch = $heroSlides[0];

        $matchCards = [
            [
                'sport' => __('Football'),
                'home' => 'Gumbal FC',
                'away' => 'Ssunesia FC',
                'date' => __('Saturday, April 18'),
                'time' => '7:30 WIB',
                'location' => __('Kanjuruhan Stadium Malang'),
                'button' => __('Join Match'),
                'homeLogo' => asset('landing/football-team-2.png'),
                'awayLogo' => asset('landing/football-team-1.png'),
                'centerIcon' => asset('landing/friendly-matche.png'),
            ],
            [
                'sport' => __('Basketball'),
                'home' => 'Jieng',
                'away' => 'Duor',
                'date' => __('Sunday, April 19'),
                'time' => '2:30 WIB',
                'location' => __('Malang Arena Basket Hall'),
                'button' => __('Join Match'),
                'homeLogo' => asset('landing/basketball-team-1.png'),
                'awayLogo' => asset('landing/basketball-team-2.png'),
            ],
            [
                'sport' => __('Volleyball'),
                'home' => 'Shirkat',
                'away' => 'Jebel Lemon',
                'date' => __('Saturday, April 18'),
                'time' => '7:30 WIB',
                'location' => __('Merdeka Volleyball Court'),
                'button' => __('Join Match'),
                'homeLogo' => asset('landing/volleyball-team-1.png'),
                'awayLogo' => asset('landing/volleyball-team-2.png'),
            ],
        ];

    @endphp

    <header class="bg-white">
        <div class="border-b border-black/25">
            <div class="flex w-full items-center justify-between px-5 py-4 md:px-10 xl:px-16 2xl:px-24">
                <div class="flex items-center gap-10 text-[#22283c]">
                    <button id="hamburger" type="button" class="flex items-center text-navy">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <div class="flex items-center gap-2 text-[15px] font-medium">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <a href="{{ route('login') }}" class="hover:text-indigo">{{ __('Login') }}</a>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-[15px] font-medium text-[#22283c]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 13a8 8 0 0116 0"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h2a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-3a2 2 0 011-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 12h2a1 1 0 011 1v3a1 1 0 01-1 1h-2a1 1 0 01-1-1v-4a1 1 0 011-1z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h2"/>
                    </svg>
                    <a href="{{ route('contact') }}" class="hover:text-indigo">{{ __('Contact') }}</a>
                    @include('partials.locale-switcher')
                </div>
            </div>
        </div>

        <nav class="hidden w-full items-center justify-center gap-16 px-5 py-3 text-[17px] font-medium text-[#2a3046] md:flex md:px-10 xl:px-16 2xl:px-24">
            <a href="{{ route('home') }}" class="hover:text-indigo">{{ __('Home') }}</a>
            <a href="{{ route('fields.index') }}" class="hover:text-indigo">{{ __('Fields') }}</a>
            <a href="{{ route('matches.index') }}" class="hover:text-indigo">{{ __('Matches') }}</a>
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="hover:text-indigo">{{ __('My Bookings') }}</a>
            <a href="{{ auth()->check() ? route('profile.edit') : route('login') }}" class="hover:text-indigo">{{ __('Profile') }}</a>
        </nav>

        <nav id="mobile-menu" class="hidden w-full flex-col gap-3 border-t border-black/15 px-5 py-4 text-sm font-medium text-[#2a3046] md:px-10 xl:px-16 2xl:px-24">
            <a href="{{ route('home') }}">{{ __('Home') }}</a>
            <a href="{{ route('fields.index') }}">{{ __('Fields') }}</a>
            <a href="{{ route('matches.index') }}">{{ __('Matches') }}</a>
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}">{{ __('My Bookings') }}</a>
            <a href="{{ auth()->check() ? route('profile.edit') : route('login') }}">{{ __('Profile') }}</a>
        </nav>
    </header>

    <main class="w-full px-5 pb-14 pt-8 md:px-10 xl:px-16 2xl:px-24">
        <section id="landing-hero-match" class="grid gap-10 md:grid-cols-[1.95fr_0.95fr] md:items-start">
            <div id="hero-visual" class="hero-visual relative overflow-hidden rounded-[2rem] shadow-soft">
                <img
                    id="hero-field-image"
                    src="{{ $heroMatch['fieldImage'] }}"
                    alt="{{ $heroMatch['title'] }}"
                    class="hero-field-image h-[300px] w-full object-cover blur-[1.2px] md:h-[470px]"
                >
                <div class="absolute inset-0 bg-gradient-to-r from-[#0f1739]/66 via-[#162853]/30 to-transparent"></div>
                <div id="hero-team-layer" class="hero-team-layer absolute inset-0 flex items-center justify-center px-6">
                    <div class="flex w-full max-w-[760px] items-center justify-between gap-6 text-white">
                        <div class="flex flex-col items-center">
                            <div class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-full bg-[#cfd6ff]/90 shadow-xl md:h-36 md:w-36">
                                <img id="hero-home-logo" src="{{ $heroMatch['homeLogo'] ?? '' }}" alt="{{ $heroMatch['home'] }}" class="{{ $heroMatch['homeLogo'] ? '' : 'hidden' }} h-[86%] w-[86%] object-contain md:h-[84%] md:w-[84%]">
                                <span id="hero-home-initials" class="{{ $heroMatch['homeLogo'] ? 'hidden' : '' }} font-heading text-[34px] font-extrabold tracking-[0.08em] text-[#10214a] md:text-[42px]">{{ $heroMatch['homeInitials'] ?? 'A' }}</span>
                            </div>
                            <p id="hero-home-name" class="mt-4 rounded-full bg-[#cfd6ff]/85 px-4 py-2 text-center font-heading text-[18px] font-bold tracking-wide text-[#10214a] shadow-lg md:text-[22px]">
                                {{ $heroMatch['home'] }}
                            </p>
                        </div>

                        <div class="flex flex-col items-center">
                            <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-full border-4 border-white/70 bg-[#cfd6ff]/85 shadow-xl backdrop-blur md:h-32 md:w-32">
                                <img id="hero-center-logo" src="{{ $heroMatch['centerLogo'] }}" alt="{{ __('Friendly Match') }}" class="h-full w-full object-cover">
                            </div>
                            <span class="mt-4 rounded-full bg-[#cfd6ff]/85 px-4 py-2 font-heading text-[16px] font-semibold tracking-[0.2em] text-[#10214a] shadow-lg md:text-[18px]">
                                {{ __('VS') }}
                            </span>
                        </div>

                        <div class="flex flex-col items-center">
                            <div class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-full bg-[#cfd6ff]/90 shadow-xl md:h-36 md:w-36">
                                <img id="hero-away-logo" src="{{ $heroMatch['awayLogo'] ?? '' }}" alt="{{ $heroMatch['away'] }}" class="{{ $heroMatch['awayLogo'] ? '' : 'hidden' }} h-[96%] w-[96%] object-contain md:h-[94%] md:w-[94%]">
                                <span id="hero-away-initials" class="{{ $heroMatch['awayLogo'] ? 'hidden' : '' }} font-heading text-[34px] font-extrabold tracking-[0.08em] text-[#10214a] md:text-[42px]">{{ $heroMatch['awayInitials'] ?? 'B' }}</span>
                            </div>
                            <p id="hero-away-name" class="mt-4 rounded-full bg-[#cfd6ff]/85 px-4 py-2 text-center font-heading text-[18px] font-bold tracking-wide text-[#10214a] shadow-lg md:text-[22px]">
                                {{ $heroMatch['away'] }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] bg-white p-6 shadow-soft">
                <div id="hero-details" class="hero-details rounded-[1.15rem] bg-navy px-5 py-7 text-white shadow-card">
                    <h2 id="hero-title" class="font-heading text-[20px] font-bold">{{ __($heroMatch['title']) }}</h2>
                    <div class="mt-5 space-y-3 text-[15px] text-white/90">
                        <div class="mini-icon flex items-center gap-2">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span id="hero-date-time">{{ __($heroMatch['date']) }} &nbsp; {{ $heroMatch['time'] }}</span>
                        </div>
                        <div class="mini-icon flex items-center gap-2">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span id="hero-location">{{ $heroMatch['location'] }}</span>
                        </div>
                        <div class="mini-icon flex items-center gap-2">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                            </svg>
                            <span id="hero-slots">{{ __('Slots Available: :slots', ['slots' => $heroMatch['slots']]) }}</span>
                        </div>
                    </div>

                    <a id="hero-join-link" href="{{ $heroMatch['detailsUrl'] }}" class="primary-btn mt-7 block rounded-[0.55rem] px-4 py-3 text-center text-[17px] font-bold text-white">
                        {{ __('Join Match') }}
                    </a>
                </div>
            </div>

        </section>

        <section class="mt-12 rounded-[2rem] bg-white px-10 py-10 shadow-soft">
            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="font-heading text-[28px] font-bold text-[#23304f] md:text-[31px]">{{ __('Book a Field, Join a Match!') }}</h1>
                    <p class="mt-2 text-[17px] text-textsoft">{{ __('Sign up to find and join matches in public fields with ease') }}</p>
                </div>
                <a href="{{ route('register') }}" class="primary-btn inline-flex min-w-[190px] justify-center rounded-[0.55rem] px-8 py-3 text-[17px] font-bold text-white">
                    {{ __('Get Started') }}
                </a>
            </div>
        </section>

        <section class="mt-8 grid gap-5 md:grid-cols-3">
            <article class="rounded-[1.55rem] bg-white px-7 py-6 shadow-soft">
                <div class="mb-3 flex items-center gap-2 text-indigo">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h2 class="font-heading text-[18px] font-bold text-[#283552]">{{ __('Book a Field') }}</h2>
                </div>
                <p class="min-h-[58px] text-[16px] leading-6 text-textsoft">{{ __('Find and reserve available fields') }}</p>
                <a href="{{ route('fields.index') }}" class="primary-btn mt-6 block rounded-[0.55rem] px-4 py-3 text-center text-[17px] font-bold text-white">{{ __('Book Now') }}</a>
            </article>

            <article class="rounded-[1.55rem] bg-white px-7 py-6 shadow-soft">
                <div class="mb-3 flex items-center gap-2 text-indigo">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h8M12 8v8"></path>
                    </svg>
                    <h2 class="font-heading text-[18px] font-bold text-[#283552]">{{ __('Join a Match') }}</h2>
                </div>
                <p class="min-h-[58px] text-[16px] leading-6 text-textsoft">{{ __('Join public matches near you') }}</p>
                <a href="{{ route('matches.index') }}" class="primary-btn mt-6 block rounded-[0.55rem] px-4 py-3 text-center text-[17px] font-bold text-white">{{ __('Explore Matches') }}</a>
            </article>

            <article class="rounded-[1.55rem] bg-white px-7 py-6 shadow-soft">
                <div class="mb-3 flex items-center gap-2 text-indigo">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                    </svg>
                    <h2 class="font-heading text-[18px] font-bold text-[#283552]">{{ __('Create a Match') }}</h2>
                </div>
                <p class="min-h-[58px] text-[16px] leading-6 text-textsoft">{{ __('Start your own match and invite others') }}</p>
                <a href="{{ auth()->check() ? route('matches.create') : route('login') }}" class="primary-btn mt-6 block rounded-[0.55rem] px-4 py-3 text-center text-[17px] font-bold text-white">{{ __('Create Match') }}</a>
            </article>
        </section>

        <section id="available-matches" class="section-card mt-12 rounded-[2.2rem] bg-lavender px-7 py-7 md:px-8 md:py-8">
            <div class="inline-flex rounded-[0.7rem] bg-navy px-6 py-2.5">
                <h2 class="font-heading text-[21px] font-bold text-white md:text-[23px]">{{ __('Available Match') }}</h2>
            </div>

            <div class="mt-5 grid gap-5 md:grid-cols-3">
                @foreach ($matchCards as $match)
                    <article class="overflow-hidden rounded-[1.45rem] bg-navy shadow-card">
                        <div class="flex items-center justify-between gap-2 px-4 py-4 text-white">
                            <div class="flex flex-col items-center gap-2">
                                <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full">
                                    @if (!empty($match['homeLogo']))
                                        <img src="{{ $match['homeLogo'] }}" alt="{{ $match['home'] }}" class="h-[88%] w-[88%] object-contain">
                                    @elseif ($match['sport'] === 'Basketball')
                                        <span class="text-lg">🏀</span>
                                    @else
                                        <span class="text-lg">🏐</span>
                                    @endif
                                </div>
                                <span class="text-[14px]">{{ $match['home'] }}</span>
                            </div>

                            <div class="flex h-8 w-8 items-center justify-center overflow-hidden rounded-full bg-white/10">
                                @if ($match['sport'] === 'Football')
                                    <span class="text-xl leading-none text-indigo">⚽</span>
                                @elseif ($match['sport'] === 'Volleyball')
                                    <span class="text-xl leading-none text-indigo">🏐</span>
                                @elseif (!empty($match['centerIcon']))
                                    <img src="{{ $match['centerIcon'] }}" alt="{{ __('Friendly Match') }}" class="h-full w-full object-cover">
                                @elseif ($match['sport'] === 'Basketball')
                                    <span class="text-xl text-indigo">🏀</span>
                                @else
                                    <span class="text-xl text-indigo">🏐</span>
                                @endif
                            </div>

                            <div class="flex flex-col items-center gap-2">
                                <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full">
                                    @if (!empty($match['awayLogo']))
                                        <img src="{{ $match['awayLogo'] }}" alt="{{ $match['away'] }}" class="h-[96%] w-[96%] object-contain">
                                    @elseif ($match['sport'] === 'Basketball')
                                        <span class="text-lg">🏀</span>
                                    @else
                                        <span class="text-lg">🏐</span>
                                    @endif
                                </div>
                                <span class="text-[14px]">{{ $match['away'] }}</span>
                            </div>
                        </div>

                        <div class="rounded-t-[1.35rem] bg-white px-4 py-4">
                            <h3 class="font-heading text-[20px] font-bold text-[#263452]">{{ __($match['sport']) }} {{ __('Friendly Match') }}</h3>
                            <div class="mt-3 space-y-2 text-[14px] text-[#3f4863]">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-[#222f53]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ __($match['date']) }} &nbsp; {{ $match['time'] }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-[#222f53]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>{{ $match['location'] }}</span>
                                </div>
                            </div>

                            <a href="{{ route('login') }}" class="primary-btn mt-5 block rounded-[0.55rem] px-4 py-3 text-center text-[17px] font-bold text-white">
                                {{ __($match['button']) }}
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section id="available-fields" class="mt-12">
            <div class="inline-flex items-center gap-4 rounded-[0.7rem] bg-navy px-6 py-2.5">
                <h2 class="font-heading text-[21px] font-bold text-white md:text-[23px]">{{ __('Available Fields') }}</h2>
                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </div>

            <div class="mt-5 grid gap-x-5 gap-y-7 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($fields as $field)
                    <article>
                        <div class="overflow-hidden bg-white shadow-soft">
                            <img src="{{ $field->image_url ?? asset('landing/football-stadium.jpg') }}" alt="{{ $field->name }}" class="h-[210px] w-full object-cover md:h-[240px]">
                        </div>
                        <p class="mt-3 text-center font-heading text-[18px] font-bold text-[#273452]">{{ $field->name }}</p>
                        <div class="mt-3 h-px w-full bg-[#bfc6ee]"></div>
                    </article>
                @endforeach
            </div>

            <div class="mt-4 flex justify-center">
                <a href="{{ route('fields.index') }}" class="primary-btn inline-flex min-w-[240px] justify-center rounded-[0.1rem] px-8 py-3 text-[17px] font-bold text-white">
                    {{ __('Explore') }}
                </a>
            </div>
        </section>
    </main>

    <footer class="mt-12 bg-navy text-white">
        <div class="w-full px-5 py-10 md:px-10 md:py-12 xl:px-16 2xl:px-24">
            <div class="grid gap-10 lg:grid-cols-[1.25fr_0.85fr_0.95fr]">
                <div class="lg:pr-10 lg:border-r lg:border-white/15">
                    <div class="mb-5 flex items-center gap-4">
                        <img src="{{ asset('landing/matchpoint-logo.png') }}" alt="{{ __('MatchPoint logo') }}" class="h-14 w-14 object-contain">
                        <span class="font-heading text-[36px] font-bold leading-none md:text-[28px]">{{ __('MatchPoint') }}</span>
                    </div>

                    <p class="max-w-[340px] text-[18px] leading-9 text-white/85 md:text-[15px] md:leading-8">
                        {{ __('Book sports fields, manage your bookings, and join exciting matches happening near you.') }}
                    </p>

                    <div class="mt-7 space-y-4 text-[18px] text-white/80 md:text-[15px]">
                        <div class="flex items-center gap-3">
                            <span class="text-indigo">📍</span>
                            <span>{{ __('Malang, Indonesia') }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-indigo">✉</span>
                            <span>support@matchpoint.com</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-indigo">☎</span>
                            <span>+62 812 1727 5362</span>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h4 class="font-heading text-[20px] font-semibold md:text-[16px]">{{ __('Follow Us') }}</h4>
                        <div class="mt-4 flex gap-3">
                            @foreach ([
                                asset('landing/social/instagram-logo.png'),
                                asset('landing/social/whatsapp-logo.png'),
                                asset('landing/social/facebook-logo.png'),
                                asset('landing/social/youtube-logo.png'),
                            ] as $social)
                                <div class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-full">
                                    <img src="{{ $social }}" alt="{{ __('Social media') }}" class="h-full w-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="lg:px-10 lg:border-r lg:border-white/15">
                    <h3 class="font-heading text-[26px] font-bold md:text-[18px]">{{ __('Quick Links') }}</h3>
                    <div class="mt-2 h-[3px] w-10 rounded-full bg-indigo"></div>
                    <div class="mt-6 space-y-5 text-[19px] text-white/90 md:text-[16px]">
                        <a href="{{ route('home') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>{{ __('Home') }}</span>
                        </a>
                        <a href="{{ route('fields.index') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>{{ __('Fields') }}</span>
                        </a>
                        <a href="{{ route('matches.index') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>{{ __('Matches') }}</span>
                        </a>
                        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>{{ __('My Bookings') }}</span>
                        </a>
                        <a href="{{ auth()->check() ? route('profile.edit') : route('login') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>{{ __('Profile') }}</span>
                        </a>
                        <a href="{{ route('login') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>{{ __('Create Match') }}</span>
                        </a>
                    </div>
                </div>

                <div class="lg:pl-10">
                    <h3 class="font-heading text-[26px] font-bold md:text-[18px]">{{ __('Support') }}</h3>
                    <div class="mt-2 h-[3px] w-10 rounded-full bg-indigo"></div>
                    <div class="mt-6 space-y-5 text-[19px] text-white/90 md:text-[16px]">
                        <a href="{{ route('contact') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>{{ __('Contact Us') }}</span>
                        </a>
                        <a href="{{ route('help') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>{{ __('Help / FAQ') }}</span>
                        </a>
                        <a href="{{ route('how-it-works') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>{{ __('How It Works') }}</span>
                        </a>
                        <a href="{{ route('terms') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>{{ __('Terms & Conditions') }}</span>
                        </a>
                        <a href="{{ route('privacy') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>{{ __('Privacy Policy') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-white/10 bg-[#25244b]">
            <div class="grid w-full gap-6 px-5 py-7 text-white/88 md:grid-cols-[1fr_auto_1fr] md:items-center md:px-10 xl:px-16 2xl:px-24">
                <div class="md:pr-8 md:border-r md:border-white/15">
                    <div class="flex items-start gap-3">
                        <div class="text-indigo">🛡</div>
                        <div>
                            <p class="font-heading text-[18px] font-semibold text-white">{{ __('Secure & Trusted') }}</p>
                            <p class="mt-1 max-w-[260px] text-[14px] leading-6 text-white/70">
                                {{ __('Your data is protected with industry-standard security.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="text-center text-[18px] md:px-8 md:text-[16px]">
                    {{ __('© 2026 MatchPoint. All rights reserved.') }}
                </div>

                <div class="md:pl-8 md:border-l md:border-white/15">
                    <p class="font-heading text-[18px] font-semibold text-white">{{ __('We Accept') }}</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ([
                            asset('landing/payments/visa-logo.png'),
                            asset('landing/payments/mastercard-logo.png'),
                            asset('landing/payments/bca-logo.png'),
                            asset('landing/payments/gopay-logo.png'),
                            asset('landing/payments/ovo-logo.png'),
                            asset('landing/payments/dana-logo.png'),
                        ] as $payment)
                            <div class="flex h-[42px] items-center justify-center rounded-md bg-white px-2 py-1 shadow-sm">
                                <img src="{{ $payment }}" alt="{{ __('Payment method') }}" class="max-h-full w-auto object-contain">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script type="application/json" id="hero-match-slides">@json($heroSlides)</script>
    <script>
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobile-menu');

        hamburger.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            mobileMenu.classList.toggle('flex');
        });

        const heroSlides = JSON.parse(document.getElementById('hero-match-slides')?.textContent || '[]');
        const slotsTemplate = @json(__('Slots Available: :slots'));
        const heroElements = {
            visual: document.getElementById('hero-visual'),
            teamLayer: document.getElementById('hero-team-layer'),
            details: document.getElementById('hero-details'),
            fieldImage: document.getElementById('hero-field-image'),
            homeLogo: document.getElementById('hero-home-logo'),
            homeInitials: document.getElementById('hero-home-initials'),
            homeName: document.getElementById('hero-home-name'),
            centerLogo: document.getElementById('hero-center-logo'),
            awayLogo: document.getElementById('hero-away-logo'),
            awayInitials: document.getElementById('hero-away-initials'),
            awayName: document.getElementById('hero-away-name'),
            title: document.getElementById('hero-title'),
            dateTime: document.getElementById('hero-date-time'),
            location: document.getElementById('hero-location'),
            slots: document.getElementById('hero-slots'),
            joinLink: document.getElementById('hero-join-link'),
        };

        let currentHeroIndex = 0;
        let heroIsAnimating = false;
        let heroCycleTimer = null;
        const heroZoomDuration = 8400;
        const heroCycleDelay = 9000;

        function updateHeroContent(slide) {
            if (slide.homeLogo) {
                heroElements.homeLogo.src = slide.homeLogo;
                heroElements.homeLogo.classList.remove('hidden');
                heroElements.homeInitials.classList.add('hidden');
            } else {
                heroElements.homeLogo.removeAttribute('src');
                heroElements.homeLogo.classList.add('hidden');
                heroElements.homeInitials.textContent = slide.homeInitials || 'A';
                heroElements.homeInitials.classList.remove('hidden');
            }

            heroElements.homeLogo.alt = slide.home;
            heroElements.homeName.textContent = slide.home;
            heroElements.centerLogo.src = slide.centerLogo;
            if (slide.awayLogo) {
                heroElements.awayLogo.src = slide.awayLogo;
                heroElements.awayLogo.classList.remove('hidden');
                heroElements.awayInitials.classList.add('hidden');
            } else {
                heroElements.awayLogo.removeAttribute('src');
                heroElements.awayLogo.classList.add('hidden');
                heroElements.awayInitials.textContent = slide.awayInitials || 'B';
                heroElements.awayInitials.classList.remove('hidden');
            }

            heroElements.awayLogo.alt = slide.away;
            heroElements.awayName.textContent = slide.away;
            heroElements.title.textContent = slide.title;
            heroElements.dateTime.textContent = `${slide.date}   ${slide.time}`;
            heroElements.location.textContent = slide.location;
            heroElements.slots.textContent = slotsTemplate.replace(':slots', slide.slots);
            heroElements.joinLink.href = slide.detailsUrl;
        }

        function resetHeroZoom() {
            heroElements.fieldImage.style.animationDuration = `${heroZoomDuration}ms`;
            heroElements.fieldImage.classList.remove('is-zooming');
            void heroElements.fieldImage.offsetWidth;
            heroElements.fieldImage.classList.add('is-zooming');
        }

        function scheduleHeroCycle() {
            window.clearTimeout(heroCycleTimer);
            heroCycleTimer = window.setTimeout(() => {
                changeHeroMatch(heroSlides.length > 1 ? currentHeroIndex + 1 : currentHeroIndex);
            }, heroCycleDelay);
        }

        function changeHeroMatch(targetIndex, manual = false) {
            if (heroIsAnimating || !heroSlides.length) {
                return;
            }

            const normalizedIndex = (targetIndex + heroSlides.length) % heroSlides.length;
            const isChangingSlide = heroSlides.length > 1 && normalizedIndex !== currentHeroIndex;

            if (manual && !isChangingSlide) {
                restartHeroAutoplay();
                return;
            }

            heroIsAnimating = true;
            currentHeroIndex = normalizedIndex;
            const nextSlide = heroSlides[currentHeroIndex];

            heroElements.details.classList.remove('is-leaving', 'is-entering');
            heroElements.teamLayer.classList.add('is-hidden');
            heroElements.details.classList.add('is-leaving');

            window.setTimeout(() => {
                if (isChangingSlide) {
                    heroElements.fieldImage.src = nextSlide.fieldImage;
                    heroElements.fieldImage.alt = nextSlide.title;
                }

                resetHeroZoom();

                window.setTimeout(() => {
                    if (isChangingSlide) {
                        updateHeroContent(nextSlide);
                    }

                    heroElements.teamLayer.classList.remove('is-hidden');
                    heroElements.details.classList.remove('is-leaving');
                    heroElements.details.classList.add('is-entering');
                }, 140);
            }, 80);

            window.setTimeout(() => {
                heroElements.details.classList.remove('is-entering');
                heroIsAnimating = false;
                scheduleHeroCycle();
            }, 820);
        }

        function startHeroAutoplay() {
            resetHeroZoom();
            scheduleHeroCycle();
        }

        function restartHeroAutoplay() {
            window.clearTimeout(heroCycleTimer);
            startHeroAutoplay();
        }

        startHeroAutoplay();
    </script>
</body>
</html>

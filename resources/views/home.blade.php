<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MatchPoint — Field & Public Match Booking</title>

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
    </style>
</head>
<body class="min-h-screen text-[#1f2740]">
    @php
        $heroMatch = [
            'title' => 'Friendly Match',
            'date' => 'Saturday, April 18',
            'time' => '3:30 WIB',
            'location' => 'Kanjuruhan Stadium Malang',
            'slots' => '5/10',
            'home' => 'Gumbal FC',
            'away' => 'Ssunesia FC',
        ];

        $matchCards = [
            [
                'sport' => 'Football',
                'home' => 'Gumbal FC',
                'away' => 'Ssunesia FC',
                'date' => 'Saturday, April 18',
                'time' => '7:30 WIB',
                'location' => 'Kanjuruhan Stadium Malang',
                'button' => 'Join Match',
                'homeLogo' => asset('landing/football-team-2.png'),
                'awayLogo' => asset('landing/football-team-1.png'),
                'centerIcon' => asset('landing/friendly-matche.png'),
            ],
            [
                'sport' => 'Basketball',
                'home' => 'Jieng',
                'away' => 'Duor',
                'date' => 'Sunday, April 19',
                'time' => '2:30 WIB',
                'location' => 'Malang Arena Basket Hall',
                'button' => 'Join Match',
                'homeLogo' => asset('landing/basketball-team-1.png'),
                'awayLogo' => asset('landing/basketball-team-2.png'),
            ],
            [
                'sport' => 'Volleyball',
                'home' => 'Shirkat',
                'away' => 'Jebel Lemon',
                'date' => 'Saturday, April 18',
                'time' => '7:30 WIB',
                'location' => 'Merdeka Volleyball Court',
                'button' => 'Join Match',
                'homeLogo' => asset('landing/volleyball-team-1.png'),
                'awayLogo' => asset('landing/volleyball-team-2.png'),
            ],
        ];

        $fieldCards = [
            ['name' => 'Football Stadium', 'image' => asset('landing/football-stadium.jpg')],
            ['name' => 'Basketball Court', 'image' => asset('landing/basketball-court.jpg')],
            ['name' => 'Tennis Court', 'image' => asset('landing/tennis-court.png')],
            ['name' => 'Badminton Court', 'image' => asset('landing/badminton-court.png')],
            ['name' => 'Futsal Court', 'image' => asset('landing/futsal-court.png')],
            ['name' => 'Volleyball Court', 'image' => asset('landing/volleyball-court.png')],
        ];
    @endphp

    <header class="bg-white">
        <div class="border-b border-black/25">
            <div class="mx-auto flex max-w-[1560px] items-center justify-between px-6 py-4">
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
                        <a href="{{ route('login') }}" class="hover:text-indigo">Login</a>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-[15px] font-medium text-[#22283c]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 13a8 8 0 0116 0"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h2a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-3a2 2 0 011-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 12h2a1 1 0 011 1v3a1 1 0 01-1 1h-2a1 1 0 01-1-1v-4a1 1 0 011-1z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h2"/>
                    </svg>
                    <a href="{{ route('contact') }}" class="hover:text-indigo">Contact</a>
                </div>
            </div>
        </div>

        <nav class="mx-auto hidden max-w-[1560px] items-center justify-center gap-16 px-6 py-3 text-[17px] font-medium text-[#2a3046] md:flex">
            <a href="{{ route('home') }}" class="hover:text-indigo">Home</a>
            <a href="{{ route('fields.index') }}" class="hover:text-indigo">Fields</a>
            <a href="#available-matches" class="hover:text-indigo">Matches</a>
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="hover:text-indigo">My Bookings</a>
            <a href="{{ auth()->check() ? route('profile.edit') : route('login') }}" class="hover:text-indigo">Profile</a>
        </nav>

        <nav id="mobile-menu" class="mx-auto hidden max-w-[1560px] flex-col gap-3 border-t border-black/15 px-6 py-4 text-sm font-medium text-[#2a3046]">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('fields.index') }}">Fields</a>
            <a href="#available-matches">Matches</a>
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}">My Bookings</a>
            <a href="{{ auth()->check() ? route('profile.edit') : route('login') }}">Profile</a>
        </nav>
    </header>

    <main class="mx-auto max-w-[1560px] px-6 pb-14 pt-8">
        <section class="grid gap-10 md:grid-cols-[1.95fr_0.95fr] md:items-start">
            <div class="relative overflow-hidden rounded-[2rem] shadow-soft">
                <img
                    src="{{ asset('landing/football-stadium.jpg') }}"
                    alt="Football stadium"
                    class="h-[300px] w-full scale-[1.05] object-cover blur-[1.2px] md:h-[470px]"
                >
                <div class="absolute inset-0 bg-gradient-to-r from-[#0f1739]/66 via-[#162853]/30 to-transparent"></div>
                <div class="absolute inset-0 flex items-center justify-center px-6">
                    <div class="flex w-full max-w-[760px] items-center justify-between gap-6 text-white">
                        <div class="flex flex-col items-center">
                            <div class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-full shadow-xl md:h-36 md:w-36">
                                <img src="{{ asset('landing/football-team-2.png') }}" alt="{{ $heroMatch['home'] }}" class="h-[86%] w-[86%] object-contain md:h-[84%] md:w-[84%]">
                            </div>
                            <p class="mt-4 rounded-full bg-[#cfd6ff]/85 px-4 py-2 text-center font-heading text-[18px] font-bold tracking-wide text-[#10214a] shadow-lg md:text-[22px]">
                                {{ $heroMatch['home'] }}
                            </p>
                        </div>

                        <div class="flex flex-col items-center">
                            <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-full border-4 border-white/70 bg-[#cfd6ff]/85 shadow-xl backdrop-blur md:h-32 md:w-32">
                                <img src="{{ asset('landing/friendly-matche.png') }}" alt="Friendly match" class="h-full w-full object-cover">
                            </div>
                            <span class="mt-4 rounded-full bg-[#cfd6ff]/85 px-4 py-2 font-heading text-[16px] font-semibold tracking-[0.2em] text-[#10214a] shadow-lg md:text-[18px]">
                                VS
                            </span>
                        </div>

                        <div class="flex flex-col items-center">
                            <div class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-full shadow-xl md:h-36 md:w-36">
                                <img src="{{ asset('landing/football-team-1.png') }}" alt="{{ $heroMatch['away'] }}" class="h-[96%] w-[96%] object-contain md:h-[94%] md:w-[94%]">
                            </div>
                            <p class="mt-4 rounded-full bg-[#cfd6ff]/85 px-4 py-2 text-center font-heading text-[18px] font-bold tracking-wide text-[#10214a] shadow-lg md:text-[22px]">
                                {{ $heroMatch['away'] }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] bg-white p-6 shadow-soft">
                <div class="rounded-[1.15rem] bg-navy px-5 py-7 text-white shadow-card">
                    <h2 class="font-heading text-[20px] font-bold">{{ $heroMatch['title'] }}</h2>
                    <div class="mt-5 space-y-3 text-[15px] text-white/90">
                        <div class="mini-icon flex items-center gap-2">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $heroMatch['date'] }} &nbsp; {{ $heroMatch['time'] }}</span>
                        </div>
                        <div class="mini-icon flex items-center gap-2">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ $heroMatch['location'] }}</span>
                        </div>
                        <div class="mini-icon flex items-center gap-2">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                            </svg>
                            <span>Slots Available: {{ $heroMatch['slots'] }}</span>
                        </div>
                    </div>

                    <a href="#available-matches" class="primary-btn mt-7 block rounded-[0.55rem] px-4 py-3 text-center text-[17px] font-bold text-white">
                        Join Match
                    </a>
                </div>
            </div>
        </section>

        <section class="mt-12 rounded-[2rem] bg-white px-10 py-10 shadow-soft">
            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="font-heading text-[28px] font-bold text-[#23304f] md:text-[31px]">Book a Field, Join a Match!</h1>
                    <p class="mt-2 text-[17px] text-textsoft">Sign up to find and join matches in public fields with ease</p>
                </div>
                <a href="{{ route('register') }}" class="primary-btn inline-flex min-w-[190px] justify-center rounded-[0.55rem] px-8 py-3 text-[17px] font-bold text-white">
                    Get Started
                </a>
            </div>
        </section>

        <section class="mt-8 grid gap-5 md:grid-cols-3">
            <article class="rounded-[1.55rem] bg-white px-7 py-6 shadow-soft">
                <div class="mb-3 flex items-center gap-2 text-indigo">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h2 class="font-heading text-[18px] font-bold text-[#283552]">Book a Field</h2>
                </div>
                <p class="min-h-[58px] text-[16px] leading-6 text-textsoft">Find and reserve available fields</p>
                <a href="{{ route('fields.index') }}" class="primary-btn mt-6 block rounded-[0.55rem] px-4 py-3 text-center text-[17px] font-bold text-white">Book Now</a>
            </article>

            <article class="rounded-[1.55rem] bg-white px-7 py-6 shadow-soft">
                <div class="mb-3 flex items-center gap-2 text-indigo">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h8M12 8v8"></path>
                    </svg>
                    <h2 class="font-heading text-[18px] font-bold text-[#283552]">Join a Match</h2>
                </div>
                <p class="min-h-[58px] text-[16px] leading-6 text-textsoft">Join public matches near you</p>
                <a href="#available-matches" class="primary-btn mt-6 block rounded-[0.55rem] px-4 py-3 text-center text-[17px] font-bold text-white">Explore Matches</a>
            </article>

            <article class="rounded-[1.55rem] bg-white px-7 py-6 shadow-soft">
                <div class="mb-3 flex items-center gap-2 text-indigo">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                    </svg>
                    <h2 class="font-heading text-[18px] font-bold text-[#283552]">Create a Match</h2>
                </div>
                <p class="min-h-[58px] text-[16px] leading-6 text-textsoft">Start your own match and invite others</p>
                <a href="{{ route('login') }}" class="primary-btn mt-6 block rounded-[0.55rem] px-4 py-3 text-center text-[17px] font-bold text-white">Create Match</a>
            </article>
        </section>

        <section id="available-matches" class="section-card mt-12 rounded-[2.2rem] bg-lavender px-7 py-7 md:px-8 md:py-8">
            <div class="inline-flex rounded-[0.7rem] bg-navy px-6 py-2.5">
                <h2 class="font-heading text-[21px] font-bold text-white md:text-[23px]">Available Match</h2>
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
                                    <img src="{{ $match['centerIcon'] }}" alt="Friendly match" class="h-full w-full object-cover">
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
                            <h3 class="font-heading text-[20px] font-bold text-[#263452]">{{ $match['sport'] }} Friendly Match</h3>
                            <div class="mt-3 space-y-2 text-[14px] text-[#3f4863]">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-[#222f53]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ $match['date'] }} &nbsp; {{ $match['time'] }}</span>
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
                                {{ $match['button'] }}
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section id="available-fields" class="mt-12">
            <div class="inline-flex items-center gap-4 rounded-[0.7rem] bg-navy px-6 py-2.5">
                <h2 class="font-heading text-[21px] font-bold text-white md:text-[23px]">Available Fields</h2>
                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </div>

            <div class="mt-5 grid gap-x-5 gap-y-7 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($fieldCards as $field)
                    <article>
                        <div class="overflow-hidden bg-white shadow-soft">
                            <img src="{{ $field['image'] }}" alt="{{ $field['name'] }}" class="h-[210px] w-full object-cover md:h-[240px]">
                        </div>
                        <p class="mt-3 text-center font-heading text-[18px] font-bold text-[#273452]">{{ $field['name'] }}</p>
                        <div class="mt-3 h-px w-full bg-[#bfc6ee]"></div>
                    </article>
                @endforeach
            </div>

            <div class="mt-4 flex justify-center">
                <a href="{{ route('fields.index') }}" class="primary-btn inline-flex min-w-[240px] justify-center rounded-[0.1rem] px-8 py-3 text-[17px] font-bold text-white">
                    Explore
                </a>
            </div>
        </section>
    </main>

    <footer class="mt-12 bg-navy text-white">
        <div class="mx-auto max-w-[1560px] px-6 py-10 md:px-10 md:py-12">
            <div class="grid gap-10 lg:grid-cols-[1.25fr_0.85fr_0.95fr]">
                <div class="lg:pr-10 lg:border-r lg:border-white/15">
                    <div class="mb-5 flex items-center gap-4">
                        <img src="{{ asset('landing/matchpoint-logo.png') }}" alt="MatchPoint logo" class="h-14 w-14 object-contain">
                        <span class="font-heading text-[36px] font-bold leading-none md:text-[28px]">MatchPoint</span>
                    </div>

                    <p class="max-w-[340px] text-[18px] leading-9 text-white/85 md:text-[15px] md:leading-8">
                        Book sports fields, manage your bookings, and join exciting matches happening near you.
                    </p>

                    <div class="mt-7 space-y-4 text-[18px] text-white/80 md:text-[15px]">
                        <div class="flex items-center gap-3">
                            <span class="text-indigo">📍</span>
                            <span>Malang, Indonesia</span>
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
                        <h4 class="font-heading text-[20px] font-semibold md:text-[16px]">Follow Us</h4>
                        <div class="mt-4 flex gap-3">
                            @foreach ([
                                asset('landing/social/instagram-logo.png'),
                                asset('landing/social/whatsapp-logo.png'),
                                asset('landing/social/facebook-logo.png'),
                                asset('landing/social/youtube-logo.png'),
                            ] as $social)
                                <div class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-full">
                                    <img src="{{ $social }}" alt="Social media" class="h-full w-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="lg:px-10 lg:border-r lg:border-white/15">
                    <h3 class="font-heading text-[26px] font-bold md:text-[18px]">Quick Links</h3>
                    <div class="mt-2 h-[3px] w-10 rounded-full bg-indigo"></div>
                    <div class="mt-6 space-y-5 text-[19px] text-white/90 md:text-[16px]">
                        <a href="{{ route('home') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>Home</span>
                        </a>
                        <a href="{{ route('fields.index') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>Fields</span>
                        </a>
                        <a href="#available-matches" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>Matches</span>
                        </a>
                        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>My Bookings</span>
                        </a>
                        <a href="{{ auth()->check() ? route('profile.edit') : route('login') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>Profile</span>
                        </a>
                        <a href="{{ route('login') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>Create Match</span>
                        </a>
                    </div>
                </div>

                <div class="lg:pl-10">
                    <h3 class="font-heading text-[26px] font-bold md:text-[18px]">Support</h3>
                    <div class="mt-2 h-[3px] w-10 rounded-full bg-indigo"></div>
                    <div class="mt-6 space-y-5 text-[19px] text-white/90 md:text-[16px]">
                        <a href="{{ route('contact') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>Contact Us</span>
                        </a>
                        <a href="{{ route('help') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>Help / FAQ</span>
                        </a>
                        <a href="{{ route('help') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>How It Works</span>
                        </a>
                        <a href="{{ route('terms') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>Terms &amp; Conditions</span>
                        </a>
                        <a href="{{ route('privacy') }}" class="flex items-center gap-3 hover:text-indigo">
                            <span class="text-indigo">›</span><span>Privacy Policy</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-white/10 bg-[#25244b]">
            <div class="mx-auto grid max-w-[1560px] gap-6 px-6 py-7 text-white/88 md:grid-cols-[1fr_auto_1fr] md:items-center md:px-10">
                <div class="md:pr-8 md:border-r md:border-white/15">
                    <div class="flex items-start gap-3">
                        <div class="text-indigo">🛡</div>
                        <div>
                            <p class="font-heading text-[18px] font-semibold text-white">Secure &amp; Trusted</p>
                            <p class="mt-1 max-w-[260px] text-[14px] leading-6 text-white/70">
                                Your data is protected with industry-standard security.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="text-center text-[18px] md:px-8 md:text-[16px]">
                    © 2026 MatchPoint. All rights reserved.
                </div>

                <div class="md:pl-8 md:border-l md:border-white/15">
                    <p class="font-heading text-[18px] font-semibold text-white">We Accept</p>
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
                                <img src="{{ $payment }}" alt="Payment method" class="max-h-full w-auto object-contain">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobile-menu');

        hamburger.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            mobileMenu.classList.toggle('flex');
        });
    </script>
</body>
</html>

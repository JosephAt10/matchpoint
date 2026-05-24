@php
    $user = auth()->user();
    $selectedBookingId = (int) old('booking_id', 0);

    $iconSvg = function (string $name): \Illuminate\Support\HtmlString {
        $icons = [
            'home' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M3 11.5L12 4l9 7.5"/><path d="M5 10.5V20h14v-9.5"/></svg>',
            'calendar' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/></svg>',
            'heart' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12.1 20.3l-.1.1-.11-.1C7.14 16.24 4 13.39 4 9.84 4 7.03 6.24 5 9.05 5c1.6 0 3.13.75 4.05 1.94A5.17 5.17 0 0117.15 5C19.96 5 22.2 7.03 22.2 9.84c0 3.55-3.14 6.4-8.99 10.46z"/></svg>',
            'bell' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M15 17H5l1.2-1.6A4 4 0 007 13V10a5 5 0 1110 0v3c0 .9.3 1.8.8 2.6L19 17h-4"/><path d="M10 18a2 2 0 004 0"/></svg>',
            'user' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M16 8a4 4 0 11-8 0 4 4 0 018 0z"/><path d="M5 20a7 7 0 0114 0"/></svg>',
            'gift' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 10h16v10H4z"/><path d="M12 10v10M4 14h16"/><path d="M12 10H7.5A2.5 2.5 0 117.5 5c2 0 4.5 5 4.5 5zm0 0h4.5A2.5 2.5 0 1016.5 5c-2 0-4.5 5-4.5 5z"/></svg>',
            'search' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="11" cy="11" r="6"/><path d="M20 20l-4.2-4.2"/></svg>',
            'plus-square' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3.5" y="3.5" width="17" height="17" rx="2.5"/><path d="M12 8v8M8 12h8"/></svg>',
            'chevron-right' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M9 6l6 6-6 6"/></svg>',
        ];

        return new \Illuminate\Support\HtmlString($icons[$name] ?? $icons['home']);
    };

    $pageUser = [
        'first_name' => str($user->name)->before(' ')->toString(),
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Create Public Match') }} - MatchPoint</title>
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
                        greenSoft: '#16a34a',
                    },
                    boxShadow: {
                        panel: '0 18px 42px rgba(34, 43, 84, 0.08)',
                        soft: '0 10px 26px rgba(34, 43, 84, 0.06)',
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
        .field {
            width: 100%;
            border: 1px solid #edf1f7;
            border-radius: 0.75rem;
            background: #fff;
            padding: 0.9rem 1rem;
            font-size: 14px;
            color: #131a33;
            box-shadow: 0 8px 20px rgba(34, 43, 84, 0.04);
        }
        .field.is-error {
            border-color: #fb7185;
            box-shadow: none;
        }
        .field:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.10);
        }
        .field.is-error:focus {
            border-color: #fb7185;
            box-shadow: none;
        }
        .field[readonly] {
            background: #f8fafc;
            color: #42506a;
        }
        .section {
            border-bottom: 1px solid #f1f4f9;
            padding: 24px;
        }
        .section:last-child { border-bottom: 0; }
        .label {
            display: block;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #364152;
        }
        .required::after {
            content: ' *';
            color: #dc2626;
        }
        textarea.field { resize: none; }
        .booking-panel[hidden] {
            display: none !important;
        }
        .booking-card {
            border: 1px solid #edf1f7;
            box-shadow: 0 12px 28px rgba(34, 43, 84, 0.06);
        }
        .booking-card.is-selected {
            border-color: #bbf7d0;
            background: linear-gradient(180deg, #f5fff8 0%, #ffffff 100%);
            box-shadow: 0 18px 34px rgba(22, 163, 74, 0.12);
        }
        .surface-card {
            border: 1px solid #edf1f7;
            box-shadow: 0 18px 42px rgba(34, 43, 84, 0.08);
        }
        .surface-section {
            border-bottom: 1px solid #f1f4f9;
        }
        .surface-section:last-child {
            border-bottom: 0;
        }
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
            background: linear-gradient(90deg, rgba(34, 197, 94, 0.22), rgba(79, 70, 229, 0.18));
            color: #ffffff;
            box-shadow: inset 0 0 0 1px rgba(167, 243, 208, 0.16);
        }
    </style>
</head>
<body class="min-h-screen bg-page text-ink">
    <header class="bg-white/96 shadow-[0_14px_34px_rgba(34,43,84,0.05)] backdrop-blur">
        <div class="flex w-full items-center justify-between px-8 py-5 lg:px-12 xl:px-16 2xl:px-20">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('landing/matchpoint-logo.png') }}" alt="{{ __('MatchPoint logo') }}" class="h-11 w-11 object-contain">
                <span class="font-heading text-[24px] font-bold tracking-[0.14em] text-[#1b2565]">{{ __('MATCHPOINT') }}</span>
            </a>
            <div class="shrink-0">@include('partials.locale-switcher')</div>
        </div>
    </header>

    <div class="grid min-h-[calc(100vh-85px)] lg:grid-cols-[292px_minmax(0,1fr)]">
        <aside class="sidebar-shell border-r border-[#dde5f3] shadow-[1px_0_0_rgba(255,255,255,0.04)] px-6 py-8">
            <div class="mb-8 px-3">
                <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-emerald-200/70">{{ __('Player Space') }}</p>
                <h2 class="mt-3 font-heading text-[24px] font-bold text-white">{{ __('MatchPoint') }}</h2>
                <p class="mt-2 text-[14px] leading-6 text-slate-300">{{ __('Choose a confirmed booking, shape the match, and publish it to the community.') }}</p>
            </div>
            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition">
                    <span class="h-6 w-6">{!! $iconSvg('home') !!}</span>
                    <span>{{ __('Dashboard') }}</span>
                </a>
                <a href="{{ route('fields.index') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition">
                    <span class="h-6 w-6">{!! $iconSvg('search') !!}</span>
                    <span>{{ __('Browse Fields') }}</span>
                </a>
                <a href="{{ route('bookings.index') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition">
                    <span class="h-6 w-6">{!! $iconSvg('calendar') !!}</span>
                    <span>{{ __('My Bookings') }}</span>
                </a>
                <a href="{{ route('matches.create') }}" class="sidebar-nav-link is-active flex items-center gap-4 rounded-2xl px-5 py-4 font-medium">
                    <span class="h-6 w-6">{!! $iconSvg('plus-square') !!}</span>
                    <span>{{ __('Public Matches') }}</span>
                </a>
                <a href="{{ route('favorites.index') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition">
                    <span class="h-6 w-6">{!! $iconSvg('heart') !!}</span>
                    <span>{{ __('Favorites') }}</span>
                </a>
                <a href="{{ route('notifications.index') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition">
                    <span class="h-6 w-6">{!! $iconSvg('bell') !!}</span>
                    <span>{{ __('Notifications') }}</span>
                </a>
                <a href="{{ route('profile.edit') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition">
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

            <div class="mt-12 rounded-[30px] border border-white/10 bg-white/5 p-7 shadow-[0_18px_38px_rgba(0,0,0,0.20)] backdrop-blur">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-400/16 text-emerald-300 shadow-[0_0_0_10px_rgba(52,211,153,0.08)]">
                    {!! $iconSvg('plus-square') !!}
                </div>
                <h3 class="mt-5 max-w-[13rem] font-heading text-[22px] font-bold leading-[1.15] text-white">{{ __('Create Public Match') }}</h3>
                <p class="mt-3 max-w-[15rem] text-[15px] leading-7 text-slate-300">{{ __('Turn a confirmed booking into a public game that other players can join.') }}</p>
                <a href="{{ route('matches.create') }}" class="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-[linear-gradient(90deg,#22c55e_0%,#4ade80_100%)] px-5 py-3.5 text-[15px] font-semibold text-slate-950 shadow-[0_16px_28px_rgba(34,197,94,0.24)]">
                    {{ __('Create Match') }}
                </a>
            </div>
        </aside>

        <main class="bg-[linear-gradient(180deg,#fbfcff_0%,#f7f8fc_100%)] px-8 py-8 lg:px-10 xl:px-12">
            <section>
                <div class="flex items-start gap-4">
                    <a href="{{ route('matches.index') }}" class="mt-1 text-[28px] text-ink">&larr;</a>
                    <div>
                        <h1 class="font-heading text-[42px] font-bold text-ink">{{ __('Create Public Match') }}</h1>
                        <p class="mt-2 text-[18px] text-copy">{{ __('Create a new public match and invite other players to join.') }}</p>
                    </div>
                </div>
            </section>

            @if ($bookingOptions->isEmpty())
                <section class="mt-8 rounded-[28px] border border-dashed border-line bg-white px-6 py-10 text-center text-[18px] text-copy shadow-panel">
                    {{ __('You need at least one confirmed booking that is not already linked to a match before creating a public match.') }}
                </section>
            @else
                <section class="mt-8 grid gap-6 xl:grid-cols-[minmax(0,1fr)_350px] xl:items-start">
                    <form action="{{ route('matches.store') }}" method="POST" class="surface-card overflow-hidden rounded-[28px] bg-white">
                        @csrf

                        <div class="section surface-section">
                            <h2 class="mb-5 flex items-center gap-2 font-heading text-[18px] font-bold"><span class="text-greenSoft">⌘</span>{{ __('Select Booking') }}</h2>
                            <p class="mb-5 text-[15px] leading-7 text-copy">{{ __('Choose one confirmed booking first. When you click a booking, the create public match form will open for that booking and the other booking cards will minimize.') }}</p>
                            <div id="booking-list" class="grid gap-4">
                                @foreach ($bookingOptions as $option)
                                    <div class="booking-panel" data-booking-panel="{{ $option['id'] }}">
                                        <button type="button" class="booking-card booking-toggle flex w-full items-start gap-4 rounded-[22px] bg-white p-5 text-left transition hover:-translate-y-0.5 hover:shadow-[0_16px_34px_rgba(34,43,84,0.10)]" data-booking-trigger="{{ $option['id'] }}">
                                            <span class="mt-1 flex h-10 w-10 items-center justify-center rounded-2xl bg-[#eef6ff] text-[#2563eb]">
                                                {!! $iconSvg('calendar') !!}
                                            </span>
                                            <span class="min-w-0 flex-1">
                                                <span class="block font-heading text-[20px] font-semibold text-ink">{{ $option['field'] }}</span>
                                                <span class="mt-1 block text-[15px] text-copy">{{ $option['sport_label'] }} • {{ $option['date'] }}</span>
                                                <span class="mt-1 block text-[15px] text-copy">{{ $option['start_time'] }}{{ $option['end_time'] ? ' - ' . $option['end_time'] : '' }}{{ $option['duration_label'] ? ' (' . $option['duration_label'] . ')' : '' }}</span>
                                            </span>
                                            <span class="flex flex-col items-end gap-3">
                                                <span class="rounded-full bg-[#eef6ff] px-3 py-1 text-[12px] font-semibold text-[#3b82f6]">{{ __('Confirmed') }}</span>
                                                <span class="inline-flex items-center gap-2 text-[13px] font-semibold text-[#16a34a]">
                                                    {{ __('Create Public Match') }}
                                                    <span class="h-4 w-4">{!! $iconSvg('chevron-right') !!}</span>
                                                </span>
                                            </span>
                                        </button>
                                        <input type="radio" name="booking_id" value="{{ $option['id'] }}" class="hidden" @checked((int) old('booking_id', $selectedBookingId) === (int) $option['id'])>
                                    </div>
                                @endforeach
                            </div>
                            <x-forms.error field="booking_id" />
                        </div>

                        <div id="create-match-sections" hidden>
                        <div class="section surface-section">
                            <div class="mb-5 flex items-center justify-between gap-4">
                                <h2 class="flex items-center gap-2 font-heading text-[18px] font-bold"><span class="text-greenSoft">▣</span>{{ __('1. Match Details') }}</h2>
                                <button type="button" id="change-booking-button" class="rounded-full border border-line px-4 py-2 text-[13px] font-semibold text-copy transition hover:bg-[#f8f9fd]">
                                    {{ __('Change Booking') }}
                                </button>
                            </div>
                            <div class="grid gap-5 md:grid-cols-2">
                                <label>
                                    <span class="label required">{{ __('Match Title') }}</span>
                                    <input id="title" name="title" maxlength="150" value="{{ old('title') }}" required class="field {{ $errors->has('title') ? 'is-error' : '' }}">
                                    <x-forms.error field="title" />
                                </label>
                                <label>
                                    <span class="label required">{{ __('Sport') }}</span>
                                    <input id="sport-display" readonly class="field">
                                </label>
                            </div>
                            <label class="mt-5 block">
                                <span class="label">{{ __('Description') }}</span>
                                <textarea id="description" name="description" maxlength="200" rows="4" required class="field {{ $errors->has('description') ? 'is-error' : '' }}">{{ old('description') }}</textarea>
                                <span id="description-count" class="mt-1 block text-right text-[12px] text-copy">0 / 200</span>
                                <x-forms.error field="description" />
                            </label>
                        </div>

                        <div class="section surface-section">
                            <h2 class="mb-5 flex items-center gap-2 font-heading text-[18px] font-bold"><span class="text-greenSoft">◴</span>{{ __('2. Date & Time') }}</h2>
                            <div class="grid gap-5 md:grid-cols-3">
                                <label>
                                    <span class="label required">{{ __('Match Date') }}</span>
                                    <input id="date-display" readonly class="field">
                                </label>
                                <label>
                                    <span class="label required">{{ __('Start Time') }}</span>
                                    <input id="start-display" readonly class="field">
                                </label>
                                <label>
                                    <span class="label required">{{ __('End Time') }}</span>
                                    <input id="end-display" readonly class="field">
                                </label>
                            </div>
                        </div>

                        <div class="section surface-section">
                            <h2 class="mb-5 flex items-center gap-2 font-heading text-[18px] font-bold"><span class="text-greenSoft">⌖</span>{{ __('3. Location') }}</h2>
                            <div class="grid gap-5 md:grid-cols-[160px_minmax(0,1fr)_minmax(0,1fr)]">
                                <div class="overflow-hidden rounded-[20px] bg-[#eef2f9] shadow-soft">
                                    <img id="field-image" src="{{ asset('landing/football-stadium.jpg') }}" alt="{{ __('Field image') }}" class="h-full min-h-[120px] w-full object-cover">
                                </div>
                                <label>
                                    <span class="label required">{{ __('Field Name') }}</span>
                                    <input id="field-display" readonly class="field">
                                </label>
                                <label>
                                    <span class="label">{{ __('Organizer Name') }}</span>
                                    <input id="organizer-display" readonly class="field">
                                </label>
                            </div>
                            <label class="mt-5 block">
                                <span class="label">{{ __('Address') }}</span>
                                <input id="address-display" readonly class="field">
                            </label>
                        </div>

                        <div class="section surface-section">
                            <h2 class="mb-5 flex items-center gap-2 font-heading text-[18px] font-bold"><span class="text-greenSoft">⚙</span>{{ __('4. Match Settings') }}</h2>
                            <div class="grid gap-5 md:grid-cols-2">
                                <label>
                                    <span class="label required">{{ __('Max Participants') }}</span>
                                    <input id="max_participants" name="max_participants" type="number" min="1" max="100" value="{{ old('max_participants', 10) }}" required class="field {{ $errors->has('max_participants') ? 'is-error' : '' }}">
                                    <x-forms.error field="max_participants" />
                                </label>
                                <label>
                                    <span class="label required">{{ __('Participant Fee') }}</span>
                                    <input id="participant_fee" name="participant_fee" type="number" min="0" step="1000" value="{{ old('participant_fee', 0) }}" required class="field {{ $errors->has('participant_fee') ? 'is-error' : '' }}">
                                    <x-forms.error field="participant_fee" />
                                </label>
                                <label class="md:col-span-2">
                                    <span class="label">{{ __('Skill Level') }}</span>
                                    <select id="skill_level" name="skill_level" class="field {{ $errors->has('skill_level') ? 'is-error' : '' }}">
                                        <option value="">{{ __('All Levels') }}</option>
                                        @foreach (['All Levels', 'Beginner', 'Intermediate', 'Advanced'] as $level)
                                            <option value="{{ $level }}" @selected(old('skill_level', 'All Levels') === $level)>{{ __($level) }}</option>
                                        @endforeach
                                    </select>
                                    <x-forms.error field="skill_level" />
                                </label>
                            </div>
                        </div>

                        <div class="section">
                            <div class="flex items-center justify-between gap-4">
                                <a href="{{ route('dashboard') }}" class="rounded-xl border border-line px-7 py-3 text-[14px] font-bold text-copy">{{ __('Cancel') }}</a>
                                <button type="submit" class="rounded-xl bg-[#16a34a] px-7 py-3 text-[14px] font-bold text-white shadow-[0_12px_24px_rgba(22,163,74,.24)]">{{ __('Create Match') }}</button>
                            </div>
                        </div>
                        </div>
                    </form>

                    <aside class="surface-card rounded-[28px] bg-white p-6">
                        <h2 class="mb-6 flex items-center gap-2 font-heading text-[18px] font-bold"><span class="text-greenSoft">▣</span>{{ __('Match Summary') }}</h2>
                        <div class="space-y-5 text-[14px]">
                            <div><p class="font-bold text-copy">{{ __('Selected Booking') }}</p><p id="summary-booking" class="mt-1 text-ink"></p></div>
                            <div><p class="font-bold text-copy">{{ __('Title') }}</p><p id="summary-title" class="mt-1 text-ink"></p></div>
                            <div><p class="font-bold text-copy">{{ __('Sport') }}</p><p id="summary-sport" class="mt-1 text-ink"></p></div>
                            <div><p class="font-bold text-copy">{{ __('Date') }}</p><p id="summary-date" class="mt-1 text-ink"></p></div>
                            <div><p class="font-bold text-copy">{{ __('Time') }}</p><p id="summary-time" class="mt-1 text-ink"></p></div>
                            <div><p class="font-bold text-copy">{{ __('Location') }}</p><p id="summary-location" class="mt-1 whitespace-pre-line text-ink"></p></div>
                            <div><p class="font-bold text-copy">{{ __('Participants') }}</p><p id="summary-participants" class="mt-1 text-ink"></p></div>
                            <div><p class="font-bold text-copy">{{ __('Skill Level') }}</p><p id="summary-skill" class="mt-1 text-ink"></p></div>
                            <div><p class="font-bold text-copy">{{ __('Participant Fee') }}</p><p id="summary-fee" class="mt-1 text-ink"></p></div>
                        </div>
                        <div class="mt-8 rounded-[18px] border border-[#b9f0ca] bg-[#edfdf2] p-5">
                            <p class="font-heading text-[16px] font-bold text-[#166534]">{{ __('Public Match') }}</p>
                            <p class="mt-2 text-[13px] leading-6 text-[#166534]">{{ __('Your match will be visible to all users. Anyone can join until the match is full.') }}</p>
                        </div>
                    </aside>
                </section>
            @endif
        </main>
    </div>

    <script>
        const bookings = @json($bookingOptions);
        const bookingInputs = Array.from(document.querySelectorAll('input[name="booking_id"]'));
        const bookingPanels = Array.from(document.querySelectorAll('[data-booking-panel]'));
        const bookingTriggers = Array.from(document.querySelectorAll('[data-booking-trigger]'));
        const createMatchSections = document.getElementById('create-match-sections');
        const changeBookingButton = document.getElementById('change-booking-button');
        const fieldImage = document.getElementById('field-image');
        const currencyFormatter = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });

        const inputs = {
            title: document.getElementById('title'),
            description: document.getElementById('description'),
            max: document.getElementById('max_participants'),
            skill: document.getElementById('skill_level'),
            fee: document.getElementById('participant_fee'),
        };

        function selectedBooking() {
            const selectedInput = bookingInputs.find((input) => input.checked);

            if (!selectedInput) {
                return null;
            }

            return bookings.find((booking) => Number(booking.id) === Number(selectedInput.value)) || null;
        }

        function setExpandedBooking(bookingId) {
            bookingPanels.forEach((panel) => {
                panel.hidden = Number(panel.dataset.bookingPanel) !== Number(bookingId);
            });

            const matchingInput = bookingInputs.find((input) => Number(input.value) === Number(bookingId));

            if (matchingInput) {
                matchingInput.checked = true;
            }

            document.querySelectorAll('.booking-card').forEach((card) => {
                card.classList.toggle('is-selected', Number(card.dataset.bookingTrigger) === Number(bookingId));
            });

            if (createMatchSections) {
                createMatchSections.hidden = false;
            }
        }

        function resetBookingPanels() {
            bookingPanels.forEach((panel) => {
                panel.hidden = false;
            });

            document.querySelectorAll('.booking-card').forEach((card) => {
                card.classList.remove('is-selected');
            });

            if (createMatchSections) {
                createMatchSections.hidden = true;
            }
        }

        function updateCount() {
            const counter = document.getElementById('description-count');
            if (counter && inputs.description) {
                counter.textContent = `${inputs.description.value.length} / ${inputs.description.maxLength}`;
            }
        }

        function updateSummary() {
            const booking = selectedBooking();

            document.getElementById('field-display').value = booking?.field || '';
            document.getElementById('sport-display').value = booking?.sport_label || '';
            document.getElementById('date-display').value = booking?.date || '';
            document.getElementById('start-display').value = booking?.start_time || '';
            document.getElementById('end-display').value = booking?.end_time || '';
            document.getElementById('organizer-display').value = booking?.organizer || '';
            document.getElementById('address-display').value = booking?.location || '';
            if (fieldImage) {
                fieldImage.src = booking?.image_url || "{{ asset('landing/football-stadium.jpg') }}";
            }

            document.getElementById('summary-booking').textContent = booking ? `${booking.field || '-'} • ${booking.date || '-'} • ${booking.start_time || '-'}${booking.end_time ? ` - ${booking.end_time}` : ''}` : '-';
            document.getElementById('summary-title').textContent = inputs.title.value || booking?.default_title || '-';
            document.getElementById('summary-sport').textContent = booking?.sport_label || '-';
            document.getElementById('summary-date').textContent = booking?.date || '-';
            document.getElementById('summary-time').textContent = booking?.start_time && booking?.end_time ? `${booking.start_time} - ${booking.end_time}${booking.duration_label ? ` (${booking.duration_label})` : ''}` : '-';
            document.getElementById('summary-location').textContent = booking ? `${booking.field || '-'}${booking.location ? `\n${booking.location}` : ''}` : '-';
            document.getElementById('summary-participants').textContent = `1 / ${inputs.max.value || 0}`;
            document.getElementById('summary-skill').textContent = inputs.skill.value || '{{ __('All Levels') }}';
            document.getElementById('summary-fee').textContent = currencyFormatter.format(Number(inputs.fee.value || 0));

            updateCount();
        }

        function applyBookingDefaults(force = false) {
            const booking = selectedBooking();

            if (!booking) {
                updateSummary();
                return;
            }

            if (force || !inputs.title.value) {
                inputs.title.value = booking.default_title || '';
            }

            if (force || !inputs.description.value) {
                inputs.description.value = booking.default_description || '';
            }

            updateSummary();
        }

        bookingTriggers.forEach((trigger) => trigger.addEventListener('click', () => {
            const bookingId = Number(trigger.dataset.bookingTrigger);

            setExpandedBooking(bookingId);
            applyBookingDefaults(true);
        }));

        bookingInputs.forEach((input) => input.addEventListener('change', () => {
            setExpandedBooking(Number(input.value));
            applyBookingDefaults(true);
        }));

        changeBookingButton?.addEventListener('click', () => {
            resetBookingPanels();
        });

        const selectedInput = bookingInputs.find((input) => input.checked);

        if (selectedInput) {
            setExpandedBooking(Number(selectedInput.value));
        } else {
            resetBookingPanels();
        }

        Object.values(inputs).forEach((input) => input?.addEventListener('input', updateSummary));

        if (selectedInput) {
            applyBookingDefaults();
        } else {
            updateSummary();
        }
    </script>
</body>
</html>

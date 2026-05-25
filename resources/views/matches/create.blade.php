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
            'search' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="11" cy="11" r="6"/><path d="M20 20l-4.2-4.2"/></svg>',
            'plus-square' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3.5" y="3.5" width="17" height="17" rx="2.5"/><path d="M12 8v8M8 12h8"/></svg>',
            'chevron-right' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M9 6l6 6-6 6"/></svg>',
            'users' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M17 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2"/><path d="M16 3.1a4 4 0 010 7.8"/><path d="M9 11a4 4 0 100-8 4 4 0 000 8z"/></svg>',
            'upload' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 16V4"/><path d="M7 9l5-5 5 5"/><path d="M4 18h16"/></svg>',
        ];

        return new \Illuminate\Support\HtmlString($icons[$name] ?? $icons['home']);
    };
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
            border-radius: 0.95rem;
            background: #fff;
            padding: 0.95rem 1rem;
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
        .drop-field {
            position: relative;
            overflow: hidden;
        }
        .drop-field input[type=file] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
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
                <p class="mt-2 text-[14px] leading-6 text-slate-300">{{ __('Choose a confirmed booking, build two teams, and publish your match to the community.') }}</p>
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
                <a href="{{ route('matches.my') }}" class="sidebar-nav-link flex items-center gap-4 rounded-2xl px-5 py-4 font-medium transition">
                    <span class="h-6 w-6">{!! $iconSvg('users') !!}</span>
                    <span>{{ __('My Matches') }}</span>
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
        </aside>

        <main class="bg-[linear-gradient(180deg,#fbfcff_0%,#f7f8fc_100%)] px-8 py-8 lg:px-10 xl:px-12">
            <section class="flex items-start gap-4">
                <a href="{{ route('matches.index') }}" class="mt-1 text-[28px] text-ink">&larr;</a>
                <div>
                    <h1 class="font-heading text-[42px] font-bold text-ink">{{ __('Create Public Match') }}</h1>
                    <p class="mt-2 text-[18px] text-copy">{{ __('Pick one confirmed booking, define both teams, and publish a match other players can join.') }}</p>
                </div>
            </section>

            @if ($bookingOptions->isEmpty())
                <section class="mt-8 rounded-[28px] border border-dashed border-line bg-white px-6 py-10 text-center text-[18px] text-copy shadow-panel">
                    {{ __('You need at least one confirmed booking that is not already linked to a match before creating a public match.') }}
                </section>
            @else
                <section class="mt-8 grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px] xl:items-start">
                    <form action="{{ route('matches.store') }}" method="POST" enctype="multipart/form-data" novalidate class="surface-card overflow-hidden rounded-[28px] bg-white">
                        @csrf

                        <div class="surface-section p-6">
                            <h2 class="font-heading text-[19px] font-bold text-ink">{{ __('1. Select Booking') }}</h2>
                            <p class="mt-2 max-w-[760px] text-[15px] leading-7 text-copy">{{ __('Link the match to one of your confirmed bookings that does not already have a public match.') }}</p>
                            <div class="mt-5">
                                <label>
                                    <span class="label required">{{ __('Confirmed Booking') }}</span>
                                    <select id="booking_id" name="booking_id" class="field {{ $errors->has('booking_id') ? 'is-error' : '' }}">
                                        <option value="">{{ __('Choose a booking') }}</option>
                                        @foreach ($bookingOptions as $option)
                                            <option value="{{ $option['id'] }}" @selected($selectedBookingId === (int) $option['id'])>
                                                {{ $option['field'] }} • {{ $option['date'] }} • {{ $option['start_time'] }}{{ $option['end_time'] ? ' - ' . $option['end_time'] : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-forms.error field="booking_id" />
                                </label>
                            </div>
                            <div class="mt-6 grid gap-4 md:grid-cols-[180px_minmax(0,1fr)_minmax(0,1fr)]">
                                <div class="overflow-hidden rounded-[22px] bg-[#eef2f9] shadow-soft">
                                    <img id="field-image" src="{{ asset('landing/football-stadium.jpg') }}" alt="{{ __('Field image') }}" class="h-full min-h-[140px] w-full object-cover">
                                </div>
                                <div class="grid gap-4 sm:grid-cols-2 md:col-span-2">
                                    <label>
                                        <span class="label">{{ __('Field Name') }}</span>
                                        <input id="field-display" readonly class="field">
                                    </label>
                                    <label>
                                        <span class="label">{{ __('Sport Type') }}</span>
                                        <input id="sport-display" readonly class="field">
                                    </label>
                                    <label>
                                        <span class="label">{{ __('Booking Date') }}</span>
                                        <input id="date-display" readonly class="field">
                                    </label>
                                    <label>
                                        <span class="label">{{ __('Time Slot') }}</span>
                                        <input id="time-display" readonly class="field">
                                    </label>
                                    <label class="sm:col-span-2">
                                        <span class="label">{{ __('Location') }}</span>
                                        <input id="location-display" readonly class="field">
                                    </label>
                                    <label class="sm:col-span-2">
                                        <span class="label">{{ __('Organizer Name') }}</span>
                                        <input id="organizer-display" readonly class="field">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="surface-section p-6">
                            <h2 class="font-heading text-[19px] font-bold text-ink">{{ __('2. Match Setup') }}</h2>
                            <p class="mt-2 max-w-[760px] text-[15px] leading-7 text-copy">{{ __('Fill in the details players will see before they join. Team size is used to calculate total match capacity automatically.') }}</p>

                            <div class="mt-6 grid gap-5 md:grid-cols-2">
                                <label class="md:col-span-2">
                                    <span class="label required">{{ __('Match Title') }}</span>
                                    <input id="title" name="title" maxlength="150" value="{{ old('title') }}" class="field {{ $errors->has('title') ? 'is-error' : '' }}">
                                    <x-forms.error field="title" />
                                </label>
                                <label class="md:col-span-2">
                                    <span class="label required">{{ __('Match Description') }}</span>
                                    <textarea id="description" name="description" maxlength="500" rows="5" class="field {{ $errors->has('description') ? 'is-error' : '' }}">{{ old('description') }}</textarea>
                                    <span id="description-count" class="mt-1 block text-right text-[12px] text-copy">0 / 500</span>
                                    <x-forms.error field="description" />
                                </label>
                                <label>
                                    <span class="label required">{{ __('Team A Name') }}</span>
                                    <input id="team_a_name" name="team_a_name" maxlength="100" value="{{ old('team_a_name') }}" class="field {{ $errors->has('team_a_name') ? 'is-error' : '' }}">
                                    <x-forms.error field="team_a_name" />
                                </label>
                                <label>
                                    <span class="label required">{{ __('Team B Name') }}</span>
                                    <input id="team_b_name" name="team_b_name" maxlength="100" value="{{ old('team_b_name') }}" class="field {{ $errors->has('team_b_name') ? 'is-error' : '' }}">
                                    <x-forms.error field="team_b_name" />
                                </label>
                                <label>
                                    <span class="label">{{ __('Team A Logo') }}</span>
                                    <div class="drop-field field {{ $errors->has('team_a_logo') ? 'is-error' : '' }}">
                                        <div class="flex items-center gap-3">
                                            <span class="h-5 w-5 text-copy">{!! $iconSvg('upload') !!}</span>
                                            <span id="team_a_logo_name" class="text-[14px] text-copy">{{ __('Upload an optional logo') }}</span>
                                        </div>
                                        <input id="team_a_logo" type="file" name="team_a_logo" accept=".jpg,.jpeg,.png,.webp">
                                    </div>
                                    <x-forms.error field="team_a_logo" />
                                </label>
                                <label>
                                    <span class="label">{{ __('Team B Logo') }}</span>
                                    <div class="drop-field field {{ $errors->has('team_b_logo') ? 'is-error' : '' }}">
                                        <div class="flex items-center gap-3">
                                            <span class="h-5 w-5 text-copy">{!! $iconSvg('upload') !!}</span>
                                            <span id="team_b_logo_name" class="text-[14px] text-copy">{{ __('Upload an optional logo') }}</span>
                                        </div>
                                        <input id="team_b_logo" type="file" name="team_b_logo" accept=".jpg,.jpeg,.png,.webp">
                                    </div>
                                    <x-forms.error field="team_b_logo" />
                                </label>
                            </div>
                        </div>

                        <div class="surface-section p-6">
                            <h2 class="font-heading text-[19px] font-bold text-ink">{{ __('3. Match Settings') }}</h2>
                            <div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                                <label>
                                    <span class="label required">{{ __('Max Players Per Team') }}</span>
                                    <input id="max_per_team" name="max_per_team" type="number" min="1" max="50" value="{{ old('max_per_team', 5) }}" class="field {{ $errors->has('max_per_team') ? 'is-error' : '' }}">
                                    <x-forms.error field="max_per_team" />
                                </label>
                                <label>
                                    <span class="label">{{ __('Total Capacity') }}</span>
                                    <input id="max_participants_display" readonly class="field" value="{{ max(0, (int) old('max_per_team', 5) * 2) }}">
                                </label>
                                <label>
                                    <span class="label required">{{ __('Participant Fee') }}</span>
                                    <input id="participant_fee" name="participant_fee" type="number" min="0" step="1000" value="{{ old('participant_fee', 0) }}" class="field {{ $errors->has('participant_fee') ? 'is-error' : '' }}">
                                    <x-forms.error field="participant_fee" />
                                </label>
                                <label>
                                    <span class="label required">{{ __('Gender') }}</span>
                                    <select id="gender" name="gender" class="field {{ $errors->has('gender') ? 'is-error' : '' }}">
                                        @foreach ($genderOptions as $gender)
                                            <option value="{{ $gender }}" @selected(old('gender', 'Open') === $gender)>{{ __($gender) }}</option>
                                        @endforeach
                                    </select>
                                    <x-forms.error field="gender" />
                                </label>
                                <label>
                                    <span class="label required">{{ __('Skill Level') }}</span>
                                    <select id="skill_level" name="skill_level" class="field {{ $errors->has('skill_level') ? 'is-error' : '' }}">
                                        @foreach ($skillLevelOptions as $level)
                                            <option value="{{ $level }}" @selected(old('skill_level', 'All Levels') === $level)>{{ __($level) }}</option>
                                        @endforeach
                                    </select>
                                    <x-forms.error field="skill_level" />
                                </label>
                                <label>
                                    <span class="label required">{{ __('Match Type') }}</span>
                                    <select id="match_type" name="match_type" class="field {{ $errors->has('match_type') ? 'is-error' : '' }}">
                                        @foreach ($matchTypeOptions as $type)
                                            <option value="{{ $type }}" @selected(old('match_type', 'Friendly') === $type)>{{ __($type) }}</option>
                                        @endforeach
                                    </select>
                                    <x-forms.error field="match_type" />
                                </label>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex items-center justify-between gap-4">
                                <a href="{{ route('matches.index') }}" class="rounded-xl border border-line px-7 py-3 text-[14px] font-bold text-copy">{{ __('Cancel') }}</a>
                                <button type="submit" class="rounded-xl bg-[#16a34a] px-7 py-3 text-[14px] font-bold text-white shadow-[0_12px_24px_rgba(22,163,74,.24)]">{{ __('Create Match') }}</button>
                            </div>
                        </div>
                    </form>

                    <aside class="surface-card rounded-[28px] bg-white p-6">
                        <h2 class="font-heading text-[19px] font-bold text-ink">{{ __('Match Summary') }}</h2>
                        <div class="mt-6 space-y-5 text-[14px]">
                            <div>
                                <p class="font-bold text-copy">{{ __('Linked Booking') }}</p>
                                <p id="summary-booking" class="mt-1 text-ink">-</p>
                            </div>
                            <div>
                                <p class="font-bold text-copy">{{ __('Match Title') }}</p>
                                <p id="summary-title" class="mt-1 text-ink">-</p>
                            </div>
                            <div>
                                <p class="font-bold text-copy">{{ __('Teams') }}</p>
                                <p id="summary-teams" class="mt-1 text-ink">-</p>
                            </div>
                            <div>
                                <p class="font-bold text-copy">{{ __('Location') }}</p>
                                <p id="summary-location" class="mt-1 whitespace-pre-line text-ink">-</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="font-bold text-copy">{{ __('Players') }}</p>
                                    <p id="summary-capacity" class="mt-1 text-ink">0</p>
                                </div>
                                <div>
                                    <p class="font-bold text-copy">{{ __('Fee') }}</p>
                                    <p id="summary-fee" class="mt-1 text-ink">Rp 0</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <p class="font-bold text-copy">{{ __('Gender') }}</p>
                                    <p id="summary-gender" class="mt-1 text-ink">-</p>
                                </div>
                                <div>
                                    <p class="font-bold text-copy">{{ __('Skill') }}</p>
                                    <p id="summary-skill" class="mt-1 text-ink">-</p>
                                </div>
                                <div>
                                    <p class="font-bold text-copy">{{ __('Type') }}</p>
                                    <p id="summary-type" class="mt-1 text-ink">-</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-8 rounded-[18px] border border-[#b9f0ca] bg-[#edfdf2] p-5">
                            <p class="font-heading text-[16px] font-bold text-[#166534]">{{ __('Public Match') }}</p>
                            <p class="mt-2 text-[13px] leading-6 text-[#166534]">{{ __('Players will see both teams, the slot balance on each side, and the full participant fee before they request to join.') }}</p>
                        </div>
                    </aside>
                </section>
            @endif
        </main>
    </div>

    <script>
        const bookings = @json($bookingOptions);
        const currencyFormatter = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });
        const bookingSelect = document.getElementById('booking_id');
        const description = document.getElementById('description');
        const maxPerTeam = document.getElementById('max_per_team');
        const titleField = document.getElementById('title');
        const teamAName = document.getElementById('team_a_name');
        const teamBName = document.getElementById('team_b_name');
        const participantFee = document.getElementById('participant_fee');
        const gender = document.getElementById('gender');
        const skillLevel = document.getElementById('skill_level');
        const matchType = document.getElementById('match_type');
        const teamALogo = document.getElementById('team_a_logo');
        const teamBLogo = document.getElementById('team_b_logo');

        function currentBooking() {
            return bookings.find((booking) => Number(booking.id) === Number(bookingSelect?.value || 0)) || null;
        }

        function updateFileName(input, labelId) {
            const label = document.getElementById(labelId);
            if (!label || !input) {
                return;
            }

            label.textContent = input.files?.[0]?.name || '{{ __('Upload an optional logo') }}';
        }

        function updateDescriptionCount() {
            const counter = document.getElementById('description-count');
            if (!counter || !description) {
                return;
            }

            counter.textContent = `${description.value.length} / ${description.maxLength}`;
        }

        function updateCapacity() {
            const total = Math.max(0, Number(maxPerTeam?.value || 0) * 2);
            const display = document.getElementById('max_participants_display');
            const summary = document.getElementById('summary-capacity');

            if (display) {
                display.value = total;
            }

            if (summary) {
                summary.textContent = total ? `2 × ${maxPerTeam.value || 0} = ${total}` : '0';
            }
        }

        function updateBookingReadOnly() {
            const booking = currentBooking();
            document.getElementById('field-display').value = booking?.field || '';
            document.getElementById('sport-display').value = booking?.sport_label || '';
            document.getElementById('date-display').value = booking?.date || '';
            document.getElementById('time-display').value = booking?.start_time && booking?.end_time ? `${booking.start_time} - ${booking.end_time}${booking.duration_label ? ` (${booking.duration_label})` : ''}` : '';
            document.getElementById('location-display').value = booking?.location || '';
            document.getElementById('organizer-display').value = booking?.organizer || '';
            document.getElementById('field-image').src = booking?.image_url || "{{ asset('landing/football-stadium.jpg') }}";
        }

        function updateSummary() {
            const booking = currentBooking();

            document.getElementById('summary-booking').textContent = booking
                ? `${booking.field || '-'} • ${booking.date || '-'} • ${booking.start_time || '-'}${booking.end_time ? ` - ${booking.end_time}` : ''}`
                : '-';
            document.getElementById('summary-title').textContent = titleField?.value || '-';
            document.getElementById('summary-teams').textContent = teamAName?.value && teamBName?.value
                ? `${teamAName.value} vs ${teamBName.value}`
                : '-';
            document.getElementById('summary-location').textContent = booking
                ? `${booking.field || '-'}${booking.location ? `\n${booking.location}` : ''}`
                : '-';
            document.getElementById('summary-fee').textContent = currencyFormatter.format(Number(participantFee?.value || 0));
            document.getElementById('summary-gender').textContent = gender?.value || '-';
            document.getElementById('summary-skill').textContent = skillLevel?.value || '-';
            document.getElementById('summary-type').textContent = matchType?.value || '-';

            updateCapacity();
            updateDescriptionCount();
            updateBookingReadOnly();
        }

        bookingSelect?.addEventListener('change', updateSummary);
        description?.addEventListener('input', updateSummary);
        maxPerTeam?.addEventListener('input', updateSummary);
        titleField?.addEventListener('input', updateSummary);
        teamAName?.addEventListener('input', updateSummary);
        teamBName?.addEventListener('input', updateSummary);
        participantFee?.addEventListener('input', updateSummary);
        gender?.addEventListener('change', updateSummary);
        skillLevel?.addEventListener('change', updateSummary);
        matchType?.addEventListener('change', updateSummary);
        teamALogo?.addEventListener('change', () => updateFileName(teamALogo, 'team_a_logo_name'));
        teamBLogo?.addEventListener('change', () => updateFileName(teamBLogo, 'team_b_logo_name'));

        updateSummary();
        updateFileName(teamALogo, 'team_a_logo_name');
        updateFileName(teamBLogo, 'team_b_logo_name');
    </script>
</body>
</html>

@php
    $slotRange = function ($booking): string {
        $slots = $booking->bookedSlots
            ->pluck('timeSlot')
            ->filter()
            ->sortBy('start_time')
            ->values();

        return $slots->isNotEmpty()
            ? substr($slots->first()->start_time, 0, 5) . ' - ' . substr($slots->last()->end_time, 0, 5)
            : __('Time not available');
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Public Matches') }} - MatchPoint</title>
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
                        ink: '#151a3b',
                        copy: '#676d92',
                        line: '#e9ebf5',
                        indigoDeep: '#5542d9',
                    },
                    fontFamily: {
                        heading: ['Outfit', 'sans-serif'],
                        body: ['DM Sans', 'sans-serif'],
                    },
                    boxShadow: {
                        panel: '0 20px 44px rgba(34,43,84,.08)',
                    },
                },
            },
        }
    </script>
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-[linear-gradient(180deg,#ffffff_0%,#f8f6ff_54%,#ffffff_100%)] text-ink">
    <header class="border-b border-[#ede9ff] bg-white/95">
        <div class="flex w-full items-center justify-between px-5 py-4 md:px-10 xl:px-16 2xl:px-24">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('landing/matchpoint-logo.png') }}" alt="{{ __('MatchPoint logo') }}" class="h-10 w-10 object-contain">
                <span class="font-heading text-[22px] font-bold tracking-[0.12em] text-[#352782]">{{ __('MATCHPOINT') }}</span>
            </a>
            @auth
                <div class="shrink-0">
                    @include('partials.locale-switcher')
                </div>
            @else
                <nav class="flex items-center gap-4 text-[14px] font-medium text-[#4f5579]">
                    <a href="{{ route('fields.index') }}" class="transition hover:text-indigoDeep">{{ __('Browse Fields') }}</a>
                    <a href="{{ route('matches.index') }}" class="text-indigoDeep">{{ __('Matches') }}</a>
                    <a href="{{ route('login') }}" class="rounded-full bg-[#5a38d6] px-5 py-3 font-semibold text-white">{{ __('Login') }}</a>
                    @include('partials.locale-switcher')
                </nav>
            @endauth
        </div>
    </header>

    <main class="w-full px-5 py-8 md:px-10 xl:px-16 2xl:px-24">
        @auth
            <div class="mb-5">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-full border border-line bg-white px-4 py-2 text-[14px] font-semibold text-copy shadow-[0_10px_24px_rgba(34,43,84,.06)] transition hover:bg-[#f8f9fd] hover:text-ink">
                    <span class="text-[18px] leading-none">&larr;</span>
                    <span>{{ __('Back to Dashboard') }}</span>
                </a>
            </div>
        @endauth

        @if (session('status'))
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif

        <section class="rounded-[30px] border border-line bg-white p-7 shadow-panel">
            <p class="text-[14px] font-bold uppercase tracking-[0.18em] text-[#5a38d6]">{{ __('Public Matches') }}</p>
            <div class="mt-3 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <h1 class="font-heading text-[42px] font-bold text-ink">{{ __('Find a public match to join') }}</h1>
                    <p class="mt-3 max-w-[820px] text-[17px] leading-8 text-copy">{{ __('Browse open matches linked to confirmed bookings, compare team slots, and submit your join request with the full participant fee.') }}</p>
                </div>
                @auth
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('matches.my') }}" class="inline-flex justify-center rounded-xl border border-[#d9d8ff] px-5 py-4 text-[14px] font-bold text-[#4f46e5] transition hover:bg-[#f7f5ff]">{{ __('My Matches') }}</a>
                        <a href="{{ route('matches.create') }}" class="inline-flex justify-center rounded-xl bg-[#5a38d6] px-6 py-4 font-bold text-white">{{ __('Create Public Match') }}</a>
                    </div>
                @endauth
            </div>
        </section>

        <section class="mt-6 grid gap-5 xl:grid-cols-2 2xl:grid-cols-3">
            @forelse ($matches as $match)
                @php
                    $booking = $match->booking;
                    $teamAReserved = $match->teamACount();
                    $teamBReserved = $match->teamBCount();
                    $teamMax = max(0, (int) ($match->max_per_team ?? 0));
                    $teamARemaining = max(0, $teamMax - $teamAReserved);
                    $teamBRemaining = max(0, $teamMax - $teamBReserved);
                    $userParticipant = auth()->check() ? $match->participants->firstWhere('user_id', auth()->id()) : null;
                    $isOrganizer = auth()->check() && $match->isCreator(auth()->id());
                    $isFull = $teamMax > 0 && $teamAReserved >= $teamMax && $teamBReserved >= $teamMax;
                @endphp
                <article class="overflow-hidden rounded-[30px] border border-line bg-white shadow-panel">
                    <img src="{{ $booking?->field?->image_url ? url($booking->field->image_url) : asset('landing/football-stadium.jpg') }}" alt="{{ $booking?->field?->name }}" class="h-[220px] w-full object-cover">
                    <div class="p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[13px] font-bold uppercase tracking-[0.12em] text-[#5a38d6]">{{ __($booking?->field?->sport_type ?? 'Match') }}</p>
                                <h2 class="mt-2 font-heading text-[30px] font-bold leading-tight text-ink">{{ $match->title }}</h2>
                                <p class="mt-2 text-[16px] text-copy">{{ $booking?->field?->name }} • {{ $booking?->field?->location }}</p>
                            </div>
                            <span class="rounded-full bg-[#eaf9ef] px-4 py-2 text-[13px] font-bold text-[#16a34a]">{{ __($match->status) }}</span>
                        </div>

                        <div class="mt-6 rounded-[24px] bg-[#f7f8fc] p-5">
                            <div class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)] sm:items-center">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $match->team_a_logo ? Storage::url($match->team_a_logo) : asset('landing/football-team-2.png') }}" alt="{{ $match->team_a_name ?: __('Team A') }}" class="h-14 w-14 rounded-full object-contain shadow-[0_8px_18px_rgba(34,43,84,.12)]">
                                    <div>
                                        <p class="font-heading text-[21px] font-bold text-ink">{{ $match->team_a_name ?: __('Team A') }}</p>
                                        <p class="text-[13px] font-semibold text-ink">{{ $teamAReserved }}/{{ $teamMax }}</p>
                                        <p class="text-[13px] text-copy">{{ __(':count slot(s) left', ['count' => $teamARemaining]) }}</p>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <p class="font-heading text-[13px] font-bold uppercase tracking-[0.24em] text-copy">{{ __('VS') }}</p>
                                </div>
                                <div class="flex items-center justify-start gap-3 sm:justify-end">
                                    <div class="text-left sm:text-right">
                                        <p class="font-heading text-[21px] font-bold text-ink">{{ $match->team_b_name ?: __('Team B') }}</p>
                                        <p class="text-[13px] font-semibold text-ink">{{ $teamBReserved }}/{{ $teamMax }}</p>
                                        <p class="text-[13px] text-copy">{{ __(':count slot(s) left', ['count' => $teamBRemaining]) }}</p>
                                    </div>
                                    <img src="{{ $match->team_b_logo ? Storage::url($match->team_b_logo) : asset('landing/football-team-1.png') }}" alt="{{ $match->team_b_name ?: __('Team B') }}" class="h-14 w-14 rounded-full object-contain shadow-[0_8px_18px_rgba(34,43,84,.12)]">
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 grid gap-3 text-[15px] text-[#4f5579] sm:grid-cols-2">
                            <p>{{ __('Date') }}: <span class="font-bold text-ink">{{ $booking?->date?->translatedFormat('j M Y') }}</span></p>
                            <p>{{ __('Time') }}: <span class="font-bold text-ink">{{ $slotRange($booking) }}</span></p>
                            <p>{{ __('Participant Fee') }}: <span class="font-bold text-ink">Rp {{ number_format((float) $match->participant_fee, 0, ',', '.') }}</span></p>
                            <p>{{ __('Players') }}: <span class="font-bold text-ink">{{ $match->filled_slots }} / {{ $match->max_participants }}</span></p>
                            <p>{{ __('Gender') }}: <span class="font-bold text-ink">{{ __($match->gender) }}</span></p>
                            <p>{{ __('Skill Level') }}: <span class="font-bold text-ink">{{ __($match->skill_level) }}</span></p>
                            <p class="sm:col-span-2">{{ __('Match Type') }}: <span class="font-bold text-ink">{{ __($match->match_type) }}</span></p>
                        </div>

                        <div class="mt-6 flex items-center justify-between gap-4">
                            <p class="max-w-[18rem] text-[14px] leading-6 text-copy">{{ __('Created by :name', ['name' => $match->creator?->name ?? __('Organizer')]) }}</p>
                            <div class="flex flex-wrap items-center justify-end gap-3">
                                <a href="{{ route('matches.show', $match) }}" class="rounded-xl border border-[#d9d8ff] px-5 py-3 text-[14px] font-bold text-[#4f46e5] transition hover:bg-[#f7f5ff]">{{ __('View Details') }}</a>
                                @if ($isOrganizer)
                                    <span class="rounded-xl bg-[#eef2ff] px-5 py-3 text-[14px] font-bold text-[#4f46e5]">{{ __('You are the organizer') }}</span>
                                @elseif ($userParticipant)
                                    <span class="rounded-xl bg-[#ecfdf3] px-5 py-3 text-[14px] font-bold text-[#15803d]">{{ __('You are in Team :team', ['team' => $userParticipant->team]) }}</span>
                                @elseif ($isFull)
                                    <span class="rounded-xl bg-[#fff4e8] px-5 py-3 text-[14px] font-bold text-[#f08b20]">{{ __('Match is Full') }}</span>
                                @elseif (auth()->check())
                                    <a href="{{ route('matches.show', $match) }}" class="rounded-xl bg-[#16a34a] px-5 py-3 text-[14px] font-bold text-white shadow-[0_14px_24px_rgba(22,163,74,.18)]">{{ __('Join Match') }}</a>
                                @else
                                    <a href="{{ route('login') }}" class="rounded-xl bg-[#16a34a] px-5 py-3 text-[14px] font-bold text-white shadow-[0_14px_24px_rgba(22,163,74,.18)]">{{ __('Join Match') }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-[28px] border border-dashed border-line bg-white px-6 py-10 text-center text-[18px] text-copy shadow-panel">{{ __('No public matches are open yet.') }}</div>
            @endforelse
        </section>

        <div class="mt-8">{{ $matches->links() }}</div>
    </main>
</body>
</html>

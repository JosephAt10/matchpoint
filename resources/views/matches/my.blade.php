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

    $logoPlaceholder = function (string $name, string $tone = 'green'): array {
        $initials = collect(explode(' ', trim($name)))
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => strtoupper(substr($part, 0, 1)))
            ->implode('');

        return [
            'initials' => $initials ?: 'MP',
            'class' => $tone === 'blue'
                ? 'bg-[linear-gradient(135deg,#dbeafe_0%,#eef2ff_100%)] text-[#1d4ed8]'
                : 'bg-[linear-gradient(135deg,#dcfce7_0%,#ecfdf5_100%)] text-[#15803d]',
        ];
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('My Matches') }} - MatchPoint</title>
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
                    colors: { ink: '#151a3b', copy: '#676d92', line: '#e9ebf5', indigoDeep: '#5542d9' },
                    fontFamily: { heading: ['Outfit', 'sans-serif'], body: ['DM Sans', 'sans-serif'] },
                    boxShadow: { panel: '0 20px 44px rgba(34,43,84,.08)' },
                },
            },
        }
    </script>
    <style>
        *{font-family:'DM Sans',sans-serif}
        h1,h2,h3,h4,h5,h6,.font-heading{font-family:'Outfit',sans-serif}
        .surface-card{border:1px solid #e8ecf5;box-shadow:0 22px 44px rgba(34,43,84,.08)}
        .match-card{border:1px solid #e7ebf4;box-shadow:0 18px 38px rgba(34,43,84,.08)}
        .match-card:hover{transform:translateY(-2px);box-shadow:0 24px 48px rgba(34,43,84,.12)}
        .info-chip{background:#f6f8fc;border:1px solid #edf1f7}
    </style>
</head>
<body class="min-h-screen bg-[linear-gradient(180deg,#ffffff_0%,#f8f6ff_54%,#ffffff_100%)] text-ink">
    <header class="border-b border-[#ede9ff] bg-white/95">
        <div class="flex w-full items-center justify-between px-5 py-4 md:px-10 xl:px-16 2xl:px-24">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('landing/matchpoint-logo.png') }}" alt="{{ __('MatchPoint logo') }}" class="h-10 w-10 object-contain">
                <span class="font-heading text-[22px] font-bold tracking-[0.12em] text-[#352782]">{{ __('MATCHPOINT') }}</span>
            </a>
            <div class="shrink-0">
                @include('partials.locale-switcher')
            </div>
        </div>
    </header>

    <main class="w-full px-5 py-8 md:px-10 xl:px-16 2xl:px-24">
        <div class="mb-5">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-full border border-line bg-white px-4 py-2 text-[14px] font-semibold text-copy shadow-[0_10px_24px_rgba(34,43,84,.06)] transition hover:bg-[#f8f9fd] hover:text-ink">
                <span class="text-[18px] leading-none">&larr;</span>
                <span>{{ __('Back to Dashboard') }}</span>
            </a>
        </div>

        <section class="surface-card rounded-[30px] bg-white p-7">
            <p class="text-[14px] font-bold uppercase tracking-[0.18em] text-[#5a38d6]">{{ __('My Matches') }}</p>
            <h1 class="mt-3 font-heading text-[42px] font-bold text-ink">{{ __('Matches you organize or joined') }}</h1>
            <p class="mt-3 max-w-[820px] text-[17px] leading-8 text-copy">{{ __('Track your public matches, see your team placement, and reopen match details whenever you need them.') }}</p>
        </section>

        <section class="mt-6 grid gap-5 xl:grid-cols-2 2xl:grid-cols-3">
            @forelse ($matches as $match)
                @php
                    $booking = $match->booking;
                    $participant = $match->participants->first();
                    $isOrganizer = $match->isCreator(auth()->id());
                    $teamAPlaceholder = $logoPlaceholder($match->team_a_name ?: __('Team A'));
                    $teamBPlaceholder = $logoPlaceholder($match->team_b_name ?: __('Team B'), 'blue');
                @endphp
                <article class="match-card flex h-full flex-col overflow-hidden rounded-[30px] bg-white transition">
                    <img src="{{ $booking?->field?->image_url ? url($booking->field->image_url) : asset('landing/football-stadium.jpg') }}" alt="{{ $booking?->field?->name }}" class="h-[190px] w-full object-cover">
                    <div class="flex flex-1 flex-col p-6">
                        <div class="-mt-12 mb-5 rounded-[24px] border border-[#e8ecf5] bg-white/96 p-4 shadow-[0_18px_34px_rgba(34,43,84,.10)] backdrop-blur">
                            <div class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)] sm:items-center">
                                <div class="flex items-center gap-3">
                                    @if ($match->team_a_logo)
                                        <img src="{{ Storage::url($match->team_a_logo) }}" alt="{{ $match->team_a_name }}" class="h-14 w-14 rounded-full object-cover shadow-[0_8px_18px_rgba(34,43,84,.12)]">
                                    @else
                                        <div class="flex h-14 w-14 items-center justify-center rounded-full text-sm font-bold {{ $teamAPlaceholder['class'] }}">{{ $teamAPlaceholder['initials'] }}</div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-heading text-[18px] font-bold text-ink">{{ $match->team_a_name }}</p>
                                        <p class="text-[12px] uppercase tracking-[0.18em] text-copy">{{ __('Team A') }}</p>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <span class="inline-flex rounded-full bg-[#f4f5ff] px-3 py-1 text-[12px] font-bold uppercase tracking-[0.18em] text-[#4f46e5]">VS</span>
                                </div>
                                <div class="flex items-center justify-start gap-3 sm:justify-end">
                                    <div class="min-w-0 text-left sm:text-right">
                                        <p class="font-heading text-[18px] font-bold text-ink">{{ $match->team_b_name }}</p>
                                        <p class="text-[12px] uppercase tracking-[0.18em] text-copy">{{ __('Team B') }}</p>
                                    </div>
                                    @if ($match->team_b_logo)
                                        <img src="{{ Storage::url($match->team_b_logo) }}" alt="{{ $match->team_b_name }}" class="h-14 w-14 rounded-full object-cover shadow-[0_8px_18px_rgba(34,43,84,.12)]">
                                    @else
                                        <div class="flex h-14 w-14 items-center justify-center rounded-full text-sm font-bold {{ $teamBPlaceholder['class'] }}">{{ $teamBPlaceholder['initials'] }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[13px] font-bold uppercase tracking-[0.12em] text-[#5a38d6]">{{ __($booking?->field?->sport_type ?? 'Match') }}</p>
                                <h2 class="mt-2 font-heading text-[30px] font-bold leading-tight text-ink">{{ $match->title }}</h2>
                                <p class="mt-2 text-[16px] text-copy">{{ $booking?->field?->name }} • {{ $booking?->field?->location }}</p>
                            </div>
                            <span class="rounded-full bg-[#eaf9ef] px-4 py-2 text-[13px] font-bold text-[#16a34a]">{{ __($match->status) }}</span>
                        </div>

                        <div class="mt-5 grid gap-3 text-[15px] text-[#4f5579]">
                            <div class="info-chip rounded-[18px] px-4 py-3">
                                <p>{{ __('Date') }}: <span class="font-bold text-ink">{{ $booking?->date?->translatedFormat('j M Y') }}</span></p>
                                <p class="mt-2">{{ __('Time') }}: <span class="font-bold text-ink">{{ $slotRange($booking) }}</span></p>
                            </div>
                            <div class="info-chip rounded-[18px] px-4 py-3">
                                <p>{{ __('Players') }}: <span class="font-bold text-ink">{{ $match->filled_slots }} / {{ $match->max_participants }}</span></p>
                                <p class="mt-2">{{ __('Match Type') }}: <span class="font-bold text-ink">{{ __($match->match_type) }}</span></p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            @if ($isOrganizer)
                                <span class="rounded-full bg-[#eef2ff] px-4 py-2 text-[13px] font-bold text-[#4f46e5]">{{ __('Organizer • Team A') }}</span>
                            @elseif ($participant)
                                <span class="rounded-full bg-[#ecfdf3] px-4 py-2 text-[13px] font-bold text-[#15803d]">{{ __('Team :team • :status', ['team' => $participant->team, 'status' => __($participant->status)]) }}</span>
                            @endif
                        </div>

                        <div class="mt-auto flex items-end justify-between gap-4 pt-8">
                            <p class="max-w-[18rem] text-[14px] leading-6 text-copy">{{ __('Created by :name', ['name' => $match->creator?->name ?? __('Organizer')]) }}</p>
                            <a href="{{ route('matches.show', $match) }}" class="inline-flex min-w-[142px] items-center justify-center rounded-2xl bg-[linear-gradient(90deg,#16a34a_0%,#22c55e_100%)] px-5 py-3 text-[14px] font-bold text-white shadow-[0_14px_26px_rgba(34,197,94,.22)]">{{ __('View Details') }}</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="surface-card col-span-full rounded-[28px] border-dashed bg-white px-6 py-10 text-center text-[18px] text-copy">{{ __('You have not created or joined any public matches yet.') }}</div>
            @endforelse
        </section>

        <div class="mt-8">{{ $matches->links() }}</div>
    </main>
</body>
</html>

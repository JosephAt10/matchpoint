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
    <style>*{font-family:'DM Sans',sans-serif}h1,h2,h3,h4,h5,h6,.font-heading{font-family:'Outfit',sans-serif}</style>
</head>
<body class="min-h-screen bg-[linear-gradient(180deg,#ffffff_0%,#f8f6ff_54%,#ffffff_100%)] text-ink">
    <header class="border-b border-[#ede9ff] bg-white/95">
        <div class="flex w-full items-center justify-between px-5 py-4 md:px-10 xl:px-16 2xl:px-24">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('landing/matchpoint-logo.png') }}" alt="{{ __('MatchPoint logo') }}" class="h-10 w-10 object-contain">
                <span class="font-heading text-[22px] font-bold tracking-[0.12em] text-[#352782]">{{ __('MATCHPOINT') }}</span>
            </a>
            <nav class="flex items-center gap-4 text-[14px] font-medium text-[#4f5579]">
                <a href="{{ route('fields.index') }}" class="transition hover:text-indigoDeep">{{ __('Browse Fields') }}</a>
                <a href="{{ route('matches.index') }}" class="transition hover:text-indigoDeep">{{ __('Matches') }}</a>
                <a href="{{ route('matches.my') }}" class="text-indigoDeep">{{ __('My Matches') }}</a>
                <a href="{{ route('matches.create') }}" class="rounded-full bg-[#5a38d6] px-5 py-3 font-semibold text-white">{{ __('Create Match') }}</a>
                @include('partials.locale-switcher')
            </nav>
        </div>
    </header>

    <main class="w-full px-5 py-8 md:px-10 xl:px-16 2xl:px-24">
        <section class="rounded-[30px] border border-line bg-white p-7 shadow-panel">
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

                        <div class="mt-5 space-y-2 text-[15px] text-[#4f5579]">
                            <p>{{ __('Date') }}: <span class="font-bold text-ink">{{ $booking?->date?->translatedFormat('j M Y') }}</span></p>
                            <p>{{ __('Time') }}: <span class="font-bold text-ink">{{ $slotRange($booking) }}</span></p>
                            <p>{{ __('Teams') }}: <span class="font-bold text-ink">{{ $match->team_a_name }} vs {{ $match->team_b_name }}</span></p>
                            <p>{{ __('Players') }}: <span class="font-bold text-ink">{{ $match->filled_slots }} / {{ $match->max_participants }}</span></p>
                        </div>

                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            @if ($isOrganizer)
                                <span class="rounded-full bg-[#eef2ff] px-4 py-2 text-[13px] font-bold text-[#4f46e5]">{{ __('Organizer • Team A') }}</span>
                            @elseif ($participant)
                                <span class="rounded-full bg-[#ecfdf3] px-4 py-2 text-[13px] font-bold text-[#15803d]">{{ __('Team :team • :status', ['team' => $participant->team, 'status' => __($participant->status)]) }}</span>
                            @endif
                        </div>

                        <div class="mt-6 flex items-center justify-between gap-4">
                            <p class="max-w-[18rem] text-[14px] leading-6 text-copy">{{ __('Created by :name', ['name' => $match->creator?->name ?? __('Organizer')]) }}</p>
                            <a href="{{ route('matches.show', $match) }}" class="rounded-xl bg-[#16a34a] px-5 py-3 text-[14px] font-bold text-white shadow-[0_14px_24px_rgba(22,163,74,.18)]">{{ __('View Details') }}</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-[28px] border border-dashed border-line bg-white px-6 py-10 text-center text-[18px] text-copy shadow-panel">{{ __('You have not created or joined any public matches yet.') }}</div>
            @endforelse
        </section>

        <div class="mt-8">{{ $matches->links() }}</div>
    </main>
</body>
</html>

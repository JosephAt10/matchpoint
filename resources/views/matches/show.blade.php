@php
    $teamAReserved = $match->teamACount();
    $teamBReserved = $match->teamBCount();
    $teamARemaining = $match->teamSlotsRemaining('A');
    $teamBRemaining = $match->teamSlotsRemaining('B');

    $placeholder = function (string $name, string $tone = 'green'): array {
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

    $teamAPlaceholder = $placeholder($match->team_a_name ?: __('Team A'));
    $teamBPlaceholder = $placeholder($match->team_b_name ?: __('Team B'), 'blue');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $match->title }} - MatchPoint</title>
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
<body class="min-h-screen bg-[linear-gradient(180deg,#ffffff_0%,#f8f6ff_54%,#ffffff_100%)] text-ink">
    <header class="border-b border-[#ede9ff] bg-white/95">
        <div class="flex w-full items-center justify-between px-5 py-4 md:px-10 xl:px-16 2xl:px-24">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('landing/matchpoint-logo.png') }}" alt="{{ __('MatchPoint logo') }}" class="h-10 w-10 object-contain">
                <span class="font-heading text-[22px] font-bold tracking-[0.12em] text-[#352782]">{{ __('MATCHPOINT') }}</span>
            </a>
            <nav class="flex items-center gap-4 text-[14px] font-medium text-[#4f5579]">
                <a href="{{ route('fields.index') }}" class="transition hover:text-[#5542d9]">{{ __('Browse Fields') }}</a>
                <a href="{{ route('matches.index') }}" class="text-[#5542d9]">{{ __('Matches') }}</a>
                @auth
                    <a href="{{ route('matches.my') }}" class="transition hover:text-[#5542d9]">{{ __('My Matches') }}</a>
                    <a href="{{ route('matches.create') }}" class="rounded-full bg-[#5a38d6] px-5 py-3 font-semibold text-white">{{ __('Create Match') }}</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-full bg-[#5a38d6] px-5 py-3 font-semibold text-white">{{ __('Login') }}</a>
                @endauth
                @include('partials.locale-switcher')
            </nav>
        </div>
    </header>

    <main class="w-full px-5 py-8 md:px-10 xl:px-16 2xl:px-24">
        @if (session('status'))
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px] xl:items-start">
            <article class="overflow-hidden rounded-[30px] border border-line bg-white shadow-panel">
                <img src="{{ $match->booking?->field?->image_url ? url($match->booking->field->image_url) : asset('landing/football-stadium.jpg') }}" alt="{{ $match->booking?->field?->name }}" class="h-[280px] w-full object-cover">
                <div class="p-7">
                    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                        <div>
                            <p class="text-[13px] font-bold uppercase tracking-[0.12em] text-[#5a38d6]">{{ __($match->booking?->field?->sport_type ?? 'Match') }}</p>
                            <h1 class="mt-2 font-heading text-[42px] font-bold leading-tight text-ink">{{ $match->title }}</h1>
                            <p class="mt-3 max-w-[760px] text-[17px] leading-8 text-copy">{{ $match->description }}</p>
                        </div>
                        <span class="rounded-full bg-[#eaf9ef] px-4 py-2 text-[13px] font-bold text-[#16a34a]">{{ __($match->status) }}</span>
                    </div>

                    <div class="mt-8 rounded-[26px] bg-[#f7f8fc] p-6">
                        <div class="grid gap-5 md:grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)] md:items-center">
                            <div class="flex items-center gap-4">
                                @if ($match->team_a_logo)
                                    <img src="{{ Storage::url($match->team_a_logo) }}" alt="{{ $match->team_a_name }}" class="h-20 w-20 rounded-full object-cover shadow-[0_10px_24px_rgba(34,43,84,.14)]">
                                @else
                                    <div class="flex h-20 w-20 items-center justify-center rounded-full text-lg font-bold {{ $teamAPlaceholder['class'] }}">{{ $teamAPlaceholder['initials'] }}</div>
                                @endif
                                <div>
                                    <p class="font-heading text-[28px] font-bold text-ink">{{ $match->team_a_name ?: __('Team A') }}</p>
                                    <p class="text-[15px] text-copy">{{ __(':count of :max spots reserved', ['count' => $teamAReserved, 'max' => $match->max_per_team]) }}</p>
                                    <p class="mt-1 text-[14px] font-semibold text-[#16a34a]">{{ __(':count slot(s) left', ['count' => $teamARemaining]) }}</p>
                                    <p class="mt-1 text-[12px] font-medium text-[#2563eb]">{{ __('1 slot reserved for organizer') }}</p>
                                </div>
                            </div>
                            <div class="text-center">
                                <p class="font-heading text-[14px] font-bold uppercase tracking-[0.28em] text-copy">VS</p>
                            </div>
                            <div class="flex items-center justify-start gap-4 md:justify-end">
                                <div class="text-left md:text-right">
                                    <p class="font-heading text-[28px] font-bold text-ink">{{ $match->team_b_name ?: __('Team B') }}</p>
                                    <p class="text-[15px] text-copy">{{ __(':count of :max spots reserved', ['count' => $teamBReserved, 'max' => $match->max_per_team]) }}</p>
                                    <p class="mt-1 text-[14px] font-semibold text-[#16a34a]">{{ __(':count slot(s) left', ['count' => $teamBRemaining]) }}</p>
                                </div>
                                @if ($match->team_b_logo)
                                    <img src="{{ Storage::url($match->team_b_logo) }}" alt="{{ $match->team_b_name }}" class="h-20 w-20 rounded-full object-cover shadow-[0_10px_24px_rgba(34,43,84,.14)]">
                                @else
                                    <div class="flex h-20 w-20 items-center justify-center rounded-full text-lg font-bold {{ $teamBPlaceholder['class'] }}">{{ $teamBPlaceholder['initials'] }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 grid gap-4 text-[15px] text-copy md:grid-cols-2">
                        <div class="rounded-[20px] border border-line bg-white p-5">
                            <p class="font-bold text-ink">{{ __('Field') }}</p>
                            <p class="mt-2">{{ $match->booking?->field?->name }}</p>
                            <p class="mt-1">{{ $match->booking?->field?->location }}</p>
                        </div>
                        <div class="rounded-[20px] border border-line bg-white p-5">
                            <p class="font-bold text-ink">{{ __('Schedule') }}</p>
                            <p class="mt-2">{{ $match->booking?->date?->translatedFormat('j M Y') }}</p>
                            <p class="mt-1">{{ $slotRange }}</p>
                        </div>
                        <div class="rounded-[20px] border border-line bg-white p-5">
                            <p class="font-bold text-ink">{{ __('Entry Fee') }}</p>
                            <p class="mt-2">Rp {{ number_format((float) $match->participant_fee, 0, ',', '.') }}</p>
                            <p class="mt-1">{{ __('Full fee paid when joining') }}</p>
                        </div>
                        <div class="rounded-[20px] border border-line bg-white p-5">
                            <p class="font-bold text-ink">{{ __('Match Rules') }}</p>
                            <p class="mt-2">{{ __('Gender') }}: {{ __($match->gender) }}</p>
                            <p class="mt-1">{{ __('Skill Level') }}: {{ __($match->skill_level) }}</p>
                            <p class="mt-1">{{ __('Match Type') }}: {{ __($match->match_type) }}</p>
                        </div>
                    </div>
                </div>
            </article>

            <aside class="rounded-[30px] border border-line bg-white p-6 shadow-panel">
                <h2 class="font-heading text-[28px] font-bold text-ink">{{ __('Join This Match') }}</h2>
                <p class="mt-3 text-[15px] leading-7 text-copy">{{ __('Choose a team, upload your payment proof, and wait for verification. A match only accepts new players while its status is Open.') }}</p>

                @if ($activeParticipant && $activeParticipant->isCreator())
                    <div class="mt-6 rounded-[22px] border border-[#d9d8ff] bg-[#f7f5ff] p-5">
                        <p class="font-heading text-[18px] font-bold text-ink">{{ __('You are the organizer') }}</p>
                        <p class="mt-2 text-[14px] leading-6 text-copy">{{ __('Your organizer slot is already confirmed in Team A.') }}</p>
                    </div>
                @elseif ($activeParticipant)
                    <div class="mt-6 rounded-[22px] border border-[#d9d8ff] bg-[#f7f5ff] p-5">
                        <p class="font-heading text-[18px] font-bold text-ink">{{ __('You are in Team :team', ['team' => $activeParticipant->team]) }}</p>
                        <p class="mt-2 text-[14px] leading-6 text-copy">{{ __('Status: :status', ['status' => __($activeParticipant->status)]) }}</p>
                        @if ($activeParticipant->payment?->proof)
                            <a href="{{ Storage::url($activeParticipant->payment->proof) }}" target="_blank" class="mt-4 inline-flex rounded-xl border border-line px-4 py-2 text-[14px] font-semibold text-[#4f46e5]">{{ __('View Payment Proof') }}</a>
                        @endif
                    </div>
                @elseif (! auth()->check())
                    <div class="mt-6 rounded-[22px] border border-line bg-[#f8f9fd] p-5">
                        <p class="font-heading text-[18px] font-bold text-ink">{{ __('Sign in to join') }}</p>
                        <p class="mt-2 text-[14px] leading-6 text-copy">{{ __('Only logged-in users can choose a team and submit the full participant fee.') }}</p>
                        <a href="{{ route('login') }}" class="mt-4 inline-flex rounded-xl bg-[#5a38d6] px-5 py-3 text-[14px] font-bold text-white">{{ __('Sign In') }}</a>
                    </div>
                @elseif (! $match->isOpen() || $isExpired)
                    <div class="mt-6 rounded-[22px] border border-amber-200 bg-amber-50 p-5 text-[14px] leading-6 text-amber-900">
                        {{ $isExpired ? __('This match has already started or finished, so new join requests are closed.') : __('This match is not open for new join requests right now.') }}
                    </div>
                @else
                    <form action="{{ route('matches.join', $match) }}" method="POST" enctype="multipart/form-data" novalidate class="mt-6 space-y-5">
                        @csrf
                        <label>
                            <span class="mb-2 block text-[12px] font-bold text-[#364152]">{{ __('Choose Team') }} *</span>
                            <select name="team" class="field {{ $errors->has('team') ? 'is-error' : '' }}">
                                <option value="">{{ __('Select your team') }}</option>
                                <option value="A" @selected(old('team') === 'A') @disabled($teamARemaining < 1)>{{ $match->team_a_name ?: __('Team A') }} {{ $teamARemaining < 1 ? '(' . __('Full') . ')' : '' }}</option>
                                <option value="B" @selected(old('team') === 'B') @disabled($teamBRemaining < 1)>{{ $match->team_b_name ?: __('Team B') }} {{ $teamBRemaining < 1 ? '(' . __('Full') . ')' : '' }}</option>
                            </select>
                            <x-forms.error field="team" />
                        </label>

                        <label>
                            <span class="mb-2 block text-[12px] font-bold text-[#364152]">{{ __('Upload Payment Proof') }} *</span>
                            <div class="drop-field field {{ $errors->has('proof') ? 'is-error' : '' }}">
                                <div class="flex items-center gap-3">
                                    <span class="h-5 w-5 text-copy">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 16V4"/><path d="M7 9l5-5 5 5"/><path d="M4 18h16"/></svg>
                                    </span>
                                    <span id="proof-name" class="text-[14px] text-copy">{{ __('Upload JPG or PNG up to 5 MB') }}</span>
                                </div>
                                <input id="proof" type="file" name="proof" accept=".jpg,.jpeg,.png">
                            </div>
                            <x-forms.error field="proof" />
                        </label>

                        <div class="rounded-[20px] border border-[#d8e9ff] bg-[#f4f8ff] p-4">
                            <p class="text-[13px] font-semibold uppercase tracking-[0.18em] text-[#2563eb]">{{ __('Payment Summary') }}</p>
                            <p class="mt-3 text-[15px] text-copy">{{ __('Full participant fee') }}</p>
                            <p class="mt-1 font-heading text-[30px] font-bold text-ink">Rp {{ number_format((float) $match->participant_fee, 0, ',', '.') }}</p>
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-[#16a34a] px-6 py-3.5 text-[15px] font-bold text-white shadow-[0_14px_24px_rgba(22,163,74,.18)]">
                            {{ __('Submit Join Request') }}
                        </button>
                    </form>
                @endif
            </aside>
        </section>
    </main>

    <script>
        const proofInput = document.getElementById('proof');
        const proofName = document.getElementById('proof-name');

        proofInput?.addEventListener('change', () => {
            if (!proofName) {
                return;
            }

            proofName.textContent = proofInput.files?.[0]?.name || '{{ __('Upload JPG or PNG up to 5 MB') }}';
        });
    </script>
</body>
</html>

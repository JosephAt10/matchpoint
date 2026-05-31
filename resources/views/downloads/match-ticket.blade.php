<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Match Ticket') }} - MatchPoint</title>
    <style>
        body { margin: 0; background: #f4f6fb; color: #151a3b; font-family: Arial, sans-serif; }
        .page { max-width: 860px; margin: 32px auto; background: #fff; border: 1px solid #e6ebf4; border-radius: 24px; overflow: hidden; }
        .header { background: #162853; color: #fff; padding: 28px 34px; }
        .brand { font-size: 26px; font-weight: 800; letter-spacing: .16em; }
        .subtitle { margin-top: 8px; color: #d7def7; font-size: 14px; }
        .content { padding: 30px 34px; }
        .badge { display: inline-block; padding: 8px 14px; border-radius: 999px; background: #eaf9ef; color: #15803d; font-weight: 700; font-size: 13px; }
        h1 { margin: 18px 0 6px; font-size: 30px; }
        .muted { color: #68718d; }
        .teams { margin-top: 22px; display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 18px; border-radius: 22px; background: #f7f8fc; }
        .team { flex: 1; text-align: center; }
        .team-name { margin-top: 8px; font-size: 20px; font-weight: 800; }
        .chosen { color: #5542d9; font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; }
        .vs { font-weight: 900; color: #68718d; letter-spacing: .18em; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 24px; }
        .box { border: 1px solid #e8edf6; border-radius: 18px; padding: 16px; }
        .label { color: #68718d; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; }
        .value { margin-top: 7px; font-size: 17px; font-weight: 700; }
        .note { margin-top: 26px; padding: 16px; border-radius: 18px; background: #f7f8fc; color: #4f5579; line-height: 1.6; }
        .footer { padding: 20px 34px 30px; color: #68718d; font-size: 13px; }
    </style>
</head>
<body>
    <main class="page">
        <section class="header">
            <div class="brand">MATCHPOINT</div>
            <div class="subtitle">{{ __('Official public match participation ticket') }}</div>
        </section>

        <section class="content">
            <span class="badge">{{ __('Confirmed') }}</span>
            <h1>{{ $match->title }}</h1>
            <p class="muted">{{ $match->booking?->field?->name }} • {{ $match->booking?->field?->location }}</p>

            <div class="teams">
                <div class="team">
                    @if ($participant->team === 'A')<div class="chosen">{{ __('Your Team') }}</div>@endif
                    <div class="team-name">{{ $match->team_a_name ?: __('Team A') }}</div>
                </div>
                <div class="vs">{{ __('VS') }}</div>
                <div class="team">
                    @if ($participant->team === 'B')<div class="chosen">{{ __('Your Team') }}</div>@endif
                    <div class="team-name">{{ $match->team_b_name ?: __('Team B') }}</div>
                </div>
            </div>

            <div class="grid">
                <div class="box"><div class="label">{{ __('Ticket ID') }}</div><div class="value">MP-MCH-{{ str_pad((string) $participant->id, 5, '0', STR_PAD_LEFT) }}</div></div>
                <div class="box"><div class="label">{{ __('Participant') }}</div><div class="value">{{ $participant->user?->name }}</div></div>
                <div class="box"><div class="label">{{ __('Selected Team') }}</div><div class="value">{{ __('Team :team', ['team' => $participant->team]) }}</div></div>
                <div class="box"><div class="label">{{ __('Participation Status') }}</div><div class="value">{{ __($participant->status) }}</div></div>
                <div class="box"><div class="label">{{ __('Date') }}</div><div class="value">{{ $match->booking?->date?->translatedFormat('j M Y') }}</div></div>
                <div class="box"><div class="label">{{ __('Time') }}</div><div class="value">{{ $slotRange }}</div></div>
                <div class="box"><div class="label">{{ __('Participant Fee') }}</div><div class="value">Rp {{ number_format((float) $match->participant_fee, 0, ',', '.') }}</div></div>
                <div class="box"><div class="label">{{ __('Payment Status') }}</div><div class="value">{{ __($participant->payment?->status ?? '-') }}</div></div>
            </div>

            <div class="note">
                {{ __('Show this ticket at the field counter as proof that you are confirmed for this public match.') }}
            </div>
        </section>

        <section class="footer">
            {{ __('Generated at') }}: {{ $generatedAt->translatedFormat('j M Y, H:i') }}
        </section>
    </main>
</body>
</html>

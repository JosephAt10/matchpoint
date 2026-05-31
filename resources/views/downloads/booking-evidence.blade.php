<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Booking Evidence') }} - MatchPoint</title>
    <style>
        body { margin: 0; background: #f4f6fb; color: #151a3b; font-family: Arial, sans-serif; }
        .page { max-width: 820px; margin: 32px auto; background: #fff; border: 1px solid #e6ebf4; border-radius: 24px; overflow: hidden; }
        .header { background: #162853; color: #fff; padding: 28px 34px; }
        .brand { font-size: 26px; font-weight: 800; letter-spacing: .16em; }
        .subtitle { margin-top: 8px; color: #d7def7; font-size: 14px; }
        .content { padding: 30px 34px; }
        .badge { display: inline-block; padding: 8px 14px; border-radius: 999px; background: #eaf9ef; color: #15803d; font-weight: 700; font-size: 13px; }
        h1 { margin: 18px 0 6px; font-size: 30px; }
        .muted { color: #68718d; }
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
            <div class="subtitle">{{ __('Official field booking evidence') }}</div>
        </section>

        <section class="content">
            <span class="badge">{{ __('Confirmed') }}</span>
            <h1>{{ $booking->field->name }}</h1>
            <p class="muted">{{ $booking->field->location }}</p>

            <div class="grid">
                <div class="box"><div class="label">{{ __('Evidence ID') }}</div><div class="value">MP-BKG-{{ str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT) }}</div></div>
                <div class="box"><div class="label">{{ __('Booked By') }}</div><div class="value">{{ $booking->user?->name }}</div></div>
                <div class="box"><div class="label">{{ __('Date') }}</div><div class="value">{{ $booking->date->translatedFormat('j M Y') }}</div></div>
                <div class="box"><div class="label">{{ __('Time') }}</div><div class="value">{{ $slotRange }}</div></div>
                <div class="box"><div class="label">{{ __('Duration') }}</div><div class="value">{{ $booking->bookedSlots->count() }} {{ $booking->bookedSlots->count() === 1 ? __('hour') : __('hours') }}</div></div>
                <div class="box"><div class="label">{{ __('Field Owner') }}</div><div class="value">{{ $booking->field->owner?->name ?? '-' }}</div></div>
                <div class="box"><div class="label">{{ __('Booking Status') }}</div><div class="value">{{ __($booking->status) }}</div></div>
                <div class="box"><div class="label">{{ __('Payment Status') }}</div><div class="value">{{ __($booking->payment?->status ?? '-') }}</div></div>
            </div>

            <div class="note">
                {{ __('Show this evidence at the field counter as proof that your booking has been confirmed by MatchPoint.') }}
            </div>
        </section>

        <section class="footer">
            {{ __('Generated at') }}: {{ $generatedAt->translatedFormat('j M Y, H:i') }}
        </section>
    </main>
</body>
</html>

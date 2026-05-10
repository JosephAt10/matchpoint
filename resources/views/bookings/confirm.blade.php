<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Confirm Your Booking') }} - MatchPoint</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { colors: { ink: '#151a3b', indigoDeep: '#5542d9', copy: '#676d92' }, fontFamily: { heading: ['Outfit', 'sans-serif'], body: ['DM Sans', 'sans-serif'] } } } }
    </script>
    <style>* { font-family: 'DM Sans', sans-serif; } h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="min-h-screen bg-[linear-gradient(180deg,#ffffff_0%,#f8f6ff_50%,#ffffff_100%)] text-ink">
    <header class="border-b border-[#ede9ff] bg-white/95">
        <div class="flex w-full items-center justify-between px-5 py-4 md:px-10 xl:px-16 2xl:px-24">
            <a href="{{ route('home') }}" class="font-heading text-[22px] font-bold tracking-[0.12em] text-[#352782]">{{ __('MATCHPOINT') }}</a>
            <nav class="flex items-center gap-4 text-[14px] font-medium text-[#4f5579]">
                <a href="{{ route('fields.index') }}" class="transition hover:text-indigoDeep">{{ __('Browse Fields') }}</a>
                <a href="{{ route('dashboard') }}" class="transition hover:text-indigoDeep">{{ __('Dashboard') }}</a>
                @include('partials.locale-switcher')
            </nav>
        </div>
    </header>

    <main class="mx-auto w-full max-w-5xl px-5 py-8 md:px-8">
        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                {{ $errors->first() }}
            </div>
        @endif

        <a href="{{ route('fields.show', ['field' => $field, 'date' => $bookingDate->toDateString()]) }}" class="mb-6 inline-flex items-center gap-2 text-[14px] font-medium text-[#4f5579] transition hover:text-indigoDeep">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
            </svg>
            {{ __('Back to Field') }}
        </a>

        <section class="grid gap-6 lg:grid-cols-[1fr_380px]">
            <article class="rounded-xl border border-[#ebe7fb] bg-white p-6 shadow-[0_18px_44px_rgba(86,75,165,0.08)] md:p-8">
                <p class="text-[13px] font-bold uppercase tracking-[0.12em] text-[#5a38d6]">{{ __('Step 2 of 2') }}</p>
                <h1 class="mt-3 font-heading text-[32px] font-bold text-ink">{{ __('Confirm Your Booking') }}</h1>
                <p class="mt-2 text-[15px] leading-7 text-copy">{{ __('Review the booking details, then upload your 50% down payment proof to finish the request.') }}</p>

                <div class="mt-7 divide-y divide-[#eceaf5] rounded-xl border border-[#eceaf5] px-4 text-[15px]">
                    <div class="flex items-center justify-between gap-4 py-4"><span class="text-copy">{{ __('Venue') }}</span><span class="font-bold text-ink">{{ $field->name }}</span></div>
                    <div class="flex items-center justify-between gap-4 py-4"><span class="text-copy">{{ __('Location') }}</span><span class="font-bold text-ink">{{ $field->location }}</span></div>
                    <div class="flex items-center justify-between gap-4 py-4"><span class="text-copy">{{ __('Date') }}</span><span class="font-bold text-ink">{{ $bookingDate->translatedFormat('M j, Y') }}</span></div>
                    <div class="flex items-center justify-between gap-4 py-4"><span class="text-copy">{{ __('Time') }}</span><span class="font-bold text-ink">{{ $slotRange }}</span></div>
                    <div class="flex items-center justify-between gap-4 py-4"><span class="text-copy">{{ __('Duration') }}</span><span class="font-bold text-ink">{{ $slotCount }} {{ $slotCount === 1 ? __('hour') : __('hours') }}</span></div>
                    <div class="flex items-center justify-between gap-4 py-4"><span class="text-copy">{{ __('Total Price') }}</span><span class="font-heading text-[24px] font-bold text-[#5a38d6]">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span></div>
                </div>

                <div class="mt-6 rounded-xl bg-[#f6f2ff] p-4">
                    <h2 class="font-heading text-[18px] font-bold text-ink">{{ __('Payment Instructions') }}</h2>
                    <p class="mt-2 text-[14px] leading-6 text-copy">{{ __('Transfer the down payment first, then upload the proof below. Once submitted, the booking will enter Pending status with a 24-hour review window.') }}</p>
                </div>
            </article>

            <aside class="rounded-xl border border-[#ebe7fb] bg-white p-6 shadow-[0_18px_44px_rgba(86,75,165,0.10)]">
                <h2 class="font-heading text-[20px] font-bold text-ink">{{ __('Booking Summary') }}</h2>
                <div class="mt-5 space-y-3 rounded-xl border border-[#eceaf5] p-4 text-[14px]">
                    <div class="flex justify-between gap-4"><span class="text-copy">{{ __('Venue') }}</span><span class="font-bold text-ink">{{ $field->name }}</span></div>
                    <div class="flex justify-between gap-4"><span class="text-copy">{{ __('Date') }}</span><span class="font-bold text-ink">{{ $bookingDate->translatedFormat('M j, Y') }}</span></div>
                    <div class="flex justify-between gap-4"><span class="text-copy">{{ __('Time') }}</span><span class="font-bold text-ink">{{ $slotRange }}</span></div>
                    <div class="flex justify-between gap-4"><span class="text-copy">{{ __('Price') }}</span><span class="font-bold text-ink">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between gap-4"><span class="text-copy">{{ __('Down Payment') }}</span><span class="font-heading text-[24px] font-bold text-[#5a38d6]">Rp {{ number_format($downPaymentAmount, 0, ',', '.') }}</span></div>
                </div>

                <form action="{{ route('bookings.store', $field) }}" method="POST" enctype="multipart/form-data" novalidate class="mt-5 space-y-4">
                    @csrf
                    <input type="hidden" name="date" value="{{ $bookingDate->toDateString() }}">
                    @foreach ($slotIds as $slotId)
                        <input type="hidden" name="slot_ids[]" value="{{ $slotId }}">
                    @endforeach

                    <label class="block">
                        <span class="text-[14px] font-bold text-ink">{{ __('Upload payment proof') }}</span>
                        <p class="mt-1 text-[13px] text-copy">{{ __('Transfer 50% of the booking total, then upload your payment proof.') }}</p>
                        <input id="payment-proof-input" type="file" name="proof" accept="image/png,image/jpeg" required class="sr-only" data-file-name-target="payment-proof-file-name">
                        <span class="mt-3 flex min-h-[52px] items-center gap-4 rounded-lg border border-[#dfdaf4] bg-white p-2 text-[14px]">
                            <span class="inline-flex shrink-0 cursor-pointer rounded-lg bg-[#5a38d6] px-4 py-3 font-bold text-white">{{ __('Choose file') }}</span>
                            <span id="payment-proof-file-name" class="min-w-0 flex-1 truncate text-ink">{{ __('No file selected') }}</span>
                        </span>
                    </label>

                    <button type="submit" class="flex w-full items-center justify-center rounded-lg bg-[#5a38d6] px-6 py-4 text-[16px] font-bold text-white shadow-[0_14px_28px_rgba(84,66,217,0.26)] transition hover:bg-[#4c2fbd]">{{ __('Confirm Booking') }}</button>
                </form>
            </aside>
        </section>
    </main>
    <script>
        document.querySelectorAll('input[type="file"][data-file-name-target]').forEach((input) => {
            const target = document.getElementById(input.dataset.fileNameTarget);
            const emptyText = @json(__('No file selected'));

            input.addEventListener('change', () => {
                target.textContent = input.files?.[0]?.name || emptyText;
            });
        });
    </script>
</body>
</html>

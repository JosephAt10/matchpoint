<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Payment - MatchPoint</title>
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
                        indigoDeep: '#5542d9',
                        copy: '#676d92',
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
    </style>
</head>
<body class="min-h-screen bg-[linear-gradient(180deg,#ffffff_0%,#f8f6ff_50%,#ffffff_100%)] text-ink">
    <header class="border-b border-[#ede9ff] bg-white/95">
        <div class="flex w-full items-center justify-between px-5 py-4 md:px-10 xl:px-16 2xl:px-24">
            <a href="{{ route('home') }}" class="font-heading text-[22px] font-bold tracking-[0.12em] text-[#352782]">MATCHPOINT</a>
            <nav class="flex items-center gap-4 text-[14px] font-medium text-[#4f5579]">
                <a href="{{ route('fields.index') }}" class="transition hover:text-indigoDeep">Browse Fields</a>
                <a href="{{ route('dashboard') }}" class="transition hover:text-indigoDeep">Dashboard</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto w-full max-w-5xl px-5 py-8 md:px-8">
        @if (session('status'))
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                {{ $errors->first() }}
            </div>
        @endif

        <a href="{{ route('fields.show', ['field' => $booking->field, 'date' => $booking->date->toDateString()]) }}" class="mb-6 inline-flex items-center gap-2 text-[14px] font-medium text-[#4f5579] transition hover:text-indigoDeep">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
            </svg>
            Back to field
        </a>

        <section class="grid gap-6 lg:grid-cols-[1fr_380px]">
            <article class="rounded-xl border border-[#ebe7fb] bg-white p-6 shadow-[0_18px_44px_rgba(86,75,165,0.08)] md:p-8">
                <p class="text-[13px] font-bold uppercase tracking-[0.12em] text-[#5a38d6]">Pending Booking</p>
                <h1 class="mt-3 font-heading text-[32px] font-bold text-ink">{{ $booking->field->name }}</h1>
                <p class="mt-2 text-[15px] font-medium text-copy">{{ $booking->field->location }}</p>

                <div class="mt-7 divide-y divide-[#eceaf5] rounded-xl border border-[#eceaf5] px-4 text-[15px]">
                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-copy">Date</span>
                        <span class="font-bold text-ink">{{ $booking->date->format('M j, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-copy">Time</span>
                        <span class="font-bold text-ink">{{ $slotRange }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-copy">Duration</span>
                        <span class="font-bold text-ink">{{ $booking->bookedSlots->count() }} {{ $booking->bookedSlots->count() === 1 ? 'hour' : 'hours' }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-copy">Booking Status</span>
                        <span class="rounded-full bg-[#fff7db] px-3 py-1 text-sm font-bold text-[#9b6a00]">{{ $booking->status }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-4">
                        <span class="text-copy">Payment Deadline</span>
                        <span class="font-bold text-ink">{{ $booking->payment_deadline->format('M j, Y H:i') }}</span>
                    </div>
                </div>
            </article>

            <aside class="rounded-xl border border-[#ebe7fb] bg-white p-6 shadow-[0_18px_44px_rgba(86,75,165,0.10)]">
                <h2 class="font-heading text-[20px] font-bold text-ink">Down Payment</h2>
                <p class="mt-2 text-[14px] leading-6 text-copy">Transfer 50% of the booking total, then upload your payment proof.</p>

                <div class="mt-5 rounded-xl bg-[#f6f2ff] p-4">
                    <p class="text-[13px] font-medium text-copy">Amount to Pay</p>
                    <p class="mt-1 font-heading text-[30px] font-bold text-[#5a38d6]">Rp {{ number_format($booking->downPaymentAmount(), 0, ',', '.') }}</p>
                </div>

                <div class="mt-5 space-y-3 rounded-xl border border-[#eceaf5] p-4 text-[14px]">
                    <div class="flex justify-between gap-4">
                        <span class="text-copy">Bank</span>
                        <span class="font-bold text-ink">BCA</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-copy">Account No.</span>
                        <span class="font-bold text-ink">1234567890</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-copy">Account Name</span>
                        <span class="font-bold text-ink">MatchPoint</span>
                    </div>
                </div>

                @if ($booking->payment?->proof)
                    <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                        Proof uploaded. Status: {{ $booking->payment->status }}.
                    </div>
                @else
                    <form action="{{ route('bookings.payment-proof.store', $booking) }}" method="POST" enctype="multipart/form-data" class="mt-5 space-y-4">
                        @csrf
                        <label class="block">
                            <span class="text-[14px] font-bold text-ink">Upload proof</span>
                            <input type="file" name="proof" accept="image/png,image/jpeg" required class="mt-2 block w-full rounded-lg border border-[#dfdaf4] bg-white px-3 py-3 text-[14px] file:mr-4 file:rounded-lg file:border-0 file:bg-[#5a38d6] file:px-4 file:py-2 file:font-bold file:text-white">
                        </label>
                        <button type="submit" class="flex w-full items-center justify-center rounded-lg bg-[#5a38d6] px-6 py-4 text-[16px] font-bold text-white shadow-[0_14px_28px_rgba(84,66,217,0.26)] transition hover:bg-[#4c2fbd]">
                            Submit Proof
                        </button>
                    </form>
                @endif
            </aside>
        </section>
    </main>
</body>
</html>

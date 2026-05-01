@php
    $favoritesUrl = auth()->check() ? route('favorites.index') : route('login');
    $isFavorite = isset($favoriteIds) && $favoriteIds->contains($field->id);
    $selectedSlot = $previewSlots->firstWhere('available', true);
    $selectedSlotLabel = $selectedSlot['label'] ?? 'No available slots';
    $slotPrice = (float) $field->price_per_slot;
    $minBookingDate = now()->addDay()->toDateString();
    $imageMap = [
        'Futsal' => asset('landing/futsal-court.png'),
        'Badminton' => asset('landing/badminton-court.png'),
        'Football' => asset('landing/football-stadium.jpg'),
        'Basketball' => asset('landing/basketball-court.jpg'),
        'Tennis' => asset('landing/tennis-court.png'),
        'Volleyball' => asset('landing/volleyball-court.png'),
    ];
    $image = $imageMap[$field->sport_type] ?? asset('landing/football-stadium.jpg');
    $displayDate = $selectedDate->format('M j, Y');
    $slotGroups = collect($allTimeSlots ?? [])->groupBy('day_of_week');
    $availableDays = $slotGroups->keys()->join(', ');
    $firstSlot = collect($allTimeSlots ?? [])->sortBy('start_time')->first();
    $lastSlot = collect($allTimeSlots ?? [])->sortBy(fn ($slot) => $slot->end_time === '00:00:00' ? '24:00:00' : $slot->end_time)->last();
    $operatingHours = $firstSlot && $lastSlot
        ? substr($firstSlot->start_time, 0, 5) . ' - ' . substr($lastSlot->end_time, 0, 5)
        : 'No slots configured';
    $description = $field->description ?: 'Professional indoor futsal arena with high-quality flooring, full lighting, and spectator seating. Suitable for training, friendly matches, and tournaments.';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $field->name }} - MatchPoint</title>
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
                        indigoSoft: '#6b63f6',
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
        .booking-date-input::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: 0;
        }
    </style>
</head>
<body class="min-h-screen bg-[linear-gradient(180deg,#ffffff_0%,#f8f6ff_50%,#ffffff_100%)] text-ink">
    <header class="border-b border-[#ede9ff] bg-white/95 backdrop-blur">
        <div class="flex w-full items-center justify-between px-5 py-4 md:px-10 xl:px-16 2xl:px-24">
            <a href="{{ route('home') }}" class="font-heading text-[22px] font-bold tracking-[0.12em] text-[#352782]">MATCHPOINT</a>

            <nav class="flex items-center gap-2 text-[14px] font-medium text-[#4f5579] md:gap-5">
                <a href="{{ route('fields.index') }}" class="hidden px-2 py-2 transition hover:text-indigoDeep sm:inline-flex">Browse Fields</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="hidden px-2 py-2 transition hover:text-indigoDeep sm:inline-flex">Dashboard</a>
                    <a href="{{ route('profile.edit') }}" class="hidden px-2 py-2 transition hover:text-indigoDeep md:inline-flex">Profile</a>
                    <form action="{{ route('logout') }}" method="POST" class="inline-flex">
                        @csrf
                        <button type="submit" class="rounded-full bg-[#5a38d6] px-5 py-3 font-semibold text-white shadow-[0_12px_24px_rgba(84,66,217,0.24)] transition hover:bg-[#4c2fbd]">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-2 py-2 transition hover:text-indigoDeep">Login</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-[#5a38d6] px-5 py-3 font-semibold text-white shadow-[0_12px_24px_rgba(84,66,217,0.24)] transition hover:bg-[#4c2fbd]">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="w-full px-5 py-8 md:px-10 xl:px-16 2xl:px-24">
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

        <a href="{{ route('fields.index') }}" class="mb-6 inline-flex items-center gap-2 text-[14px] font-medium text-[#4f5579] transition hover:text-indigoDeep">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
            </svg>
            Back to all fields
        </a>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_520px] 2xl:grid-cols-[minmax(0,1fr)_560px] xl:items-start">
            <div class="space-y-6">
                <article class="overflow-hidden rounded-xl border border-[#ebe7fb] bg-white shadow-[0_18px_44px_rgba(86,75,165,0.10)]">
                    <div class="relative h-[250px] overflow-hidden md:h-[315px]">
                        <img src="{{ $image }}" alt="{{ $field->name }}" class="h-full w-full object-cover">
                        <span class="absolute left-5 top-5 rounded-xl bg-white px-5 py-3 text-[14px] font-bold text-ink shadow-[0_10px_26px_rgba(21,26,59,0.12)]">{{ $field->type }}</span>
                    </div>

                    <div class="p-6 md:p-8">
                        <div class="flex flex-col gap-5 border-b border-[#eceaf5] pb-7 md:flex-row md:items-center md:justify-between">
                            <div class="flex items-center gap-5">
                                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-[#f3efff] text-[32px]">
                                    @if ($field->sport_type === 'Futsal')
                                        ⚽
                                    @elseif ($field->sport_type === 'Basketball')
                                        🏀
                                    @elseif ($field->sport_type === 'Tennis')
                                        🎾
                                    @else
                                        🏟
                                    @endif
                                </div>
                                <div>
                                    <p class="text-[12px] font-bold uppercase tracking-[0.08em] text-ink">{{ $field->sport_type }}</p>
                                    <h1 class="mt-1 font-heading text-[28px] font-bold leading-tight text-ink md:text-[34px]">{{ $field->name }}</h1>
                                    <p class="mt-3 flex items-center gap-2 text-[14px] font-medium text-[#363b5f]">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-4.35-6-10a6 6 0 1112 0c0 5.65-6 10-6 10z"/>
                                            <circle cx="12" cy="11" r="2.5"></circle>
                                        </svg>
                                        {{ $field->location }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 md:justify-end">
                                <div class="text-left md:text-right">
                                    <p class="font-heading text-[24px] font-bold text-[#5a38d6]">Rp {{ number_format((float) $field->price_per_slot, 0, ',', '.') }}<span class="text-[12px] font-semibold text-ink"> /hour</span></p>
                                    <p class="mt-2 text-[13px] font-medium text-copy"><span class="text-[#f4a51c]">★</span> 4.8 (120 reviews)</p>
                                </div>
                                @auth
                                    <form action="{{ route('fields.favorite.toggle', $field) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#f6f2ff] text-[#7a5df3] transition hover:bg-[#ece5ff]" aria-label="Toggle favorite">
                                            <svg class="h-6 w-6 {{ $isFavorite ? 'fill-current' : '' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.8 4.6a5.5 5.5 0 00-7.8 0L12 5.6l-1-1a5.5 5.5 0 00-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 000-7.8z"/>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ $favoritesUrl }}" class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#f6f2ff] text-[#7a5df3] transition hover:bg-[#ece5ff]" aria-label="Sign in to favorite">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.8 4.6a5.5 5.5 0 00-7.8 0L12 5.6l-1-1a5.5 5.5 0 00-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 000-7.8z"/>
                                        </svg>
                                    </a>
                                @endauth
                            </div>
                        </div>

                        <div class="pt-6">
                            <h2 class="font-heading text-[17px] font-bold text-ink">About this venue</h2>
                            <p class="mt-3 max-w-[760px] text-[14px] leading-7 text-[#4f5579]">{{ $description }}</p>

                            <div class="mt-6 grid gap-3 text-[14px] text-[#4f5579] sm:grid-cols-2 lg:grid-cols-4">
                                @foreach ([$field->type, $field->sport_type, 'Parking', 'Changing Room', 'Shower', 'Toilet', 'Lighting'] as $amenity)
                                    <span class="inline-flex items-center gap-3">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#f4f0ff] text-[#6d52ea]">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </span>
                                        {{ $amenity }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </article>

                <section class="rounded-xl border border-[#ebe7fb] bg-white p-6 shadow-[0_18px_44px_rgba(86,75,165,0.08)] md:p-8">
                    <h2 class="font-heading text-[18px] font-bold text-ink">Venue Details</h2>
                    <div class="mt-6 grid gap-6 md:grid-cols-2">
                        @foreach ([
                            ['label' => 'Owner', 'value' => $field->owner->name],
                            ['label' => 'Sport Type', 'value' => $field->sport_type],
                            ['label' => 'Field Type', 'value' => $field->type],
                            ['label' => 'Price per Slot', 'value' => 'Rp ' . number_format((float) $field->price_per_slot, 0, ',', '.')],
                            ['label' => 'Available Days', 'value' => $availableDays ?: 'No days configured'],
                            ['label' => 'Operating Hours', 'value' => $operatingHours],
                        ] as $detail)
                            <div class="flex gap-4">
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#f6f2ff] text-[#8a61ff]">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="8"></circle>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-[12px] font-medium text-copy">{{ $detail['label'] }}</p>
                                    <p class="mt-1 text-[14px] font-bold text-ink">{{ $detail['value'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>

            <aside class="rounded-xl border border-[#ebe7fb] bg-white p-6 shadow-[0_18px_44px_rgba(86,75,165,0.10)] xl:sticky xl:top-6">
                <h2 class="font-heading text-[18px] font-bold text-ink">Select Date</h2>
                <form method="GET" action="{{ route('fields.show', $field) }}" class="mt-4">
                    <button
                        type="button"
                        id="date-picker-trigger"
                        class="flex w-full cursor-pointer items-center justify-between rounded-lg border border-[#dfdaf4] bg-white px-4 py-4 text-left text-[15px] font-semibold text-[#222746] transition hover:border-[#bfb2ff]"
                    >
                        <span class="inline-flex min-w-0 items-center gap-3">
                            <svg class="h-5 w-5 shrink-0 text-[#8b63ff]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/>
                            </svg>
                            <span id="selected-date-label">{{ $selectedDate->format('m/d/Y') }}</span>
                        </span>
                        <svg class="h-4 w-4 shrink-0 text-[#414668]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/>
                        </svg>
                    </button>
                        <input
                            id="booking-date-input"
                            type="date"
                            name="date"
                            value="{{ $selectedDate->toDateString() }}"
                            min="{{ $minBookingDate }}"
                            class="booking-date-input sr-only"
                        >
                </form>

                <div class="mt-6 flex items-center justify-between gap-4">
                    <h3 class="font-heading text-[17px] font-bold text-ink">Available Time Slots</h3>
                    <div class="flex items-center gap-4 text-[12px] text-copy">
                        <span class="inline-flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-[#55d77a]"></span>Available</span>
                        <span class="inline-flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-[#ff9ca0]"></span>Booked</span>
                    </div>
                </div>

                @if ($previewSlots->isNotEmpty())
                    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($previewSlots as $slotIndex => $slot)
                            <button
                                type="button"
                                class="slot-button rounded-lg border px-3 py-3 text-center text-[14px] font-bold transition {{ $slot['available'] ? 'border-[#cfeeda] bg-[#edfff1] text-[#008b2f] hover:border-[#83d99a]' : 'cursor-not-allowed border-[#ffd8d8] bg-[#fff0f0] text-[#d20b18]' }}"
                                data-slot-label="{{ $slot['label'] }}"
                                data-slot-index="{{ $slotIndex }}"
                                data-slot-id="{{ $slot['id'] }}"
                                data-slot-start="{{ $slot['start'] }}"
                                data-slot-end="{{ $slot['end'] }}"
                                {{ $slot['available'] ? '' : 'disabled' }}
                            >
                                {{ $slot['label'] }}
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="mt-4 rounded-lg border border-dashed border-[#dfdaf4] bg-[#faf9ff] px-4 py-6 text-center text-[14px] font-medium text-copy">
                        No time slots are available in the database for this date.
                    </div>
                @endif

                <div class="mt-6 border-t border-[#eceaf5] pt-6">
                    <h3 class="font-heading text-[18px] font-bold text-ink">Booking Summary</h3>
                    <div class="mt-4 divide-y divide-[#eceaf5] rounded-lg border border-[#eceaf5] px-3 text-[14px]">
                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="text-copy">Venue</span>
                            <span class="text-right font-bold text-ink">{{ $field->name }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="text-copy">Date</span>
                            <span class="font-bold text-ink">{{ $displayDate }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="text-copy">Time</span>
                            <span id="selected-slot-label" class="font-bold text-ink">{{ $selectedSlotLabel }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="text-copy">Duration</span>
                            <span id="selected-duration-label" class="font-bold text-ink">{{ $selectedSlot ? '1 hour' : '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="font-heading text-[16px] font-bold text-ink">Price</span>
                            <span id="selected-price-label" class="font-heading text-[22px] font-bold text-[#5a38d6]">Rp {{ $selectedSlot ? number_format($slotPrice, 0, ',', '.') : '0' }}</span>
                        </div>
                    </div>

                    @auth
                        <form id="booking-form" method="POST" action="{{ route('bookings.store', $field) }}" class="mt-4">
                            @csrf
                            <input type="hidden" name="date" value="{{ $selectedDate->toDateString() }}">
                            <div id="selected-slot-inputs"></div>
                            <button id="book-now-button" type="submit" {{ $selectedSlot ? '' : 'disabled' }} class="flex w-full items-center justify-center gap-2 rounded-lg bg-[#5a38d6] px-6 py-4 text-[17px] font-bold text-white shadow-[0_14px_28px_rgba(84,66,217,0.26)] transition hover:bg-[#4c2fbd] disabled:cursor-not-allowed disabled:opacity-50">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/>
                            </svg>
                            Book Now
                            </button>
                        </form>
                        <p class="mt-4 flex items-center justify-center gap-2 text-[13px] font-medium text-copy">
                            <svg class="h-4 w-4 text-[#5a38d6]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                            You won't be charged yet
                        </p>
                    @else
                        <a href="{{ route('login') }}" class="mt-4 flex w-full items-center justify-center gap-2 rounded-lg bg-[#5a38d6] px-6 py-4 text-[17px] font-bold text-white shadow-[0_14px_28px_rgba(84,66,217,0.26)] transition hover:bg-[#4c2fbd]">
                            Book Now
                        </a>
                        <p class="mt-4 text-center text-[13px] font-medium text-copy">Sign in to continue booking.</p>
                    @endauth
                </div>
            </aside>
        </section>

        <section class="mt-6 rounded-xl border border-[#ebe7fb] bg-white p-6 shadow-[0_18px_44px_rgba(86,75,165,0.08)] md:p-8">
            <div class="grid gap-6 lg:grid-cols-[190px_1fr_1fr_1fr_150px] lg:items-start">
                <div>
                    <h2 class="font-heading text-[17px] font-bold text-ink">What people say</h2>
                    <p class="mt-5 font-heading text-[30px] font-bold text-[#4931c8]"><span class="text-[#4931c8]">★</span> 4.8</p>
                    <p class="mt-2 text-[14px] font-semibold text-[#7a5df3]">(120 reviews)</p>
                </div>

                @foreach ([
                    ['name' => 'Andi Pratama', 'time' => '2 days ago', 'text' => 'The field is clean, comfortable, and great for futsal.'],
                    ['name' => 'Rizky Maulana', 'time' => '1 week ago', 'text' => 'Booking was easy, the service was friendly, highly recommended.'],
                    ['name' => 'Siti Aisyah', 'time' => '2 weeks ago', 'text' => 'Complete facilities and the price matches the quality.'],
                ] as $review)
                    <article class="border-[#eceaf5] lg:border-l lg:pl-8">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-[linear-gradient(135deg,#d8fff0,#d8d3ff)]"></div>
                            <div>
                                <h3 class="font-heading text-[15px] font-bold text-ink">{{ $review['name'] }}</h3>
                                <p class="text-[13px] text-copy"><span class="text-[#f4a51c]">★★★★★</span> <span class="ml-2">{{ $review['time'] }}</span></p>
                            </div>
                        </div>
                        <p class="mt-4 text-[14px] leading-6 text-[#4f5579]">{{ $review['text'] }}</p>
                    </article>
                @endforeach

                <div class="lg:text-right">
                    <a href="#" class="inline-flex rounded-xl bg-[#f3efff] px-5 py-3 text-[14px] font-bold text-[#5a38d6] transition hover:bg-[#ece5ff]">View all reviews</a>
                </div>
            </div>
        </section>
    </main>

    <script>
        const slotButtons = Array.from(document.querySelectorAll('.slot-button'));
        const selectedSlotLabel = document.getElementById('selected-slot-label');
        const selectedDurationLabel = document.getElementById('selected-duration-label');
        const selectedPriceLabel = document.getElementById('selected-price-label');
        const bookNowButton = document.getElementById('book-now-button');
        const selectedSlotInputs = document.getElementById('selected-slot-inputs');
        const slotPrice = @json($slotPrice);
        const datePickerTrigger = document.getElementById('date-picker-trigger');
        const bookingDateInput = document.getElementById('booking-date-input');
        let selectedIndexes = [];

        if (datePickerTrigger && bookingDateInput instanceof HTMLInputElement) {
            datePickerTrigger.addEventListener('click', () => {
                if (typeof bookingDateInput.showPicker === 'function') {
                    bookingDateInput.showPicker();
                    return;
                }

                bookingDateInput.click();
            });

            bookingDateInput.addEventListener('change', () => {
                bookingDateInput.form?.submit();
            });
        }

        const formatRupiah = (amount) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        }).format(amount).replace(/\s/g, ' ');

        const selectedSlots = () => selectedIndexes
            .map((index) => slotButtons.find((button) => Number(button.dataset.slotIndex) === index))
            .filter(Boolean);

        const updateBookingSummary = () => {
            slotButtons.forEach((slotButton) => {
                slotButton.classList.remove('ring-2', 'ring-[#7d68f2]', 'border-[#7d68f2]', 'bg-[#e6dcff]', 'text-[#4b2fc4]');
            });

            const slots = selectedSlots();

            slots.forEach((slotButton) => {
                slotButton.classList.add('ring-2', 'ring-[#7d68f2]', 'border-[#7d68f2]', 'bg-[#e6dcff]', 'text-[#4b2fc4]');
            });

            if (slots.length === 0) {
                if (selectedSlotLabel) {
                    selectedSlotLabel.textContent = 'No available slots';
                }
                if (selectedDurationLabel) {
                    selectedDurationLabel.textContent = '-';
                }
                if (selectedPriceLabel) {
                    selectedPriceLabel.textContent = 'Rp 0';
                }
                if (bookNowButton instanceof HTMLButtonElement) {
                    bookNowButton.disabled = true;
                }
                if (selectedSlotInputs) {
                    selectedSlotInputs.innerHTML = '';
                }
                return;
            }

            const firstSlot = slots[0];
            const lastSlot = slots[slots.length - 1];
            const durationLabel = `${slots.length} ${slots.length === 1 ? 'hour' : 'hours'}`;

            if (selectedSlotLabel) {
                selectedSlotLabel.textContent = `${firstSlot.dataset.slotStart} - ${lastSlot.dataset.slotEnd}`;
            }
            if (selectedDurationLabel) {
                selectedDurationLabel.textContent = durationLabel;
            }
            if (selectedPriceLabel) {
                selectedPriceLabel.textContent = formatRupiah(slots.length * slotPrice);
            }
            if (bookNowButton instanceof HTMLButtonElement) {
                bookNowButton.disabled = false;
            }
            if (selectedSlotInputs) {
                selectedSlotInputs.innerHTML = slots
                    .map((slotButton) => `<input type="hidden" name="slot_ids[]" value="${slotButton.dataset.slotId}">`)
                    .join('');
            }
        };

        const isContinuous = (indexes) => {
            if (indexes.length <= 1) {
                return true;
            }

            const sorted = [...indexes].sort((a, b) => a - b);

            return sorted.every((index, position) => position === 0 || index === sorted[position - 1] + 1);
        };

        slotButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const index = Number(button.dataset.slotIndex);
                const alreadySelected = selectedIndexes.includes(index);

                if (alreadySelected) {
                    const nextIndexes = selectedIndexes.filter((selectedIndex) => selectedIndex !== index);

                    selectedIndexes = isContinuous(nextIndexes) ? nextIndexes : [index];
                    selectedIndexes.sort((a, b) => a - b);
                    updateBookingSummary();
                    return;
                }

                const nextIndexes = [...selectedIndexes, index].sort((a, b) => a - b);
                selectedIndexes = isContinuous(nextIndexes) ? nextIndexes : [index];
                updateBookingSummary();
            });
        });

        const firstAvailableSlot = slotButtons.find((button) => ! button.disabled);

        if (firstAvailableSlot) {
            selectedIndexes = [Number(firstAvailableSlot.dataset.slotIndex)];
            updateBookingSummary();
        } else {
            updateBookingSummary();
        }
    </script>
</body>
</html>

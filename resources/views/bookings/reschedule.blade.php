@php
    $minDate = now()->addDay()->toDateString();
    $currentDateLabel = $booking->date->translatedFormat('M j, Y');
    $selectedDateLabel = $selectedDate->translatedFormat('M j, Y');
    $firstAvailable = $previewSlots->firstWhere('available', true);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Request Reschedule') }} - MatchPoint</title>
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
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('landing/matchpoint-logo.png') }}" alt="{{ __('MatchPoint logo') }}" class="h-10 w-10 object-contain">
                <span class="font-heading text-[22px] font-bold tracking-[0.12em] text-[#352782]">{{ __('MATCHPOINT') }}</span>
            </a>
            <div class="shrink-0">@include('partials.locale-switcher')</div>
        </div>
    </header>

    <main class="mx-auto w-full max-w-6xl px-5 py-8 md:px-8">
        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">{{ $errors->first() }}</div>
        @endif

        <a href="{{ route('bookings.show', $booking) }}" class="mb-6 inline-flex items-center gap-2 text-[14px] font-medium text-[#4f5579] transition hover:text-indigoDeep">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
            {{ __('Back to Booking') }}
        </a>

        <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_380px]">
            <article class="rounded-[28px] border border-[#ebe7fb] bg-white p-6 shadow-[0_18px_44px_rgba(86,75,165,0.08)] md:p-8">
                <p class="text-[13px] font-bold uppercase tracking-[0.16em] text-[#5a38d6]">{{ __('Outdoor Reschedule') }}</p>
                <h1 class="mt-3 font-heading text-[34px] font-bold text-ink">{{ __('Request Reschedule') }}</h1>
                <p class="mt-3 max-w-2xl text-[16px] leading-7 text-copy">{{ __('Choose a new available time slot for this confirmed outdoor booking. Indoor bookings and past booking dates cannot be rescheduled.') }}</p>

                <form method="GET" action="{{ route('bookings.reschedule', $booking) }}" class="mt-7 rounded-2xl border border-[#eceaf5] bg-[#fbfaff] p-5">
                    <label for="date" class="text-[14px] font-bold text-ink">{{ __('New Date') }}</label>
                    <div class="mt-3 flex flex-col gap-3 sm:flex-row">
                        <input id="date" type="date" name="date" value="{{ $selectedDate->toDateString() }}" min="{{ $minDate }}" class="w-full rounded-xl border border-[#dfdaf4] bg-white px-4 py-3 text-[15px] font-semibold text-ink outline-none transition focus:border-[#5a38d6]">
                        <button type="submit" class="rounded-xl bg-[#5a38d6] px-5 py-3 text-[15px] font-bold text-white shadow-[0_12px_24px_rgba(90,56,214,.18)]">{{ __('Check Slots') }}</button>
                    </div>
                </form>

                <form method="POST" action="{{ route('bookings.reschedule.update', $booking) }}" class="mt-6">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="date" value="{{ $selectedDate->toDateString() }}">

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="font-heading text-[20px] font-bold text-ink">{{ __('Available Time Slots') }}</h2>
                            <p class="mt-1 text-[14px] text-copy">{{ __('Selected date: :date', ['date' => $selectedDateLabel]) }}</p>
                        </div>
                        <div class="flex items-center gap-4 text-[12px] text-copy">
                            <span class="inline-flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-[#16a34a]"></span>{{ __('Available') }}</span>
                            <span class="inline-flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-[#ef4444]"></span>{{ __('Booked') }}</span>
                        </div>
                    </div>

                    @if ($previewSlots->isNotEmpty())
                        <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-3">
                            @foreach ($previewSlots as $slot)
                                <button type="button" class="slot-button rounded-xl border px-4 py-4 text-center text-[14px] font-bold transition {{ $slot['available'] ? 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:border-emerald-400' : 'cursor-not-allowed border-rose-200 bg-rose-50 text-rose-600' }}" data-slot-id="{{ $slot['id'] }}" data-slot-index="{{ $slot['index'] }}" data-slot-start="{{ $slot['start'] }}" data-slot-end="{{ $slot['end'] }}" {{ $slot['available'] ? '' : 'disabled' }}>
                                    {{ $slot['label'] }}
                                </button>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-4 rounded-xl border border-dashed border-[#dfdaf4] bg-[#faf9ff] px-4 py-8 text-center text-[15px] font-medium text-copy">{{ __('No time slots are available for this date.') }}</div>
                    @endif

                    <div id="selected-slot-inputs"></div>

                    <div class="mt-6 rounded-2xl border border-[#eceaf5] bg-white p-5">
                        <p class="text-[13px] font-bold uppercase tracking-[0.12em] text-[#5a38d6]">{{ __('New Schedule Summary') }}</p>
                        <div class="mt-4 grid gap-4 sm:grid-cols-3">
                            <div><p class="text-[13px] text-copy">{{ __('Field') }}</p><p class="mt-1 font-bold text-ink">{{ $booking->field->name }}</p></div>
                            <div><p class="text-[13px] text-copy">{{ __('Date') }}</p><p class="mt-1 font-bold text-ink">{{ $selectedDateLabel }}</p></div>
                            <div><p class="text-[13px] text-copy">{{ __('Time') }}</p><p id="selected-time-label" class="mt-1 font-bold text-ink">{{ $firstAvailable['label'] ?? __('No available slots') }}</p></div>
                        </div>
                    </div>

                    <button id="reschedule-button" type="submit" class="mt-5 w-full rounded-xl bg-[#5a38d6] px-5 py-4 text-[16px] font-bold text-white shadow-[0_14px_28px_rgba(90,56,214,.22)] transition hover:bg-[#4c2fbd] disabled:cursor-not-allowed disabled:opacity-50" {{ $firstAvailable ? '' : 'disabled' }}>
                        {{ __('Confirm Reschedule') }}
                    </button>
                </form>
            </article>

            <aside class="rounded-[28px] border border-[#ebe7fb] bg-white p-6 shadow-[0_18px_44px_rgba(86,75,165,0.10)]">
                <h2 class="font-heading text-[20px] font-bold text-ink">{{ __('Current Booking') }}</h2>
                <div class="mt-5 overflow-hidden rounded-2xl bg-[#eef1f8]">
                    <img src="{{ $booking->field->image_url ? url($booking->field->image_url) : asset('landing/football-stadium.jpg') }}" alt="{{ $booking->field->name }}" class="h-[180px] w-full object-cover">
                </div>
                <div class="mt-5 space-y-4 text-[15px]">
                    <div><p class="text-copy">{{ __('Field') }}</p><p class="mt-1 font-bold text-ink">{{ $booking->field->name }}</p></div>
                    <div><p class="text-copy">{{ __('Location') }}</p><p class="mt-1 font-bold text-ink">{{ $booking->field->location }}</p></div>
                    <div><p class="text-copy">{{ __('Current Date') }}</p><p class="mt-1 font-bold text-ink">{{ $currentDateLabel }}</p></div>
                    <div><p class="text-copy">{{ __('Current Time') }}</p><p class="mt-1 font-bold text-ink">{{ $currentSlotRange }}</p></div>
                    <div><p class="text-copy">{{ __('Field Type') }}</p><p class="mt-1 inline-flex rounded-full bg-emerald-50 px-3 py-1 font-bold text-emerald-700">{{ __($booking->field->type) }}</p></div>
                </div>
            </aside>
        </section>
    </main>

    <script>
        const slotButtons = Array.from(document.querySelectorAll('.slot-button'));
        const selectedSlotInputs = document.getElementById('selected-slot-inputs');
        const selectedTimeLabel = document.getElementById('selected-time-label');
        const rescheduleButton = document.getElementById('reschedule-button');
        const noAvailableSlotsText = @json(__('No available slots'));
        let selectedIndexes = [];

        const selectedSlots = () => selectedIndexes.map((index) => slotButtons.find((button) => Number(button.dataset.slotIndex) === index)).filter(Boolean);
        const isContinuous = (indexes) => {
            if (indexes.length <= 1) return true;
            const sorted = [...indexes].sort((a, b) => a - b);
            return sorted.every((index, position) => position === 0 || index === sorted[position - 1] + 1);
        };
        const updateSummary = () => {
            slotButtons.forEach((button) => button.classList.remove('ring-2', 'ring-[#5a38d6]', 'border-[#5a38d6]', 'bg-[#eee8ff]', 'text-[#4c2fbd]'));
            const slots = selectedSlots();
            slots.forEach((button) => button.classList.add('ring-2', 'ring-[#5a38d6]', 'border-[#5a38d6]', 'bg-[#eee8ff]', 'text-[#4c2fbd]'));

            if (! slots.length) {
                selectedSlotInputs.innerHTML = '';
                selectedTimeLabel.textContent = noAvailableSlotsText;
                rescheduleButton.disabled = true;
                return;
            }

            const first = slots[0];
            const last = slots[slots.length - 1];
            selectedTimeLabel.textContent = `${first.dataset.slotStart} - ${last.dataset.slotEnd}`;
            selectedSlotInputs.innerHTML = slots.map((button) => `<input type="hidden" name="slot_ids[]" value="${button.dataset.slotId}">`).join('');
            rescheduleButton.disabled = false;
        };

        slotButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const index = Number(button.dataset.slotIndex);
                if (selectedIndexes.includes(index)) {
                    const nextIndexes = selectedIndexes.filter((selectedIndex) => selectedIndex !== index);
                    selectedIndexes = isContinuous(nextIndexes) ? nextIndexes : [index];
                } else {
                    const nextIndexes = [...selectedIndexes, index].sort((a, b) => a - b);
                    selectedIndexes = isContinuous(nextIndexes) ? nextIndexes : [index];
                }
                selectedIndexes.sort((a, b) => a - b);
                updateSummary();
            });
        });

        const firstAvailable = slotButtons.find((button) => ! button.disabled);
        if (firstAvailable) {
            selectedIndexes = [Number(firstAvailable.dataset.slotIndex)];
        }
        updateSummary();
    </script>
</body>
</html>

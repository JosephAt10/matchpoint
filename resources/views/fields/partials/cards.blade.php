@php
    $sportIcons = [
        'Futsal' => '⚽',
        'Badminton' => '🏸',
        'Football' => '🥅',
        'Basketball' => '🏀',
        'Tennis' => '🎾',
        'Volleyball' => '🏐',
    ];

    $defaultImage = asset('landing/football-stadium.jpg');
@endphp

@php
    $favoriteIds = collect($favoriteIds ?? []);
@endphp

@forelse ($fields as $field)
    @php
        $image = $field->image_url ?? $defaultImage;
        $sportIcon = $sportIcons[$field->sport_type] ?? '🏟️';
        $ownerFirstName = str($field->owner->name)->before(' ');
        $isFavorited = $favoriteIds->contains($field->id);
    @endphp
    <article class="group relative overflow-hidden rounded-[1.9rem] border border-[#ebe6ff] bg-white shadow-card transition duration-300 hover:-translate-y-1 hover:shadow-[0_28px_56px_rgba(89,83,178,0.16)]">
        <a href="{{ route('fields.show', $field) }}" class="absolute inset-0 z-10" aria-label="Open {{ $field->name }} details"></a>

        <div class="relative h-[220px] overflow-hidden">
            <img src="{{ $image }}" alt="{{ $field->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.04]">
            <span class="absolute right-4 top-4 z-20 rounded-xl bg-white/95 px-4 py-2 text-[15px] font-semibold text-indigoDeep shadow-sm">{{ $field->type }}</span>
            <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-[#1d2047]/55 to-transparent"></div>
        </div>

        <div class="relative z-20 px-4 pb-5 pt-4">
            <div class="flex items-start justify-between gap-4">
                <div class="flex gap-3">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-[#f2efff] text-[28px]">
                        <span>{{ $sportIcon }}</span>
                    </div>
                    <div>
                        <p class="text-[18px] font-semibold uppercase tracking-wide text-indigoDeep">{{ $field->sport_type }}</p>
                        <h3 class="mt-1 font-heading text-[34px] font-bold leading-tight text-ink">{{ $field->name }}</h3>
                        <div class="mt-2 flex items-center gap-2 text-[18px] text-copy">
                            <svg class="h-4 w-4 text-[#8e92b4]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-4.35-6-10a6 6 0 1112 0c0 5.65-6 10-6 10z"/>
                                <circle cx="12" cy="11" r="2.5"></circle>
                            </svg>
                            <span>{{ $field->location }}</span>
                        </div>
                    </div>
                </div>

                @auth
                    <form action="{{ route('fields.favorite.toggle', $field) }}" method="POST" class="relative z-30 mt-1">
                        @csrf
                        <input type="hidden" name="redirect_to" value="{{ url()->full() }}">
                        <button type="submit" class="mt-2 transition {{ $isFavorited ? 'text-[#6359eb]' : 'text-[#7f7ca2] hover:text-indigoDeep' }}" aria-label="{{ $isFavorited ? 'Remove from favorites' : 'Add to favorites' }}">
                            <svg class="h-7 w-7" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12.1 20.3l-.1.1-.11-.1C7.14 16.24 4 13.39 4 9.84 4 7.03 6.24 5 9.05 5c1.6 0 3.13.75 4.05 1.94A5.17 5.17 0 0117.15 5C19.96 5 22.2 7.03 22.2 9.84c0 3.55-3.14 6.4-8.99 10.46z"/>
                            </svg>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="relative z-30 mt-2 text-[#7f7ca2] transition hover:text-indigoDeep" aria-label="Sign in to save favorites">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12.1 20.3l-.1.1-.11-.1C7.14 16.24 4 13.39 4 9.84 4 7.03 6.24 5 9.05 5c1.6 0 3.13.75 4.05 1.94A5.17 5.17 0 0117.15 5C19.96 5 22.2 7.03 22.2 9.84c0 3.55-3.14 6.4-8.99 10.46z"/>
                        </svg>
                    </a>
                @endauth
            </div>

            <div class="mt-4 flex flex-wrap gap-3 text-[16px] text-[#656a90]">
                <span class="rounded-xl bg-[#f1eeff] px-4 py-2">Owner: {{ $ownerFirstName }}</span>
                <span class="rounded-xl bg-[#f1eeff] px-4 py-2">Slots: {{ $field->timeSlots->count() }}</span>
                <span class="rounded-xl bg-[#f1eeff] px-4 py-2">Rp {{ number_format((float) $field->price_per_slot, 0, ',', '.') }}</span>
            </div>

            <div class="mt-4 flex items-center justify-between border-t border-[#f0ebff] pt-4 text-[17px] font-semibold text-indigoDeep">
                <span>View field details</span>
                <svg class="h-5 w-5 transition duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/>
                </svg>
            </div>
        </div>
    </article>
@empty
    <div class="col-span-full rounded-[2rem] border border-dashed border-[#dbd4ff] bg-white/70 px-6 py-8 text-center text-[18px] text-copy">
        No fields matched your filters.
    </div>
@endforelse

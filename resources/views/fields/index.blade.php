<x-layouts.app title="Browse Fields">
    <section class="space-y-8">
        <div class="space-y-2">
            <p class="text-sm uppercase tracking-[0.2em] text-orange-300">Field search</p>
            <h1 class="text-4xl font-semibold">Browse approved sports venues.</h1>
            <p class="max-w-3xl text-stone-300">This covers your public search phase with location, sport type, and field type filters using the seeded field data.</p>
        </div>

        <form method="GET" action="{{ route('fields.index') }}" class="grid gap-4 rounded-[2rem] border border-white/10 bg-white/5 p-6 md:grid-cols-4">
            <label class="space-y-2">
                <span class="text-sm text-stone-300">Location</span>
                <select name="location" class="w-full rounded-2xl border border-white/10 bg-stone-950/70 px-4 py-3 outline-none transition focus:border-orange-300">
                    <option value="">All locations</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location }}" @selected(($filters['location'] ?? '') === $location)>{{ $location }}</option>
                    @endforeach
                </select>
            </label>

            <label class="space-y-2">
                <span class="text-sm text-stone-300">Sport type</span>
                <select name="sport_type" class="w-full rounded-2xl border border-white/10 bg-stone-950/70 px-4 py-3 outline-none transition focus:border-orange-300">
                    <option value="">All sports</option>
                    @foreach ($sports as $sport)
                        <option value="{{ $sport }}" @selected(($filters['sport_type'] ?? '') === $sport)>{{ $sport }}</option>
                    @endforeach
                </select>
            </label>

            <label class="space-y-2">
                <span class="text-sm text-stone-300">Field type</span>
                <select name="type" class="w-full rounded-2xl border border-white/10 bg-stone-950/70 px-4 py-3 outline-none transition focus:border-orange-300">
                    <option value="">Indoor and outdoor</option>
                    <option value="Indoor" @selected(($filters['type'] ?? '') === 'Indoor')>Indoor</option>
                    <option value="Outdoor" @selected(($filters['type'] ?? '') === 'Outdoor')>Outdoor</option>
                </select>
            </label>

            <div class="flex items-end gap-3">
                <button type="submit" class="flex-1 rounded-2xl bg-orange-400 px-5 py-3 font-medium text-stone-950 transition hover:bg-orange-300">Apply filters</button>
                <a href="{{ route('fields.index') }}" class="rounded-2xl border border-white/10 px-5 py-3 transition hover:bg-white/10">Reset</a>
            </div>
        </form>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($fields as $field)
                <article class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm uppercase tracking-[0.2em] text-orange-300">{{ $field->sport_type }}</p>
                            <h2 class="mt-2 text-2xl font-semibold">{{ $field->name }}</h2>
                        </div>
                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs">{{ $field->type }}</span>
                    </div>
                    <p class="mt-3 text-stone-300">{{ $field->location }}</p>
                    <div class="mt-5 flex flex-wrap gap-2 text-sm text-stone-300">
                        <span class="rounded-full bg-stone-950/60 px-3 py-1">Owner: {{ $field->owner->name }}</span>
                        <span class="rounded-full bg-stone-950/60 px-3 py-1">Slots: {{ $field->timeSlots->count() }}</span>
                        <span class="rounded-full bg-stone-950/60 px-3 py-1">Rp {{ number_format((float) $field->price_per_slot, 0, ',', '.') }}</span>
                    </div>
                </article>
            @empty
                <p class="rounded-[2rem] border border-dashed border-white/10 px-5 py-6 text-stone-300">No fields matched your filters.</p>
            @endforelse
        </div>

        <div>
            {{ $fields->links() }}
        </div>
    </section>
</x-layouts.app>

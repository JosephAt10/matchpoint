<x-layouts.app title="Field Owner Dashboard">
    <section class="space-y-8">
        <div class="space-y-2">
            <p class="text-sm uppercase tracking-[0.2em] text-orange-300">Field owner dashboard</p>
            <h1 class="text-4xl font-semibold">Manage your venues and keep an eye on slot coverage.</h1>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <article class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-stone-400">Your fields</p>
                <p class="mt-3 text-4xl font-semibold">{{ $stats['fields'] }}</p>
            </article>
            <article class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-stone-400">Approved fields</p>
                <p class="mt-3 text-4xl font-semibold">{{ $stats['approvedFields'] }}</p>
            </article>
            <article class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-stone-400">Configured time slots</p>
                <p class="mt-3 text-4xl font-semibold">{{ $stats['timeSlots'] }}</p>
            </article>
        </div>

        <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
            <div class="mb-5">
                <h2 class="text-2xl font-semibold">Managed fields</h2>
                <p class="text-stone-300">This gives you a real Phase 2 dashboard using your seeded field data.</p>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                @forelse ($fields as $field)
                    <article class="rounded-3xl border border-white/10 bg-stone-950/60 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-xl font-semibold">{{ $field->name }}</h3>
                                <p class="text-stone-300">{{ $field->location }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs {{ $field->is_approved ? 'bg-emerald-400/20 text-emerald-200' : 'bg-amber-400/20 text-amber-200' }}">
                                {{ $field->is_approved ? 'Approved' : 'Pending' }}
                            </span>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2 text-sm text-stone-300">
                            <span class="rounded-full bg-white/5 px-3 py-1">{{ $field->sport_type }}</span>
                            <span class="rounded-full bg-white/5 px-3 py-1">{{ $field->type }}</span>
                            <span class="rounded-full bg-white/5 px-3 py-1">Rp {{ number_format((float) $field->price_per_slot, 0, ',', '.') }}</span>
                        </div>
                    </article>
                @empty
                    <p class="rounded-3xl border border-dashed border-white/10 px-5 py-6 text-stone-300">No fields found for this owner yet.</p>
                @endforelse
            </div>
        </section>
    </section>
</x-layouts.app>

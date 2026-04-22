<x-layouts.app title="User Dashboard">
    <section class="space-y-8">
        <div class="space-y-2">
            <p class="text-sm uppercase tracking-[0.2em] text-orange-300">User dashboard</p>
            <h1 class="text-4xl font-semibold">Discover approved venues and prepare for the booking workflow.</h1>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <article class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-stone-400">Approved fields</p>
                <p class="mt-3 text-4xl font-semibold">{{ $stats['availableFields'] }}</p>
            </article>
            <article class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-stone-400">Sport categories</p>
                <p class="mt-3 text-4xl font-semibold">{{ $stats['sports'] }}</p>
            </article>
            <article class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-stone-400">Locations</p>
                <p class="mt-3 text-4xl font-semibold">{{ $stats['locations'] }}</p>
            </article>
        </div>

        <section class="space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold">Featured fields</h2>
                    <p class="text-stone-300">Seeded venues are now visible through a real user-facing page.</p>
                </div>
                <a href="{{ route('fields.index') }}" class="rounded-full bg-orange-400 px-4 py-2 font-medium text-stone-950 transition hover:bg-orange-300">Browse all fields</a>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($featuredFields as $field)
                    <article class="rounded-3xl border border-white/10 bg-white/5 p-5">
                        <p class="text-sm uppercase tracking-[0.2em] text-orange-300">{{ $field->sport_type }}</p>
                        <h3 class="mt-2 text-2xl font-semibold">{{ $field->name }}</h3>
                        <p class="mt-1 text-stone-300">{{ $field->location }}</p>
                        <p class="mt-4 text-sm text-stone-400">Owner: {{ $field->owner->name }}</p>
                    </article>
                @endforeach
            </div>
        </section>
    </section>
</x-layouts.app>

<x-layouts.app title="Admin Dashboard">
    <section class="space-y-8">
        <div class="space-y-2">
            <p class="text-sm uppercase tracking-[0.2em] text-orange-300">Admin dashboard</p>
            <h1 class="text-4xl font-semibold">Keep the platform healthy and approve new field owners.</h1>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <article class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-stone-400">Registered users</p>
                <p class="mt-3 text-4xl font-semibold">{{ $stats['users'] }}</p>
            </article>
            <article class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-stone-400">Pending owners</p>
                <p class="mt-3 text-4xl font-semibold">{{ $stats['pendingOwners'] }}</p>
            </article>
            <article class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-stone-400">Approved fields</p>
                <p class="mt-3 text-4xl font-semibold">{{ $stats['approvedFields'] }}</p>
            </article>
        </div>

        <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold">Pending field owners</h2>
                    <p class="text-stone-300">Review newly registered owners before they can use the platform.</p>
                </div>
                <a href="{{ route('admin.field-owners.index') }}" class="rounded-full border border-white/10 px-4 py-2 text-sm transition hover:bg-white/10">Open full approval page</a>
            </div>

            <div class="space-y-4">
                @forelse ($pendingOwners as $owner)
                    <div class="flex flex-col gap-4 rounded-3xl border border-white/10 bg-stone-950/60 p-5 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-xl font-semibold">{{ $owner->name }}</p>
                            <p class="text-stone-300">{{ $owner->email }}</p>
                        </div>
                        <form action="{{ route('admin.field-owners.approve', $owner) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="rounded-full bg-emerald-400 px-4 py-2 font-medium text-stone-950 transition hover:bg-emerald-300">Approve owner</button>
                        </form>
                    </div>
                @empty
                    <p class="rounded-3xl border border-dashed border-white/10 px-5 py-6 text-stone-300">No pending field owners right now.</p>
                @endforelse
            </div>
        </section>
    </section>
</x-layouts.app>

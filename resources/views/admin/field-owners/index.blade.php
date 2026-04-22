<x-layouts.app title="Field Owner Approval">
    <section class="space-y-8">
        <div class="space-y-2">
            <p class="text-sm uppercase tracking-[0.2em] text-orange-300">Admin tools</p>
            <h1 class="text-4xl font-semibold">Approve and manage field owner accounts.</h1>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
                <h2 class="text-2xl font-semibold">Pending approval</h2>
                <div class="mt-5 space-y-4">
                    @forelse ($pendingOwners as $owner)
                        <article class="rounded-3xl border border-white/10 bg-stone-950/60 p-5">
                            <p class="text-xl font-semibold">{{ $owner->name }}</p>
                            <p class="mt-1 text-stone-300">{{ $owner->email }}</p>
                            <div class="mt-4 flex gap-3">
                                <form action="{{ route('admin.field-owners.approve', $owner) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-full bg-emerald-400 px-4 py-2 font-medium text-stone-950 transition hover:bg-emerald-300">Approve</button>
                                </form>
                                <form action="{{ route('admin.field-owners.deactivate', $owner) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-full border border-white/10 px-4 py-2 transition hover:bg-white/10">Deactivate</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <p class="rounded-3xl border border-dashed border-white/10 px-5 py-6 text-stone-300">No pending accounts.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
                <h2 class="text-2xl font-semibold">Approved owners</h2>
                <div class="mt-5 space-y-4">
                    @forelse ($approvedOwners as $owner)
                        <article class="rounded-3xl border border-white/10 bg-stone-950/60 p-5">
                            <p class="text-xl font-semibold">{{ $owner->name }}</p>
                            <p class="mt-1 text-stone-300">{{ $owner->email }}</p>
                            <p class="mt-4 inline-flex rounded-full bg-emerald-400/20 px-3 py-1 text-sm text-emerald-200">Active</p>
                        </article>
                    @empty
                        <p class="rounded-3xl border border-dashed border-white/10 px-5 py-6 text-stone-300">No approved field owners yet.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </section>
</x-layouts.app>

<x-layouts.app title="Admin Dashboard">
    <section class="space-y-8">
        <div class="space-y-2">
            <p class="text-sm uppercase tracking-[0.2em] text-orange-300">Admin dashboard</p>
            <h1 class="text-4xl font-semibold">Keep the platform healthy and approve new field owners.</h1>
        </div>

        <div class="grid gap-4 md:grid-cols-4">
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
            <article class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-stone-400">Payment proofs</p>
                <p class="mt-3 text-4xl font-semibold">{{ $stats['pendingPayments'] }}</p>
            </article>
        </div>

        <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
            <div class="mb-5">
                <h2 class="text-2xl font-semibold">Booking payment notifications</h2>
                <p class="text-stone-300">New uploaded proofs that need admin confirmation.</p>
            </div>

            <div class="space-y-3">
                @forelse ($notifications as $notification)
                    <div class="rounded-3xl border border-violet-300/20 bg-violet-300/10 px-5 py-4">
                        <p class="font-medium text-violet-50">{{ $notification->message }}</p>
                        <p class="mt-1 text-sm text-stone-300">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="rounded-3xl border border-dashed border-white/10 px-5 py-6 text-stone-300">No unread booking payment notifications.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6">
            <div class="mb-5">
                <h2 class="text-2xl font-semibold">Confirm booking payments</h2>
                <p class="text-stone-300">Verify uploaded DP proofs to confirm bookings.</p>
            </div>

            <div class="space-y-4">
                @forelse ($pendingPayments as $payment)
                    @php
                        $booking = $payment->booking;
                        $slots = $booking?->bookedSlots
                            ? $booking->bookedSlots->pluck('timeSlot')->filter()->sortBy('start_time')->values()
                            : collect();
                        $slotRange = $slots->isNotEmpty()
                            ? substr($slots->first()->start_time, 0, 5) . ' - ' . substr($slots->last()->end_time, 0, 5)
                            : 'No slots';
                    @endphp
                    <article class="rounded-3xl border border-white/10 bg-stone-950/60 p-5">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <p class="text-xl font-semibold">{{ $booking->field->name }}</p>
                                <p class="mt-1 text-stone-300">{{ $booking->user->name }} • {{ $booking->date->format('M j, Y') }} • {{ $slotRange }}</p>
                                <p class="mt-2 text-sm text-stone-400">DP amount: Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</p>
                                <a href="{{ Illuminate\Support\Facades\Storage::url($payment->proof) }}" target="_blank" class="mt-3 inline-flex rounded-full border border-white/10 px-4 py-2 text-sm font-medium transition hover:bg-white/10">View proof</a>
                            </div>

                            <div class="flex flex-col gap-3 sm:min-w-[280px]">
                                <form action="{{ route('admin.payments.verify', $payment) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full rounded-full bg-emerald-400 px-4 py-2 font-medium text-stone-950 transition hover:bg-emerald-300">Confirm booking</button>
                                </form>

                                <form action="{{ route('admin.payments.reject', $payment) }}" method="POST" class="space-y-2">
                                    @csrf
                                    @method('PATCH')
                                    <input name="rejection_reason" required maxlength="500" placeholder="Reason for rejection" class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-2 text-sm text-white placeholder:text-stone-400">
                                    <button type="submit" class="w-full rounded-full border border-rose-300/40 px-4 py-2 font-medium text-rose-100 transition hover:bg-rose-400/10">Reject proof</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="rounded-3xl border border-dashed border-white/10 px-5 py-6 text-stone-300">No booking payment proofs waiting for confirmation.</p>
                @endforelse
            </div>
        </section>

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

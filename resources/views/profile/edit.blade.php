<x-layouts.app title="{{ __('Profile') }}">
    <section class="mx-auto max-w-3xl space-y-8">
        <div class="space-y-2">
            <p class="text-sm uppercase tracking-[0.2em] text-orange-300">{{ __('Profile') }}</p>
            <h1 class="text-4xl font-semibold">{{ __('Update your account details.') }}</h1>
        </div>

        <section class="rounded-[2rem] border border-white/10 bg-white/5 p-8">
            <div class="mb-6 grid gap-3 md:grid-cols-3">
                <article class="rounded-3xl bg-stone-950/60 p-4">
                    <p class="text-sm text-stone-400">{{ __('Role') }}</p>
                    <p class="mt-2 text-xl font-semibold">{{ __($user->role) }}</p>
                </article>
                <article class="rounded-3xl bg-stone-950/60 p-4">
                    <p class="text-sm text-stone-400">{{ __('Status') }}</p>
                    <p class="mt-2 text-xl font-semibold">{{ __($user->status) }}</p>
                </article>
                <article class="rounded-3xl bg-stone-950/60 p-4">
                    <p class="text-sm text-stone-400">{{ __('Member since') }}</p>
                    <p class="mt-2 text-xl font-semibold">{{ $user->created_at->format('d M Y') }}</p>
                </article>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" class="grid gap-5 md:grid-cols-2">
                @csrf
                @method('PATCH')
                <label class="block space-y-2 md:col-span-2">
                    <span class="text-sm text-stone-300">{{ __('Name') }}</span>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-2xl border border-white/10 bg-stone-950/70 px-4 py-3 outline-none transition focus:border-orange-300" required>
                </label>

                <label class="block space-y-2 md:col-span-2">
                    <span class="text-sm text-stone-300">{{ __('Email Address') }}</span>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-2xl border border-white/10 bg-stone-950/70 px-4 py-3 outline-none transition focus:border-orange-300" required>
                </label>

                <label class="block space-y-2">
                    <span class="text-sm text-stone-300">{{ __('New password') }}</span>
                    <input type="password" name="password" class="w-full rounded-2xl border border-white/10 bg-stone-950/70 px-4 py-3 outline-none transition focus:border-orange-300">
                </label>

                <label class="block space-y-2">
                    <span class="text-sm text-stone-300">{{ __('Confirm new password') }}</span>
                    <input type="password" name="password_confirmation" class="w-full rounded-2xl border border-white/10 bg-stone-950/70 px-4 py-3 outline-none transition focus:border-orange-300">
                </label>

                <button type="submit" class="md:col-span-2 rounded-2xl bg-orange-400 px-5 py-3 font-medium text-stone-950 transition hover:bg-orange-300">
                    {{ __('Save Changes') }}
                </button>
            </form>
        </section>
    </section>
</x-layouts.app>

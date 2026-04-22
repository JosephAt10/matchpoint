<x-layouts.app title="Register">
    <section class="mx-auto max-w-2xl rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-2xl shadow-black/20 backdrop-blur">
        <div class="mb-8 space-y-2">
            <p class="text-sm uppercase tracking-[0.2em] text-orange-300">Get started</p>
            <h1 class="text-3xl font-semibold">Create your MatchPoint account</h1>
            <p class="text-stone-300">Users become active immediately. Field Owners are created with pending approval, matching your SRS.</p>
        </div>

        <form action="{{ route('register.store') }}" method="POST" class="grid gap-5 md:grid-cols-2">
            @csrf
            <label class="block space-y-2 md:col-span-2">
                <span class="text-sm text-stone-300">Full name</span>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-2xl border border-white/10 bg-stone-950/70 px-4 py-3 outline-none transition focus:border-orange-300" required>
            </label>

            <label class="block space-y-2 md:col-span-2">
                <span class="text-sm text-stone-300">Email</span>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-2xl border border-white/10 bg-stone-950/70 px-4 py-3 outline-none transition focus:border-orange-300" required>
            </label>

            <label class="block space-y-2 md:col-span-2">
                <span class="text-sm text-stone-300">Role</span>
                <select name="role" class="w-full rounded-2xl border border-white/10 bg-stone-950/70 px-4 py-3 outline-none transition focus:border-orange-300" required>
                    <option value="User" @selected(old('role') === 'User')>User</option>
                    <option value="FieldOwner" @selected(old('role') === 'FieldOwner')>Field Owner</option>
                </select>
            </label>

            <label class="block space-y-2">
                <span class="text-sm text-stone-300">Password</span>
                <input type="password" name="password" class="w-full rounded-2xl border border-white/10 bg-stone-950/70 px-4 py-3 outline-none transition focus:border-orange-300" required>
            </label>

            <label class="block space-y-2">
                <span class="text-sm text-stone-300">Confirm password</span>
                <input type="password" name="password_confirmation" class="w-full rounded-2xl border border-white/10 bg-stone-950/70 px-4 py-3 outline-none transition focus:border-orange-300" required>
            </label>

            <button type="submit" class="md:col-span-2 rounded-2xl bg-orange-400 px-5 py-3 font-medium text-stone-950 transition hover:bg-orange-300">
                Register account
            </button>
        </form>
    </section>
</x-layouts.app>

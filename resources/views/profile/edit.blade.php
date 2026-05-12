<x-layouts.app title="{{ __('Profile') }}">
    <section class="space-y-6">
        <div class="flex items-center gap-3 text-sm text-[#7b7fa1]">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 transition hover:text-[#4d43cb]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 11.5L12 4l9 7.5"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 10.5V20h14v-9.5"/>
                </svg>
                <span>{{ __('Dashboard') }}</span>
            </a>
            <svg class="h-4 w-4 text-[#b3b6cf]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6"/>
            </svg>
            <span class="font-medium text-[#5b44e8]">{{ __('Profile') }}</span>
        </div>

        <section class="rounded-[2rem] border border-[#ece8ff] bg-white px-6 py-7 shadow-[0_24px_56px_rgba(93,78,190,0.08)] md:px-8 md:py-8">
            <div>
                <h1 class="font-heading text-[28px] font-bold text-[#1d2343] md:text-[36px]">{{ __('Update your account details.') }}</h1>
                <p class="mt-2 text-[15px] text-[#73799d]">{{ __('Keep your information up to date.') }}</p>
            </div>

            <div class="mt-7 grid gap-4 xl:grid-cols-3">
                <article class="rounded-[1.5rem] bg-[#f6f2ff] p-5">
                    <div class="flex items-start gap-4">
                        <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#efe9ff] text-[#6154ef]">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 8a4 4 0 11-8 0 4 4 0 018 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 20a7 7 0 0114 0"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm text-[#6e7397]">{{ __('Role') }}</p>
                            <p class="mt-2 text-lg font-bold text-[#1d2343]">{{ __($user->role) }}</p>
                        </div>
                    </div>
                </article>

                <article class="rounded-[1.5rem] bg-[#f6f2ff] p-5">
                    <div class="flex items-start gap-4">
                        <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#efe9ff] text-[#6154ef]">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm text-[#6e7397]">{{ __('Status') }}</p>
                            <p class="mt-2 text-lg font-bold text-[#1d2343]">{{ __($user->status) }}</p>
                        </div>
                    </div>
                </article>

                <article class="rounded-[1.5rem] bg-[#f6f2ff] p-5">
                    <div class="flex items-start gap-4">
                        <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#efe9ff] text-[#6154ef]">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm text-[#6e7397]">{{ __('Member since') }}</p>
                            <p class="mt-2 text-lg font-bold text-[#1d2343]">{{ $user->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </article>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" novalidate class="mt-8 space-y-6">
                @csrf
                @method('PATCH')

                <div class="grid gap-6 md:grid-cols-2">
                    <label class="block space-y-2 md:col-span-2">
                        <span class="text-sm font-medium text-[#404666]">{{ __('Name') }}</span>
                        <div class="flex items-center gap-3 rounded-2xl border {{ $errors->has('name') ? 'border-rose-300' : 'border-[#e2ddf6]' }} bg-white px-4 py-4 shadow-[inset_0_1px_0_rgba(255,255,255,0.7)]">
                            <svg class="h-5 w-5 text-[#8d8ab2]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 8a4 4 0 11-8 0 4 4 0 018 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 20a7 7 0 0114 0"/>
                            </svg>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border-0 bg-transparent p-0 text-[16px] text-[#24283d] outline-none placeholder:text-[#9a98b9]" placeholder="{{ __('Full Name') }}" required>
                        </div>
                        <x-forms.error field="name" />
                    </label>

                    <label class="block space-y-2 md:col-span-2">
                        <span class="text-sm font-medium text-[#404666]">{{ __('Email Address') }}</span>
                        <div class="flex items-center gap-3 rounded-2xl border {{ $errors->has('email') ? 'border-rose-300' : 'border-[#e2ddf6]' }} bg-white px-4 py-4 shadow-[inset_0_1px_0_rgba(255,255,255,0.7)]">
                            <svg class="h-5 w-5 text-[#8d8ab2]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8m-16 9h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                            </svg>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border-0 bg-transparent p-0 text-[16px] text-[#24283d] outline-none placeholder:text-[#9a98b9]" placeholder="{{ __('Email Address') }}" required>
                        </div>
                        <x-forms.error field="email" />
                    </label>

                    <label class="block space-y-2">
                        <span class="text-sm font-medium text-[#404666]">{{ __('New password') }}</span>
                        <div class="flex items-center gap-3 rounded-2xl border {{ $errors->has('password') ? 'border-rose-300' : 'border-[#e2ddf6]' }} bg-white px-4 py-4 shadow-[inset_0_1px_0_rgba(255,255,255,0.7)]">
                            <svg class="h-5 w-5 text-[#8d8ab2]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.105 0 2 .672 2 1.5S13.105 14 12 14s-2-.672-2-1.5S10.895 11 12 11z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 11V8a5 5 0 10-10 0v3M6 11h12a1 1 0 011 1v6a1 1 0 01-1 1H6a1 1 0 01-1-1v-6a1 1 0 011-1z"/>
                            </svg>
                            <input type="password" name="password" class="w-full border-0 bg-transparent p-0 text-[16px] text-[#24283d] outline-none placeholder:text-[#9a98b9]" placeholder="{{ __('Enter new password') }}">
                        </div>
                        <x-forms.error field="password" />
                    </label>

                    <label class="block space-y-2">
                        <span class="text-sm font-medium text-[#404666]">{{ __('Confirm new password') }}</span>
                        <div class="flex items-center gap-3 rounded-2xl border {{ $errors->has('password_confirmation') || $errors->has('password') ? 'border-rose-300' : 'border-[#e2ddf6]' }} bg-white px-4 py-4 shadow-[inset_0_1px_0_rgba(255,255,255,0.7)]">
                            <svg class="h-5 w-5 text-[#8d8ab2]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.105 0 2 .672 2 1.5S13.105 14 12 14s-2-.672-2-1.5S10.895 11 12 11z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 11V8a5 5 0 10-10 0v3M6 11h12a1 1 0 011 1v6a1 1 0 01-1 1H6a1 1 0 01-1-1v-6a1 1 0 011-1z"/>
                            </svg>
                            <input type="password" name="password_confirmation" class="w-full border-0 bg-transparent p-0 text-[16px] text-[#24283d] outline-none placeholder:text-[#9a98b9]" placeholder="{{ __('Confirm new password') }}">
                        </div>
                        <x-forms.error field="password_confirmation" />
                    </label>
                </div>

                <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-[#f97316] to-[#ff8a1e] px-6 py-4 text-[18px] font-bold text-white shadow-[0_18px_32px_rgba(249,115,22,0.24)] transition hover:opacity-95">
                    {{ __('Save Changes') }}
                </button>
            </form>
        </section>
    </section>
</x-layouts.app>

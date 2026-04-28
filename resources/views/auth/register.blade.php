<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - MatchPoint</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#1b2248',
                        indigo: '#6d63ea',
                        indigoDark: '#5747d8',
                        softBg: '#f8f7ff',
                        softBorder: '#e8e3fb',
                        copy: '#6e7395',
                    },
                    fontFamily: {
                        heading: ['Outfit', 'sans-serif'],
                        body: ['DM Sans', 'sans-serif'],
                    },
                    boxShadow: {
                        auth: '0 24px 48px rgba(96, 85, 183, 0.18)',
                        sport: '0 14px 26px rgba(106, 92, 224, 0.12)',
                    },
                }
            }
        };
    </script>
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-white text-[#22263d]">
    <main class="grid min-h-screen lg:grid-cols-2">
        <section class="relative hidden overflow-hidden lg:block">
            <img src="{{ asset('landing/login-page-image.png') }}" alt="MatchPoint multi-sport register" class="absolute inset-0 h-full w-full object-cover object-left opacity-40">
            <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(8,7,40,0.88)_0%,rgba(19,16,70,0.76)_34%,rgba(44,35,111,0.52)_64%,rgba(95,77,190,0.26)_100%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(145,118,255,0.18),transparent_34%),linear-gradient(180deg,rgba(8,7,40,0.18)_0%,rgba(8,7,40,0.46)_100%)]"></div>

            <div class="relative z-10 flex h-full items-start px-14 pt-12">
                <div class="max-w-[430px] text-white">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('landing/matchpoint-logo.png') }}" alt="MatchPoint logo" class="h-12 w-12 object-contain">
                        <span class="font-heading text-[32px] font-bold tracking-tight">MatchPoint</span>
                    </div>

                    <div class="mt-16 max-w-[290px]">
                        <h2 class="font-heading text-[38px] font-bold leading-[1.08] tracking-tight">
                            Every Game.<br>
                            Every Player.<br>
                            <span class="text-[#836dff]">One Platform.</span>
                        </h2>
                        <p class="mt-5 text-[16px] leading-8 text-white/88">
                            Book any sport, any time.
                            Play, compete, and connect
                            with players near you.
                        </p>
                    </div>

                    <div class="mt-20 grid max-w-[290px] grid-cols-3 gap-5 text-center">
                        <div>
                            <p class="font-heading text-[28px] font-bold text-[#8b78ff]">10K+</p>
                            <p class="mt-1 text-[12px] text-white/78">Active Players</p>
                        </div>
                        <div>
                            <p class="font-heading text-[28px] font-bold text-[#8b78ff]">500+</p>
                            <p class="mt-1 text-[12px] text-white/78">Fields</p>
                        </div>
                        <div>
                            <p class="font-heading text-[28px] font-bold text-[#8b78ff]">20+</p>
                            <p class="mt-1 text-[12px] text-white/78">Sports</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="flex items-center justify-center bg-[radial-gradient(circle_at_top_right,_rgba(138,118,255,0.14),_transparent_22%),linear-gradient(180deg,_#ffffff_0%,_#faf8ff_100%)] px-5 py-8 lg:px-10">
            <div class="w-full max-w-[640px] rounded-[2rem] border border-softBorder bg-white p-6 shadow-auth md:p-8">
                @if ($errors->any())
                    <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="text-center">
                    <h1 class="font-heading text-[42px] font-bold tracking-tight text-[#292d43] md:text-[48px]">Create Your Account</h1>
                    <p class="mt-2 text-[17px] text-copy">Join MatchPoint and start your game journey</p>
                </div>

                <div class="mt-7 grid grid-cols-4 gap-2 text-center">
                    <div class="space-y-2">
                        <div class="mx-auto flex h-9 w-9 items-center justify-center rounded-full bg-indigo text-sm font-bold text-white">1</div>
                        <p class="text-[12px] font-semibold text-indigo">Personal Info</p>
                    </div>
                    <div class="space-y-2">
                        <div class="mx-auto flex h-9 w-9 items-center justify-center rounded-full border border-softBorder bg-white text-sm font-bold text-[#9a97bb]">2</div>
                        <p class="text-[12px] text-[#9a97bb]">Account Info</p>
                    </div>
                    <div class="space-y-2">
                        <div class="mx-auto flex h-9 w-9 items-center justify-center rounded-full border border-softBorder bg-white text-sm font-bold text-[#9a97bb]">3</div>
                        <p class="text-[12px] text-[#9a97bb]">Preferences</p>
                    </div>
                    <div class="space-y-2">
                        <div class="mx-auto flex h-9 w-9 items-center justify-center rounded-full border border-softBorder bg-white text-sm font-bold text-[#9a97bb]">4</div>
                        <p class="text-[12px] text-[#9a97bb]">You're In!</p>
                    </div>
                </div>

                <form action="{{ route('register.store') }}" method="POST" class="mt-7 space-y-4">
                    @csrf
                    <input type="hidden" name="role" value="User">

                    <label class="block">
                        <div class="flex items-center gap-3 rounded-2xl border border-softBorder bg-white px-4 py-4">
                            <svg class="h-5 w-5 text-[#9994b9]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Full Name" class="w-full border-0 p-0 text-[16px] text-[#363b55] outline-none placeholder:text-[#9d98ba]" required>
                        </div>
                    </label>

                    <label class="block">
                        <div class="flex items-center gap-3 rounded-2xl border border-softBorder bg-white px-4 py-4">
                            <svg class="h-5 w-5 text-[#9994b9]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8m-16 9h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                            </svg>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" class="w-full border-0 p-0 text-[16px] text-[#363b55] outline-none placeholder:text-[#9d98ba]" required>
                        </div>
                    </label>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="flex items-center gap-3 rounded-2xl border border-softBorder bg-white px-4 py-4 text-[#9d98ba]">
                            <svg class="h-5 w-5 text-[#9994b9]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/>
                            </svg>
                            <span class="text-[16px]">Date of Birth</span>
                        </div>

                        <div class="flex items-center justify-between gap-3 rounded-2xl border border-softBorder bg-white px-4 py-4 text-[#9d98ba]">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5 text-[#9994b9]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="10" cy="8" r="3"></circle>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 19a5 5 0 0110 0M19 8h3m-1.5-1.5v3"/>
                                </svg>
                                <span class="text-[16px]">Gender</span>
                            </div>
                            <svg class="h-5 w-5 text-[#9994b9]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/>
                            </svg>
                        </div>
                    </div>

                    <div class="pt-1">
                        <div class="flex items-baseline gap-2">
                            <h2 class="font-heading text-[19px] font-bold text-[#292d43]">Choose Your Sport Interests</h2>
                            <span class="text-[12px] text-copy">(Select all that you play)</span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-3">
                            <div class="rounded-2xl border-2 border-indigo bg-[#fbfaff] p-4 text-center shadow-sport">
                                <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-[#efebff] text-[22px]">⚽</div>
                                <p class="mt-3 text-[13px] font-semibold text-[#31355c]">Football / Futsal</p>
                            </div>
                            <div class="rounded-2xl border border-softBorder bg-white p-4 text-center">
                                <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-[#f8f7ff] text-[22px]">🏀</div>
                                <p class="mt-3 text-[13px] font-semibold text-[#5f6488]">Basketball</p>
                            </div>
                            <div class="rounded-2xl border border-softBorder bg-white p-4 text-center">
                                <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-[#f8f7ff] text-[22px]">🎾</div>
                                <p class="mt-3 text-[13px] font-semibold text-[#5f6488]">Tennis</p>
                            </div>
                            <div class="rounded-2xl border border-softBorder bg-white p-4 text-center">
                                <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-[#f8f7ff] text-[22px]">🏸</div>
                                <p class="mt-3 text-[13px] font-semibold text-[#5f6488]">Badminton</p>
                            </div>
                            <div class="rounded-2xl border border-softBorder bg-white p-4 text-center">
                                <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-[#f8f7ff] text-[22px]">🏐</div>
                                <p class="mt-3 text-[13px] font-semibold text-[#5f6488]">Volleyball</p>
                            </div>
                            <div class="rounded-2xl border border-softBorder bg-white p-4 text-center">
                                <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-[#f8f7ff] text-[22px]">•••</div>
                                <p class="mt-3 text-[13px] font-semibold text-[#5f6488]">Other</p>
                            </div>
                        </div>
                    </div>

                    <label class="block">
                        <div class="flex items-center gap-3 rounded-2xl border border-softBorder bg-white px-4 py-4">
                            <svg class="h-5 w-5 text-[#9994b9]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.105 0 2 .672 2 1.5S13.105 14 12 14s-2-.672-2-1.5S10.895 11 12 11z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 11V8a5 5 0 10-10 0v3M6 11h12a1 1 0 011 1v6a1 1 0 01-1 1H6a1 1 0 01-1-1v-6a1 1 0 011-1z"/>
                            </svg>
                            <input type="password" name="password" placeholder="Create Password" class="w-full border-0 p-0 text-[16px] text-[#363b55] outline-none placeholder:text-[#9d98ba]" required>
                            <svg class="h-5 w-5 text-[#9994b9]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </div>
                    </label>

                    <label class="block">
                        <div class="flex items-center gap-3 rounded-2xl border border-softBorder bg-white px-4 py-4">
                            <svg class="h-5 w-5 text-[#9994b9]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.105 0 2 .672 2 1.5S13.105 14 12 14s-2-.672-2-1.5S10.895 11 12 11z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 11V8a5 5 0 10-10 0v3M6 11h12a1 1 0 011 1v6a1 1 0 01-1 1H6a1 1 0 01-1-1v-6a1 1 0 011-1z"/>
                            </svg>
                            <input type="password" name="password_confirmation" placeholder="Confirm Password" class="w-full border-0 p-0 text-[16px] text-[#363b55] outline-none placeholder:text-[#9d98ba]" required>
                            <svg class="h-5 w-5 text-[#9994b9]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </div>
                    </label>

                    <div class="rounded-2xl border border-[#e5e0fb] bg-[#f6f3ff] px-4 py-3">
                        <p class="text-[12px] font-semibold text-indigo">Password must contain:</p>
                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-2 text-[12px] text-[#6a6f92]">
                            <span class="flex items-center gap-1.5"><span class="text-green-600">●</span> At least 8 characters</span>
                            <span class="flex items-center gap-1.5"><span class="text-green-600">●</span> One uppercase letter</span>
                            <span class="flex items-center gap-1.5"><span class="text-green-600">●</span> One number or symbol</span>
                        </div>
                    </div>

                    <label class="flex items-start gap-3 pt-1 text-[14px] text-[#5f6488]">
                        <input type="checkbox" class="mt-1 h-4 w-4 rounded border-softBorder text-indigo focus:ring-indigo">
                        <span>I agree to the <a href="{{ route('terms') }}" class="font-semibold text-indigo hover:text-indigoDark">Terms &amp; Conditions</a> and <a href="{{ route('privacy') }}" class="font-semibold text-indigo hover:text-indigoDark">Privacy Policy</a></span>
                    </label>

                    <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-indigoDark to-indigo px-6 py-4 text-[18px] font-bold text-white transition hover:opacity-95">
                        Continue
                    </button>

                    <div class="flex items-center gap-4">
                        <div class="h-px flex-1 bg-softBorder"></div>
                        <span class="text-[15px] text-copy">OR</span>
                        <div class="h-px flex-1 bg-softBorder"></div>
                    </div>

                    <button type="button" class="flex w-full items-center justify-center gap-3 rounded-2xl border border-softBorder bg-white px-6 py-4 text-[16px] font-semibold text-[#4a4f68] shadow-sm transition hover:bg-softBg">
                        <img src="{{ asset('landing/social/google-logo.png') }}" alt="Google" class="h-6 w-6 object-contain">
                        <span>Sign up with Google</span>
                    </button>
                </form>

                <p class="mt-6 text-center text-[15px] text-copy">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold text-indigo hover:text-indigoDark">Login here</a>
                </p>
            </div>
        </section>
    </main>
</body>
</html>

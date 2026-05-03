<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MatchPoint</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                        copy: '#5d6285',
                    },
                    fontFamily: {
                        heading: ['Outfit', 'sans-serif'],
                        body: ['DM Sans', 'sans-serif'],
                    },
                    boxShadow: {
                        auth: '0 24px 48px rgba(96, 85, 183, 0.18)',
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
            <img src="{{ asset('landing/login-page-image.png') }}" alt="MatchPoint multi-sport login" class="absolute inset-0 h-full w-full object-cover object-left opacity-42">
            <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(8,7,40,0.88)_0%,rgba(19,16,70,0.76)_34%,rgba(44,35,111,0.52)_64%,rgba(95,77,190,0.26)_100%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(145,118,255,0.18),transparent_34%),linear-gradient(180deg,rgba(8,7,40,0.18)_0%,rgba(8,7,40,0.46)_100%)]"></div>
            <div class="relative z-10 flex h-full items-start px-14 pt-12">
                <div class="max-w-[430px] text-white">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('landing/matchpoint-logo.png') }}" alt="MatchPoint logo" class="h-12 w-12 object-contain">
                        <span class="font-heading text-[32px] font-bold tracking-tight">MatchPoint</span>
                    </div>
                    <div class="mt-16 max-w-[280px]">
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

        <section class="flex items-center justify-center bg-[radial-gradient(circle_at_top_right,_rgba(138,118,255,0.14),_transparent_22%),linear-gradient(180deg,_#ffffff_0%,_#faf8ff_100%)] px-6 py-12 lg:px-10">
            <div class="w-full max-w-[540px] rounded-[2rem] border border-softBorder bg-white p-7 shadow-auth md:p-10">
                @if (session('status'))
                    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

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
                    <h1 class="font-heading text-[48px] font-bold tracking-tight text-[#292d43] md:text-[52px]">Welcome Back</h1>
                    <p class="mt-3 text-[20px] text-copy md:text-[18px]">Sign in to continue your match journey</p>
                </div>

                <form action="{{ route('login.store') }}" method="POST" class="mt-10 space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="mb-3 block text-[20px] font-semibold text-[#353a52] md:text-[16px]">Email Address</label>
                        <div class="flex items-center gap-3 rounded-2xl border border-softBorder bg-white px-4 py-4">
                            <svg class="h-5 w-5 text-[#9994b9]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8m-16 9h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                            </svg>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" class="w-full border-0 p-0 text-[19px] text-[#363b55] outline-none placeholder:text-[#9d98ba] md:text-[16px]" required>
                        </div>
                    </div>

                    <div>
                        <label for="password" class="mb-3 block text-[20px] font-semibold text-[#353a52] md:text-[16px]">Password</label>
                        <div class="flex items-center gap-3 rounded-2xl border border-softBorder bg-white px-4 py-4">
                            <svg class="h-5 w-5 text-[#9994b9]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.105 0 2 .672 2 1.5S13.105 14 12 14s-2-.672-2-1.5S10.895 11 12 11z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 11V8a5 5 0 10-10 0v3M6 11h12a1 1 0 011 1v6a1 1 0 01-1 1H6a1 1 0 01-1-1v-6a1 1 0 011-1z"/>
                            </svg>
                            <input id="password" type="password" name="password" placeholder="Password" class="w-full border-0 p-0 text-[19px] text-[#363b55] outline-none placeholder:text-[#9d98ba] md:text-[16px]" required>
                            <button type="button" id="toggle-password" class="text-[#9994b9] transition hover:text-[#6d63ea]" aria-label="Show password" aria-pressed="false">
                                <svg id="password-eye-open" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg id="password-eye-closed" class="hidden h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58A2 2 0 0012 14a2 2 0 001.42-.58"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 5.09A9.77 9.77 0 0112 5c7 0 11 7 11 7a21.76 21.76 0 01-5.17 5.94"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.1 6.1A21.8 21.8 0 001 12s4 7 11 7a10.7 10.7 0 005.04-1.24"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 text-[18px] text-copy md:flex-row md:items-center md:justify-between md:text-[16px]">
                        <label class="flex items-center gap-3">
                            <input type="checkbox" name="remember" value="1" class="h-5 w-5 rounded border-softBorder text-indigo focus:ring-indigo">
                            <span>Remember me</span>
                        </label>
                        <a href="#" class="font-medium text-indigo hover:text-indigoDark">Forgot password?</a>
                    </div>

                    <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-indigoDark to-indigo px-6 py-4 text-[22px] font-bold text-white transition hover:opacity-95 md:text-[18px]">
                        Sign in
                    </button>

                    <div class="flex items-center gap-4">
                        <div class="h-px flex-1 bg-softBorder"></div>
                        <span class="text-[18px] text-copy md:text-[16px]">or</span>
                        <div class="h-px flex-1 bg-softBorder"></div>
                    </div>

                    <button type="button" class="flex w-full items-center justify-center gap-3 rounded-2xl border border-softBorder bg-white px-6 py-4 text-[20px] font-semibold text-[#4a4f68] shadow-sm transition hover:bg-softBg md:text-[17px]">
                        <img src="{{ asset('landing/social/google-logo.png') }}" alt="Google" class="h-6 w-6 object-contain">
                        <span>Continue with Google</span>
                    </button>
                </form>

                <p class="mt-8 text-center text-[18px] text-copy md:text-[16px]">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-semibold text-indigo hover:text-indigoDark">Sign Up</a>
                </p>
            </div>
        </section>
    </main>
    <script>
        const passwordInput = document.getElementById('password');
        const togglePasswordButton = document.getElementById('toggle-password');
        const eyeOpenIcon = document.getElementById('password-eye-open');
        const eyeClosedIcon = document.getElementById('password-eye-closed');

        if (passwordInput && togglePasswordButton && eyeOpenIcon && eyeClosedIcon) {
            togglePasswordButton.addEventListener('click', () => {
                const isHidden = passwordInput.type === 'password';

                passwordInput.type = isHidden ? 'text' : 'password';
                togglePasswordButton.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
                togglePasswordButton.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
                eyeOpenIcon.classList.toggle('hidden', isHidden);
                eyeClosedIcon.classList.toggle('hidden', ! isHidden);
            });
        }
    </script>
</body>
</html>

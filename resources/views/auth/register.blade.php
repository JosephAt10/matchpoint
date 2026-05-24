<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Create Account') }} - MatchPoint</title>
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
                        ink: '#13203a',
                        copy: '#66748c',
                        line: '#e7edf5',
                        green: '#16c75a',
                        greenDeep: '#0ea74b',
                    },
                    fontFamily: {
                        heading: ['Outfit', 'sans-serif'],
                        body: ['DM Sans', 'sans-serif'],
                    },
                    boxShadow: {
                        panel: '0 28px 70px rgba(20, 32, 58, 0.14)',
                        soft: '0 14px 30px rgba(20, 32, 58, 0.08)',
                    },
                },
            },
        };
    </script>
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Outfit', sans-serif; }
        body {
            overflow-x: hidden;
            background: radial-gradient(circle at top center, rgba(255,255,255,0.96) 0%, rgba(245,248,253,0.96) 38%, rgba(232,238,247,0.98) 100%);
        }
        .arena-floor {
            position: absolute;
            inset: auto 0 0 0;
            height: 30vh;
            background:
                radial-gradient(circle at 50% -10%, rgba(255,255,255,0.95), rgba(255,255,255,0.5) 42%, transparent 62%),
                linear-gradient(180deg, rgba(15, 29, 54, 0) 0%, rgba(10, 21, 40, 0.84) 24%, #071120 100%);
            clip-path: ellipse(88% 100% at 50% 100%);
        }
        .arena-floor::before,
        .arena-floor::after {
            content: '';
            position: absolute;
            left: -8%;
            right: -8%;
            height: 2px;
            border-radius: 999px;
        }
        .arena-floor::before {
            top: 30%;
            background: linear-gradient(90deg, transparent, rgba(74, 222, 128, 0.85), transparent);
            box-shadow: 0 0 22px rgba(74, 222, 128, 0.45);
            transform: rotate(-7deg);
        }
        .arena-floor::after {
            top: 54%;
            background: linear-gradient(90deg, transparent, rgba(74, 222, 128, 0.45), transparent);
            box-shadow: 0 0 18px rgba(74, 222, 128, 0.28);
            transform: rotate(8deg);
        }
        .motion-line {
            position: absolute;
            border-radius: 999px;
            border: 1.5px solid rgba(34, 197, 94, 0.44);
            border-color: rgba(34, 197, 94, 0.4) transparent transparent transparent;
            animation: driftLine 18s ease-in-out infinite;
        }
        .motion-line.alt { animation-duration: 24s; animation-delay: -6s; }
        .motion-line.soft { border-color: rgba(255,255,255,0.68) transparent transparent transparent; animation-duration: 28s; }
        .particle {
            position: absolute;
            border-radius: 999px;
            background: rgba(134, 239, 172, 0.9);
            box-shadow: 0 0 14px rgba(134, 239, 172, 0.55);
            animation: floatParticle 12s ease-in-out infinite;
        }
        .spark {
            position: absolute;
            color: rgba(74, 222, 128, 0.7);
            animation: pulseGlow 7s ease-in-out infinite;
        }
        .sport-asset {
            position: absolute;
            animation: floatGhost 16s ease-in-out infinite;
        }
        .sport-asset img {
            display: block;
            max-width: 100%;
            height: auto;
            filter: drop-shadow(0 16px 24px rgba(161, 175, 194, 0.22));
        }
        .register-card {
            backdrop-filter: blur(18px);
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.82);
            box-shadow: 0 28px 70px rgba(20, 32, 58, 0.15), inset 0 1px 0 rgba(255,255,255,0.8);
        }
        .field-shell {
            border: 1px solid #e7edf5;
            box-shadow: 0 10px 24px rgba(20, 32, 58, 0.05);
        }
        .field-shell.is-error {
            border-color: #fb7185;
        }
        .field-shell:focus-within {
            border-color: #6d63ea;
            box-shadow: 0 0 0 4px rgba(109, 99, 234, 0.14);
        }
        .field-shell.is-error:focus-within {
            border-color: #fb7185;
            box-shadow: none;
        }
        @keyframes floatGhost {
            0%, 100% { transform: translate3d(0, 0, 0) rotate(0deg); }
            50% { transform: translate3d(0, -14px, 0) rotate(3deg); }
        }
        @keyframes floatParticle {
            0%, 100% { transform: translate3d(0, 0, 0) scale(1); opacity: 0.72; }
            50% { transform: translate3d(0, -18px, 0) scale(1.08); opacity: 1; }
        }
        @keyframes driftLine {
            0%, 100% { transform: translate3d(0, 0, 0) rotate(-8deg); opacity: 0.55; }
            50% { transform: translate3d(12px, -8px, 0) rotate(-5deg); opacity: 0.9; }
        }
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.38; transform: scale(1); }
            50% { opacity: 0.95; transform: scale(1.08); }
        }
    </style>
</head>
<body class="min-h-screen text-ink">
    <main class="relative min-h-screen overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute left-8 top-8 z-20 flex items-center gap-4">
                <img src="{{ asset('landing/matchpoint-logo.png') }}" alt="{{ __('MatchPoint logo') }}" class="h-14 w-14 object-contain">
                <p class="font-heading text-[28px] font-bold tracking-[0.14em] text-[#23357a] md:text-[32px]">{{ __('MATCHPOINT') }}</p>
            </div>

            <div class="absolute right-8 top-8 z-20">
                @include('partials.locale-switcher')
            </div>

            <div class="sport-asset left-[8%] top-[16%] hidden w-[220px] md:block">
                <img src="{{ asset('landing/futsal-svg.svg') }}" alt="" aria-hidden="true">
            </div>
            <div class="sport-asset right-[16%] top-[12%] hidden w-[170px] opacity-70 xl:block" style="animation-delay:-4s;">
                <img src="{{ asset('landing/basketball-svg.svg') }}" alt="" aria-hidden="true">
            </div>
            <div class="sport-asset left-[2%] bottom-[26%] w-[145px]" style="animation-delay:-2s;">
                <img src="{{ asset('landing/BadmintoRacker-svg.svg') }}" alt="" aria-hidden="true">
            </div>
            <div class="sport-asset right-[8%] bottom-[14%] w-[138px]" style="animation-delay:-10s;">
                <img src="{{ asset('landing/Tennis-svg.svg') }}" alt="" aria-hidden="true">
            </div>
            <div class="sport-asset left-[18%] bottom-[10%] hidden w-[170px] opacity-80 lg:block" style="animation-delay:-7s;">
                <img src="{{ asset('landing/Football-svg.svg') }}" alt="" aria-hidden="true">
            </div>

            <div class="motion-line left-[-12%] bottom-[24%] h-[240px] w-[62%]"></div>
            <div class="motion-line alt right-[-10%] bottom-[22%] h-[220px] w-[58%]"></div>
            <div class="motion-line soft left-[18%] bottom-[36%] h-[180px] w-[40%]"></div>
            <div class="motion-line soft right-[14%] bottom-[34%] h-[170px] w-[34%]"></div>

            <span class="particle left-[11%] top-[20%] h-3 w-3" style="animation-delay:-2s;"></span>
            <span class="particle left-[22%] top-[58%] h-2.5 w-2.5" style="animation-delay:-8s;"></span>
            <span class="particle left-[73%] top-[24%] h-2 w-2" style="animation-delay:-4s;"></span>
            <span class="particle right-[18%] top-[66%] h-4 w-4" style="animation-delay:-6s;"></span>
            <span class="particle right-[8%] bottom-[14%] h-2.5 w-2.5" style="animation-delay:-10s;"></span>
            <span class="particle left-[30%] bottom-[22%] h-5 w-5 opacity-70" style="animation-delay:-5s;"></span>

            <div class="spark left-[17%] top-[44%]">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none"><path d="M14 4V24M4 14H24" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <div class="spark right-[9%] top-[27%]" style="animation-delay:-3s;">
                <svg width="26" height="26" viewBox="0 0 26 26" fill="none"><path d="M13 4V22M4 13H22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <div class="spark right-[23%] top-[8%]" style="animation-delay:-6s;">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M10 3V17M3 10H17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </div>

            <div class="absolute right-[6%] top-[8%] hidden md:grid grid-cols-5 gap-3 opacity-50">
                @for ($i = 0; $i < 20; $i++)
                    <span class="h-1.5 w-1.5 rounded-full bg-green/60"></span>
                @endfor
            </div>

            <div class="arena-floor"></div>
        </div>

        <div class="relative z-10 flex min-h-screen items-center justify-center px-4 py-24">
            <section class="register-card w-full max-w-[560px] rounded-[32px] p-6 shadow-panel sm:p-8 md:p-10">
                <div class="text-center">
                    <div class="mx-auto flex h-28 w-28 items-center justify-center rounded-[32px] bg-[linear-gradient(180deg,#f6fff9_0%,#ffffff_100%)] shadow-soft">
                        <img src="{{ asset('landing/matchpoint-logo.png') }}" alt="{{ __('MatchPoint logo') }}" class="h-20 w-20 object-contain">
                    </div>
                    <h1 class="mt-6 font-heading text-[38px] font-bold tracking-tight text-ink sm:text-[44px]">{{ __('Create Your Account') }}</h1>
                    <p class="mt-3 text-[18px] text-copy">{{ __('Join MatchPoint and start your game journey today.') }}</p>
                </div>

                <form action="{{ route('register.store') }}" method="POST" novalidate class="mt-8 space-y-5">
                    @csrf

                    <label class="block">
                        <span class="mb-2 block text-[13px] font-semibold text-ink">{{ __('Full Name') }}</span>
                        <div class="field-shell {{ $errors->has('name') ? 'is-error' : '' }} flex items-center gap-3 rounded-[18px] bg-white px-4 py-4">
                            <svg class="h-5 w-5 text-green" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('Enter your full name') }}" class="w-full border-0 bg-transparent p-0 text-[16px] text-ink outline-none placeholder:text-[#9d98ba]" required>
                        </div>
                        <x-forms.error field="name" />
                    </label>

                    <label class="block">
                        <span class="mb-2 block text-[13px] font-semibold text-ink">{{ __('Email Address') }}</span>
                        <div class="field-shell {{ $errors->has('email') ? 'is-error' : '' }} flex items-center gap-3 rounded-[18px] bg-white px-4 py-4">
                            <svg class="h-5 w-5 text-green" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8m-16 9h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Enter your email address') }}" class="w-full border-0 bg-transparent p-0 text-[16px] text-ink outline-none placeholder:text-[#9d98ba]" required>
                        </div>
                        <x-forms.error field="email" />
                    </label>

                    <label class="block">
                        <span class="mb-2 block text-[13px] font-semibold text-ink">{{ __('Password') }}</span>
                        <div class="field-shell {{ $errors->has('password') ? 'is-error' : '' }} flex items-center gap-3 rounded-[18px] bg-white px-4 py-4">
                            <svg class="h-5 w-5 text-green" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.105 0 2 .672 2 1.5S13.105 14 12 14s-2-.672-2-1.5S10.895 11 12 11z"/><path stroke-linecap="round" stroke-linejoin="round" d="M17 11V8a5 5 0 10-10 0v3M6 11h12a1 1 0 011 1v6a1 1 0 01-1 1H6a1 1 0 01-1-1v-6a1 1 0 011-1z"/></svg>
                            <input id="register-password" type="password" name="password" placeholder="{{ __('Create a password') }}" class="w-full border-0 bg-transparent p-0 text-[16px] text-ink outline-none placeholder:text-[#9d98ba]" required>
                            <button type="button" data-password-toggle="register-password" class="text-[#97a2b9] transition hover:text-[#6d63ea]" aria-label="{{ __('Password') }}" aria-pressed="false">
                                <svg data-eye-open class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"></circle></svg>
                                <svg data-eye-closed class="hidden h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/><path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58A2 2 0 0012 14a2 2 0 001.42-.58"/><path stroke-linecap="round" stroke-linejoin="round" d="M9.88 5.09A9.77 9.77 0 0112 5c7 0 11 7 11 7a21.76 21.76 0 01-5.17 5.94"/><path stroke-linecap="round" stroke-linejoin="round" d="M6.1 6.1A21.8 21.8 0 001 12s4 7 11 7a10.7 10.7 0 005.04-1.24"/></svg>
                            </button>
                        </div>
                        <x-forms.error field="password" />
                    </label>

                    <label class="block">
                        <span class="mb-2 block text-[13px] font-semibold text-ink">{{ __('Confirm Password') }}</span>
                        <div class="field-shell {{ $errors->has('password_confirmation') || $errors->has('password') ? 'is-error' : '' }} flex items-center gap-3 rounded-[18px] bg-white px-4 py-4">
                            <svg class="h-5 w-5 text-green" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.105 0 2 .672 2 1.5S13.105 14 12 14s-2-.672-2-1.5S10.895 11 12 11z"/><path stroke-linecap="round" stroke-linejoin="round" d="M17 11V8a5 5 0 10-10 0v3M6 11h12a1 1 0 011 1v6a1 1 0 01-1 1H6a1 1 0 01-1-1v-6a1 1 0 011-1z"/></svg>
                            <input id="register-password-confirmation" type="password" name="password_confirmation" placeholder="{{ __('Confirm your password') }}" class="w-full border-0 bg-transparent p-0 text-[16px] text-ink outline-none placeholder:text-[#9d98ba]" required>
                            <button type="button" data-password-toggle="register-password-confirmation" class="text-[#97a2b9] transition hover:text-[#6d63ea]" aria-label="{{ __('Confirm Password') }}" aria-pressed="false">
                                <svg data-eye-open class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"></circle></svg>
                                <svg data-eye-closed class="hidden h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/><path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58A2 2 0 0012 14a2 2 0 001.42-.58"/><path stroke-linecap="round" stroke-linejoin="round" d="M9.88 5.09A9.77 9.77 0 0112 5c7 0 11 7 11 7a21.76 21.76 0 01-5.17 5.94"/><path stroke-linecap="round" stroke-linejoin="round" d="M6.1 6.1A21.8 21.8 0 001 12s4 7 11 7a10.7 10.7 0 005.04-1.24"/></svg>
                            </button>
                        </div>
                        <x-forms.error field="password_confirmation" />
                    </label>

                    <label class="block">
                        <span class="mb-2 block text-[13px] font-semibold text-ink">{{ __('Register as') }}</span>
                        <div class="field-shell {{ $errors->has('role') ? 'is-error' : '' }} flex items-center gap-3 rounded-[18px] bg-white px-4 py-4">
                            <svg class="h-5 w-5 text-green" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path stroke-linecap="round" stroke-linejoin="round" d="M23 21v-2a4 4 0 00-3-3.87"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 010 7.75"/></svg>
                            <select name="role" class="w-full border-0 bg-transparent p-0 text-[16px] text-ink outline-none" required>
                                <option value="User" @selected(old('role', 'User') === 'User')>{{ __('User') }}</option>
                                <option value="FieldOwner" @selected(old('role') === 'FieldOwner')>{{ __('Field Owner') }}</option>
                            </select>
                        </div>
                        <x-forms.error field="role" />
                    </label>

                    <button type="submit" class="flex w-full items-center justify-center gap-3 rounded-[18px] bg-gradient-to-r from-[#5747d8] to-[#6d63ea] px-6 py-4 text-[19px] font-bold text-white shadow-[0_18px_34px_rgba(96,85,183,0.28)] transition hover:opacity-95">
                        <span>{{ __('Create Account') }}</span>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
                    </button>

                    <div class="flex items-center gap-4 pt-1">
                        <div class="h-px flex-1 bg-line"></div>
                        <span class="text-[14px] text-copy">{{ __('OR') }}</span>
                        <div class="h-px flex-1 bg-line"></div>
                    </div>

                    <button type="button" class="field-shell flex w-full items-center justify-center gap-3 rounded-[18px] bg-white px-6 py-4 text-[18px] font-semibold text-ink transition hover:bg-[#fbfdff]">
                        <img src="{{ asset('landing/social/google-logo.png') }}" alt="Google" class="h-6 w-6 object-contain">
                        <span>{{ __('Continue with Google') }}</span>
                    </button>
                </form>

                <p class="mt-7 text-center text-[16px] text-copy">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('login') }}" class="font-semibold text-green hover:text-greenDeep">{{ __('Login') }}</a>
                </p>
            </section>
        </div>
    </main>
    <script>
        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            const input = document.getElementById(button.dataset.passwordToggle);
            const eyeOpen = button.querySelector('[data-eye-open]');
            const eyeClosed = button.querySelector('[data-eye-closed]');

            if (!input || !eyeOpen || !eyeClosed) {
                return;
            }

            button.addEventListener('click', () => {
                const isHidden = input.type === 'password';

                input.type = isHidden ? 'text' : 'password';
                button.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
                eyeOpen.classList.toggle('hidden', isHidden);
                eyeClosed.classList.toggle('hidden', !isHidden);
            });
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? __('MatchPoint') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="min-h-screen bg-[linear-gradient(180deg,_#f7f5ff_0%,_#ece8ff_100%)] text-[#24283d]">
    <header class="bg-white/96 shadow-[0_14px_34px_rgba(34,43,84,0.05)] backdrop-blur">
        <div class="flex w-full items-center justify-between px-6 py-4 lg:px-10 xl:px-14 2xl:px-20">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('landing/matchpoint-logo.png') }}" alt="{{ __('MatchPoint logo') }}" class="h-11 w-11 object-contain">
                <span class="font-heading text-[24px] font-bold tracking-[0.14em] text-[#1b2565]">{{ __('MATCHPOINT') }}</span>
            </a>

            <div class="shrink-0">
                @include('partials.locale-switcher')
            </div>
        </div>
    </header>

    <main class="mx-auto w-full max-w-[1480px] px-6 py-10 lg:px-10 xl:px-14 2xl:px-20">
        @if (session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->has('profile'))
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                {{ $errors->first('profile') }}
            </div>
        @endif

        {{ $slot }}
    </main>
</body>
</html>

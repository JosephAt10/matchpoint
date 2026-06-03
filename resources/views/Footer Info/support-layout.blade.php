@php
    $supportLinks = [
        'contact' => ['label' => __('support.nav.contact'), 'route' => route('contact')],
        'help' => ['label' => __('support.nav.help'), 'route' => route('help')],
        'how' => ['label' => __('support.nav.how'), 'route' => route('how-it-works')],
        'terms' => ['label' => __('support.nav.terms'), 'route' => route('terms')],
        'privacy' => ['label' => __('support.nav.privacy'), 'route' => route('privacy')],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }} - MatchPoint</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#252b50',
                        copy: '#626a86',
                        indigoDeep: '#5446ea',
                        mist: '#f7f5ff',
                        line: '#e7e1ff',
                    },
                    boxShadow: {
                        panel: '0 24px 60px rgba(98, 80, 214, 0.10)',
                        card: '0 18px 44px rgba(89, 83, 178, 0.10)',
                    },
                    fontFamily: {
                        heading: ['Outfit', 'sans-serif'],
                        body: ['DM Sans', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        h1, h2, h3, .font-heading { font-family: 'Outfit', sans-serif; }
        body {
            background: linear-gradient(180deg, #ffffff 0%, #f7f4ff 42%, #ffffff 100%);
        }
    </style>
</head>
<body class="min-h-screen text-ink">
    <header class="border-b border-line bg-white/95 backdrop-blur">
        <div class="flex w-full items-center justify-between px-8 py-4 lg:px-14 2xl:px-20">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('landing/matchpoint-logo.png') }}" alt="{{ __('MatchPoint logo') }}" class="h-11 w-11 object-contain">
                <span class="font-heading text-[28px] font-bold tracking-[0.16em] text-[#2f3273]">{{ __('MATCHPOINT') }}</span>
            </a>

            <div class="flex items-center gap-4">
                <div class="shrink-0">@include('partials.locale-switcher')</div>
            </div>
        </div>
    </header>

    <main class="mx-auto grid w-full max-w-[1820px] gap-8 px-8 py-10 lg:grid-cols-[320px_minmax(0,1fr)] lg:px-14 2xl:px-20">
        <aside class="rounded-[2rem] border border-line bg-white p-5 shadow-card lg:sticky lg:top-8 lg:h-fit">
            <p class="px-4 pb-4 text-[14px] font-bold uppercase tracking-[0.2em] text-[#7c86a4]">{{ __('support.common.support') }}</p>
            <nav class="space-y-2">
                @foreach ($supportLinks as $key => $link)
                    <a href="{{ $link['route'] }}" class="block rounded-2xl px-5 py-4 text-[17px] font-bold transition {{ $active === $key ? 'bg-gradient-to-r from-[#5d55df] to-[#6b63f4] text-white shadow-[0_16px_30px_rgba(98,83,232,0.25)]' : 'text-[#4b5573] hover:bg-mist hover:text-indigoDeep' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>
        </aside>

        <section class="min-w-0">
            <div class="relative overflow-hidden rounded-[2rem] border border-line bg-white p-8 shadow-panel md:p-10 xl:p-12">
                <div class="absolute right-0 top-0 h-full w-[42%] bg-[radial-gradient(circle_at_top_right,rgba(255,154,31,0.18),transparent_38%),linear-gradient(135deg,transparent,rgba(90,56,214,0.09))]"></div>
                <div class="relative max-w-[980px]">
                    <p class="text-[15px] font-bold uppercase tracking-[0.22em] text-[#ff9a1f]">{{ __('support.common.support') }}</p>
                    <h1 class="mt-4 font-heading text-[38px] font-extrabold leading-[1.08] tracking-tight text-ink md:text-[48px] 2xl:text-[54px]">{{ $pageTitle }}</h1>
                    <p class="mt-5 max-w-[820px] text-[16px] leading-8 text-copy 2xl:text-[18px]">{{ $pageSubtitle }}</p>
                </div>
            </div>

            <div class="mt-8">
                {{ $slot }}
            </div>
        </section>
    </main>
</body>
</html>

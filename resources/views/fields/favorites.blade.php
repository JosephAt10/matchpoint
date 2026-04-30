@php
    $favoritesUrl = route('favorites.index');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorites - MatchPoint</title>
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
                        ink: '#252b50',
                        indigoSoft: '#6b63f6',
                        indigoDeep: '#5446ea',
                        copy: '#6d7296',
                    },
                    boxShadow: {
                        card: '0 20px 48px rgba(89, 83, 178, 0.10)',
                    },
                    fontFamily: {
                        heading: ['Outfit', 'sans-serif'],
                        body: ['DM Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-[linear-gradient(180deg,#ffffff_0%,#f7f4ff_42%,#ffffff_100%)] text-ink">
    <header class="border-b border-[#e7e1ff] bg-white/95 backdrop-blur">
        <div class="mx-auto flex w-full max-w-[1820px] items-center justify-between px-8 py-4 lg:px-14 2xl:px-20">
            <a href="{{ route('home') }}" class="font-heading text-[28px] font-bold tracking-[0.16em] text-[#2f3273]">MATCHPOINT</a>

            <nav class="flex items-center gap-3 text-[16px] font-medium text-[#5d6385]">
                <a href="{{ route('fields.index') }}" class="px-4 py-3 transition hover:text-indigoDeep">Browse Fields</a>
                <a href="{{ $favoritesUrl }}" class="border-b-2 border-indigoDeep px-4 py-3 text-indigoDeep">Favorites</a>
                <form action="{{ route('logout') }}" method="POST" class="inline-flex">
                    @csrf
                    <button type="submit" class="px-4 py-3 transition hover:text-indigoDeep">Logout</button>
                </form>
            </nav>
        </div>
    </header>

    <main class="mx-auto w-full max-w-[1820px] px-8 py-10 lg:px-14 2xl:px-20">
        @if (session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <section class="rounded-[2.2rem] border border-[#eae4ff] bg-[linear-gradient(180deg,#ffffff_0%,#f7f4ff_100%)] px-8 py-10">
            <p class="text-[15px] font-semibold uppercase tracking-[0.24em] text-[#ff9a1f]">Saved Venues</p>
            <h1 class="mt-3 font-heading text-[44px] font-bold leading-tight text-ink">Your favorite fields</h1>
            <p class="mt-3 max-w-[620px] text-[18px] leading-8 text-copy">
                Keep the venues you like in one place, then come back anytime to review them before booking.
            </p>
        </section>

        <section class="mt-10">
            <div class="grid gap-5 lg:grid-cols-3">
                @include('fields.partials.cards', ['fields' => $fields, 'favoriteIds' => $favoriteIds])
            </div>
        </section>
    </main>
</body>
</html>

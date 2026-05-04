@php
    $favoritesUrl = auth()->check() ? route('favorites.index') : route('login');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Fields - MatchPoint</title>
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
                        indigoLine: '#dcd7ff',
                        surface: '#ffffff',
                        mist: '#f7f5ff',
                        copy: '#6d7296',
                        orangeAccent: '#ff9a1f',
                    },
                    boxShadow: {
                        panel: '0 24px 60px rgba(98, 80, 214, 0.10)',
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
                <a href="{{ route('fields.index') }}" class="border-b-2 border-indigoDeep px-4 py-3 text-indigoDeep">Browse Fields</a>
                <a href="{{ $favoritesUrl }}" class="px-4 py-3 transition hover:text-indigoDeep">Favorites</a>

                @auth
                    <form action="{{ route('logout') }}" method="POST" class="inline-flex">
                        @csrf
                        <button type="submit" class="px-4 py-3 transition hover:text-indigoDeep">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-3 transition hover:text-indigoDeep">Login</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-gradient-to-r from-[#5f55dc] to-[#6f66f7] px-7 py-3 font-semibold text-white shadow-[0_16px_30px_rgba(98,83,232,0.25)] transition hover:opacity-95">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="mx-auto w-full max-w-[1820px] px-0 py-0">
        @if (session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="relative overflow-hidden border-b border-[#ece7ff] bg-[linear-gradient(180deg,#ffffff_0%,#f7f4ff_86%,#f5f1ff_100%)] px-8 pb-8 pt-8 lg:px-14 2xl:px-20">
            <div class="grid gap-4 lg:grid-cols-[0.86fr_1.14fr] lg:items-end">
                <div class="relative z-10 max-w-[560px] pt-6">
                    <p class="text-[16px] font-semibold uppercase tracking-[0.26em] text-orangeAccent">Field Search</p>
                    <h1 class="mt-4 font-heading text-[42px] font-bold leading-[1.05] tracking-tight text-ink lg:text-[50px] 2xl:text-[56px]">
                        Find the perfect field
                        <br>
                        for <span class="text-indigoSoft">your game.</span>
                    </h1>
                    <p class="mt-5 max-w-[520px] text-[18px] leading-9 text-copy">
                        Browse approved sports venues and book the perfect place to play, compete, and have fun.
                    </p>
                </div>

                <div class="relative min-h-[250px] lg:min-h-[330px] 2xl:min-h-[380px]">
                    <img src="{{ asset('landing/field-top-right-image.png') }}" alt="Sports equipment and stadium" class="absolute inset-0 h-full w-full object-contain object-right-bottom">
                </div>
            </div>

            <form method="GET" action="{{ route('fields.index') }}" class="relative z-10 mt-2 grid gap-5 rounded-[2rem] border border-white/80 bg-white/95 p-6 shadow-panel lg:grid-cols-[1fr_1fr_1fr_220px_160px] lg:items-end">
                <label class="space-y-3">
                    <span class="block text-[15px] font-medium text-copy">Location</span>
                    <div class="flex items-center gap-3 rounded-2xl border border-[#e6e0ff] bg-white px-4 py-4">
                        <svg class="h-5 w-5 text-[#7a76a8]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-4.35-6-10a6 6 0 1112 0c0 5.65-6 10-6 10z"/>
                            <circle cx="12" cy="11" r="2.5"></circle>
                        </svg>
                        <select name="location" class="w-full border-0 bg-transparent p-0 text-[18px] text-[#50567d] outline-none">
                            <option value="">All locations</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location }}" @selected(($filters['location'] ?? '') === $location)>{{ $location }}</option>
                            @endforeach
                        </select>
                    </div>
                </label>

                <label class="space-y-3">
                    <span class="block text-[15px] font-medium text-copy">Sport type</span>
                    <div class="flex items-center gap-3 rounded-2xl border border-[#e6e0ff] bg-white px-4 py-4">
                        <svg class="h-5 w-5 text-[#7a76a8]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="8"></circle>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16M4 12h16M7.5 7.5c2 1.2 4.4 1.8 4.5 4.5-.1 2.7-2.5 3.3-4.5 4.5m9-9c-2 1.2-4.4 1.8-4.5 4.5.1 2.7 2.5 3.3 4.5 4.5"/>
                        </svg>
                        <select name="sport_type" class="w-full border-0 bg-transparent p-0 text-[18px] text-[#50567d] outline-none">
                            <option value="">All sports</option>
                            @foreach ($sports as $sport)
                                <option value="{{ $sport }}" @selected(($filters['sport_type'] ?? '') === $sport)>{{ $sport }}</option>
                            @endforeach
                        </select>
                    </div>
                </label>

                <label class="space-y-3">
                    <span class="block text-[15px] font-medium text-copy">Field type</span>
                    <div class="flex items-center gap-3 rounded-2xl border border-[#e6e0ff] bg-white px-4 py-4">
                        <svg class="h-5 w-5 text-[#7a76a8]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="6" width="18" height="12" rx="2"></rect>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 6v12m6-12v12M3 12h18"/>
                        </svg>
                        <select name="type" class="w-full border-0 bg-transparent p-0 text-[18px] text-[#50567d] outline-none">
                            <option value="">Indoor and outdoor</option>
                            <option value="Indoor" @selected(($filters['type'] ?? '') === 'Indoor')>Indoor</option>
                            <option value="Outdoor" @selected(($filters['type'] ?? '') === 'Outdoor')>Outdoor</option>
                        </select>
                    </div>
                </label>

                <button type="submit" class="flex items-center justify-center gap-3 rounded-2xl bg-gradient-to-r from-[#5d55df] to-[#6b63f4] px-6 py-4 text-[18px] font-semibold text-white shadow-[0_18px_34px_rgba(99,89,237,0.24)] transition hover:opacity-95">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M7 12h10M10 18h4"/>
                    </svg>
                    Apply filters
                </button>

                <a href="{{ route('fields.index') }}" class="flex items-center justify-center gap-3 rounded-2xl border border-[#e6e0ff] bg-white px-6 py-4 text-[18px] font-medium text-[#5f6488] transition hover:bg-mist">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v6h6M20 20v-6h-6"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 9a8 8 0 00-13.66-5.66L4 10m16 4l-2.34 2.66A8 8 0 014 15"/>
                    </svg>
                    Reset
                </a>
            </form>
        </section>

        <section class="mx-auto mt-10 w-full max-w-[1820px] px-8 pb-12 lg:px-14 2xl:px-20">
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span class="block h-8 w-1.5 rounded-full bg-indigoDeep"></span>
                    <h2 class="font-heading text-[34px] font-bold text-ink">Popular Sports Venues</h2>
                </div>
                <a href="{{ route('fields.index') }}" class="flex items-center gap-2 text-[20px] font-medium text-indigoDeep transition hover:opacity-80">
                    View all
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div id="field-grid" class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @include('fields.partials.cards', ['fields' => $fields, 'favoriteIds' => $favoriteIds])
            </div>

            <div class="mt-8 flex justify-center">
                @if ($fields->hasMorePages())
                    <button
                        id="load-more-button"
                        type="button"
                        data-next-page="{{ $fields->nextPageUrl() }}"
                        class="inline-flex items-center gap-3 rounded-[1.3rem] border-2 border-[#7568f8] bg-white px-10 py-4 text-[20px] font-semibold text-indigoDeep transition hover:bg-[#f7f4ff]"
                    >
                        Load more venues
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0l6-6m-6 6l-6-6"/>
                        </svg>
                    </button>
                @else
                    <span id="load-more-button" class="inline-flex items-center gap-3 rounded-[1.3rem] border-2 border-[#7568f8] bg-white px-10 py-4 text-[20px] font-semibold text-indigoDeep">
                        Load more venues
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0l6-6m-6 6l-6-6"/>
                        </svg>
                    </span>
                @endif
            </div>
        </section>
    </main>

    <script>
        const loadMoreButton = document.getElementById('load-more-button');
        const fieldGrid = document.getElementById('field-grid');

        if (loadMoreButton instanceof HTMLButtonElement && fieldGrid) {
            loadMoreButton.addEventListener('click', async () => {
                const nextPageUrl = loadMoreButton.dataset.nextPage;

                if (!nextPageUrl || loadMoreButton.disabled) {
                    return;
                }

                loadMoreButton.disabled = true;
                loadMoreButton.classList.add('opacity-70', 'cursor-wait');

                try {
                    const url = new URL(nextPageUrl);
                    url.searchParams.set('load_more', '1');

                    const response = await fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });

                    if (!response.ok) {
                        throw new Error('Failed to load more venues.');
                    }

                    const payload = await response.json();
                    fieldGrid.insertAdjacentHTML('beforeend', payload.html);

                    if (payload.hasMorePages && payload.nextPageUrl) {
                        loadMoreButton.dataset.nextPage = payload.nextPageUrl;
                        loadMoreButton.disabled = false;
                        loadMoreButton.classList.remove('opacity-70', 'cursor-wait');
                    } else {
                        loadMoreButton.outerHTML = `
                            <span class="inline-flex items-center gap-3 rounded-[1.3rem] border-2 border-[#7568f8] bg-white px-10 py-4 text-[20px] font-semibold text-indigoDeep">
                                Load more venues
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0l6-6m-6 6l-6-6"/>
                                </svg>
                            </span>
                        `;
                    }
                } catch (error) {
                    console.error(error);
                    loadMoreButton.disabled = false;
                    loadMoreButton.classList.remove('opacity-70', 'cursor-wait');
                }
            });
        }
    </script>
</body>
</html>

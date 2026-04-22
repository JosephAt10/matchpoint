{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'MatchPoint' }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="min-h-screen bg-stone-950 text-stone-100">
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,_rgba(251,146,60,0.25),_transparent_30%),radial-gradient(circle_at_bottom_right,_rgba(14,165,233,0.18),_transparent_28%),linear-gradient(180deg,_#1c1917_0%,_#0c0a09_100%)]"></div>

    <header class="border-b border-white/10 bg-black/20 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <a href="{{ route('home') }}" class="text-xl font-semibold tracking-[0.2em] text-orange-300">MATCHPOINT</a>

            <nav class="flex items-center gap-3 text-sm">
                <a href="{{ route('fields.index') }}" class="rounded-full px-4 py-2 text-stone-200 transition hover:bg-white/10">Browse Fields</a>

                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-full px-4 py-2 text-stone-200 transition hover:bg-white/10">Dashboard</a>
                    <a href="{{ route('profile.edit') }}" class="rounded-full px-4 py-2 text-stone-200 transition hover:bg-white/10">Profile</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded-full bg-orange-400 px-4 py-2 font-medium text-stone-950 transition hover:bg-orange-300">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-full px-4 py-2 text-stone-200 transition hover:bg-white/10">Login</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-orange-400 px-4 py-2 font-medium text-stone-950 transition hover:bg-orange-300">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-6 py-10">
        @if (session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-400/30 bg-rose-400/10 px-4 py-3 text-sm text-rose-100">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot }}
    </main>
</body>
</html> --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MatchPoint</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gradient-to-br from-indigo-100 to-purple-200">

    {{-- Navbar --}}
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <span class="font-bold text-lg">MatchPoint</span>
            </div>

            <div class="flex gap-6 text-sm">
                <a href="#" class="hover:text-indigo-600">Home</a>
                <a href="#" class="hover:text-indigo-600">Fields</a>
                <a href="#" class="hover:text-indigo-600">Matches</a>
                <a href="#" class="hover:text-indigo-600">My Bookings</a>
                <a href="#" class="hover:text-indigo-600">Profile</a>
            </div>

            <div class="flex gap-4 text-sm">
                <a href="#">Login</a>
                <a href="#">Contact</a>
            </div>
        </div>
    </nav>

    {{-- Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('partials.footer')

</body>
</html>
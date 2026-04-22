<!DOCTYPE html>
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
<body class="min-h-screen bg-[linear-gradient(180deg,_#f7f5ff_0%,_#ece8ff_100%)] text-[#24283d]">
    <header class="border-b border-[#e4defc] bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <a href="{{ route('home') }}" class="font-semibold tracking-[0.16em] text-[#352f6c]">MATCHPOINT</a>

            <nav class="flex items-center gap-3 text-sm">
                <a href="{{ route('fields.index') }}" class="rounded-full px-4 py-2 text-[#4b5070] transition hover:bg-[#f3efff]">Browse Fields</a>

                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-full px-4 py-2 text-[#4b5070] transition hover:bg-[#f3efff]">Dashboard</a>
                    <a href="{{ route('profile.edit') }}" class="rounded-full px-4 py-2 text-[#4b5070] transition hover:bg-[#f3efff]">Profile</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded-full bg-[#5f55dc] px-4 py-2 font-medium text-white transition hover:bg-[#4d43cb]">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-full px-4 py-2 text-[#4b5070] transition hover:bg-[#f3efff]">Login</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-[#5f55dc] px-4 py-2 font-medium text-white transition hover:bg-[#4d43cb]">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-6 py-10">
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

        {{ $slot }}
    </main>
</body>
</html>

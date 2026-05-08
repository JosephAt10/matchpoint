<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Privacy Policy') }} - MatchPoint</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#E8E8F5] text-[#1a1f3a]">
    <main class="mx-auto max-w-3xl px-4 py-16">
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="text-sm font-medium text-[#5B5BD6] hover:underline">{{ __('Back to home') }}</a>
            @include('partials.locale-switcher')
        </div>
        <section class="mt-6 rounded-3xl bg-white p-8 shadow-sm">
            <h1 class="text-3xl font-bold">{{ __('Privacy Policy') }}</h1>
            <p class="mt-4 text-sm text-slate-600">{{ __('This placeholder keeps your footer and support links working until the policy content is finalized.') }}</p>
        </section>
    </main>
</body>
</html>

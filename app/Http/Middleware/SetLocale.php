<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('app.supported_locales', ['en', 'id']);
        $locale = $request->session()->get('locale') ?: $request->cookie('locale', config('app.locale'));

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = config('app.fallback_locale', 'en');
        }

        App::setLocale($locale);
        Carbon::setLocale($locale);
        $request->session()->put('locale', $locale);

        return $next($request);
    }
}

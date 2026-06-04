<?php

namespace App\Filament\Auth;

use Filament\Auth\Http\Responses\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $locale = $request->session()->get('locale') ?: $request->cookie('locale', Config::get('app.locale'));
        $request->session()->put('locale', $locale);

        return redirect()
            ->route('login')
            ->withCookie(cookie('locale', $locale, 60 * 24 * 365));
    }
}


<?php

namespace App\Filament\Auth;

use Filament\Auth\Http\Responses\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $request->session()->put('locale', $request->session()->get('locale', Config::get('app.locale')));

        return redirect()->route('login');
    }
}


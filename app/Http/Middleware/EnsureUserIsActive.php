<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->isActive()) {
            return $next($request);
        }

        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors([
            'email' => $user->isRejected()
                ? 'Your field owner account request was rejected. Please contact the admin if you think this is a mistake.'
                : 'Your account is not active yet. Please contact the admin if you think this is a mistake.',
        ]);
    }
}

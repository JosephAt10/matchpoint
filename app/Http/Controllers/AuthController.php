<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'The provided credentials do not match our records.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        if (! $request->user()->isActive()) {
            $user = $request->user();

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return to_route('login')->withErrors([
                'email' => $user->isRejected()
                    ? 'Your field owner account request was rejected. Please contact the admin if you need more information.'
                    : 'Your account is not active yet. Please wait for admin approval.',
            ]);
        }

        if ($request->user()->isAdmin()) {
            return redirect()->intended(url('/admin'));
        }

        if ($request->user()->isFieldOwner()) {
            return redirect()->intended(url('/owner'));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:100'],
            'email'                 => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'role'                  => ['required', Rule::in(['User', 'FieldOwner'])],
            'password'              => ['required', 'confirmed', 'min:8'],
        ]);
        $role = $data['role'];

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'role'     => $role,
            'status'   => $role === 'FieldOwner' ? 'PendingApproval' : 'Active',
            'password' => Hash::make($data['password']),
        ]);

        if ($user->isUser()) {
            Auth::login($user);
            $request->session()->regenerate();

            return to_route('dashboard')->with('status', 'Welcome to MatchPoint. Your account is ready to use.');
        }

        return to_route('login')->with('status', 'Field Owner account created. Please wait for admin approval before logging in.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('home')->with('status', 'You have been logged out.');
    }
}

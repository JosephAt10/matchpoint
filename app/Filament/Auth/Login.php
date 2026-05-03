<?php

namespace App\Filament\Auth;

class Login extends \Filament\Auth\Pages\Login
{
    public function mount(): void
    {
        if (filament()->auth()->check()) {
            redirect()->intended(filament()->getUrl());

            return;
        }

        $this->redirect(route('login'), navigate: false);
    }
}

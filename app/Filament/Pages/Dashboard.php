<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('logout')
                ->label('Logout')
                ->icon('heroicon-o-arrow-right-start-on-rectangle')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function () {
                    Auth::logout();
                    request()->session()->invalidate();
                    request()->session()->regenerateToken();

                    $this->redirect(route('login'), navigate: false);
                }),
        ];
    }
}

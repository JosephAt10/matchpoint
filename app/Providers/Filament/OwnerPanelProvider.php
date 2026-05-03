<?php

namespace App\Providers\Filament;

use App\Filament\Auth\Login;
use App\Filament\Owner\Pages\Dashboard;
use App\Filament\Owner\Resources\FieldResource;
use App\Filament\Owner\Resources\TimeSlotResource;
use App\Filament\Resources\AppNotificationResource;
use App\Filament\Resources\BookingResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class OwnerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('owner')
            ->path('owner')
            ->login(Login::class)
            ->viteTheme('resources/css/filament/owner/theme.css')
            ->brandName('MATCHPOINT')
            ->sidebarWidth('17rem')
            ->profile()
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->resources([
                FieldResource::class,
                BookingResource::class,
                TimeSlotResource::class,
                AppNotificationResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Owner/Pages'), for: 'App\Filament\Owner\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->renderHook(
                PanelsRenderHook::SIDEBAR_LOGO_AFTER,
                fn (): \Illuminate\Contracts\View\View => view('filament.owner.components.sidebar-subtitle'),
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_FOOTER,
                fn (): \Illuminate\Contracts\View\View => view('filament.owner.components.sidebar-footer'),
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

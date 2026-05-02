<?php

namespace App\Filament\Resources\AppNotificationResource\Pages;

use App\Filament\Resources\AppNotificationResource;
use App\Models\Notification;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListAppNotifications extends ListRecords
{
    protected static string $resource = AppNotificationResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('unreadOnly')
                ->label('Unread Only')
                ->icon('heroicon-o-bell-alert')
                ->color('warning')
                ->url(AppNotificationResource::getUrl('index', [
                    'tableFilters[status][value]' => 'Unread',
                ])),
            Actions\Action::make('markAllAsRead')
                ->label('Mark All Read')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn (): bool => Notification::query()->forUser(auth()->id())->unread()->exists())
                ->action(function (): void {
                    Notification::query()
                        ->forUser(auth()->id())
                        ->unread()
                        ->update(['status' => 'Read']);
                }),
        ];
    }
}

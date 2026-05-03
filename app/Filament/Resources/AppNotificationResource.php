<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppNotificationResource\Pages;
use App\Models\Notification;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AppNotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationLabel = 'Notifications';

    protected static ?string $modelLabel = 'Notification';

    protected static ?string $pluralModelLabel = 'Notifications';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->forUser(auth()->id())
                ->latest())
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'Unread',
                        'success' => 'Read',
                    ]),
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'Booking',
                        'warning' => 'Payment',
                        'success' => 'Match',
                        'gray' => 'System',
                    ]),
                Tables\Columns\TextColumn::make('message')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Unread' => 'Unread',
                        'Read' => 'Read',
                    ]),
                SelectFilter::make('type')
                    ->options([
                        'Booking' => 'Booking',
                        'Payment' => 'Payment',
                        'Match' => 'Match',
                        'System' => 'System',
                    ]),
            ])
            ->actions([
                Action::make('markAsRead')
                    ->label('Mark as Read')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Notification $record): bool => $record->isUnread())
                    ->action(function (Notification $record): void {
                        $record->markAsRead();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getNavigationBadge(): ?string
    {
        $userId = auth()->id();

        if (! $userId) {
            return null;
        }

        $unreadCount = Notification::query()
            ->forUser($userId)
            ->unread()
            ->count();

        return $unreadCount > 0 ? (string) $unreadCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() ? 'warning' : null;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppNotifications::route('/'),
        ];
    }
}

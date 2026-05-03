<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'Users';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->latest())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\BadgeColumn::make('role')
                    ->colors([
                        'primary' => 'Admin',
                        'warning' => 'FieldOwner',
                        'success' => 'User',
                    ]),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'Active',
                        'warning' => 'PendingApproval',
                        'danger' => ['Rejected', 'Deactivated'],
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'Admin' => 'Admin',
                        'FieldOwner' => 'Field Owner',
                        'User' => 'User',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'Active' => 'Active',
                        'PendingApproval' => 'Pending Approval',
                        'Rejected' => 'Rejected',
                        'Deactivated' => 'Deactivated',
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (User $record): bool => $record->role === 'FieldOwner' && $record->status === 'PendingApproval')
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $record->update(['status' => 'Active']);
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (User $record): bool => $record->role === 'FieldOwner' && $record->status === 'PendingApproval')
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $record->update(['status' => 'Rejected']);
                    }),
                Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn (User $record): bool => $record->status === 'Active' && $record->role !== 'Admin')
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $record->update(['status' => 'Deactivated']);
                    }),
                Action::make('reactivate')
                    ->label('Reactivate')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->visible(fn (User $record): bool => in_array($record->status, ['Rejected', 'Deactivated'], true))
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $record->update(['status' => 'Active']);
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getNavigationBadge(): ?string
    {
        if (! static::canViewAny()) {
            return null;
        }

        $pendingCount = User::query()
            ->where('role', 'FieldOwner')
            ->where('status', 'PendingApproval')
            ->count();

        return $pendingCount > 0 ? (string) $pendingCount : null;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}

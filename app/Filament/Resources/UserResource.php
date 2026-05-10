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

    public static function getNavigationLabel(): string
    {
        return __('Users');
    }

    public static function getModelLabel(): string
    {
        return __('User');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Users');
    }

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
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email Address'))
                    ->searchable()
                    ->copyable(),
                Tables\Columns\BadgeColumn::make('role')
                    ->label(__('Role'))
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'FieldOwner' => __('Field Owner'),
                        'User' => __('User'),
                        default => __($state),
                    })
                    ->colors([
                        'primary' => 'Admin',
                        'warning' => 'FieldOwner',
                        'success' => 'User',
                    ]),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'PendingApproval' => __('Pending Approval'),
                        'Deactivated' => __('Deactivated'),
                        default => __($state),
                    })
                    ->colors([
                        'success' => 'Active',
                        'warning' => 'PendingApproval',
                        'danger' => ['Rejected', 'Deactivated'],
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label(__('Role'))
                    ->options([
                        'Admin' => __('Admin'),
                        'FieldOwner' => __('Field Owner'),
                        'User' => __('User'),
                    ]),
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'Active' => __('Active'),
                        'PendingApproval' => __('Pending Approval'),
                        'Rejected' => __('Rejected'),
                        'Deactivated' => __('Deactivated'),
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label(__('Approve'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (User $record): bool => $record->role === 'FieldOwner' && $record->status === 'PendingApproval')
                    ->requiresConfirmation()
                    ->modalHeading(__('Approve this field owner?'))
                    ->modalDescription(__('This field owner will be able to sign in and manage their fields.'))
                    ->modalSubmitActionLabel(__('Approve'))
                    ->action(function (User $record): void {
                        $record->update(['status' => 'Active']);
                    }),
                Action::make('reject')
                    ->label(__('Reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (User $record): bool => $record->role === 'FieldOwner' && $record->status === 'PendingApproval')
                    ->requiresConfirmation()
                    ->modalHeading(__('Reject this field owner?'))
                    ->modalDescription(__('This field owner request will be rejected and the user will need admin help to continue.'))
                    ->modalSubmitActionLabel(__('Reject'))
                    ->action(function (User $record): void {
                        $record->update(['status' => 'Rejected']);
                    }),
                Action::make('deactivate')
                    ->label(__('Deactivate'))
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn (User $record): bool => $record->status === 'Active' && $record->role !== 'Admin')
                    ->requiresConfirmation()
                    ->modalHeading(__('Deactivate this account?'))
                    ->modalDescription(__('This account will lose access until you reactivate it.'))
                    ->modalSubmitActionLabel(__('Deactivate'))
                    ->action(function (User $record): void {
                        $record->update(['status' => 'Deactivated']);
                    }),
                Action::make('reactivate')
                    ->label(__('Reactivate'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->visible(fn (User $record): bool => in_array($record->status, ['Rejected', 'Deactivated'], true))
                    ->requiresConfirmation()
                    ->modalHeading(__('Reactivate this account?'))
                    ->modalDescription(__('This account will regain access immediately.'))
                    ->modalSubmitActionLabel(__('Reactivate'))
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
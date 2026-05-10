<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FieldResource\Pages;
use App\Models\Field;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FieldResource extends Resource
{
    protected static ?string $model = Field::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Fields';

    protected static ?string $modelLabel = 'Field';

    protected static ?string $pluralModelLabel = 'Fields';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('Fields');
    }

    public static function getModelLabel(): string
    {
        return __('Field');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Fields');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['owner'])->latest())
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label(__('Image'))
                    ->getStateUsing(fn (Field $record): ?string => $record->image_url ? url($record->image_url) : null)
                    ->checkFileExistence(false)
                    ->square(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('owner.name')
                    ->label(__('Owner'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('sport_type')
                    ->label(__('Sport'))
                    ->badge(),
                Tables\Columns\TextColumn::make('location')
                    ->label(__('Location'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label(__('Field Type'))
                    ->formatStateUsing(fn (string $state): string => __($state))
                    ->colors([
                        'success' => 'Outdoor',
                        'info' => 'Indoor',
                    ]),
                Tables\Columns\BadgeColumn::make('approval_status')
                    ->label(__('Approval'))
                    ->formatStateUsing(fn (string $state): string => __($state))
                    ->colors([
                        'success' => 'Approved',
                        'warning' => 'Pending',
                        'danger' => 'Rejected',
                    ]),
                Tables\Columns\TextColumn::make('price_per_slot')
                    ->label(__('Price'))
                    ->formatStateUsing(fn ($state): string => 'Rp ' . number_format((float) $state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_approved')
                    ->label(__('Approval'))
                    ->options([
                        '0' => __('Pending'),
                        '1' => __('Approved'),
                    ]),
                SelectFilter::make('sport_type')
                    ->label(__('Sport'))
                    ->options(fn (): array => Field::query()
                        ->orderBy('sport_type')
                        ->pluck('sport_type', 'sport_type')
                        ->all()),
                SelectFilter::make('owner_status')
                    ->label(__('Owner Status'))
                    ->relationship('owner', 'status')
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
                    ->visible(fn (Field $record): bool => ! $record->is_approved)
                    ->requiresConfirmation()
                    ->modalHeading(__('Approve this field?'))
                    ->modalDescription(__('The field will become visible to users after approval.'))
                    ->modalSubmitActionLabel(__('Approve'))
                    ->action(fn (Field $record) => $record->update(['is_approved' => true, 'rejected_at' => null])),
                Action::make('reject')
                    ->label(__('Reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Field $record): bool => ! $record->is_approved)
                    ->requiresConfirmation()
                    ->modalHeading(__('Reject this field?'))
                    ->modalDescription(__('The field will remain hidden until it is reviewed again.'))
                    ->modalSubmitActionLabel(__('Reject'))
                    ->action(fn (Field $record) => $record->update(['is_approved' => false, 'rejected_at' => now()])),
                Action::make('markPending')
                    ->label(__('Pending'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (Field $record): bool => $record->is_approved || $record->isRejected())
                    ->requiresConfirmation()
                    ->modalHeading(__('Mark this field as pending?'))
                    ->modalDescription(__('The field will be hidden from users until it is approved again.'))
                    ->modalSubmitActionLabel(__('Pending'))
                    ->action(fn (Field $record) => $record->update(['is_approved' => false, 'rejected_at' => null])),
            ])
            ->bulkActions([]);
    }

    public static function getNavigationBadge(): ?string
    {
        if (! static::canViewAny()) {
            return null;
        }

        $pendingCount = Field::query()
            ->where('is_approved', false)
            ->whereNull('rejected_at')
            ->count();

        return $pendingCount > 0 ? (string) $pendingCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() ? 'warning' : null;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFields::route('/'),
        ];
    }
}
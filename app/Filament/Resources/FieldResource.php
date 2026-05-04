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
                    ->label('Image')
                    ->getStateUsing(fn (Field $record): ?string => $record->image_url ? url($record->image_url) : null)
                    ->checkFileExistence(false)
                    ->square(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Owner')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sport_type')
                    ->label('Sport')
                    ->badge(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'success' => 'Outdoor',
                        'info' => 'Indoor',
                    ]),
                Tables\Columns\BadgeColumn::make('approval_status')
                    ->label('Approval')
                    ->colors([
                        'success' => 'Approved',
                        'warning' => 'Pending',
                        'danger' => 'Rejected',
                    ]),
                Tables\Columns\TextColumn::make('price_per_slot')
                    ->label('Price')
                    ->formatStateUsing(fn ($state): string => 'Rp ' . number_format((float) $state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_approved')
                    ->label('Approval')
                    ->options([
                        '0' => 'Pending',
                        '1' => 'Approved',
                    ]),
                SelectFilter::make('sport_type')
                    ->label('Sport')
                    ->options(fn (): array => Field::query()
                        ->orderBy('sport_type')
                        ->pluck('sport_type', 'sport_type')
                        ->all()),
                SelectFilter::make('owner_status')
                    ->label('Owner Status')
                    ->relationship('owner', 'status')
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
                    ->visible(fn (Field $record): bool => ! $record->is_approved)
                    ->requiresConfirmation()
                    ->action(fn (Field $record) => $record->update(['is_approved' => true, 'rejected_at' => null])),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Field $record): bool => ! $record->is_approved)
                    ->requiresConfirmation()
                    ->action(fn (Field $record) => $record->update(['is_approved' => false, 'rejected_at' => now()])),
                Action::make('markPending')
                    ->label('Mark Pending')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (Field $record): bool => $record->is_approved || $record->isRejected())
                    ->requiresConfirmation()
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

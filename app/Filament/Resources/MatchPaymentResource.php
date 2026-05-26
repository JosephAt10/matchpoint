<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MatchPaymentResource\Pages;
use App\Models\Payment;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MatchPaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Match Payments';

    protected static ?string $modelLabel = 'Match Payment';

    protected static ?string $pluralModelLabel = 'Match Payments';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('Match Payments');
    }

    public static function getModelLabel(): string
    {
        return __('Match Payment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Match Payments');
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
        $user = auth()->user();

        if (! $user || ! $record instanceof Payment || ! $record->isMatchFee()) {
            return false;
        }

        return $user->isAdmin();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                $user = auth()->user();

                $query->where('type', 'MatchFee')
                    ->with([
                        'payer',
                        'matchParticipant.user',
                        'matchParticipant.game.booking.field',
                    ])
                    ->latest();

                return $query;
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('matchParticipant.game.title')
                    ->label(__('Match'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('matchParticipant.game.booking.field.name')
                    ->label(__('Field'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('payer.name')
                    ->label(__('Player'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('matchParticipant.team')
                    ->label(__('Team'))
                    ->badge()
                    ->color(fn (string $state): string => $state === 'B' ? 'info' : 'success'),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Fee'))
                    ->formatStateUsing(fn ($state): string => 'Rp ' . number_format((float) $state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('Payment Status'))
                    ->formatStateUsing(fn (string $state): string => __($state))
                    ->colors([
                        'warning' => 'Pending',
                        'success' => 'Verified',
                        'danger' => 'Rejected',
                    ]),
                Tables\Columns\BadgeColumn::make('matchParticipant.status')
                    ->label(__('Join Status'))
                    ->formatStateUsing(fn (string $state): string => __($state))
                    ->colors([
                        'warning' => 'Pending',
                        'success' => 'Confirmed',
                        'danger' => 'Cancelled',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Uploaded At'))
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Payment Status'))
                    ->options([
                        'Pending' => __('Pending'),
                        'Verified' => __('Verified'),
                        'Rejected' => __('Rejected'),
                    ]),
                SelectFilter::make('team')
                    ->label(__('Team'))
                    ->options([
                        'A' => __('Team A'),
                        'B' => __('Team B'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! filled($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas('matchParticipant', fn (Builder $participantQuery) => $participantQuery->where('team', $data['value']));
                    }),
            ])
            ->actions([
                Action::make('viewProof')
                    ->label(__('View Proof'))
                    ->icon('heroicon-o-photo')
                    ->color('gray')
                    ->visible(fn (Payment $record): bool => filled($record->proof))
                    ->url(fn (Payment $record): string => Storage::url($record->proof), shouldOpenInNewTab: true),
            ])
            ->bulkActions([]);
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();

        if (! $user || ! $user->isAdmin()) {
            return null;
        }

        $query = Payment::query()
            ->where('type', 'MatchFee')
            ->where('status', 'Pending')
            ->whereNotNull('proof');
        $count = $query->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() ? 'warning' : null;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMatchPayments::route('/'),
        ];
    }
}

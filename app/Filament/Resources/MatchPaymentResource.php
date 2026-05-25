<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MatchPaymentResource\Pages;
use App\Models\AuditLog;
use App\Models\MatchParticipant;
use App\Models\Notification;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
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
        $user = auth()->user();

        return $user?->isAdmin() || $user?->isFieldOwner();
    }

    public static function canView(Model $record): bool
    {
        $user = auth()->user();

        if (! $user || ! $record instanceof Payment || ! $record->isMatchFee()) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $user->isFieldOwner() && $record->matchParticipant?->game?->booking?->field?->owner_id === $user->id;
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

                if ($user?->isFieldOwner()) {
                    $query->whereHas('matchParticipant.game.booking.field', fn (Builder $fieldQuery) => $fieldQuery->where('owner_id', $user->id));
                }

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
                Action::make('confirmJoin')
                    ->label(__('Confirm Join'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('Confirm this player payment?'))
                    ->modalDescription(__('The join request will be confirmed and the player will be placed on the selected team.'))
                    ->modalSubmitActionLabel(__('Confirm Join'))
                    ->visible(fn (Payment $record): bool => $record->isPending() && static::canReviewPayment($record))
                    ->action(function (Payment $record): void {
                        static::verifyMatchPayment($record);
                    }),
                Action::make('rejectProof')
                    ->label(__('Reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Payment $record): bool => $record->isPending() && static::canReviewPayment($record))
                    ->requiresConfirmation()
                    ->modalHeading(__('Reject this match payment proof?'))
                    ->modalDescription(__('The player will be notified and the reserved slot will open again.'))
                    ->modalSubmitActionLabel(__('Reject'))
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label(__('Reason for rejection'))
                            ->validationAttribute(__('Reason for rejection'))
                            ->required()
                            ->maxLength(500)
                            ->rows(4),
                    ])
                    ->action(function (Payment $record, array $data): void {
                        static::rejectMatchPayment($record, $data['rejection_reason']);
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();

        if (! $user || (! $user->isAdmin() && ! $user->isFieldOwner())) {
            return null;
        }

        $query = Payment::query()
            ->where('type', 'MatchFee')
            ->where('status', 'Pending')
            ->whereNotNull('proof');

        if ($user->isFieldOwner()) {
            $query->whereHas('matchParticipant.game.booking.field', fn (Builder $fieldQuery) => $fieldQuery->where('owner_id', $user->id));
        }

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

    private static function verifyMatchPayment(Payment $payment): void
    {
        abort_unless($payment->isMatchFee(), 404);
        abort_unless(static::canReviewPayment($payment), 403);

        $payment->loadMissing('payer', 'matchParticipant.user', 'matchParticipant.game.booking.field');

        $participant = $payment->matchParticipant;
        $match = $participant->game;

        $payment->update([
            'status' => 'Verified',
            'rejection_reason' => null,
        ]);

        $participant->update([
            'status' => 'Confirmed',
        ]);

        $match->refreshParticipationState();

        Notification::create([
            'user_id' => $participant->user_id,
            'message' => __('Your join request for :title was confirmed on Team :team.', [
                'title' => $match->title,
                'team' => $participant->team,
            ]),
            'type' => 'Match',
            'status' => 'Unread',
            'notifiable_type' => MatchParticipant::class,
            'notifiable_id' => $participant->id,
        ]);

        AuditLog::record('match.join_confirmed', $participant, [
            'payment_id' => $payment->id,
            'match_id' => $match->id,
            'team' => $participant->team,
            'match_status' => $match->status,
        ]);
    }

    private static function rejectMatchPayment(Payment $payment, string $reason): void
    {
        abort_unless($payment->isMatchFee(), 404);
        abort_unless(static::canReviewPayment($payment), 403);

        $payment->loadMissing('matchParticipant.user', 'matchParticipant.game');

        $participant = $payment->matchParticipant;
        $match = $participant->game;

        $payment->update([
            'status' => 'Rejected',
            'rejection_reason' => $reason,
        ]);

        $participant->update([
            'status' => 'Cancelled',
        ]);

        $match->refreshParticipationState();

        Notification::create([
            'user_id' => $participant->user_id,
            'message' => __('Your join request for :title was rejected. You can submit a new payment proof if slots are still open.', [
                'title' => $match->title,
            ]),
            'type' => 'Payment',
            'status' => 'Unread',
            'notifiable_type' => Payment::class,
            'notifiable_id' => $payment->id,
        ]);

        AuditLog::record('match.join_rejected', $participant, [
            'payment_id' => $payment->id,
            'match_id' => $match->id,
            'reason' => $reason,
        ]);
    }

    private static function canReviewPayment(Payment $payment): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $user->isFieldOwner() && $payment->matchParticipant?->game?->booking?->field?->owner_id === $user->id;
    }
}

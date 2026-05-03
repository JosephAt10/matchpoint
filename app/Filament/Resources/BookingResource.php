<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\Notification;
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

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Bookings';

    protected static ?string $modelLabel = 'Booking';

    protected static ?string $pluralModelLabel = 'Bookings';

    protected static ?int $navigationSort = 2;

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

        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $user->isFieldOwner() && $record instanceof Booking && $record->field?->owner_id === $user->id;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                $user = auth()->user();

                $query->with(['user', 'field', 'bookedSlots.timeSlot', 'payment.payer'])
                    ->latest();

                if ($user?->isFieldOwner()) {
                    $query->whereHas('field', fn (Builder $fieldQuery) => $fieldQuery->where('owner_id', $user->id));
                }

                return $query;
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('field.name')
                    ->label('Field')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Booked By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Booking Date')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('slot_range')
                    ->label('Slot')
                    ->state(fn (Booking $record): string => static::slotRange($record)),
                Tables\Columns\TextColumn::make('payment.amount')
                    ->label('DP Amount')
                    ->formatStateUsing(fn ($state): string => 'Rp ' . number_format((float) $state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Booking Status')
                    ->colors([
                        'warning' => 'Pending',
                        'success' => 'Confirmed',
                        'gray' => 'Completed',
                        'danger' => 'Cancelled',
                    ]),
                Tables\Columns\BadgeColumn::make('payment.status')
                    ->label('Payment Status')
                    ->colors([
                        'warning' => 'Pending',
                        'success' => 'Verified',
                        'danger' => 'Rejected',
                    ]),
                Tables\Columns\TextColumn::make('payment_deadline')
                    ->label('Deadline')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('payment.payer.name')
                    ->label('Uploaded By')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('payment.created_at')
                    ->label('Uploaded At')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Booking Status')
                    ->options([
                        'Pending' => 'Pending',
                        'Confirmed' => 'Confirmed',
                        'Completed' => 'Completed',
                        'Cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->relationship('payment', 'status')
                    ->options([
                        'Pending' => 'Pending',
                        'Verified' => 'Verified',
                        'Rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Action::make('viewProof')
                    ->label('View Proof')
                    ->icon('heroicon-o-photo')
                    ->color('gray')
                    ->visible(fn (Booking $record): bool => filled($record->payment?->proof))
                    ->url(fn (Booking $record): string => Storage::url($record->payment->proof), shouldOpenInNewTab: true),
                Action::make('confirmBooking')
                    ->label('Confirm Booking')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Booking $record): bool => $record->isPending() && $record->payment?->isPending() && static::canReviewBooking($record))
                    ->action(function (Booking $record): void {
                        static::verifyBookingPayment($record);
                    }),
                Action::make('rejectProof')
                    ->label('Reject Proof')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Booking $record): bool => $record->isPending() && $record->payment?->isPending() && static::canReviewBooking($record))
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label('Reason for rejection')
                            ->required()
                            ->maxLength(500)
                            ->rows(4),
                    ])
                    ->action(function (Booking $record, array $data): void {
                        static::rejectBookingPayment($record, $data['rejection_reason']);
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

        $pendingQuery = Booking::query()
            ->where('status', 'Pending')
            ->whereHas('payment', fn (Builder $query) => $query
                ->where('type', 'BookingDP')
                ->where('status', 'Pending')
                ->whereNotNull('proof'));

        if ($user->isFieldOwner()) {
            $pendingQuery->whereHas('field', fn (Builder $fieldQuery) => $fieldQuery->where('owner_id', $user->id));
        }

        $pendingCount = $pendingQuery->count();

        return $pendingCount > 0 ? (string) $pendingCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() ? 'warning' : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        $pendingCount = static::getNavigationBadge();

        if (! $pendingCount) {
            return null;
        }

        $reviewer = auth()->user()?->isFieldOwner() ? 'your' : 'field owner';

        return "{$pendingCount} booking proof" . ($pendingCount === '1' ? '' : 's') . " waiting for {$reviewer} review.";
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
        ];
    }

    private static function verifyBookingPayment(Booking $booking): void
    {
        abort_unless($booking->payment?->isBookingDP(), 404);
        abort_unless(static::canReviewBooking($booking), 403);

        $booking->loadMissing('payment', 'user', 'field');

        $booking->payment->update([
            'status' => 'Verified',
            'rejection_reason' => null,
        ]);

        $booking->update([
            'status' => 'Confirmed',
            'version' => $booking->version + 1,
        ]);

        Notification::create([
            'user_id' => $booking->user_id,
            'message' => "Your booking for {$booking->field->name} has been confirmed.",
            'type' => 'Booking',
            'status' => 'Unread',
            'notifiable_type' => Booking::class,
            'notifiable_id' => $booking->id,
        ]);

        AuditLog::record('payment.verified', $booking->payment, [
            'booking_id' => $booking->id,
            'booking_status' => 'Confirmed',
        ]);
    }

    private static function rejectBookingPayment(Booking $booking, string $reason): void
    {
        abort_unless($booking->payment?->isBookingDP(), 404);
        abort_unless(static::canReviewBooking($booking), 403);

        $booking->loadMissing('payment', 'user', 'field');

        $booking->payment->update([
            'status' => 'Rejected',
            'rejection_reason' => $reason,
        ]);

        Notification::create([
            'user_id' => $booking->user_id,
            'message' => "Your payment proof for {$booking->field->name} was rejected. Please upload a new proof.",
            'type' => 'Payment',
            'status' => 'Unread',
            'notifiable_type' => $booking->payment::class,
            'notifiable_id' => $booking->payment->id,
        ]);

        AuditLog::record('payment.rejected', $booking->payment, [
            'booking_id' => $booking->id,
            'reason' => $reason,
        ]);
    }

    private static function slotRange(Booking $booking): string
    {
        $slots = $booking->bookedSlots
            ->pluck('timeSlot')
            ->filter()
            ->sortBy('start_time')
            ->values();

        if ($slots->isEmpty()) {
            return 'No slots';
        }

        return substr($slots->first()->start_time, 0, 5) . ' - ' . substr($slots->last()->end_time, 0, 5);
    }

    private static function canReviewBooking(Booking $booking): bool
    {
        $user = auth()->user();

        return $user?->isFieldOwner() && $booking->field?->owner_id === $user->id;
    }
}

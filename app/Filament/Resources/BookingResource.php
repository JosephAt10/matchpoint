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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Bookings';

    protected static ?string $modelLabel = 'Booking';

    protected static ?string $pluralModelLabel = 'Bookings';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('Bookings');
    }

    public static function getModelLabel(): string
    {
        return __('Booking');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Bookings');
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
                    ->label(__('Field'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('Booked By'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label(__('Booking Date'))
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('slot_range')
                    ->label(__('Slot'))
                    ->state(fn (Booking $record): string => static::slotRange($record)),
                Tables\Columns\TextColumn::make('payment.amount')
                    ->label(__('DP Amount'))
                    ->formatStateUsing(fn ($state): string => 'Rp ' . number_format((float) $state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('Booking Status'))
                    ->formatStateUsing(fn (string $state): string => __($state))
                    ->colors([
                        'warning' => 'Pending',
                        'success' => 'Confirmed',
                        'gray' => 'Completed',
                        'danger' => 'Cancelled',
                    ]),
                Tables\Columns\BadgeColumn::make('payment.status')
                    ->label(__('Payment Status'))
                    ->formatStateUsing(fn (string $state): string => __($state))
                    ->colors([
                        'warning' => 'Pending',
                        'success' => 'Verified',
                        'danger' => 'Rejected',
                    ]),
                Tables\Columns\TextColumn::make('payment_deadline')
                    ->label(__('Confirmation Deadline'))
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('payment.payer.name')
                    ->label(__('Uploaded By'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('payment.created_at')
                    ->label(__('Uploaded At'))
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Booking Status'))
                    ->options([
                        'Pending' => __('Pending'),
                        'Confirmed' => __('Confirmed'),
                        'Completed' => __('Completed'),
                        'Cancelled' => __('Cancelled'),
                    ]),
                SelectFilter::make('payment_status')
                    ->label(__('Payment Status'))
                    ->relationship('payment', 'status')
                    ->options([
                        'Pending' => __('Pending'),
                        'Verified' => __('Verified'),
                        'Rejected' => __('Rejected'),
                    ]),
            ])
            ->actions([
                Action::make('viewProof')
                    ->label(__('View Proof'))
                    ->icon('heroicon-o-photo')
                    ->color('gray')
                    ->visible(fn (Booking $record): bool => filled($record->payment?->proof))
                    ->url(fn (Booking $record): string => Storage::url($record->payment->proof), shouldOpenInNewTab: true),
                Action::make('confirmBooking')
                    ->label(__('Confirm Booking'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('Confirm this booking payment?'))
                    ->modalDescription(__('The booking will be marked as confirmed and the user will be notified.'))
                    ->modalSubmitActionLabel(__('Confirm Booking'))
                    ->visible(fn (Booking $record): bool => $record->isPending() && $record->payment?->isPending() && static::canReviewBooking($record))
                    ->action(function (Booking $record): void {
                        static::verifyBookingPayment($record);
                    }),
                Action::make('rejectProof')
                    ->label(__('Reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Booking $record): bool => $record->isPending() && $record->payment?->isPending() && static::canReviewBooking($record))
                    ->requiresConfirmation()
                    ->modalHeading(__('Reject this payment proof?'))
                    ->modalDescription(__('The booking will be cancelled, the selected slot will be released, and the user will be notified.'))
                    ->modalSubmitActionLabel(__('Reject'))
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label(__('Reason for rejection'))
                            ->validationAttribute(__('Reason for rejection'))
                            ->validationMessages([
                                'required' => __('validation.required', ['attribute' => __('Reason for rejection')]),
                                'max.string' => __('validation.max.string', ['attribute' => __('Reason for rejection'), 'max' => 500]),
                            ])
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

        $reviewer = auth()->user()?->isFieldOwner() ? __('your') : __('Field Owner');

        return $pendingCount === '1'
            ? __('1 booking proof is waiting for your review.')
            : __(':count booking proofs are waiting for review by :reviewer.', ['count' => $pendingCount, 'reviewer' => $reviewer]);
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
            'message' => __('Your booking for :field has been confirmed.', ['field' => $booking->field->name]),
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

        DB::transaction(function () use ($booking, $reason): void {
            $booking->loadMissing('payment', 'user', 'field', 'bookedSlots');

            $booking->payment->update([
                'status' => 'Rejected',
                'rejection_reason' => $reason,
            ]);

            $booking->update([
                'status' => 'Cancelled',
                'version' => $booking->version + 1,
            ]);

            $booking->bookedSlots()->delete();

            Notification::create([
                'user_id' => $booking->user_id,
                'message' => __('Your payment proof for :field was rejected and the booking was cancelled. Reason: :reason', [
                    'field' => $booking->field->name,
                    'reason' => $reason,
                ]),
                'type' => 'Payment',
                'status' => 'Unread',
                'notifiable_type' => $booking->payment::class,
                'notifiable_id' => $booking->payment->id,
            ]);

            AuditLog::record('payment.rejected', $booking->payment, [
                'booking_id' => $booking->id,
                'booking_status' => 'Cancelled',
                'released_slots' => true,
                'reason' => $reason,
            ]);
        });
    }

    private static function slotRange(Booking $booking): string
    {
        $slots = $booking->bookedSlots
            ->pluck('timeSlot')
            ->filter()
            ->sortBy('start_time')
            ->values();

        if ($slots->isEmpty()) {
            return __('No available slots');
        }

        return substr($slots->first()->start_time, 0, 5) . ' - ' . substr($slots->last()->end_time, 0, 5);
    }

    private static function canReviewBooking(Booking $booking): bool
    {
        $user = auth()->user();

        return $user?->isFieldOwner() && $booking->field?->owner_id === $user->id;
    }
}

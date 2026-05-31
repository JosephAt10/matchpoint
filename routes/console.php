<?php

use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('bookings:cancel-overdue', function () {
    $cancelled = 0;

    Booking::query()
        ->pending()
        ->where('created_at', '<=', now()->subHours(48))
        ->whereHas('payment', fn ($query) => $query
            ->where('type', 'BookingDP')
            ->where('status', 'Pending')
            ->whereNotNull('proof'))
        ->with(['field.owner', 'payment', 'bookedSlots'])
        ->chunkById(100, function ($bookings) use (&$cancelled) {
            foreach ($bookings as $booking) {
                DB::transaction(function () use ($booking, &$cancelled): void {
                    $booking = Booking::query()
                        ->with(['field.owner', 'payment', 'bookedSlots'])
                        ->lockForUpdate()
                        ->find($booking->id);

                    if (
                        ! $booking
                        || ! $booking->isPending()
                        || ! $booking->payment?->isBookingDP()
                        || ! $booking->payment?->isPending()
                        || blank($booking->payment->proof)
                        || $booking->created_at->gt(now()->subHours(48))
                    ) {
                        return;
                    }

                    $releasedSlotIds = $booking->bookedSlots->pluck('timeslot_id')->all();
                    $reason = 'Field owner did not confirm the booking within 48 hours.';

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
                        'message' => "Your booking for {$booking->field->name} was automatically cancelled because the Field Owner did not confirm it within 48 hours. The time slot has been released.",
                        'type' => 'Booking',
                        'status' => 'Unread',
                        'notifiable_type' => Booking::class,
                        'notifiable_id' => $booking->id,
                    ]);

                    Notification::create([
                        'user_id' => $booking->field->owner_id,
                        'message' => "A pending booking for {$booking->field->name} was automatically cancelled because it was not confirmed within 48 hours. The time slot has been released.",
                        'type' => 'Booking',
                        'status' => 'Unread',
                        'notifiable_type' => Booking::class,
                        'notifiable_id' => $booking->id,
                    ]);

                    AuditLog::record('booking.auto_cancelled', $booking, [
                        'reason' => 'field_owner_confirmation_timeout',
                        'released_slots' => true,
                        'slot_ids' => $releasedSlotIds,
                        'payment_status' => 'Rejected',
                        'timeout_hours' => 48,
                    ]);

                    $cancelled++;
                });
            }
        });

    $this->info("Cancelled {$cancelled} overdue bookings.");
})->purpose('Cancel pending bookings not confirmed by field owners within 48 hours.');

Schedule::command('bookings:cancel-overdue')->hourly();

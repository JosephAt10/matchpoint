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

Artisan::command('bookings:complete-finished', function () {
    $completed = 0;

    $scheduleQuery = DB::table('booked_slots')
        ->join('time_slots', 'time_slots.id', '=', 'booked_slots.timeslot_id')
        ->selectRaw("
            booked_slots.booking_id,
            MAX(
                CASE
                    WHEN time_slots.end_time = '00:00:00'
                        THEN DATE_ADD(TIMESTAMP(booked_slots.date, '00:00:00'), INTERVAL 1 DAY)
                    ELSE TIMESTAMP(booked_slots.date, time_slots.end_time)
                END
            ) as booking_ends_at
        ")
        ->groupBy('booked_slots.booking_id');

    Booking::query()
        ->select('bookings.*')
        ->joinSub($scheduleQuery, 'booking_schedule', fn ($join) => $join->on('booking_schedule.booking_id', '=', 'bookings.id'))
        ->where('bookings.status', 'Confirmed')
        ->where('booking_schedule.booking_ends_at', '<=', now())
        ->with(['field.owner', 'user', 'bookedSlots.timeSlot'])
        ->chunkById(100, function ($bookings) use (&$completed): void {
            foreach ($bookings as $booking) {
                DB::transaction(function () use ($booking, &$completed): void {
                    $booking = Booking::query()
                        ->with(['field.owner', 'user', 'bookedSlots.timeSlot'])
                        ->lockForUpdate()
                        ->find($booking->id);

                    if (! $booking || ! $booking->isConfirmed()) {
                        return;
                    }

                    $latestSlot = $booking->bookedSlots
                        ->pluck('timeSlot')
                        ->filter()
                        ->sortByDesc(fn ($slot) => $slot->end_time === '00:00:00' ? '24:00:00' : $slot->end_time)
                        ->first();

                    if (! $latestSlot) {
                        return;
                    }

                    $bookingEndsAt = $latestSlot->end_time === '00:00:00'
                        ? $booking->date->copy()->addDay()->startOfDay()
                        : \Illuminate\Support\Carbon::parse($booking->date->toDateString() . ' ' . $latestSlot->end_time);

                    if ($bookingEndsAt->isFuture()) {
                        return;
                    }

                    $booking->update([
                        'status' => 'Completed',
                        'version' => $booking->version + 1,
                    ]);

                    Notification::create([
                        'user_id' => $booking->user_id,
                        'message' => "Your booking for {$booking->field->name} has been marked as completed.",
                        'type' => 'Booking',
                        'status' => 'Unread',
                        'notifiable_type' => Booking::class,
                        'notifiable_id' => $booking->id,
                    ]);

                    if ($booking->field?->owner_id) {
                        Notification::create([
                            'user_id' => $booking->field->owner_id,
                            'message' => "A booking for {$booking->field->name} has been marked as completed.",
                            'type' => 'Booking',
                            'status' => 'Unread',
                            'notifiable_type' => Booking::class,
                            'notifiable_id' => $booking->id,
                        ]);
                    }

                    AuditLog::record('booking.completed', $booking, [
                        'completed_at' => now()->toDateTimeString(),
                        'booking_ends_at' => $bookingEndsAt->toDateTimeString(),
                    ]);

                    $completed++;
                });
            }
        }, 'bookings.id');

    $this->info("Completed {$completed} finished bookings.");
})->purpose('Mark confirmed bookings as completed after their booked time has passed.');

Schedule::command('bookings:complete-finished')->hourly();

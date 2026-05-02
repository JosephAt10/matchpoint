<?php

use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('bookings:cancel-overdue', function () {
    $cancelled = 0;

    Booking::query()
        ->pending()
        ->where('payment_deadline', '<', now())
        ->whereDoesntHave('payment', fn ($query) => $query->whereNotNull('proof'))
        ->with(['field.owner'])
        ->chunkById(100, function ($bookings) use (&$cancelled) {
            foreach ($bookings as $booking) {
                $booking->update([
                    'status' => 'Cancelled',
                    'version' => $booking->version + 1,
                ]);

                Notification::create([
                    'user_id' => $booking->user_id,
                    'message' => "Your booking for {$booking->field->name} was cancelled because no payment proof was uploaded before the deadline.",
                    'type' => 'Booking',
                    'status' => 'Unread',
                    'notifiable_type' => Booking::class,
                    'notifiable_id' => $booking->id,
                ]);

                Notification::create([
                    'user_id' => $booking->field->owner_id,
                    'message' => "A booking for {$booking->field->name} was cancelled because the payment deadline expired.",
                    'type' => 'Booking',
                    'status' => 'Unread',
                    'notifiable_type' => Booking::class,
                    'notifiable_id' => $booking->id,
                ]);

                AuditLog::record('booking.auto_cancelled', $booking, [
                    'reason' => 'payment_deadline_expired_without_proof',
                ]);

                $cancelled++;
            }
        });

    $this->info("Cancelled {$cancelled} overdue bookings.");
})->purpose('Cancel overdue pending bookings that have no uploaded payment proof.');

Schedule::command('bookings:cancel-overdue')->hourly();

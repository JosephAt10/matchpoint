<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentVerificationController extends Controller
{
    public function verify(Payment $payment): RedirectResponse
    {
        abort_unless($payment->isBookingDP() && $payment->booking, 404);

        $payment->load('booking.user', 'booking.field');

        $payment->update([
            'status' => 'Verified',
            'rejection_reason' => null,
        ]);

        $payment->booking->update([
            'status' => 'Confirmed',
            'version' => $payment->booking->version + 1,
        ]);

        Notification::create([
            'user_id' => $payment->booking->user_id,
            'message' => "Your booking for {$payment->booking->field->name} has been confirmed.",
            'type' => 'Booking',
            'status' => 'Unread',
            'notifiable_type' => Booking::class,
            'notifiable_id' => $payment->booking->id,
        ]);

        AuditLog::record('payment.verified', $payment, [
            'booking_id' => $payment->booking_id,
            'booking_status' => 'Confirmed',
        ]);

        return redirect()->route('dashboard')
            ->with('status', 'Payment verified and booking confirmed.');
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        abort_unless($payment->isBookingDP() && $payment->booking, 404);

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $payment->load('booking.user', 'booking.field');

        $payment->update([
            'status' => 'Rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        Notification::create([
            'user_id' => $payment->booking->user_id,
            'message' => "Your payment proof for {$payment->booking->field->name} was rejected. Please upload a new proof.",
            'type' => 'Payment',
            'status' => 'Unread',
            'notifiable_type' => $payment::class,
            'notifiable_id' => $payment->id,
        ]);

        AuditLog::record('payment.rejected', $payment, [
            'booking_id' => $payment->booking_id,
            'reason' => $validated['rejection_reason'],
        ]);

        return redirect()->route('dashboard')
            ->with('status', 'Payment proof rejected. The booking remains pending.');
    }
}

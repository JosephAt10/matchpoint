<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserActivityController extends Controller
{
    public function payments(Request $request): View
    {
        $user = $request->user();

        $payments = $user->payments()
            ->with([
                'booking.field',
                'booking.bookedSlots.timeSlot',
                'matchParticipant.game.booking.field',
                'matchParticipant.game.booking.bookedSlots.timeSlot',
            ])
            ->latest()
            ->get()
            ->map(function ($payment): array {
                $booking = $payment->isBookingDP()
                    ? $payment->booking
                    : $payment->matchParticipant?->game?->booking;
                $match = $payment->matchParticipant?->game;
                $slots = $booking?->bookedSlots
                    ->pluck('timeSlot')
                    ->filter()
                    ->sortBy('start_time')
                    ->values() ?? collect();

                $timeRange = $slots->isNotEmpty()
                    ? substr($slots->first()->start_time, 0, 5) . ' - ' . substr($slots->last()->end_time, 0, 5)
                    : __('Time not available');

                return [
                    'title' => $payment->isMatchFee() ? $match?->title : $booking?->field?->name,
                    'field_name' => $booking?->field?->name,
                    'location' => $booking?->field?->location,
                    'image_url' => $booking?->field?->image_url,
                    'type_label' => $payment->isMatchFee() ? __('Match Fee') : __('Booking Payment'),
                    'amount' => 'Rp ' . number_format((float) $payment->amount, 0, ',', '.'),
                    'status' => $payment->status,
                    'status_tone' => match ($payment->status) {
                        'Verified' => 'emerald',
                        'Rejected' => 'rose',
                        default => 'amber',
                    },
                    'date_label' => $booking?->date?->translatedFormat('j M Y'),
                    'time_label' => $timeRange,
                    'proof_url' => $payment->proof ? url('/storage/' . ltrim($payment->proof, '/')) : null,
                    'booking_url' => $booking ? route('bookings.show', $booking) : null,
                    'match_url' => $match ? route('matches.show', $match) : null,
                    'team_label' => $payment->isMatchFee() ? $payment->matchParticipant?->team : null,
                    'rejection_reason' => $payment->rejection_reason,
                    'submitted_at' => $payment->created_at?->translatedFormat('j M Y, H:i'),
                ];
            });

        return view('user.payments', [
            'page' => [
                'user' => $this->userMeta($request),
                'payments' => $payments,
                'pending_count' => $payments->where('status', 'Pending')->count(),
                'verified_count' => $payments->where('status', 'Verified')->count(),
                'rejected_count' => $payments->where('status', 'Rejected')->count(),
            ],
        ]);
    }

    public function notifications(Request $request): View
    {
        $user = $request->user();

        $notifications = Notification::query()
            ->forUser($user->id)
            ->latest()
            ->get()
            ->map(fn (Notification $notification): array => [
                'message' => $notification->message,
                'type' => $notification->type,
                'status' => $notification->status,
                'time_label' => $notification->created_at?->diffForHumans(),
                'created_label' => $notification->created_at?->translatedFormat('j M Y, H:i'),
                'tone' => match ($notification->type) {
                    'Payment' => 'amber',
                    'Booking' => 'emerald',
                    default => 'indigo',
                },
            ]);

        Notification::query()
            ->forUser($user->id)
            ->unread()
            ->update(['status' => 'Read']);

        return view('user.notifications', [
            'page' => [
                'user' => $this->userMeta($request),
                'notifications' => $notifications,
                'unread_count' => $notifications->where('status', 'Unread')->count(),
            ],
        ]);
    }

    private function userMeta(Request $request): array
    {
        $user = $request->user();

        return [
            'name' => $user->name,
            'first_name' => str($user->name)->before(' ')->toString(),
            'initials' => str($user->name)
                ->explode(' ')
                ->filter()
                ->take(2)
                ->map(fn (string $part): string => strtoupper(substr($part, 0, 1)))
                ->implode(''),
            'unread_notifications' => Notification::query()->forUser($user->id)->unread()->count(),
        ];
    }
}

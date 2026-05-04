<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Field;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->to('/admin');
        }

        if ($user->isFieldOwner()) {
            return redirect()->to('/owner');
        }

        $today = Carbon::today();

        $bookings = $user->bookings()
            ->with(['field.owner', 'field.timeSlots', 'bookedSlots.timeSlot', 'payment'])
            ->latest('date')
            ->get();

        $upcomingBookings = $bookings
            ->filter(fn (Booking $booking): bool => ! $booking->isCancelled() && $booking->date?->greaterThanOrEqualTo($today))
            ->sortBy([
                fn (Booking $booking) => $booking->date?->timestamp ?? PHP_INT_MAX,
                fn (Booking $booking) => optional($booking->bookedSlots->sortBy('timeSlot.start_time')->first()?->timeSlot)->start_time ?? '99:99:99',
            ])
            ->values();

        $favoriteFields = $user->favoriteFields()
            ->with(['owner', 'timeSlots'])
            ->latest('favorite_fields.created_at')
            ->get();

        $notifications = Notification::query()
            ->forUser($user->id)
            ->latest()
            ->take(4)
            ->get();

        $pendingPaymentsCount = $bookings
            ->filter(fn (Booking $booking): bool => $booking->payment?->isPending() ?? false)
            ->count();

        $dashboardBookings = $upcomingBookings
            ->take(4)
            ->map(function (Booking $booking): array {
                $orderedSlots = $booking->bookedSlots
                    ->pluck('timeSlot')
                    ->filter()
                    ->sortBy('start_time')
                    ->values();

                $slotRange = $orderedSlots->isNotEmpty()
                    ? substr($orderedSlots->first()->start_time, 0, 5) . ' - ' . substr($orderedSlots->last()->end_time, 0, 5)
                    : 'Time not available';

                return [
                    'id' => $booking->id,
                    'field_name' => $booking->field?->name,
                    'location' => $booking->field?->location,
                    'image_url' => $booking->field?->image_url,
                    'date_label' => $booking->date?->format('j M Y'),
                    'time_label' => $slotRange,
                    'status_label' => $this->bookingStatusLabel($booking),
                    'status_tone' => $this->bookingStatusTone($booking),
                    'view_url' => route('bookings.show', $booking),
                ];
            });

        $favoriteCards = $favoriteFields
            ->take(2)
            ->map(fn (Field $field): array => [
                'name' => $field->name,
                'location' => $field->location,
                'price' => 'Rp ' . number_format((float) $field->price_per_slot, 0, ',', '.'),
                'image_url' => $field->image_url,
                'show_url' => route('fields.show', $field),
            ]);

        $recentNotifications = $notifications
            ->map(fn (Notification $notification): array => [
                'message' => $notification->message,
                'time_label' => $notification->created_at?->diffForHumans(),
                'tone' => $this->notificationTone($notification),
            ]);

        return view('dashboards.user', [
            'dashboard' => [
                'user' => [
                    'name' => $user->name,
                    'first_name' => str($user->name)->before(' ')->toString(),
                    'initials' => str($user->name)->explode(' ')->filter()->take(2)->map(fn (string $part) => strtoupper(substr($part, 0, 1)))->implode(''),
                ],
                'unread_notifications' => Notification::query()->forUser($user->id)->unread()->count(),
                'stats' => [
                    [
                        'label' => 'Upcoming Bookings',
                        'value' => $bookings->filter(fn (Booking $booking): bool => $booking->isConfirmed() && $booking->date?->greaterThanOrEqualTo($today))->count(),
                        'hint' => 'Confirmed bookings',
                        'tone' => 'indigo',
                        'icon' => 'calendar',
                    ],
                    [
                        'label' => 'Pending Payments',
                        'value' => $pendingPaymentsCount,
                        'hint' => 'Awaiting confirmation',
                        'tone' => 'amber',
                        'icon' => 'clock',
                    ],
                    [
                        'label' => 'Completed Sessions',
                        'value' => $bookings->where('status', 'Completed')->count(),
                        'hint' => 'All time',
                        'tone' => 'emerald',
                        'icon' => 'check-circle',
                    ],
                    [
                        'label' => 'Favorite Venues',
                        'value' => $favoriteFields->count(),
                        'hint' => 'Saved venues',
                        'tone' => 'pink',
                        'icon' => 'heart',
                    ],
                ],
                'bookings' => $dashboardBookings,
                'bookings_total' => $upcomingBookings->count(),
                'favorites' => $favoriteCards,
                'favorites_total' => $favoriteFields->count(),
                'recent_notifications' => $recentNotifications,
                'notifications_total' => $notifications->count(),
                'links' => [
                    'browse_fields' => route('fields.index'),
                    'favorites' => route('favorites.index'),
                    'profile' => route('profile.edit'),
                    'dashboard' => route('dashboard'),
                    'bookings_anchor' => route('dashboard') . '#upcoming-bookings',
                    'notifications_anchor' => route('notifications.index'),
                    'payments_anchor' => route('payments.index'),
                ],
            ],
        ]);
    }

    private function bookingStatusLabel(Booking $booking): string
    {
        if ($booking->isCompleted()) {
            return 'Completed';
        }

        if ($booking->isConfirmed()) {
            return 'Confirmed';
        }

        if ($booking->payment?->isRejected()) {
            return 'Payment Rejected';
        }

        if ($booking->payment?->isPending()) {
            return 'Pending Payment';
        }

        return $booking->status;
    }

    private function bookingStatusTone(Booking $booking): string
    {
        if ($booking->isCompleted()) {
            return 'slate';
        }

        if ($booking->isConfirmed()) {
            return 'emerald';
        }

        if ($booking->payment?->isRejected()) {
            return 'rose';
        }

        if ($booking->payment?->isPending()) {
            return 'amber';
        }

        return 'indigo';
    }

    private function notificationTone(Notification $notification): string
    {
        return match ($notification->type) {
            'Payment' => 'amber',
            'Booking' => 'emerald',
            default => 'indigo',
        };
    }
}

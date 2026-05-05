<?php

namespace App\Filament\Owner\Pages;

use App\Filament\Owner\Resources\FieldResource;
use App\Filament\Owner\Resources\TimeSlotResource;
use App\Filament\Resources\AppNotificationResource;
use App\Filament\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Field;
use App\Models\Notification;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Page
{
    protected string $view = 'filament.owner.pages.dashboard';

    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?int $navigationSort = -2;

    protected Width|string|null $maxContentWidth = Width::Full;

    public function getHeading(): string | Htmlable | null
    {
        return null;
    }

    public function getSubheading(): string | Htmlable | null
    {
        return null;
    }

    protected function getViewData(): array
    {
        $user = Auth::user();
        $today = Carbon::today();
        $startOfWeek = $today->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $today->copy()->endOfWeek(Carbon::SUNDAY);

        $fields = Field::query()
            ->where('owner_id', $user->id)
            ->with(['timeSlots'])
            ->get();

        $bookings = Booking::query()
            ->whereIn('field_id', $fields->pluck('id'))
            ->with(['field', 'payment', 'bookedSlots.timeSlot'])
            ->get();

        $timeSlots = TimeSlot::query()
            ->whereIn('field_id', $fields->pluck('id'))
            ->with([
                'field',
                'bookedSlots' => fn ($query) => $query->whereDate('date', $today),
            ])
            ->orderBy('start_time')
            ->get();

        $pendingProofs = $bookings
            ->filter(fn (Booking $booking): bool => $booking->isPending()
                && $booking->payment?->type === 'BookingDP'
                && $booking->payment?->status === 'Pending'
                && filled($booking->payment?->proof))
            ->count();

        $todaysBookings = $bookings
            ->filter(fn (Booking $booking): bool => $booking->date?->isSameDay($today) && ! $booking->isCancelled())
            ->count();

        $statusCards = [
            [
                'label' => 'Pending',
                'value' => $bookings->filter(fn (Booking $booking): bool => $booking->status === 'Pending')->count(),
                'hint' => 'Needs review',
                'tone' => 'emerald',
                'icon' => 'clock',
            ],
            [
                'label' => 'Confirmed',
                'value' => $bookings->filter(fn (Booking $booking): bool => $booking->status === 'Confirmed')->count(),
                'hint' => 'Upcoming',
                'tone' => 'blue',
                'icon' => 'check-circle',
            ],
            [
                'label' => 'Completed',
                'value' => $bookings->filter(fn (Booking $booking): bool => $booking->status === 'Completed')->count(),
                'hint' => 'Finished',
                'tone' => 'green',
                'icon' => 'badge-check',
            ],
            [
                'label' => 'Cancelled',
                'value' => $bookings->filter(fn (Booking $booking): bool => $booking->status === 'Cancelled')->count(),
                'hint' => 'Archived',
                'tone' => 'rose',
                'icon' => 'x-mark',
            ],
        ];

        $weeklyBookings = collect(range(0, 6))
            ->map(function (int $offset) use ($bookings, $startOfWeek): array {
                $day = $startOfWeek->copy()->addDays($offset);

                return [
                    'label' => $day->format('D'),
                    'count' => $bookings->filter(fn (Booking $booking): bool => $booking->date?->isSameDay($day))->count(),
                ];
            });

        $myFields = $fields
            ->take(4)
            ->map(function (Field $field) use ($startOfWeek, $endOfWeek): array {
                $weeklyBookedSlots = Booking::query()
                    ->where('field_id', $field->id)
                    ->whereIn('status', ['Confirmed', 'Completed'])
                    ->whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
                    ->withCount('bookedSlots')
                    ->get()
                    ->sum('booked_slots_count');

                $totalSlots = max(1, $field->timeSlots->count());
                $occupancyRate = min(100, (int) round(($weeklyBookedSlots / ($totalSlots * 7)) * 100));

                return [
                    'id' => $field->id,
                    'name' => $field->name,
                    'location' => $field->location,
                    'sport' => $field->sport_type,
                    'type' => $field->type,
                    'price' => 'Rp ' . number_format((float) $field->price_per_slot, 0, ',', '.') . ' / slot',
                    'approved' => $field->is_approved,
                    'approval_status' => $field->approval_status,
                    'time_slots' => $totalSlots,
                    'weekly_booked_slots' => $weeklyBookedSlots,
                    'occupancy_rate' => $occupancyRate,
                    'image_url' => $field->image_url,
                    'surface_style' => $this->getFieldSurfaceStyle($field),
                    'accent_style' => $this->getFieldAccentStyle($field),
                    'bookings_url' => BookingResource::getUrl('index'),
                    'edit_url' => FieldResource::getUrl('edit', ['record' => $field]),
                ];
            });

        $alerts = collect([
            $pendingProofs > 0 ? [
                'title' => "{$pendingProofs} booking proof" . ($pendingProofs === 1 ? '' : 's') . ' need your review',
                'subtitle' => 'Review and confirm or reject new uploads.',
                'tone' => 'amber',
                'icon' => 'warning',
                'url' => BookingResource::getUrl('index', [
                    'tableFilters[status][value]' => 'Pending',
                    'tableFilters[payment_status][value]' => 'Pending',
                ]),
            ] : null,
            $fields->filter(fn (Field $field): bool => $field->isPendingApproval())->count() > 0 ? [
                'title' => $fields->filter(fn (Field $field): bool => $field->isPendingApproval())->count() . ' field pending admin approval',
                'subtitle' => 'Waiting to be visible to players.',
                'tone' => 'blue',
                'icon' => 'info',
                'url' => FieldResource::getUrl('index'),
            ] : null,
            $todaysBookings > 0 ? [
                'title' => "{$todaysBookings} booking" . ($todaysBookings === 1 ? '' : 's') . ' scheduled today',
                'subtitle' => 'Keep an eye on today\'s field activity.',
                'tone' => 'emerald',
                'icon' => 'calendar',
                'url' => BookingResource::getUrl('index'),
            ] : null,
            Notification::query()->forUser($user->id)->unread()->count() > 0 ? [
                'title' => Notification::query()->forUser($user->id)->unread()->count() . ' unread notification' . (Notification::query()->forUser($user->id)->unread()->count() === 1 ? '' : 's'),
                'subtitle' => 'Catch up on the latest platform updates.',
                'tone' => 'violet',
                'icon' => 'bell',
                'url' => AppNotificationResource::getUrl('index'),
            ] : null,
        ])->filter()->values();

        $todaySchedule = $timeSlots
            ->take(6)
            ->map(function (TimeSlot $slot): array {
                $isBooked = $slot->bookedSlots->isNotEmpty();
                $isAvailable = $slot->is_available_base && ! $isBooked;

                return [
                    'field' => $slot->field?->name,
                    'range' => substr($slot->start_time, 0, 5) . ' - ' . substr($slot->end_time, 0, 5),
                    'status' => $isBooked ? 'Booked' : ($isAvailable ? 'Available' : 'Closed'),
                    'tone' => $isBooked ? 'rose' : ($isAvailable ? 'emerald' : 'slate'),
                ];
            });

        return [
            'dashboard' => [
                'owner' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'initials' => $this->getInitials($user->name),
                ],
                'today_label' => $today->format('M d, Y'),
                'stats' => [
                    [
                        'label' => 'Total Fields',
                        'value' => $fields->count(),
                        'hint' => 'All your venues',
                        'tone' => 'emerald',
                        'icon' => 'field',
                    ],
                    [
                        'label' => 'Approved Fields',
                        'value' => $fields->where('is_approved', true)->count(),
                        'hint' => 'Active and visible',
                        'tone' => 'blue',
                        'icon' => 'shield-check',
                    ],
                    [
                        'label' => 'Pending Booking Proofs',
                        'value' => $pendingProofs,
                        'hint' => 'Waiting for your review',
                        'tone' => 'amber',
                        'icon' => 'document',
                    ],
                    [
                        'label' => "Today's Bookings",
                        'value' => $todaysBookings,
                        'hint' => 'Scheduled for today',
                        'tone' => 'violet',
                        'icon' => 'calendar',
                    ],
                    [
                        'label' => 'Configured Time Slots',
                        'value' => $timeSlots->count(),
                        'hint' => 'Total time slots',
                        'tone' => 'cyan',
                        'icon' => 'clock',
                    ],
                ],
                'status_cards' => $statusCards,
                'weekly_bookings' => $weeklyBookings,
                'my_fields' => $myFields,
                'alerts' => $alerts,
                'today_schedule' => $todaySchedule,
                'today_schedule_overflow' => max(0, $timeSlots->count() - $todaySchedule->count()),
                'schedule_url' => TimeSlotResource::getUrl('index'),
                'quick_actions' => [
                    [
                        'label' => 'Add New Field',
                        'description' => 'Create a new field or venue',
                        'url' => FieldResource::getUrl('create'),
                        'tone' => 'emerald',
                        'icon' => 'plus',
                    ],
                    [
                        'label' => 'Add Time Slot',
                        'description' => 'Configure field availability',
                        'url' => TimeSlotResource::getUrl('create'),
                        'tone' => 'blue',
                        'icon' => 'clock',
                    ],
                    [
                        'label' => 'View All Bookings',
                        'description' => 'Manage bookings on your fields',
                        'url' => BookingResource::getUrl('index'),
                        'tone' => 'violet',
                        'icon' => 'list',
                    ],
                ],
            ],
        ];
    }

    protected function getFieldSurfaceStyle(Field $field): string
    {
        return match (strtolower($field->sport_type)) {
            'basketball' => 'linear-gradient(145deg, rgba(17, 24, 39, 0.72), rgba(59, 130, 246, 0.25)), radial-gradient(circle at top left, rgba(96, 165, 250, 0.35), transparent 55%), #111827',
            'tennis' => 'linear-gradient(145deg, rgba(17, 24, 39, 0.72), rgba(34, 197, 94, 0.22)), radial-gradient(circle at top left, rgba(74, 222, 128, 0.3), transparent 55%), #111827',
            'futsal', 'football' => 'linear-gradient(145deg, rgba(17, 24, 39, 0.72), rgba(16, 185, 129, 0.22)), radial-gradient(circle at top left, rgba(52, 211, 153, 0.3), transparent 55%), #111827',
            'badminton', 'volleyball' => 'linear-gradient(145deg, rgba(17, 24, 39, 0.72), rgba(168, 85, 247, 0.25)), radial-gradient(circle at top left, rgba(196, 181, 253, 0.28), transparent 55%), #111827',
            default => 'linear-gradient(145deg, rgba(17, 24, 39, 0.72), rgba(20, 184, 166, 0.2)), radial-gradient(circle at top left, rgba(45, 212, 191, 0.28), transparent 55%), #111827',
        };
    }

    protected function getFieldAccentStyle(Field $field): string
    {
        return match (strtolower($field->sport_type)) {
            'basketball' => 'linear-gradient(135deg, #22c55e, #14532d)',
            'tennis' => 'linear-gradient(135deg, #60a5fa, #1d4ed8)',
            'futsal', 'football' => 'linear-gradient(135deg, #34d399, #065f46)',
            'badminton', 'volleyball' => 'linear-gradient(135deg, #a78bfa, #4c1d95)',
            default => 'linear-gradient(135deg, #2dd4bf, #155e75)',
        };
    }

    protected function getInitials(string $name): string
    {
        return str($name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => strtoupper(substr($part, 0, 1)))
            ->implode('');
    }
}

<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\BookingResource;
use App\Filament\Resources\UserResource;
use App\Models\Booking;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminOverviewWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $user = auth()->user();

        if ($user?->isFieldOwner()) {
            $pendingOwnerBookings = Booking::query()
                ->where('status', 'Pending')
                ->whereHas('field', fn ($query) => $query->where('owner_id', $user->id))
                ->whereHas('payment', fn ($query) => $query
                    ->where('type', 'BookingDP')
                    ->where('status', 'Pending')
                    ->whereNotNull('proof'))
                ->count();

            $activeFields = $user->fields()->where('is_approved', true)->count();

            return [
                Stat::make(__('Pending Booking Proofs'), (string) $pendingOwnerBookings)
                    ->description($pendingOwnerBookings > 0 ? __('Bookings on your fields waiting for review') : __('No booking proofs are waiting on your fields'))
                    ->descriptionIcon('heroicon-m-clock', IconPosition::Before)
                    ->color($pendingOwnerBookings > 0 ? 'warning' : 'success')
                    ->url(BookingResource::getUrl('index', [
                        'tableFilters[status][value]' => 'Pending',
                        'tableFilters[payment_status][value]' => 'Pending',
                    ])),
                Stat::make(__('Approved Fields'), (string) $activeFields)
                    ->description($activeFields > 0 ? __('Active venues under your account') : __('No approved fields yet'))
                    ->descriptionIcon('heroicon-m-map-pin', IconPosition::Before)
                    ->color($activeFields > 0 ? 'success' : 'gray'),
            ];
        }

        $pendingOwners = User::query()
            ->where('role', 'FieldOwner')
            ->where('status', 'PendingApproval')
            ->count();

        $pendingBookings = Booking::query()
            ->where('status', 'Pending')
            ->whereHas('payment', fn ($query) => $query
                ->where('type', 'BookingDP')
                ->where('status', 'Pending')
                ->whereNotNull('proof'))
            ->count();

        return [
            Stat::make(__('Pending Field Owners'), (string) $pendingOwners)
                ->description($pendingOwners > 0 ? __('Accounts waiting for admin approval') : __('No field owner approvals are waiting'))
                ->descriptionIcon('heroicon-m-user-plus', IconPosition::Before)
                ->color($pendingOwners > 0 ? 'warning' : 'success')
                ->url(UserResource::getUrl('index', [
                    'tableFilters[role][value]' => 'FieldOwner',
                    'tableFilters[status][value]' => 'PendingApproval',
                ])),
            Stat::make(__('Pending Booking Proofs'), (string) $pendingBookings)
                ->description($pendingBookings > 0 ? __('Monitor proofs waiting for field owner review') : __('No booking proofs are waiting for field owner review'))
                ->descriptionIcon('heroicon-m-clock', IconPosition::Before)
                ->color($pendingBookings > 0 ? 'warning' : 'success')
                ->url(BookingResource::getUrl('index', [
                    'tableFilters[status][value]' => 'Pending',
                    'tableFilters[payment_status][value]' => 'Pending',
                ])),
        ];
    }

    protected function getHeading(): ?string
    {
        return auth()->user()?->isFieldOwner() ? __('Field Owner Review Queue') : __('Admin Booking Overview');
    }

    protected function getDescription(): ?string
    {
        return auth()->user()?->isFieldOwner()
            ? __('Review booking proofs for your own fields from here.')
            : __('View booking activity across the platform while field owners handle proof review for their own fields.');
    }
}

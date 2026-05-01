<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return view('dashboards.admin', [
                'stats' => [
                    'users'          => User::count(),
                    'pendingOwners'  => User::where('role', 'FieldOwner')->where('status', 'PendingApproval')->count(),
                    'approvedFields' => Field::where('is_approved', true)->count(),
                    'pendingPayments' => Payment::forBookings()->pending()->whereNotNull('proof')->count(),
                ],
                'pendingOwners' => User::where('role', 'FieldOwner')
                    ->where('status', 'PendingApproval')
                    ->latest()
                    ->get(),
                'pendingPayments' => Payment::query()
                    ->forBookings()
                    ->pending()
                    ->whereNotNull('proof')
                    ->with(['booking.user', 'booking.field', 'booking.bookedSlots.timeSlot', 'payer'])
                    ->latest()
                    ->get(),
                'notifications' => Notification::query()
                    ->forUser($user->id)
                    ->unread()
                    ->latest()
                    ->take(6)
                    ->get(),
            ]);
        }

        if ($user->isFieldOwner()) {
            return view('dashboards.field-owner', [
                'stats' => [
                    'fields'         => $user->fields()->count(),
                    'approvedFields' => $user->fields()->where('is_approved', true)->count(),
                    'timeSlots'      => $user->fields()->withCount('timeSlots')->get()->sum('time_slots_count'),
                ],
                'fields' => $user->fields()->latest()->get(),
            ]);
        }

        return view('dashboards.user', [
            'stats' => [
                'availableFields' => Field::approved()->count(),
                'sports'          => Field::approved()->distinct('sport_type')->count('sport_type'),
                'locations'       => Field::approved()->distinct('location')->count('location'),
            ],
            'featuredFields' => Field::approved()
                ->with('owner')
                ->latest()
                ->take(4)
                ->get(),
        ]);
    }
}

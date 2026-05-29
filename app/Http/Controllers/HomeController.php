<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Field;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $featuredMatch = null;
        $matches = collect();
        $fields = collect();

        if (Schema::hasTable('matches')) {
            $scheduleQuery = DB::table('bookings')
                ->join('booked_slots', 'booked_slots.booking_id', '=', 'bookings.id')
                ->join('time_slots', 'time_slots.id', '=', 'booked_slots.timeslot_id')
                ->selectRaw("
                    bookings.id as booking_id,
                    MAX(
                        CASE
                            WHEN time_slots.end_time = '00:00:00'
                                THEN DATE_ADD(TIMESTAMP(bookings.date, '00:00:00'), INTERVAL 1 DAY)
                            ELSE TIMESTAMP(bookings.date, time_slots.end_time)
                        END
                    ) as booking_ends_at
                ")
                ->groupBy('bookings.id');

            $query = Game::query()
                ->select('matches.*')
                ->with(['booking.field', 'booking.bookedSlots.timeSlot', 'participants'])
                ->joinSub($scheduleQuery, 'booking_schedule', fn ($join) => $join->on('booking_schedule.booking_id', '=', 'matches.booking_id'))
                ->where('matches.status', 'Open')
                ->whereNotNull('matches.title')
                ->whereNotNull('matches.team_a_name')
                ->whereNotNull('matches.team_b_name')
                ->whereNotNull('matches.max_per_team')
                ->where('booking_schedule.booking_ends_at', '>', now())
                ->latest('matches.created_at');

            $featuredMatch = (clone $query)->first();
            $matches = (clone $query)->take(3)->get();
        }

        if (Schema::hasTable('fields')) {
            $fields = Field::query()
                ->where('is_approved', true)
                ->latest()
                ->take(6)
                ->get();
        }

        return view('home', [
            'featuredMatch' => $featuredMatch,
            'matches' => $matches,
            'fields' => $fields,
        ]);
    }
}

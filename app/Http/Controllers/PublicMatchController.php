<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\Game;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PublicMatchController extends Controller
{
    public function index(): View
    {
        $matches = Game::query()
            ->with(['booking.field', 'booking.bookedSlots.timeSlot', 'creator'])
            ->open()
            ->latest()
            ->paginate(9);

        return view('matches.index', [
            'matches' => $matches,
        ]);
    }

    public function create(Request $request): View
    {
        $eligibleBookings = $this->eligibleBookings($request);

        return view('matches.create', [
            'eligibleBookings' => $eligibleBookings,
            'bookingOptions' => $eligibleBookings->map(fn (Booking $booking): array => $this->bookingOption($booking))->values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $eligibleBookingIds = $this->eligibleBookings($request)->pluck('id')->all();

        $validated = $request->validate([
            'booking_id' => ['required', 'integer', Rule::in($eligibleBookingIds)],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:200'],
            'max_participants' => ['required', 'integer', 'min:1', 'max:100'],
            'participant_fee' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'skill_level' => ['nullable', 'string', Rule::in(['All Levels', 'Beginner', 'Intermediate', 'Advanced'])],
        ], [
            'booking_id.in' => __('Please choose one of your confirmed bookings that is not already linked to a public match.'),
            'max_participants.min' => __('Participant slots must be at least 1.'),
            'participant_fee.min' => __('Participant fee cannot be negative.'),
        ]);

        try {
            $match = DB::transaction(function () use ($validated, $request): Game {
                $booking = Booking::query()
                    ->whereKey($validated['booking_id'])
                    ->where('user_id', $request->user()->id)
                    ->where('status', 'Confirmed')
                    ->doesntHave('game')
                    ->lockForUpdate()
                    ->firstOrFail();

                $match = Game::create([
                    'booking_id' => $booking->id,
                    'creator_id' => $request->user()->id,
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'max_participants' => (int) $validated['max_participants'],
                    'filled_slots' => 0,
                    'participant_fee' => $validated['participant_fee'],
                    'gender' => 'Open',
                    'skill_level' => $validated['skill_level'] ?? 'All Levels',
                    'match_type' => 'Friendly',
                    'status' => 'Open',
                ]);

                AuditLog::record('match.created', $match, [
                    'booking_id' => $booking->id,
                    'max_participants' => $match->max_participants,
                    'participant_fee' => $match->participant_fee,
                    'skill_level' => $match->skill_level,
                ]);

                return $match;
            });
        } catch (QueryException) {
            throw ValidationException::withMessages([
                'booking_id' => __('This booking is already linked to a public match.'),
            ]);
        }

        return redirect()
            ->route('matches.index')
            ->with('status', __('Public match created successfully.'));
    }

    private function bookingOption(Booking $booking): array
    {
        $slots = $booking->bookedSlots
            ->pluck('timeSlot')
            ->filter()
            ->sortBy('start_time')
            ->values();

        $start = $slots->isNotEmpty() ? substr($slots->first()->start_time, 0, 5) : '';
        $end = $slots->isNotEmpty() ? substr($slots->last()->end_time, 0, 5) : '';
        $durationMinutes = $slots->sum(function ($slot): int {
            $startAt = \Carbon\Carbon::parse($slot->start_time);
            $endAt = \Carbon\Carbon::parse($slot->end_time === '00:00:00' ? '24:00:00' : $slot->end_time);

            return max(0, $startAt->diffInMinutes($endAt));
        });

        return [
            'id' => $booking->id,
            'image_url' => $booking->field?->image_url,
            'field' => $booking->field?->name,
            'sport' => $booking->field?->sport_type,
            'date' => $booking->date?->translatedFormat('d M Y'),
            'start_time' => $start,
            'end_time' => $end,
            'duration_label' => $durationMinutes > 0
                ? floor($durationMinutes / 60) . __('h') . ($durationMinutes % 60 ? ' ' . ($durationMinutes % 60) . __('m') : '')
                : '',
            'location' => $booking->field?->location,
            'organizer' => $booking->user?->name,
            'default_title' => __('Friendly :sport Match', ['sport' => $booking->field?->sport_type ?? __('Sport')]),
            'default_description' => __('Let us play a friendly match. All skill levels are welcome.'),
        ];
    }

    private function eligibleBookings(Request $request)
    {
        return $request->user()
            ->bookings()
            ->with(['field', 'bookedSlots.timeSlot', 'user'])
            ->where('status', 'Confirmed')
            ->doesntHave('game')
            ->orderBy('date')
            ->get();
    }
}

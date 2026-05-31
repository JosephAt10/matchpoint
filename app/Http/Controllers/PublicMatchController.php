<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\Game;
use App\Models\MatchParticipant;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PublicMatchController extends Controller
{
    public function index(): View
    {
        $this->expirePastMatches();

        $user = auth()->user();

        $matches = $this->publicListingQuery()
            ->with([
                'booking.field',
                'booking.bookedSlots.timeSlot',
                'creator',
                'participants',
            ])
            ->latest()
            ->paginate(9);

        return view('matches.index', [
            'matches' => $matches,
        ]);
    }

    public function show(Request $request, Game $match): View
    {
        $this->expirePastMatches();

        $match->load([
            'booking.field.owner',
            'booking.bookedSlots.timeSlot',
            'creator',
            'participants.user',
            'participants.payment',
        ]);

        $activeParticipant = $request->user()
            ? $match->participants
                ->where('user_id', $request->user()->id)
                ->first()
            : null;

        return view('matches.show', [
            'match' => $match,
            'slotRange' => $this->slotRange($match->booking),
            'activeParticipant' => $activeParticipant,
            'isExpired' => $this->matchHasExpired($match),
        ]);
    }

    public function downloadTicket(Request $request, Game $match)
    {
        $match->load([
            'booking.field.owner',
            'booking.bookedSlots.timeSlot',
            'creator',
            'participants.user',
            'participants.payment',
        ]);

        $participant = $match->participants
            ->where('user_id', $request->user()->id)
            ->first();

        abort_unless($participant?->isConfirmed(), 403);

        $filename = 'matchpoint-match-ticket-' . Str::padLeft((string) $match->id, 5, '0') . '.html';

        return response()
            ->view('downloads.match-ticket', [
                'match' => $match,
                'participant' => $participant,
                'slotRange' => $this->slotRange($match->booking),
                'generatedAt' => now(),
            ])
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function create(Request $request): View
    {
        $eligibleBookings = $this->eligibleBookings($request);

        return view('matches.create', [
            'eligibleBookings' => $eligibleBookings,
            'bookingOptions' => $eligibleBookings->map(
                fn (Booking $booking): array => $this->bookingOption($booking)
            )->values(),
            'genderOptions' => ['Open', 'Male', 'Female'],
            'skillLevelOptions' => ['All Levels', 'Beginner', 'Intermediate', 'Advanced'],
            'matchTypeOptions' => ['Friendly', 'Competitive', 'Training'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $eligibleBookingIds = $this->eligibleBookings($request)->pluck('id')->all();

        $validated = $request->validate([
            'booking_id' => ['required', 'integer', Rule::in($eligibleBookingIds)],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:500'],
            'team_a_name' => ['required', 'string', 'max:100'],
            'team_b_name' => ['required', 'string', 'max:100', 'different:team_a_name'],
            'team_a_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'team_b_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'max_per_team' => ['required', 'integer', 'min:1', 'max:50'],
            'participant_fee' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'gender' => ['required', 'string', Rule::in(['Open', 'Male', 'Female'])],
            'skill_level' => ['required', 'string', Rule::in(['All Levels', 'Beginner', 'Intermediate', 'Advanced'])],
            'match_type' => ['required', 'string', Rule::in(['Friendly', 'Competitive', 'Training'])],
        ], [
            'booking_id.in' => __('Please choose one of your confirmed bookings that is not already linked to a public match.'),
            'team_b_name.different' => __('Team B name must be different from Team A name.'),
        ]);

        $teamALogoPath = $request->file('team_a_logo')?->store('match-logos', 'public');
        $teamBLogoPath = $request->file('team_b_logo')?->store('match-logos', 'public');

        try {
            DB::transaction(function () use ($request, $validated, $teamALogoPath, $teamBLogoPath): void {
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
                    'team_a_name' => $validated['team_a_name'],
                    'team_b_name' => $validated['team_b_name'],
                    'team_a_logo' => $teamALogoPath,
                    'team_b_logo' => $teamBLogoPath,
                    'max_per_team' => (int) $validated['max_per_team'],
                    'max_participants' => (int) $validated['max_per_team'] * 2,
                    'filled_slots' => 0,
                    'participant_fee' => $validated['participant_fee'],
                    'gender' => $validated['gender'],
                    'skill_level' => $validated['skill_level'],
                    'match_type' => $validated['match_type'],
                    'status' => 'Open',
                ]);

                MatchParticipant::create([
                    'match_id' => $match->id,
                    'user_id' => $request->user()->id,
                    'team' => 'A',
                    'is_creator' => true,
                    'status' => 'Confirmed',
                    'joined_at' => now(),
                ]);

                $match->increment('filled_slots');

                AuditLog::record('match.created', $match, [
                    'booking_id' => $booking->id,
                    'team_a_name' => $match->team_a_name,
                    'team_b_name' => $match->team_b_name,
                    'max_per_team' => $match->max_per_team,
                    'participant_fee' => $match->participant_fee,
                    'gender' => $match->gender,
                    'skill_level' => $match->skill_level,
                    'match_type' => $match->match_type,
                    'creator_joined_team' => 'A',
                ]);
            });
        } catch (QueryException) {
            foreach (array_filter([$teamALogoPath, $teamBLogoPath]) as $path) {
                Storage::disk('public')->delete($path);
            }

            throw ValidationException::withMessages([
                'booking_id' => __('This booking is already linked to a public match.'),
            ]);
        }

        return redirect()
            ->route('matches.index')
            ->with('status', __('Public match created successfully.'));
    }

    public function join(Request $request, Game $match): RedirectResponse
    {
        $validated = $request->validate([
            'team' => ['required', Rule::in(['A', 'B'])],
            'proof' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $proofPath = $validated['proof']->store('match-fees', 'public');

        try {
            DB::transaction(function () use ($request, $match, $validated, $proofPath): void {
                /** @var Game $lockedMatch */
                $lockedMatch = Game::query()
                    ->with(['booking.field.owner', 'creator'])
                    ->lockForUpdate()
                    ->findOrFail($match->id);

                if (! $lockedMatch->isOpen()) {
                    throw ValidationException::withMessages([
                        'team' => __('This match is no longer open for new players.'),
                    ]);
                }

                if ($this->matchHasExpired($lockedMatch)) {
                    throw ValidationException::withMessages([
                        'team' => __('This match has already started or ended.'),
                    ]);
                }

                if ($lockedMatch->isCreator($request->user()->id)) {
                    throw ValidationException::withMessages([
                        'team' => __('You cannot join a match that you created yourself.'),
                    ]);
                }

                $existingParticipant = MatchParticipant::query()
                    ->where('match_id', $lockedMatch->id)
                    ->where('user_id', $request->user()->id)
                    ->lockForUpdate()
                    ->first();

                if ($existingParticipant) {
                    throw ValidationException::withMessages([
                        'team' => __('You already joined this match.'),
                    ]);
                }

                if ($validated['team'] === 'A' && $lockedMatch->teamACount() >= (int) $lockedMatch->max_per_team) {
                    throw ValidationException::withMessages([
                        'team' => __('This team is already full. Please choose the other side.'),
                    ]);
                }

                if ($validated['team'] === 'B' && $lockedMatch->teamBCount() >= (int) $lockedMatch->max_per_team) {
                    throw ValidationException::withMessages([
                        'team' => __('This team is already full. Please choose the other side.'),
                    ]);
                }

                $participant = MatchParticipant::create([
                    'match_id' => $lockedMatch->id,
                    'user_id' => $request->user()->id,
                    'team' => $validated['team'],
                    'is_creator' => false,
                    'status' => 'Pending',
                    'joined_at' => now(),
                ]);

                $payment = Payment::updateOrCreate(
                    [
                        'match_participant_id' => $participant->id,
                        'type' => 'MatchFee',
                    ],
                    [
                        'payer_id' => $request->user()->id,
                        'amount' => $lockedMatch->participant_fee,
                        'proof' => $proofPath,
                        'status' => 'Pending',
                        'rejection_reason' => null,
                    ],
                );

                AuditLog::record('match.join_requested', $participant, [
                    'match_id' => $lockedMatch->id,
                    'team' => $participant->team,
                    'payment_id' => $payment->id,
                    'amount' => $payment->amount,
                ]);

                $this->notifyMatchFeeUploaded($request->user()->name, $lockedMatch, $participant, $payment);
            });
        } catch (ValidationException $exception) {
            Storage::disk('public')->delete($proofPath);

            throw $exception;
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($proofPath);

            throw $exception;
        }

        return redirect()
            ->route('matches.show', $match)
            ->with('status', __('Your join request and payment proof were submitted successfully. Please wait for verification.'));
    }

    public function myMatches(Request $request): View
    {
        $this->expirePastMatches();

        $user = $request->user();

        $matches = Game::query()
            ->with([
                'booking.field',
                'booking.bookedSlots.timeSlot',
                'creator',
                'participants' => fn ($query) => $query
                    ->where('user_id', $user->id)
                    ->with('payment'),
            ])
            ->where(function (Builder $query) use ($user): void {
                $query->where('creator_id', $user->id)
                    ->orWhereHas('participants', fn (Builder $participantQuery) => $participantQuery->where('user_id', $user->id));
            })
            ->latest()
            ->paginate(9);

        return view('matches.my', [
            'matches' => $matches,
        ]);
    }

    public function confirmParticipant(Request $request, Game $match, MatchParticipant $participant): RedirectResponse
    {
        $this->authorizeMatchCreator($request, $match, $participant);

        abort_unless($participant->payment?->isMatchFee() && $participant->payment->isPending(), 404);

        $participant->payment->update([
            'status' => 'Verified',
            'rejection_reason' => null,
        ]);

        $participant->update([
            'status' => 'Confirmed',
        ]);

        $match->refreshParticipationState();

        Notification::create([
            'user_id' => $participant->user_id,
            'message' => __('Your join request for :title was confirmed on Team :team.', [
                'title' => $match->title,
                'team' => $participant->team,
            ]),
            'type' => 'Match',
            'status' => 'Unread',
            'notifiable_type' => MatchParticipant::class,
            'notifiable_id' => $participant->id,
        ]);

        AuditLog::record('match.join_confirmed', $participant, [
            'payment_id' => $participant->payment->id,
            'match_id' => $match->id,
            'team' => $participant->team,
            'match_status' => $match->status,
        ]);

        return redirect()
            ->route('matches.show', $match)
            ->with('status', __('Participant fee confirmed successfully.'));
    }

    public function rejectParticipant(Request $request, Game $match, MatchParticipant $participant): RedirectResponse
    {
        $this->authorizeMatchCreator($request, $match, $participant);

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        abort_unless($participant->payment?->isMatchFee() && $participant->payment->isPending(), 404);

        $participant->payment->update([
            'status' => 'Rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        $participant->update([
            'status' => 'Cancelled',
        ]);

        $match->refreshParticipationState();

        Notification::create([
            'user_id' => $participant->user_id,
            'message' => __('Your join request for :title was rejected. Please review the organizer note and submit a new payment proof if needed.', [
                'title' => $match->title,
            ]),
            'type' => 'Payment',
            'status' => 'Unread',
            'notifiable_type' => Payment::class,
            'notifiable_id' => $participant->payment->id,
        ]);

        AuditLog::record('match.join_rejected', $participant, [
            'payment_id' => $participant->payment->id,
            'match_id' => $match->id,
            'reason' => $validated['rejection_reason'],
        ]);

        return redirect()
            ->route('matches.show', $match)
            ->with('status', __('Participant fee rejected successfully.'));
    }

    private function notifyMatchFeeUploaded(string $payerName, Game $match, MatchParticipant $participant, Payment $payment): void
    {
        $match->loadMissing('booking.field.owner', 'creator');

        $recipients = User::query()
            ->where('role', 'Admin')
            ->where('status', 'Active')
            ->get()
            ->push($match->booking->field->owner)
            ->push($match->creator)
            ->unique('id');

        foreach ($recipients as $recipient) {
            Notification::create([
                'user_id' => $recipient->id,
                'message' => __(':payer uploaded a match fee proof for :title and requested Team :team.', [
                    'payer' => $payerName,
                    'title' => $match->title,
                    'team' => $participant->team,
                ]),
                'type' => 'Payment',
                'status' => 'Unread',
                'notifiable_type' => Payment::class,
                'notifiable_id' => $payment->id,
            ]);
        }
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
            $startAt = Carbon::parse($slot->start_time);
            $endAt = Carbon::parse($slot->end_time === '00:00:00' ? '24:00:00' : $slot->end_time);

            return max(0, $startAt->diffInMinutes($endAt));
        });

        return [
            'id' => $booking->id,
            'image_url' => $booking->field?->image_url,
            'field' => $booking->field?->name,
            'sport' => $booking->field?->sport_type,
            'sport_label' => __($booking->field?->sport_type ?? 'Sport'),
            'date' => $booking->date?->translatedFormat('d M Y'),
            'start_time' => $start,
            'end_time' => $end,
            'duration_label' => $durationMinutes > 0
                ? floor($durationMinutes / 60) . __('h') . ($durationMinutes % 60 ? ' ' . ($durationMinutes % 60) . __('m') : '')
                : '',
            'location' => $booking->field?->location,
            'organizer' => $booking->user?->name,
        ];
    }

    private function eligibleBookings(Request $request): Collection
    {
        return $request->user()
            ->bookings()
            ->with(['field', 'bookedSlots.timeSlot', 'user'])
            ->where('status', 'Confirmed')
            ->doesntHave('game')
            ->orderBy('date')
            ->get();
    }

    private function slotRange(?Booking $booking): string
    {
        if (! $booking) {
            return __('Time not available');
        }

        $slots = $booking->bookedSlots
            ->pluck('timeSlot')
            ->filter()
            ->sortBy('start_time')
            ->values();

        if ($slots->isEmpty()) {
            return __('Time not available');
        }

        return substr($slots->first()->start_time, 0, 5) . ' - ' . substr($slots->last()->end_time, 0, 5);
    }

    private function publicListingQuery()
    {
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

        return Game::query()
            ->select('matches.*')
            ->joinSub($scheduleQuery, 'booking_schedule', fn ($join) => $join->on('booking_schedule.booking_id', '=', 'matches.booking_id'))
            ->where('matches.status', 'Open')
            ->whereNotNull('matches.title')
            ->whereNotNull('matches.team_a_name')
            ->whereNotNull('matches.team_b_name')
            ->whereNotNull('matches.max_per_team')
            ->where('booking_schedule.booking_ends_at', '>', now());
    }

    private function matchHasExpired(Game $match): bool
    {
        $match->loadMissing('booking.bookedSlots.timeSlot');

        $latestSlot = $match->booking?->bookedSlots
            ?->pluck('timeSlot')
            ->filter()
            ->sortByDesc('end_time')
            ->first();

        if (! $match->booking || ! $latestSlot) {
            return false;
        }

        $endAt = $latestSlot->end_time === '00:00:00'
            ? $match->booking->date->copy()->addDay()->startOfDay()
            : Carbon::parse($match->booking->date->toDateString() . ' ' . $latestSlot->end_time);

        return $endAt->lessThanOrEqualTo(now());
    }

    private function authorizeMatchCreator(Request $request, Game $match, MatchParticipant $participant): void
    {
        abort_unless($match->isCreator($request->user()->id), 403);
        abort_unless($participant->match_id === $match->id, 404);

        $participant->loadMissing('payment');
    }

    private function expirePastMatches(): void
    {
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

        $expiredMatchIds = Game::query()
            ->select('matches.id')
            ->joinSub($scheduleQuery, 'booking_schedule', fn ($join) => $join->on('booking_schedule.booking_id', '=', 'matches.booking_id'))
            ->whereIn('matches.status', ['Open', 'Full'])
            ->where('booking_schedule.booking_ends_at', '<=', now())
            ->pluck('matches.id');

        if ($expiredMatchIds->isNotEmpty()) {
            Game::query()
                ->whereIn('id', $expiredMatchIds)
                ->update(['status' => 'Completed']);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\BookedSlot;
use App\Models\Booking;
use App\Models\Field;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $bookings = $user->bookings()
            ->with(['field.owner', 'bookedSlots.timeSlot', 'payment'])
            ->latest('date')
            ->get()
            ->map(function (Booking $booking): array {
                $orderedSlots = $booking->bookedSlots
                    ->pluck('timeSlot')
                    ->filter()
                    ->sortBy('start_time')
                    ->values();

                $slotRange = $orderedSlots->isNotEmpty()
                    ? substr($orderedSlots->first()->start_time, 0, 5) . ' - ' . substr($orderedSlots->last()->end_time, 0, 5)
                    : __('Time not available');

                return [
                    'id' => $booking->id,
                    'field_name' => $booking->field?->name,
                    'location' => $booking->field?->location,
                    'image_url' => $booking->field?->image_url,
                    'date_label' => $booking->date?->translatedFormat('j M Y'),
                    'time_label' => $slotRange,
                    'status_label' => $this->bookingStatusLabel($booking),
                    'status_tone' => $this->bookingStatusTone($booking),
                    'amount' => 'Rp ' . number_format((float) ($booking->field?->price_per_slot ?? 0) * max(1, $orderedSlots->count()), 0, ',', '.'),
                    'payment_status' => $booking->payment?->status,
                    'payment_deadline' => $booking->payment_deadline?->translatedFormat('j M Y, H:i'),
                    'show_url' => route('bookings.show', $booking),
                ];
            });

        return view('user.bookings', [
            'page' => [
                'user' => [
                    'name' => $user->name,
                    'first_name' => str($user->name)->before(' ')->toString(),
                    'initials' => str($user->name)->explode(' ')->filter()->take(2)->map(fn (string $part) => strtoupper(substr($part, 0, 1)))->implode(''),
                    'unread_notifications' => Notification::query()->forUser($user->id)->unread()->count(),
                ],
                'bookings' => $bookings,
                'pending_count' => $bookings->where('status_label', 'Pending Payment')->count() + $bookings->where('status_label', 'Pending')->count(),
                'confirmed_count' => $bookings->where('status_label', 'Confirmed')->count(),
                'completed_count' => $bookings->where('status_label', 'Completed')->count(),
            ],
        ]);
    }
    public function confirm(Request $request, Field $field): View
    {
        abort_unless(
            $field->is_approved && $field->owner()->where('status', 'Active')->exists(),
            404,
        );

        [$bookingDate, $slotIds] = $this->validatedBookingSelection($request);
        $slots = $this->resolveSlots($field, $bookingDate, $slotIds);

        return view('bookings.confirm', [
            'field' => $field->load('owner'),
            'bookingDate' => $bookingDate,
            'slotIds' => $slotIds->all(),
            'slotRange' => $this->slotRangeFromTimeSlots($slots),
            'slotCount' => $slots->count(),
            'totalPrice' => (float) $field->price_per_slot * $slots->count(),
            'downPaymentAmount' => round((float) $field->price_per_slot * $slots->count() * 0.5, 2),
        ]);
    }

    public function store(Request $request, Field $field): RedirectResponse
    {
        abort_unless(
            $field->is_approved && $field->owner()->where('status', 'Active')->exists(),
            404,
        );

        [$bookingDate, $slotIds] = $this->validatedBookingSelection($request);
        $validated = $request->validate([
            'proof' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $path = $validated['proof']->store('payment-proofs', 'public');

        try {
            $booking = DB::transaction(function () use ($field, $bookingDate, $slotIds, $request, $path): Booking {
                $slots = $this->resolveSlots($field, $bookingDate, $slotIds, true);

                $booking = Booking::create([
                    'user_id' => $request->user()->id,
                    'field_id' => $field->id,
                    'timeslot_id' => $slots->first()->id,
                    'date' => $bookingDate,
                    'status' => 'Pending',
                    'payment_deadline' => now()->addHours(48),
                ]);

                foreach ($slots as $slot) {
                    BookedSlot::create([
                        'timeslot_id' => $slot->id,
                        'booking_id' => $booking->id,
                        'date' => $bookingDate,
                    ]);
                }

                AuditLog::record('booking.created', $booking, [
                    'field_id' => $field->id,
                    'slot_ids' => $slots->pluck('id')->all(),
                    'date' => $bookingDate->toDateString(),
                ]);

                $payment = Payment::create([
                    'booking_id' => $booking->id,
                    'type' => 'BookingDP',
                    'payer_id' => $request->user()->id,
                    'amount' => $booking->downPaymentAmount(),
                    'proof' => $path,
                    'status' => 'Pending',
                    'rejection_reason' => null,
                ]);

                AuditLog::record('payment.proof_uploaded', $booking, [
                    'proof' => $path,
                    'amount' => $booking->downPaymentAmount(),
                ]);

                $this->notifyBookingPaymentUploaded($request->user()->name, $booking, $payment);

                return $booking;
            });
        } catch (ValidationException|QueryException $exception) {
            Storage::disk('public')->delete($path);

            if ($exception instanceof ValidationException) {
                throw $exception;
            }

            throw ValidationException::withMessages([
                'slot_ids' => __('One or more selected slots were just booked. Please choose another time.'),
            ]);
        }

        return redirect()->route('bookings.show', $booking)
            ->with('status', __('Booking created and payment proof uploaded. Please wait for verification.'));
    }

    public function show(Booking $booking): View
    {
        $this->authorizeBookingOwner($booking);

        $booking->load(['field.owner', 'bookedSlots.timeSlot', 'payment']);

        return view('bookings.show', [
            'booking' => $booking,
            'slotRange' => $this->slotRange($booking),
        ]);
    }

    public function reschedule(Request $request, Booking $booking): View
    {
        $this->authorizeBookingOwner($booking);

        $booking->load(['field.owner', 'bookedSlots.timeSlot', 'payment']);
        abort_unless($booking->canBeRescheduled(), 403);

        $minimumDate = now()->addDay()->startOfDay();
        $selectedDate = $request->date('date')
            ?? ($booking->date->greaterThan($minimumDate) ? $booking->date->copy() : $minimumDate);
        $selectedDate = $selectedDate->startOfDay();
        $selectedDay = $selectedDate->format('l');

        $slots = $booking->field->timeSlots()
            ->where('day_of_week', $selectedDay)
            ->orderBy('start_time')
            ->get();

        $previewSlots = $slots
            ->values()
            ->map(function (TimeSlot $slot, int $index) use ($selectedDate): array {
                $available = $slot->isAvailableOn($selectedDate);

                return [
                    'id' => $slot->id,
                    'index' => $index,
                    'label' => substr($slot->start_time, 0, 5) . ' - ' . substr($slot->end_time, 0, 5),
                    'start' => substr($slot->start_time, 0, 5),
                    'end' => substr($slot->end_time, 0, 5),
                    'available' => $available,
                ];
            });

        return view('bookings.reschedule', [
            'booking' => $booking,
            'currentSlotRange' => $this->slotRange($booking),
            'selectedDate' => $selectedDate,
            'previewSlots' => $previewSlots,
        ]);
    }

    public function updateReschedule(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeBookingOwner($booking);

        $validated = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:tomorrow'],
            'slot_ids' => ['required', 'array', 'min:1'],
            'slot_ids.*' => ['integer', 'distinct', 'exists:time_slots,id'],
        ], [
            'date.after_or_equal' => __('Please choose a reschedule date from tomorrow onward.'),
        ]);

        $newDate = Carbon::parse($validated['date'])->startOfDay();
        $slotIds = collect($validated['slot_ids'])->map(fn ($id) => (int) $id)->sort()->values();

        DB::transaction(function () use ($booking, $newDate, $slotIds, $request): void {
            $booking = Booking::query()
                ->with(['user', 'field.owner', 'bookedSlots.timeSlot'])
                ->lockForUpdate()
                ->findOrFail($booking->id);

            $this->authorizeBookingOwner($booking);
            abort_unless($booking->canBeRescheduled(), 403);

            $oldDate = $booking->date->copy();
            $oldSlotIds = $booking->bookedSlots->pluck('timeslot_id')->sort()->values();

            if ($oldDate->isSameDay($newDate) && $oldSlotIds->values()->all() === $slotIds->all()) {
                throw ValidationException::withMessages([
                    'slot_ids' => __('Please choose a different available time slot.'),
                ]);
            }

            $newSlots = $this->resolveSlots($booking->field, $newDate, $slotIds, true);
            $oldSlotRange = $this->slotRange($booking);
            $newSlotRange = $this->slotRangeFromTimeSlots($newSlots);

            $booking->bookedSlots()->delete();

            foreach ($newSlots as $slot) {
                BookedSlot::create([
                    'timeslot_id' => $slot->id,
                    'booking_id' => $booking->id,
                    'date' => $newDate,
                ]);
            }

            $booking->update([
                'timeslot_id' => $newSlots->first()->id,
                'date' => $newDate,
                'version' => $booking->version + 1,
            ]);

            Notification::create([
                'user_id' => $booking->user_id,
                'message' => __('Your booking for :field was rescheduled to :date, :time.', [
                    'field' => $booking->field->name,
                    'date' => $newDate->translatedFormat('j M Y'),
                    'time' => $newSlotRange,
                ]),
                'type' => 'Booking',
                'status' => 'Unread',
                'notifiable_type' => Booking::class,
                'notifiable_id' => $booking->id,
            ]);

            if ($booking->field?->owner_id) {
                Notification::create([
                    'user_id' => $booking->field->owner_id,
                    'message' => __(':user rescheduled their booking for :field from :old_date, :old_time to :new_date, :new_time.', [
                        'user' => $booking->user?->name ?? __('A user'),
                        'field' => $booking->field->name,
                        'old_date' => $oldDate->translatedFormat('j M Y'),
                        'old_time' => $oldSlotRange,
                        'new_date' => $newDate->translatedFormat('j M Y'),
                        'new_time' => $newSlotRange,
                    ]),
                    'type' => 'Booking',
                    'status' => 'Unread',
                    'notifiable_type' => Booking::class,
                    'notifiable_id' => $booking->id,
                ]);
            }

            AuditLog::record('booking.rescheduled', $booking, [
                'old_date' => $oldDate->toDateString(),
                'old_slot_ids' => $oldSlotIds->all(),
                'old_slot_range' => $oldSlotRange,
                'new_date' => $newDate->toDateString(),
                'new_slot_ids' => $slotIds->all(),
                'new_slot_range' => $newSlotRange,
                'released_old_slots' => true,
            ], $request->user()->id);
        });

        return redirect()->route('bookings.show', $booking)
            ->with('status', __('Booking rescheduled successfully.'));
    }

    public function downloadEvidence(Request $request, Booking $booking)
    {
        $this->authorizeBookingOwner($booking);

        abort_unless($booking->isConfirmed(), 403);

        $booking->load(['user', 'field.owner', 'bookedSlots.timeSlot', 'payment']);
        $filename = 'matchpoint-booking-' . Str::padLeft((string) $booking->id, 5, '0') . '.html';

        return response()
            ->view('downloads.booking-evidence', [
                'booking' => $booking,
                'slotRange' => $this->slotRange($booking),
                'generatedAt' => now(),
            ])
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeBookingOwner($booking);

        DB::transaction(function () use ($booking): void {
            $booking = Booking::query()
                ->with(['user', 'field.owner', 'payment', 'bookedSlots'])
                ->lockForUpdate()
                ->findOrFail($booking->id);

            $this->authorizeBookingOwner($booking);
            abort_unless($booking->isPending(), 403);

            $reason = __('Cancelled by user before field owner confirmation. Down payment refunds are handled directly between the user and Field Owner outside MatchPoint.');

            if ($booking->payment && ! $booking->payment->isVerified()) {
                $booking->payment->update([
                    'status' => 'Rejected',
                    'rejection_reason' => $reason,
                ]);
            }

            $releasedSlotIds = $booking->bookedSlots->pluck('timeslot_id')->all();

            $booking->update([
                'status' => 'Cancelled',
                'version' => $booking->version + 1,
            ]);

            $booking->bookedSlots()->delete();

            Notification::create([
                'user_id' => $booking->user_id,
                'message' => __('Your booking for :field was cancelled. The time slot has been released. Refunds must be handled directly with the Field Owner outside MatchPoint.', [
                    'field' => $booking->field->name,
                ]),
                'type' => 'Booking',
                'status' => 'Unread',
                'notifiable_type' => Booking::class,
                'notifiable_id' => $booking->id,
            ]);

            if ($booking->field?->owner_id) {
                Notification::create([
                    'user_id' => $booking->field->owner_id,
                    'message' => __(':user cancelled their pending booking for :field. The time slot has been released. Any down payment refund is handled directly outside MatchPoint.', [
                        'user' => $booking->user?->name ?? __('A user'),
                        'field' => $booking->field->name,
                    ]),
                    'type' => 'Booking',
                    'status' => 'Unread',
                    'notifiable_type' => Booking::class,
                    'notifiable_id' => $booking->id,
                ]);
            }

            AuditLog::record('booking.cancelled_by_user', $booking, [
                'released_slots' => true,
                'slot_ids' => $releasedSlotIds,
                'payment_status' => $booking->payment?->status,
                'refund_handling' => 'outside_system',
            ]);
        });

        return redirect()->route('bookings.show', $booking)
            ->with('status', __('Booking cancelled successfully. The slot is available again. Refunds must be handled directly with the Field Owner outside MatchPoint.'));
    }

    public function uploadProof(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeBookingOwner($booking);

        abort_unless($booking->isPending(), 403);

        if ($booking->payment?->proof && ! $booking->payment->isRejected()) {
            return redirect()->route('bookings.show', $booking)
                ->with('status', __('Payment proof has already been uploaded and is waiting for review.'));
        }

        $validated = $request->validate([
            'proof' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $booking->load(['field', 'bookedSlots']);

        if ($booking->payment?->proof) {
            Storage::disk('public')->delete($booking->payment->proof);
        }

        $path = $validated['proof']->store('payment-proofs', 'public');

        $payment = Payment::updateOrCreate(
            [
                'booking_id' => $booking->id,
                'type' => 'BookingDP',
            ],
            [
                'payer_id' => $request->user()->id,
                'amount' => $booking->downPaymentAmount(),
                'proof' => $path,
                'status' => 'Pending',
                'rejection_reason' => null,
            ],
        );

        AuditLog::record('payment.proof_uploaded', $booking, [
            'proof' => $path,
            'amount' => $booking->downPaymentAmount(),
        ]);

        $this->notifyBookingPaymentUploaded($request->user()->name, $booking, $payment);

        return redirect()->route('bookings.show', $booking)
            ->with('status', __('Payment proof uploaded. Please wait for verification.'));
    }

    private function notifyBookingPaymentUploaded(string $payerName, Booking $booking, Payment $payment): void
    {
        $recipients = User::query()
            ->where('role', 'Admin')
            ->where('status', 'Active')
            ->get()
            ->push($booking->field->owner)
            ->unique('id');

        foreach ($recipients as $recipient) {
            Notification::create([
                'user_id' => $recipient->id,
                'message' => __(':payer uploaded booking payment proof for :field.', ['payer' => $payerName, 'field' => $booking->field->name]),
                'type' => 'Payment',
                'status' => 'Unread',
                'notifiable_type' => Payment::class,
                'notifiable_id' => $payment->id,
            ]);
        }
    }

    private function authorizeBookingOwner(Booking $booking): void
    {
        abort_unless($booking->user_id === Auth::id(), 403);
    }

    private function validatedBookingSelection(Request $request): array
    {
        $validated = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:tomorrow'],
            'slot_ids' => ['required', 'array', 'min:1'],
            'slot_ids.*' => ['integer', 'distinct', 'exists:time_slots,id'],
        ], [
            'date.after_or_equal' => __('Please choose a booking date from tomorrow onward.'),
        ]);

        $bookingDate = Carbon::parse($validated['date'])->startOfDay();
        $slotIds = collect($validated['slot_ids'])->map(fn ($id) => (int) $id)->sort()->values();

        return [$bookingDate, $slotIds];
    }

    private function resolveSlots(Field $field, Carbon $bookingDate, Collection $slotIds, bool $lockForUpdate = false): Collection
    {
        $selectedDay = $bookingDate->format('l');
        $query = TimeSlot::query()
            ->where('field_id', $field->id)
            ->where('day_of_week', $selectedDay)
            ->whereIn('id', $slotIds)
            ->orderBy('start_time');

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        $slots = $query->get();

        if ($slots->count() !== $slotIds->count()) {
            throw ValidationException::withMessages([
                'slot_ids' => __('One or more selected slots are not valid for this field and date.'),
            ]);
        }

        if ($slots->contains(fn (TimeSlot $slot) => ! $slot->is_available_base)) {
            throw ValidationException::withMessages([
                'slot_ids' => __('One or more selected slots are not currently offered by this field.'),
            ]);
        }

        $orderedDaySlots = TimeSlot::query()
            ->where('field_id', $field->id)
            ->where('day_of_week', $selectedDay)
            ->orderBy('start_time')
            ->pluck('id')
            ->values();

        $selectedPositions = $slots
            ->map(fn (TimeSlot $slot) => $orderedDaySlots->search($slot->id))
            ->sort()
            ->values();

        $isContinuous = $selectedPositions->every(
            fn ($position, $index) => $index === 0 || $position === $selectedPositions[$index - 1] + 1
        );

        if (! $isContinuous) {
            throw ValidationException::withMessages([
                'slot_ids' => __('Please select continuous time slots for one booking.'),
            ]);
        }

        $alreadyBooked = BookedSlot::query()
            ->whereDate('date', $bookingDate)
            ->whereIn('timeslot_id', $slots->pluck('id'))
            ->exists();

        if ($alreadyBooked) {
            throw ValidationException::withMessages([
                'slot_ids' => __('One or more selected slots were just booked. Please choose another time.'),
            ]);
        }

        return $slots;
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

    private function slotRange(Booking $booking): string
    {
        return $this->slotRangeFromTimeSlots(
            $booking->bookedSlots
                ->pluck('timeSlot')
                ->filter()
                ->sortBy('start_time')
                ->values()
        );
    }

    private function slotRangeFromTimeSlots(Collection $slots): string
    {
        if ($slots->isEmpty()) {
            return __('No available slots');
        }

        return substr($slots->first()->start_time, 0, 5) . ' - ' . substr($slots->last()->end_time, 0, 5);
    }
}


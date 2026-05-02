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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
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
                    'payment_deadline' => now()->addDay(),
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
                'slot_ids' => 'One or more selected slots were just booked. Please choose another time.',
            ]);
        }

        return redirect()->route('bookings.show', $booking)
            ->with('status', 'Booking created and payment proof uploaded. Please wait for verification.');
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

    public function uploadProof(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeBookingOwner($booking);

        abort_unless($booking->isPending(), 403);

        if ($booking->payment?->proof && ! $booking->payment->isRejected()) {
            return redirect()->route('bookings.show', $booking)
                ->with('status', 'Payment proof has already been uploaded and is waiting for review.');
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
            ->with('status', 'Payment proof uploaded. Please wait for verification.');
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
                'message' => "{$payerName} uploaded booking payment proof for {$booking->field->name}.",
                'type' => 'Payment',
                'status' => 'Unread',
                'notifiable_type' => Payment::class,
                'notifiable_id' => $payment->id,
            ]);
        }
    }

    private function authorizeBookingOwner(Booking $booking): void
    {
        abort_unless($booking->user_id === auth()->id(), 403);
    }

    private function validatedBookingSelection(Request $request): array
    {
        $validated = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:tomorrow'],
            'slot_ids' => ['required', 'array', 'min:1'],
            'slot_ids.*' => ['integer', 'distinct', 'exists:time_slots,id'],
        ], [
            'date.after_or_equal' => 'Please choose a booking date from tomorrow onward.',
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
                'slot_ids' => 'One or more selected slots are not valid for this field and date.',
            ]);
        }

        if ($slots->contains(fn (TimeSlot $slot) => ! $slot->is_available_base)) {
            throw ValidationException::withMessages([
                'slot_ids' => 'One or more selected slots are not currently offered by this field.',
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
                'slot_ids' => 'Please select continuous time slots for one booking.',
            ]);
        }

        $alreadyBooked = BookedSlot::query()
            ->whereDate('date', $bookingDate)
            ->whereIn('timeslot_id', $slots->pluck('id'))
            ->exists();

        if ($alreadyBooked) {
            throw ValidationException::withMessages([
                'slot_ids' => 'One or more selected slots were just booked. Please choose another time.',
            ]);
        }

        return $slots;
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
            return 'No slots';
        }

        return substr($slots->first()->start_time, 0, 5) . ' - ' . substr($slots->last()->end_time, 0, 5);
    }
}

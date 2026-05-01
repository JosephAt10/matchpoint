<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class FieldBrowserController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $activeApprovedFields = Field::query()
            ->approved()
            ->whereHas('owner', fn ($ownerQuery) => $ownerQuery->where('status', 'Active'));

        $query = (clone $activeApprovedFields)
            ->with(['owner', 'timeSlots']);

        if ($request->filled('location')) {
            $query->byLocation($request->string('location')->toString());
        }

        if ($request->filled('sport_type')) {
            $query->bySport($request->string('sport_type')->toString());
        }

        if ($request->filled('type')) {
            $query->byType($request->string('type')->toString());
        }

        $fields = $query->latest()->paginate(6)->withQueryString();
        $favoriteIds = $request->user()
            ? $request->user()->favoriteFields()->pluck('fields.id')
            : collect();

        if ($request->boolean('load_more')) {
            return response()->json([
                'html' => view('fields.partials.cards', [
                    'fields' => $fields,
                    'favoriteIds' => $favoriteIds,
                ])->render(),
                'hasMorePages' => $fields->hasMorePages(),
                'nextPageUrl' => $fields->nextPageUrl(),
            ]);
        }

        return view('fields.index', [
            'fields'    => $fields,
            'favoriteIds' => $favoriteIds,
            'filters'   => $request->only(['location', 'sport_type', 'type']),
            'sports'    => (clone $activeApprovedFields)->distinct()->orderBy('sport_type')->pluck('sport_type'),
            'locations' => (clone $activeApprovedFields)->distinct()->orderBy('location')->pluck('location'),
        ]);
    }

    public function show(Field $field, Request $request): View
    {
        abort_unless(
            $field->is_approved && $field->owner()->where('status', 'Active')->exists(),
            404,
        );

        $selectedDate = $request->date('date') ?? Carbon::tomorrow();
        $selectedDay = $selectedDate->format('l');

        $field->load([
            'owner',
            'timeSlots' => fn ($query) => $query
                ->where('day_of_week', $selectedDay)
                ->orderBy('start_time'),
        ]);

        $allTimeSlots = $field->timeSlots()
            ->orderByRaw("FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
            ->orderBy('start_time')
            ->get();

        $previewSlots = $field->timeSlots
            ->values()
            ->map(function ($slot) use ($selectedDate) {
                $available = $slot->isAvailableOn($selectedDate);

                return [
                    'id' => $slot->id,
                    'label' => substr($slot->start_time, 0, 5) . ' - ' . substr($slot->end_time, 0, 5),
                    'start' => substr($slot->start_time, 0, 5),
                    'end' => substr($slot->end_time, 0, 5),
                    'available' => $available,
                    'meta' => $available ? 'Available' : 'Booked',
                ];
            });

        $relatedFields = Field::query()
            ->approved()
            ->whereHas('owner', fn ($ownerQuery) => $ownerQuery->where('status', 'Active'))
            ->whereKeyNot($field->id)
            ->where('sport_type', $field->sport_type)
            ->with(['owner', 'timeSlots'])
            ->latest()
            ->take(3)
            ->get();

        $favoriteIds = $request->user()
            ? $request->user()->favoriteFields()->pluck('fields.id')
            : collect();

        return view('fields.show', [
            'field' => $field,
            'previewSlots' => $previewSlots,
            'allTimeSlots' => $allTimeSlots,
            'selectedDate' => $selectedDate,
            'relatedFields' => $relatedFields,
            'favoriteIds' => $favoriteIds,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

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

        if ($request->boolean('load_more')) {
            return response()->json([
                'html' => view('fields.partials.cards', [
                    'fields' => $fields,
                ])->render(),
                'hasMorePages' => $fields->hasMorePages(),
                'nextPageUrl' => $fields->nextPageUrl(),
            ]);
        }

        return view('fields.index', [
            'fields'    => $fields,
            'filters'   => $request->only(['location', 'sport_type', 'type']),
            'sports'    => (clone $activeApprovedFields)->distinct()->orderBy('sport_type')->pluck('sport_type'),
            'locations' => (clone $activeApprovedFields)->distinct()->orderBy('location')->pluck('location'),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class FieldBrowserController extends Controller
{
    public function index(Request $request): View
    {
        $query = Field::query()
            ->approved()
            ->with(['owner', 'timeSlots'])
            ->whereHas('owner', fn ($ownerQuery) => $ownerQuery->where('status', 'Active'));

        if ($request->filled('location')) {
            $query->byLocation($request->string('location')->toString());
        }

        if ($request->filled('sport_type')) {
            $query->bySport($request->string('sport_type')->toString());
        }

        if ($request->filled('type')) {
            $query->byType($request->string('type')->toString());
        }

        return view('fields.index', [
            'fields'    => $query->latest()->paginate(9)->withQueryString(),
            'filters'   => $request->only(['location', 'sport_type', 'type']),
            'sports'    => Field::approved()->distinct()->orderBy('sport_type')->pluck('sport_type'),
            'locations' => Field::approved()->distinct()->orderBy('location')->pluck('location'),
        ]);
    }
}

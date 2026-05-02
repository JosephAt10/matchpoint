<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->to('/admin');
        }

        if ($user->isFieldOwner()) {
            return redirect()->to('/owner');
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

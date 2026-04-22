<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Field;

class HomeController extends Controller
{
    public function index()
    {
        // Base query for open matches
        $query = Game::with('booking.field')
            ->where('status', 'Open')
            ->latest();

        return view('home', [
            // One featured match (top section)
            'featuredMatch' => (clone $query)->first(),

            // 3 latest matches (cards section)
            'matches'       => (clone $query)->take(3)->get(),

            // 4 approved fields (fields section)
            'fields'        => Field::where('is_approved', true)
                ->latest()
                ->take(4)
                ->get(),
        ]);
    }
}

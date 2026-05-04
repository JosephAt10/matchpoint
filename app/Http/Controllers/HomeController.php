<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Field;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $featuredMatch = null;
        $matches = collect();
        $fields = collect();

        if (Schema::hasTable('matches')) {
            $query = Game::with('booking.field')
                ->where('status', 'Open')
                ->latest();

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

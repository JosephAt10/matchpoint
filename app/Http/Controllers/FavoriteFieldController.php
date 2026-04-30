<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteFieldController extends Controller
{
    public function index(Request $request): View
    {
        $fields = $request->user()
            ->favoriteFields()
            ->with(['owner', 'timeSlots'])
            ->latest('favorite_fields.created_at')
            ->get();

        return view('fields.favorites', [
            'fields' => $fields,
            'favoriteIds' => $fields->pluck('id'),
        ]);
    }

    public function toggle(Request $request, Field $field): RedirectResponse
    {
        $user = $request->user();
        $isFavorited = $user->favoriteFields()
            ->where('field_id', $field->id)
            ->exists();

        if ($isFavorited) {
            $user->favoriteFields()->detach($field->id);
            $status = "{$field->name} removed from favorites.";
        } else {
            $user->favoriteFields()->syncWithoutDetaching([$field->id]);
            $status = "{$field->name} added to favorites.";
        }

        $redirectTo = $request->string('redirect_to')->toString();

        if ($redirectTo !== '' && str_starts_with($redirectTo, url('/'))) {
            return redirect()->to($redirectTo)->with('status', $status);
        }

        return back()->with('status', $status);
    }
}

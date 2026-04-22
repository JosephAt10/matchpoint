<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FieldOwnerApprovalController extends Controller
{
    public function index(): View
    {
        return view('admin.field-owners.index', [
            'pendingOwners' => User::where('role', 'FieldOwner')
                ->where('status', 'PendingApproval')
                ->latest()
                ->get(),
            'approvedOwners' => User::where('role', 'FieldOwner')
                ->where('status', 'Active')
                ->latest()
                ->get(),
        ]);
    }

    public function approve(User $user): RedirectResponse
    {
        abort_unless($user->isFieldOwner(), 404);

        $user->update(['status' => 'Active']);

        return back()->with('status', "{$user->name} is now approved as a Field Owner.");
    }

    public function deactivate(User $user): RedirectResponse
    {
        abort_unless($user->isFieldOwner(), 404);

        $user->update(['status' => 'Deactivated']);

        return back()->with('status', "{$user->name} has been deactivated.");
    }
}

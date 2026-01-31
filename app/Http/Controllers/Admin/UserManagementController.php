<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $this->authorizeAccess();
        $users = User::orderBy('name')->paginate(50);
        return view('admin.users.index', compact('users'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAccess();

        $request->validate([
            'role' => 'required|string|in:super_admin,admin,editor,creator,viewer'
        ]);

        $user->update(['role' => $request->input('role')]);

        return back()->with('success', 'User role updated');
    }

    protected function authorizeAccess()
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403);
        }
    }
}

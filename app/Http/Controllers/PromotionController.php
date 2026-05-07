<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PromotionController extends Controller
{
    public function showPromoteForm($id)
    {
        $user = User::findOrFail($id);

        return view('hr.promote', compact('user'));
    }

    public function promote(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:employee,supervisor,hr',
        ]);

        $user = User::findOrFail($id);

        $oldRole = $user->role;

        $user->update([
            'role' => $request->role,
        ]);

        // optional email
        Mail::raw(
            "Hello {$user->name}, your role has been updated from {$oldRole} to {$request->role}.",
            function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Role Promotion Notification');
            }
        );

        return redirect()
            ->route('hr.employees.index')
            ->with('success', 'User promoted successfully.');
    }

    public function index()
    {
        $promotions = Promotion::with(['user', 'promoter'])->latest()->get();
        return view('hr.promotions.index', compact('promotions'));
    }
}

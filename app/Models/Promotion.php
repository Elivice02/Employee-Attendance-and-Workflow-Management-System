<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Mail\PromotionNotificationMail;
use Illuminate\Support\Facades\Mail;

class Promotion extends Model
{
    protected $fillable = [
        'user_id',
        'old_role',
        'new_role',
        'promoted_by',
        'reason'
    ];

    private function isValidRoleTransition($from, $to)
    {
        $allowed = [
            'employee' => ['supervisor'],
            'supervisor' => ['hr'],
            'hr' => []
        ];

        return in_array($to, $allowed[$from] ?? []);
    }

    public function promote(Request $request, User $employee)
    {
        $request->validate([
            'role' => 'required|string',
            'reason' => 'nullable|string'
        ]);

        $oldRole = $employee->role;
        $newRole = $request->role;

        // safety rule
        if (!$this->isValidRoleTransition($oldRole, $newRole)) {
            return back()->with('error', 'Invalid role transition');
        }

        // update user
        $employee->update([
            'role' => $newRole
        ]);

        Mail::to($employee->email)->send(
            new PromotionNotificationMail($employee, $newRole)
        );

        // log promotion
        Promotion::create([
            'user_id' => $employee->id,
            'old_role' => $oldRole,
            'new_role' => $newRole,
            'promoted_by' => auth()->id(),
            'reason' => $request->reason
        ]);

        return redirect()
            ->route('hr.employees.index')
            ->with('success', 'Employee promoted successfully');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function promoter()
    {
        return $this->belongsTo(User::class, 'promoted_by');
    }
}
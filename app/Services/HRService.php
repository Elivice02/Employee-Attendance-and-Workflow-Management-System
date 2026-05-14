<?php

namespace App\Services;

use App\Models\User;
use App\Events\HRCreated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class HRService
{
    public function create(array $data, $file = null, int $adminId)
    {
        return DB::transaction(function () use ($data, $file, $adminId) {

            $tempPassword = Str::random(10);

            $path = $file ? $file->store('profiles', 'public') : null;

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'gender' => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'phone' => $data['phone'] ?? null,
                'profile_picture' => $path,
                'password' => Hash::make($tempPassword),
                'role' => 'hr',
                'created_by' => $adminId,
                'must_change_password' => true,
            ]);

            // 🔥 ONLY responsibility: signal system
            HRCreated::dispatch($user, $tempPassword);

            return $user;
        });
    }

    public function update(User $hr, array $data, $file = null)
    {
        return DB::transaction(function () use ($hr, $data, $file) {

            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'gender' => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'phone' => $data['phone'] ?? null,
            ];

            if ($file) {
                // Delete old profile picture if exists
                if ($hr->profile_picture) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($hr->profile_picture);
                }
                $updateData['profile_picture'] = $file->store('profiles', 'public');
            }

            $hr->update($updateData);

            return $hr;
        });
    }

    public function delete(User $hr)
    {
        return DB::transaction(function () use ($hr) {
            // Delete profile picture if exists
            if ($hr->profile_picture) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($hr->profile_picture);
            }

            $hr->delete();
        });
    }
}

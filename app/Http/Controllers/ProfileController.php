<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Update profil user.
     * - nama    → tabel users.name
     * - sisanya → tabel profiles (relasi hasOne)
     */
    public function update(Request $request)
    {
        if (! $request->filled('nama') && $request->filled('name')) {
            $request->merge(['nama' => $request->input('name')]);
        }

        $validated = $request->validate([
            'nama'   => 'required|string|min:2|max:50',
            'usia'   => 'required|integer|min:5|max:120',
            'gender' => 'required|in:pria,wanita,lainnya',
            'goldar' => 'nullable|string|max:3',
            'tinggi' => 'nullable|integer|min:30|max:300',
            'berat'  => 'nullable|integer|min:2|max:500',
            'bio'    => 'nullable|string|max:200',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = $request->user();

        // Update nama di tabel users
        $user->forceFill(['name' => trim($validated['nama'])])->save();

        // Data untuk tabel profiles
        $profileData = [
            'nama'   => trim($validated['nama']),
            'usia'   => $validated['usia']   ?? null,
            'gender' => $validated['gender'] ?? null,
            'goldar' => $validated['goldar'] ?? null,
            'tinggi' => $validated['tinggi'] ?? null,
            'berat'  => $validated['berat']  ?? null,
            'bio'    => $validated['bio']    ?? null,
            'profile_completed_at' => now(),
        ];

        // Handle upload avatar
        if ($request->hasFile('avatar')) {
            $oldProfile = $user->profile;
            if ($oldProfile && $oldProfile->avatar && Storage::disk('public')->exists($oldProfile->avatar)) {
                Storage::disk('public')->delete($oldProfile->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $profileData['avatar'] = $path;
        }

        // Update atau create profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        $profile = $user->fresh()->profile;

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil disimpan.',
            'profile' => [
                'nama'   => $user->name,
                'email'  => $user->email,
                'usia'   => $profile->usia   ?? '',
                'gender' => $profile->gender ?? '',
                'goldar' => $profile->goldar ?? '',
                'tinggi' => $profile->tinggi ?? '',
                'berat'  => $profile->berat  ?? '',
                'bio'    => $profile->bio    ?? '',
                'avatar' => $profile && $profile->avatar
                    ? asset('storage/' . $profile->avatar)
                    : asset('images/avatar.png'),
            ],
        ]);
    }
}
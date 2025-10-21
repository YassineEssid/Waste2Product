<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile
     */
    public function show()
    {
        $user = Auth::user();
        
        // Load relationships based on role
        $user->load(['wasteItems', 'repairRequests', 'transformations', 'eventRegistrations']);
        
        // Calculate statistics
        $stats = [
            'waste_items' => $user->wasteItems()->count(),
            'repair_requests' => $user->repairRequests()->count(),
            'transformations' => $user->transformations()->count(),
            'events_attended' => $user->eventRegistrations()->count(),
            'member_since' => $user->created_at->diffForHumans(),
            'account_age_days' => $user->created_at->diffInDays(now()),
        ];
        
        // Role-specific statistics
        if ($user->role === 'repairer') {
            $stats['repairs_completed'] = $user->repairRequests()
                ->where('status', 'completed')
                ->count();
            $stats['repairs_pending'] = $user->repairRequests()
                ->where('status', 'pending')
                ->count();
        }
        
        if ($user->role === 'artisan') {
            $stats['transformations_sold'] = $user->transformations()
                ->where('status', 'sold')
                ->count();
            $stats['marketplace_items'] = $user->transformations()
                ->where('status', 'available')
                ->count();
        }
        
        return view('profile.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing the user's profile
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        // Update user data
        $user->update($validated);

        return redirect()
            ->route('profile.show')
            ->with('success', 'Votre profil a été mis à jour avec succès !');
    }

    /**
     * Update the user's password
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Le mot de passe actuel est incorrect.'
            ])->withInput();
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()
            ->route('profile.show')
            ->with('success', 'Votre mot de passe a été modifié avec succès !');
    }

    /**
     * Delete the user's avatar
     */
    public function deleteAvatar()
    {
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Votre avatar a été supprimé avec succès !');
    }

    /**
     * Delete the user's account
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        // Verify password before deletion
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Le mot de passe est incorrect.'
            ]);
        }

        // Delete avatar
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Logout and delete account
        Auth::logout();
        $user->delete();

        return redirect('/')
            ->with('success', 'Votre compte a été supprimé avec succès.');
    }
}

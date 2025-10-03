<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\User;
use App\Models\WasteItem;
use App\Models\RepairRequest;
use App\Models\CommunityEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminProfileController extends Controller
{
    /**
     * Display the admin's profile
     */
    public function show()
    {
        $user = Auth::user();
        
        // Calculate admin statistics
        $stats = [
            'member_since' => $user->created_at->diffForHumans(),
            'account_age_days' => $user->created_at->diffInDays(now()),
        ];

        // Platform statistics
        $platformStats = [
            'total_users' => User::count(),
            'total_items' => WasteItem::count(),
            'total_repairs' => RepairRequest::count(),
            'total_events' => CommunityEvent::count(),
        ];

        return view('admin.profile.show', compact('user', 'stats', 'platformStats'));
    }

    /**
     * Show the form for editing the admin's profile
     */
    public function edit()
    {
        $user = Auth::user();
        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Update the admin's profile information
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
            ->route('admin.profile.show')
            ->with('success', 'Votre profil a été mis à jour avec succès !');
    }

    /**
     * Update the admin's password
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
            ->route('admin.profile.show')
            ->with('success', 'Votre mot de passe a été modifié avec succès !');
    }

    /**
     * Delete the admin's avatar
     */
    public function deleteAvatar()
    {
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return redirect()
            ->route('admin.profile.edit')
            ->with('success', 'Votre avatar a été supprimé avec succès !');
    }
}

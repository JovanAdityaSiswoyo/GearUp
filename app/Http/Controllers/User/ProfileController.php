<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{
    /**
     * Show the profile page.
     */
    public function show()
    {
        $user = Auth::user();
        return view('user.profile.show', compact('user'));
    }

    /**
     * Switch application language.
     */
    public function switchLanguage(Request $request)
    {
        $request->validate([
            'language' => 'required|in:en,id',
        ]);

        Session::put('locale', $request->language);
        App::setLocale($request->language);

        return redirect()->back()->with('success', __('profile.success_language_updated'));
    }

    /**
     * Update the user profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Delete old photo if exists
        if ($user->profile_photo && file_exists(public_path('storage/' . $user->profile_photo))) {
            unlink(public_path('storage/' . $user->profile_photo));
        }

        // Store new photo
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $path = $file->store('profiles', 'public');
            $user->update(['profile_photo' => $path]);
        }

        return redirect()->back()->with('success', __('profile.success_photo_updated'));
    }

    /**
     * Update user profile.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
        ]);

        Auth::user()->update($request->only('name', 'email', 'phone'));

        return redirect()->back()->with('success', __('profile.success_profile_updated'));
    }
}

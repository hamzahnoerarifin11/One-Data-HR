<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('pages.profile');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'profile-photos/' . $user->id . '-' . time() . '.jpg';

            try {
                // Use Intervention Image to orient, fit and compress image for consistency
                $image = Image::make($file)->orientate()->fit(1024, 1024);
                Storage::disk('public')->put($filename, (string) $image->encode('jpg', 85));
            } catch (\Throwable $e) {
                // Fallback: store original file (and try to convert to jpg if necessary)
                try {
                    $path = $file->storeAs('profile-photos', $user->id . '-' . time() . '.' . $file->getClientOriginalExtension(), 'public');
                    // If original is not jpg, attempt simple re-encode with GD if available
                } catch (\Throwable $e2) {
                    // Last resort: store as-is using default store
                    $path = $file->store('profile-photos', 'public');
                }
                // Use the stored path as filename variable for consistency
                $filename = isset($path) ? $path : $filename;
            }

            // remove previous photo if present
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            $user->photo = $filename;
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'name'          => 'sometimes|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['name', 'phone']);

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) { Storage::disk('public')->delete($user->profile_image); }
            $data['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        $user->update($data);
        return response()->json($user);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);
        return response()->json(['message' => 'Password updated successfully.']);
    }
}

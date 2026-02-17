<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;

class ProfileController extends Controller
{
    private function storeImage($base64Image)
    {
        if (!$base64Image || !preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            return $base64Image;
        }

        $image = substr($base64Image, strpos($base64Image, ',') + 1);
        $type = strtolower($type[1]);

        if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
            return $base64Image;
        }

        $image = str_replace(' ', '+', $image);
        $imageName = strtolower(Str::random(10)) . '.' . $type;

        $directory = public_path('uploads/profiles');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($directory . '/' . $imageName, base64_decode($image));

        return url('uploads/profiles/' . $imageName);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string',
            'gender' => 'nullable|string|in:male,female,other',
        ]);

        $user->update($request->only('name', 'email', 'phone', 'gender'));

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Current password does not match'
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'image' => 'required'
        ]);

        $user = auth()->user();
        $user->update([
            'image' => $this->storeImage($request->image)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Profile photo updated successfully',
            'image' => $user->image
        ]);
    }
}

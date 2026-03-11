<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function doLogin(Request $request)
    {
        $request->validate([
            'login_name' => 'required|string',
            'password' => 'required|string',
            // 'role' => 'required|in:library,student',
            // 'library_code' => 'required_if:role,student'
        ]);

        $query = User::where('login_name', $request->login_name)->where('is_active', 1);
        // ->where('role', $request->role);

        // Students must belong to a library
        // if ($request->role === 'student') {
        //     $query->where('library_code', $request->library_code);
        // }

        $user = $query->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'status' => false,
                'message' => 'Your account has been deactivated. Please contact administrator.'
            ], 403);
        }

        // Create token
        $token = $user->createToken('api-token')->plainTextToken;

        // Save Device Information if provided
        if ($request->has('device_token') && $request->has('device_id')) {
            \App\Models\Device::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'device_id' => $request->device_id
                ],
                [
                    'device_token' => $request->device_token,
                    'device_type' => $request->get('device_type', 'android')
                ]
            );
        }

        if ($user->role == "student") {
            $user->load(['student', 'student.slotPackage', 'student.library']);
        } else if ($user->role == "library") {
            $user->load(['library', 'library.plan']);
        }

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            if ($request->has('device_id')) {
                \App\Models\Device::where([
                    'user_id' => $user->id,
                    'device_id' => $request->device_id
                ])->update(['device_token' => '']);
            }
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['login_name' => 'required']);

        $user = User::where('login_name', $request->login_name)->first();

        if ($user) {
            $newPassword = Str::random(8);
            $user->update([
                'password' => Hash::make($newPassword)
            ]);

            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ForgotPasswordMail($newPassword));
            } catch (\Exception $e) {
                // Silently log or handle if mail fails
            }

            return response()->json([
                'status' => true,
                'message' => 'If your account exists and has a registered email, you will receive your new password shortly.'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'If your account exists and has a registered email, you will receive your new password shortly.'
        ]);
    }
}

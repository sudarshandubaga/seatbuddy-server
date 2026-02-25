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
            'role' => 'required|in:library,student',
            'library_code' => 'required_if:role,student'
        ]);

        $query = User::where('login_name', $request->login_name)
            ->where('role', $request->role);

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

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'library_code' => $user->library_code
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

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

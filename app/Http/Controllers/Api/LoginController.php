<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
        if ($request->role === 'student') {
            $query->where('library_code', $request->library_code);
        }

        $user = $query->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials'
            ], 401);
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
}

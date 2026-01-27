<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function startup(Request $request)
    {
        $request->validate([
            'device_id' => 'nullable|string',
            'device_token' => 'nullable|string',
            'device_type' => 'nullable|string|in:android,ios',
        ]);

        $user = auth()->user();

        if ($request->device_id && $request->device_token && $request->device_type) {
            Device::updateOrCreate([
                'user_id' => $user->id,
                'device_id' => $request->device_id,
            ], [
                'device_token' => $request->device_token,
                'device_type' => $request->device_type,
            ]);
        }

        if ($user->role === 'library') {
            return response()->json([
                'status' => true,
                'message' => 'Library data',
                'data' => $user->load("library")
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'User data',
            'data' => $user->load(["student", "library"])
        ]);
    }
}

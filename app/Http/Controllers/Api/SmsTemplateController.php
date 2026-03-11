<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SmsTemplateController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user()->load('library');
        if (!$user->library) {
            return response()->json(['message' => 'No library associated'], 400);
        }

        return response()->json([
            'status' => true,
            'data' => $user->library->sms_templates ?? null
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'templates' => 'required|array'
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user()->load('library');
        if (!$user->library) {
            return response()->json(['message' => 'No library associated'], 400);
        }

        $user->library->update([
            'sms_templates' => $request->templates
        ]);

        return response()->json([
            'status' => true,
            'message' => 'SMS templates updated successfully',
            'data' => $user->library->sms_templates
        ]);
    }
}

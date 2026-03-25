<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LegalPageController extends Controller
{
    /**
     * Get legal pages content for the authenticated user's library.
     */
    public function index()
    {
        $user = auth()->user()->load('library');

        if (!$user->library) {
            return response()->json([
                'status' => false,
                'message' => 'Library not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'terms_conditions' => $user->library->terms_conditions,
                'privacy_policy' => $user->library->privacy_policy,
                'disclaimer' => $user->library->disclaimer,
            ]
        ]);
    }

    /**
     * Update legal pages content.
     */
    public function update(Request $request)
    {
        $user = auth()->user()->load('library');

        if (!$user->library) {
            return response()->json([
                'status' => false,
                'message' => 'Library not found'
            ], 404);
        }

        $validated = $request->validate([
            'terms_conditions' => 'nullable|string',
            'privacy_policy' => 'nullable|string',
            'disclaimer' => 'nullable|string',
        ]);

        $user->library->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Legal pages updated successfully',
            'data' => [
                'terms_conditions' => $user->library->terms_conditions,
                'privacy_policy' => $user->library->privacy_policy,
                'disclaimer' => $user->library->disclaimer,
            ]
        ]);
    }
}

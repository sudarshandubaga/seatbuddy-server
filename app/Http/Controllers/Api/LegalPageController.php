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
     * Get support content.
     */
    public function support()
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
                'support_phone' => $user->library->support_phone,
                'support_email' => $user->library->support_email,
                'support_whatsapp' => $user->library->support_whatsapp,
                'faqs' => json_decode($user->library->faqs),
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
            'support_phone' => 'nullable|string',
            'support_email' => 'nullable|string',
            'support_whatsapp' => 'nullable|string',
            'faqs' => 'nullable|string', // Send as JSON string from client
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

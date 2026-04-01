<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GlobalSetting;

class LegalPageController extends Controller
{
    /**
     * Get legal pages content.
     */
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => [
                'terms_conditions' => GlobalSetting::where('key', 'terms_conditions')->first()?->value,
                'privacy_policy' => GlobalSetting::where('key', 'privacy_policy')->first()?->value,
                'disclaimer' => GlobalSetting::where('key', 'disclaimer')->first()?->value,
                'account_deletion_url' => url('/account-deletion'),
            ]
        ]);
    }

    /**
     * Get support content.
     */
    public function support()
    {
        return response()->json([
            'status' => true,
            'data' => [
                'support_phone' => GlobalSetting::where('key', 'support_phone')->first()?->value,
                'support_email' => GlobalSetting::where('key', 'support_email')->first()?->value,
                'support_whatsapp' => GlobalSetting::where('key', 'support_whatsapp')->first()?->value,
                'faqs' => json_decode(GlobalSetting::where('key', 'faqs')->first()?->value ?? '[]'),
            ]
        ]);
    }

    /**
     * Update global settings content.
     */
    public function update(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
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

        foreach ($validated as $key => $value) {
            GlobalSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Global settings updated successfully',
        ]);
    }
}

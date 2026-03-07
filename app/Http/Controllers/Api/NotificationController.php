<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $notifications
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $notification = Notification::where('user_id', $user->id)->find($id);

        if ($notification) {
            $notification->update(['is_read' => $request->get('is_read', true)]);
        }

        return response()->json(['status' => true]);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $notification = Notification::where('user_id', $user->id)->find($id);
        if ($notification) {
            $notification->delete();
        }
        return response()->json(['status' => true]);
    }
}

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

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'student_id' => 'required' // 'all' or specific student user_id
        ]);

        $user = auth()->user();
        
        if ($user->role !== 'library') {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($request->student_id === 'all') {
            $students = \App\Models\User::where('role', 'student')
                ->where('library_code', $user->library_code)
                ->get();

            foreach ($students as $student) {
                Notification::create([
                    'user_id' => $student->id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'purpose' => 'general'
                ]);
            }
        } else {
            Notification::create([
                'user_id' => $request->student_id,
                'title' => $request->title,
                'description' => $request->description,
                'purpose' => 'general'
            ]);
        }

        return response()->json(['status' => true, 'message' => 'Notification sent successfully']);
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

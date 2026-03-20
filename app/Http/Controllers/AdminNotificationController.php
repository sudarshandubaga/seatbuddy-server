<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'recipient_role' => 'nullable|in:library,student',
            'library_ids' => 'nullable',
            'user_ids' => 'nullable'
        ]);

        $recipientRole = $request->recipient_role ?? 'library';
        $userIds = is_array($request->user_ids) ? $request->user_ids : ($request->user_ids ? [$request->user_ids] : []);

        if (!empty($userIds)) {
            $query = \App\Models\User::whereIn('id', $userIds);
        } else {
            $libraryIds = is_array($request->library_ids) ? $request->library_ids : [$request->library_ids];
            $isAll = in_array('all', $libraryIds);

            $query = \App\Models\User::where('role', $recipientRole);

            if (!$isAll) {
                if ($recipientRole === 'library') {
                    $query->whereIn('id', $libraryIds);
                } else {
                    $query->whereHas('student', function ($q) use ($libraryIds) {
                        $q->whereIn('library_id', $libraryIds);
                    });
                }
            }
        }

        $users = $query->get();

        foreach ($users as $user) {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'description' => $request->description,
                'purpose' => 'general'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Notification sent to ' . $recipientRole . 's successfully!'
        ]);
    }
}

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
            'library_ids' => 'required' 
        ]);

        $libraryIds = is_array($request->library_ids) ? $request->library_ids : [$request->library_ids];
        $isAll = in_array('all', $libraryIds);

        $query = \App\Models\User::where('role', 'library');
        if (!$isAll) {
            $query->whereIn('id', $libraryIds);
        }

        $libraries = $query->get();

        foreach ($libraries as $libUser) {
            \App\Models\Notification::create([
                'user_id' => $libUser->id,
                'title' => $request->title,
                'description' => $request->description,
                'purpose' => 'general'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Notification sent to libraries successfully!'
        ]);
    }
}

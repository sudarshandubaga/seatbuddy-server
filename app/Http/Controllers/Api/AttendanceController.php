<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $attendances = User::with('attendances')->where('library_id', $user->library_id)->where('role', 'student')->get();
        return response()->json($attendances);
    }

    /**
     * Auto Check In / Out
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $now = Carbon::now();

        // Get last attendance
        $lastAttendance = Attendance::where('user_id', $request->user_id)
            ->latest('created_at')
            ->first();

        // ⏱ Minimum 5 minutes difference
        if ($lastAttendance) {
            $diffInMinutes = $lastAttendance->created_at->diffInMinutes($now);

            if ($diffInMinutes < 5) {
                return response()->json([
                    'message' => 'You can mark attendance only after 5 minutes.'
                ], 422);
            }
        }

        // 🔁 Auto determine type
        $type = (!$lastAttendance || $lastAttendance->type === 'out')
            ? 'in'
            : 'out';

        $attendance = Attendance::create([
            'id' => Str::uuid(),
            'user_id' => $request->user_id,
            'date' => $now->toDateString(),
            'time' => $now->toTimeString(),
            'type' => $type,
        ]);

        return response()->json([
            'message' => "Successfully checked {$type}",
            'data' => $attendance
        ], 201);
    }

    public function show(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $user = auth()->user();
        $attendance = Attendance::where('user_id', $user->id)->where('date', $request->date)->get();
        return response()->json($attendance);
    }
}
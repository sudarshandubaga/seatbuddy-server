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
        $user = auth()->user()->load('library');
        $attendances = User::with('attendances')->where('library_id', $user->library->id)->where('role', 'student')->get();
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

    public function history(Request $request)
    {
        $user = auth()->user();
        $targetUserId = $request->user_id ?? $user->id;

        if ($user->role === 'library' && $request->user_id) {
            $student = User::where('id', $targetUserId)->where('library_id', $user->library_id)->first();
            if (!$student) {
                return response()->json(['message' => 'Student not found or access denied'], 403);
            }
        } elseif ($user->role === 'student') {
            $targetUserId = $user->id;
        }

        $attendances = Attendance::where('user_id', $targetUserId)
            ->orderBy('date', 'desc')
            ->orderBy('time', 'asc')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->date)->toDateString();
            });

        $history = [];
        foreach ($attendances as $date => $records) {
            $totalSeconds = 0;
            $logs = [];

            $tempIn = null;
            foreach ($records as $record) {
                $timeFormatted = Carbon::parse($record->time)->format('h:i A');
                $logs[] = [
                    'type' => $record->type,
                    'time' => $timeFormatted
                ];

                if ($record->type === 'in') {
                    $tempIn = Carbon::parse($record->date->toDateString() . ' ' . $record->time);
                } elseif ($record->type === 'out' && $tempIn) {
                    $currentOut = Carbon::parse($record->date->toDateString() . ' ' . $record->time);
                    $totalSeconds += $currentOut->diffInSeconds($tempIn);
                    $tempIn = null;
                }
            }

            // Calculation of hours
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds / 60) % 60);
            $durationFormatted = "{$hours}h {$minutes}m";

            $history[] = [
                'id' => $date,
                'date' => Carbon::parse($date)->format('d-m-Y'),
                'day' => Carbon::parse($date)->format('l'),
                'duration' => $durationFormatted,
                'logs' => $logs,
                'status' => count($records) > 0 ? 'Present' : 'Absent'
            ];
        }

        return response()->json($history);
    }
}
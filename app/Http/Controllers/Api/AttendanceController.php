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

        // Fetch all records sequentially to pair IN/OUT correctly
        $attendances = Attendance::where('user_id', $targetUserId)
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();

        $processedHistory = [];
        $tempIn = null;

        foreach ($attendances as $record) {
            // Ensure date is treated as string for grouping
            $dateString = Carbon::parse($record->date)->toDateString();

            if (!isset($processedHistory[$dateString])) {
                $processedHistory[$dateString] = [
                    'id' => $dateString,
                    'date' => Carbon::parse($dateString)->format('d-m-Y'),
                    'day' => Carbon::parse($dateString)->format('l'),
                    'totalSeconds' => 0,
                    'logs' => [],
                    'status' => 'Present'
                ];
            }

            // Log time formatting
            $timeFormatted = Carbon::parse($record->time)->format('h:i A');
            $processedHistory[$dateString]['logs'][] = [
                'type' => $record->type,
                'time' => $timeFormatted
            ];

            // Pairing Logic
            if ($record->type === 'in') {
                // We pair from the IN record. If multiple INs occur, we keep the first one as session start.
                if (!$tempIn) {
                    $tempIn = Carbon::parse($dateString . ' ' . $record->time);
                }
            } elseif ($record->type === 'out' && $tempIn) {
                // Calculate duration when an OUT follows an IN
                $currentOut = Carbon::parse($dateString . ' ' . $record->time);
                $duration = $currentOut->diffInSeconds($tempIn);

                // Attribute the duration to the session's start date
                $startDate = $tempIn->toDateString();
                if (isset($processedHistory[$startDate])) {
                    $processedHistory[$startDate]['totalSeconds'] += $duration;
                }

                $tempIn = null; // Session closed
            }
        }

        // Final formatting
        $finalHistory = [];
        foreach ($processedHistory as $date => $data) {
            $totalSeconds = $data['totalSeconds'];
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds / 60) % 60);

            $data['duration'] = "{$hours}h {$minutes}m";
            unset($data['totalSeconds']);
            $finalHistory[] = $data;
        }

        // Show most recent dates first
        return response()->json(array_reverse($finalHistory));
    }
}
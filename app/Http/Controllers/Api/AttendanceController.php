<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('library');
        $attendances = User::with('attendances')->whereHas('student', function ($q) use ($user) {
            $q->where('library_id', $user->library->id);
        })->where('role', 'student')->get();
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
        $user = auth()->user()->load('library');
        $targetUserId = $request->user_id ?? $user->id;

        if ($user->role === 'library' && $targetUserId) {
            $studentUser = User::with('student')->where('id', $targetUserId)->whereHas('student', function ($q) use ($user) {
                $q->where('library_id', $user->library->id);
            })->first();
            if (!$studentUser) {
                return response()->json(['message' => 'Student not found or access denied'], 403);
            }
            $student = $studentUser->student;
        } elseif ($user->role === 'student') {
            $targetUserId = $user->id;
            $student = Student::where('user_id', $user->id)->first();
        }

        // Handle filters
        $hasMonthFilter = $request->has('month');
        $month = intval($request->get('month', Carbon::now()->month));
        $year = intval($request->get('year', Carbon::now()->year));

        if ($hasMonthFilter) {
            $startRange = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endRange = clone $startRange;
            $endRange->endOfMonth();
        } else {
            // Full Year View
            $startRange = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $endRange = clone $startRange;
            $endRange->endOfYear();
        }

        // Respect joining date
        if ($student && $student->join_date) {
            $joinDate = Carbon::parse($student->join_date)->startOfDay();
            if ($startRange->lessThan($joinDate)) {
                $startRange = clone $joinDate;
            }
        }

        // Don't show future dates
        $today = Carbon::now()->endOfDay();
        if ($endRange->greaterThan($today)) {
            $endRange = $today;
        }

        // If after adjusting, start is after end, return empty
        if ($startRange->greaterThan($endRange)) {
            return response()->json([]);
        }

        // Fetch records for the range
        $attendances = Attendance::where('user_id', $targetUserId)
            ->whereBetween('date', [$startRange->toDateString(), $endRange->toDateString()])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();

        $processedHistory = [];
        $tempIn = null;

        foreach ($attendances as $record) {
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

            $timeFormatted = Carbon::parse($record->time)->format('h:i A');
            $processedHistory[$dateString]['logs'][] = [
                'type' => $record->type,
                'time' => $timeFormatted
            ];

            $recordDateTime = Carbon::parse($dateString . ' ' . $record->time);

            if ($record->type === 'in') {
                $tempIn = $recordDateTime;
            } elseif ($record->type === 'out' && $tempIn) {
                $seconds = $recordDateTime->diffInSeconds($tempIn);
                if ($recordDateTime->greaterThan($tempIn)) {
                    $startDateString = $tempIn->toDateString();
                    if (isset($processedHistory[$startDateString])) {
                        $processedHistory[$startDateString]['totalSeconds'] += $seconds;
                    }
                }
                $tempIn = null;
            }
        }

        $fullHistory = [];
        for ($date = clone $startRange; $date->lte($endRange); $date->addDay()) {
            $dateString = $date->toDateString();

            if (isset($processedHistory[$dateString])) {
                $data = $processedHistory[$dateString];
                $totalSeconds = $data['totalSeconds'];
                $hours = floor($totalSeconds / 3600);
                $minutes = floor(($totalSeconds / 60) % 60);

                $data['duration'] = sprintf('%02d h : %02d m', $hours, $minutes);
                unset($data['totalSeconds']);
                $fullHistory[] = $data;
            } else {
                $fullHistory[] = [
                    'id' => $dateString,
                    'date' => $date->format('d-m-Y'),
                    'day' => $date->format('l'),
                    'duration' => '00 h : 00 m',
                    'logs' => [],
                    'status' => 'Absent'
                ];
            }
        }

        return response()->json(array_reverse($fullHistory));
    }
}
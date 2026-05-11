<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fees;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $libraryId = null;
        if ($user->role === 'library') {
            $user->load('library');
            $libraryId = $user->library->id ?? null;
        }

        if ($user->role === 'student') {
            $user->load('student');
            $libraryId = $user->student->library_id ?? null;
        }

        if (!$libraryId && $user->role === 'library') {
            return response()->json(['status' => false, 'message' => 'Library not found'], 404);
        }

        $query = Fees::with(['student', 'student.user']);

        if ($user->role === 'library') {
            $query->whereHas('student', function ($q) use ($libraryId) {
                $q->where('library_id', $libraryId);
            });

            if ($request->has('student_id')) {
                $query->where('student_id', $request->student_id);
            }
        } else {
            // Student role
            $query->where('student_id', $user->student->id);
        }

        if ($request->has('status') && in_array($request->status, ['paid', 'due'])) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $user->role === 'library') {
            $search = $request->search;
            $query->whereHas('student.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('month')) {
            $query->whereMonth('date', $request->month);
        }

        if ($request->has('year')) {
            $query->whereYear('date', $request->year);
        }

        $fees = $query->orderBy('date', 'desc')->get();

        // Calculate stats
        $statsQuery = Fees::query();
        if ($user->role === 'library') {
            $statsQuery->whereHas('student', function ($q) use ($libraryId) {
                $q->where('library_id', $libraryId);
            });
            if ($request->has('student_id')) {
                $statsQuery->where('student_id', $request->student_id);
            }
        } else {
            $statsQuery->where('student_id', $user->student->id);
        }

        if ($request->has('month')) {
            $statsQuery->whereMonth('date', $request->month);
        }

        if ($request->has('year')) {
            $statsQuery->whereYear('date', $request->year);
        }

        $totalDue = (clone $statsQuery)->where('status', 'due')->sum('amount');
        $totalPaid = (clone $statsQuery)->where('status', 'paid')->sum('amount');

        return response()->json([
            'status' => true,
            'data' => $fees,
            'stats' => [
                'total_due' => floatval($totalDue),
                'total_paid' => floatval($totalPaid)
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->role !== 'library') {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $fee = Fees::findOrFail($id);

        $request->validate([
            'status' => 'required|in:paid,due',
            'payment_mode' => 'nullable|string'
        ]);

        $fee->update([
            'status' => $request->status,
            'payment_mode' => $request->payment_mode ?? ($request->status === 'paid' ? 'Cash' : null)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Fee updated successfully',
            'data' => $fee
        ]);
    }
}

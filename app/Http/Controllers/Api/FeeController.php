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
        $user = auth()->user()->load('library');
        if (!$user->library) {
            return response()->json(['status' => false, 'message' => 'Library not found'], 404);
        }
        $libraryId = $user->library->id;

        $query = Fees::with(['student', 'student.user'])
            ->whereHas('student', function ($q) use ($libraryId) {
                $q->where('library_id', $libraryId);
            });

        if ($request->has('status') && in_array($request->status, ['paid', 'due'])) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('student.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('month')) {
            $year = $request->get('year', date('Y'));
            $query->whereMonth('date', $request->month)
                ->whereYear('date', $year);
        }

        $fees = $query->orderBy('date', 'desc')->get();

        // Calculate stats with filters
        $statsQuery = Fees::whereHas('student', function ($q) use ($libraryId) {
            $q->where('library_id', $libraryId);
        });

        if ($request->has('month')) {
            $year = $request->get('year', date('Y'));
            $statsQuery->whereMonth('date', $request->month)
                ->whereYear('date', $year);
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
        $fee = Fees::findOrFail($id);

        $request->validate([
            'status' => 'required|in:paid,due'
        ]);

        $fee->update([
            'status' => $request->status
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Fee updated successfully',
            'data' => $fee
        ]);
    }
}

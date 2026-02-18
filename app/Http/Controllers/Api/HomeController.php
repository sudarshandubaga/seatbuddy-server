<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function startup(Request $request)
    {
        $request->validate([
            'device_id' => 'nullable|string',
            'device_token' => 'nullable|string',
            'device_type' => 'nullable|string|in:android,ios',
        ]);

        $user = auth()->user();

        if ($request->device_id && $request->device_token && $request->device_type) {
            Device::updateOrCreate([
                'user_id' => $user->id,
                'device_id' => $request->device_id,
            ], [
                'device_token' => $request->device_token,
                'device_type' => $request->device_type,
            ]);
        }

        if ($user->role === 'library') {
            return response()->json([
                'status' => true,
                'message' => 'Library data',
                'data' => $user->load("library.plan")
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'User data',
            'data' => $user->load(["student", "library.plan"])
        ]);
    }

    public function dashboard(Request $request)
    {
        $user = auth()->user()->load('library');
        if (!$user->library) {
            return response()->json(['status' => false, 'message' => 'Library not found'], 404);
        }
        $libraryId = $user->library->id;

        $activeStudents = \App\Models\User::where('role', 'student')
            ->whereHas('student', fn($q) => $q->where('library_id', $libraryId))
            ->where('is_active', true)
            ->count();

        $dueFeesCount = \App\Models\Fees::whereHas('student', fn($q) => $q->where('library_id', $libraryId))
            ->where('status', 'due')
            ->count();

        $totalExpense = \App\Models\Expense::where('library_id', $libraryId)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $totalEarnings = \App\Models\Fees::whereHas('student', fn($q) => $q->where('library_id', $libraryId))
            ->where('status', 'paid')
            ->sum('amount');

        // Chart Data (Last 6 Months)
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $amount = \App\Models\Fees::whereHas('student', fn($q) => $q->where('library_id', $libraryId))
                ->where('status', 'paid')
                ->whereMonth('date', $month->month)
                ->whereYear('date', $month->year)
                ->sum('amount');

            $chartData[] = [
                'label' => $month->format('M'),
                'value' => (int) $amount
            ];
        }

        return response()->json([
            'status' => true,
            'data' => [
                'active_students' => $activeStudents,
                'due_fees_count' => $dueFeesCount,
                'total_expense' => $totalExpense,
                'total_earnings' => $totalEarnings,
                'chart_data' => $chartData
            ]
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Expense;
use App\Models\Fees;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = auth()->user()->load('library');
        if (!$user->library) {
            return response()->json(['status' => false, 'message' => 'Library not found'], 404);
        }
        $libraryId = $user->library->id;

        // Active Students (by library_id in user table)
        $activeStudentsCount = User::where('library_id', $libraryId)
            ->where('role', 'student')
            ->where('is_active', true)
            ->count();

        // Due Fees (count)
        $dueFeesCount = Fees::whereHas('student', function ($q) use ($libraryId) {
            $q->where('library_id', $libraryId);
        })->where('status', 'due')->count();

        // Total Expense (current month)
        $totalExpense = Expense::where('library_id', $libraryId)
            ->whereMonth('date', date('m'))
            ->whereYear('date', date('Y'))
            ->sum('amount');

        // Total Earnings (sum of paid fees)
        $totalEarnings = Fees::whereHas('student', function ($q) use ($libraryId) {
            $q->where('library_id', $libraryId);
        })->where('status', 'paid')->sum('amount');

        // Monthly Earnings Graph (last 6 months)
        $monthlyEarnings = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('m');
            $year = $date->format('Y');
            $label = $date->format('M');

            $val = Fees::whereHas('student', function ($q) use ($libraryId) {
                $q->where('library_id', $libraryId);
            })
                ->where('status', 'paid')
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('amount');

            $monthlyEarnings[] = ['label' => $label, 'value' => floatval($val)];
        }

        // Yearly Earnings Graph (last 6 years)
        $yearlyEarnings = [];
        for ($i = 5; $i >= 0; $i--) {
            $year = now()->subYears($i)->format('Y');

            $val = Fees::whereHas('student', function ($q) use ($libraryId) {
                $q->where('library_id', $libraryId);
            })
                ->where('status', 'paid')
                ->whereYear('date', $year)
                ->sum('amount');

            $yearlyEarnings[] = ['label' => $year, 'value' => floatval($val)];
        }

        return response()->json([
            'status' => true,
            'data' => [
                'active_students' => $activeStudentsCount,
                'due_fees_count' => $dueFeesCount,
                'total_expense' => floatval($totalExpense),
                'total_earnings' => floatval($totalEarnings),
                'monthly_earnings' => $monthlyEarnings,
                'yearly_earnings' => $yearlyEarnings,
            ]
        ]);
    }

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
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses for a library.
     */
    public function index()
    {
        $user = auth()->user()->load('library');
        if (!$user->library) {
            return response()->json(['message' => 'Library not found'], 404);
        }

        $expenses = Expense::where('library_id', $user->library->id)
            ->orderBy('date', 'desc')
            ->get();

        // Calculate monthly total for current month
        $monthlyTotal = Expense::where('library_id', $user->library->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        // Calculate grand total
        $grandTotal = Expense::where('library_id', $user->library->id)
            ->sum('amount');

        return response()->json([
            'expenses' => $expenses,
            'stats' => [
                'monthly_total' => $monthlyTotal,
                'grand_total' => $grandTotal
            ]
        ], 200);
    }

    /**
     * Store a newly created expense.
     */
    public function store(Request $request)
    {
        $user = auth()->user()->load('library');
        if (!$user->library) {
            return response()->json(['message' => 'Library not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|in:rent,electricity,maintenance,other',
            'date' => 'required|date',
        ]);

        $validated['library_id'] = $user->library->id;

        $expense = Expense::create($validated);

        return response()->json($expense, 201);
    }

    /**
     * Display a specific expense.
     */
    public function show(Expense $expense)
    {
        $user = auth()->user()->load('library');
        if ($expense->library_id !== $user->library->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($expense, 200);
    }

    /**
     * Update an expense.
     */
    public function update(Request $request, Expense $expense)
    {
        $user = auth()->user()->load('library');
        if ($expense->library_id !== $user->library->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'sometimes|required|numeric|min:0',
            'category' => 'sometimes|required|in:rent,electricity,maintenance,other',
            'date' => 'sometimes|required|date',
        ]);

        $expense->update($validated);

        return response()->json($expense, 200);
    }

    /**
     * Remove an expense.
     */
    public function destroy(Expense $expense)
    {
        $user = auth()->user()->load('library');
        if ($expense->library_id !== $user->library->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $expense->delete();

        return response()->json([
            'message' => 'Expense deleted successfully'
        ], 200);
    }
}

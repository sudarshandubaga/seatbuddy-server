<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Library;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses for a library.
     */
    public function index()
    {
        $user = auth()->user();
        $library = $user->library_id;
        $expenses = Expense::where('library_id', $library)->get();
        return response()->json($expenses, 200);
    }

    /**
     * Store a newly created expense.
     */
    public function store(Request $request, Library $library)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|in:rent,electricity,maintenance,other',
            'date' => 'required|date',
        ]);

        $expense = $library->expenses()->create($validated);

        return response()->json($expense, 201);
    }

    /**
     * Display a specific expense.
     */
    public function show(Library $library, Expense $expense)
    {
        $this->authorizeExpense($library, $expense);

        return response()->json($expense, 200);
    }

    /**
     * Update an expense.
     */
    public function update(Request $request, Library $library, Expense $expense)
    {
        $this->authorizeExpense($library, $expense);

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
    public function destroy(Library $library, Expense $expense)
    {
        $this->authorizeExpense($library, $expense);

        $expense->delete();

        return response()->json([
            'message' => 'Expense deleted successfully'
        ], 200);
    }

    /**
     * Ensure expense belongs to the library.
     */
    private function authorizeExpense(Library $library, Expense $expense)
    {
        if ($expense->library_id !== $library->id) {
            abort(404, 'Expense not found for this library');
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Library;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('library');
        $expenses = Expense::where('library_id', $user->library->id)
            ->orderBy('date', 'desc')
            ->get();
        return response()->json($expenses);
    }

    public function store(Request $request)
    {
        $user = auth()->user()->load('library');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|in:Rent,Electricity,Maintenance,Other',
            'date' => 'required|date',
        ]);

        $expense = Expense::create([
            'library_id' => $user->library->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'category' => $validated['category'],
            'date' => $validated['date'],
        ]);

        return response()->json($expense, 201);
    }

    public function show($id)
    {
        $user = auth()->user()->load('library');
        $expense = Expense::where('library_id', $user->library->id)->findOrFail($id);

        return response()->json($expense);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user()->load('library');
        $expense = Expense::where('library_id', $user->library->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'sometimes|required|numeric|min:0',
            'category' => 'sometimes|required|in:rent,electricity,maintenance,other',
            'date' => 'sometimes|required|date',
        ]);

        $expense->update($validated);

        return response()->json($expense);
    }

    public function destroy($id)
    {
        $user = auth()->user()->load('library');
        $expense = Expense::where('library_id', $user->library->id)->findOrFail($id);

        $expense->delete();

        return response()->json([
            'message' => 'Expense deleted successfully'
        ]);
    }
}

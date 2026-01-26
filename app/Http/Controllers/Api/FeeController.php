<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fees;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function updateStatus(Fees $fees, Request $request)
    {
        $fees->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Fee status updated successfully',
            'data' => $fees
        ]);
    }

    public function index(Request $request)
    {
        $request->validate([
            'type' => 'required|in:due,paid,all'
        ]);

        $user = auth()->user();
        $fees = User::with([
            'student',
            'fees' => function ($query) use ($request) {
                if ($request->type === 'due') {
                    $query->where('status', 'due');
                } elseif ($request->type === 'paid') {
                    $query->where('status', 'paid');
                }
            }
        ])
            ->where('library_id', $user->library_id)
            ->whereHas('fees', function ($query) use ($request) {
                if ($request->type === 'due') {
                    $query->where('status', 'due');
                } elseif ($request->type === 'paid') {
                    $query->where('status', 'paid');
                }
            })
            ->get();

        return response()->json($fees);
    }
}

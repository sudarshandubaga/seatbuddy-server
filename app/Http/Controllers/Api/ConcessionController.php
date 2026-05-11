<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Concession;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConcessionController extends Controller
{
    public function index()
    {
        $libraryId = Auth::user()->library->id;
        $concessions = Concession::where('library_id', $libraryId)->get();

        return response()->json([
            'status' => true,
            'data' => $concessions
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        $libraryId = Auth::user()->library->id;

        $concession = Concession::create([
            'library_id' => $libraryId,
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'description' => $request->description
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Concession created successfully',
            'data' => $concession
        ]);
    }

    public function update(Request $request, $id)
    {
        $concession = Concession::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        $concession->update([
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'description' => $request->description
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Concession updated successfully',
            'data' => $concession
        ]);
    }

    public function destroy($id)
    {
        $concession = Concession::findOrFail($id);
        $concession->delete();

        return response()->json([
            'status' => true,
            'message' => 'Concession deleted successfully'
        ]);
    }

    public function allocate(Request $request, $id)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id'
        ]);

        $concession = Concession::findOrFail($id);

        // Clear previous allocations for this concession
        Student::where('concession_id', $id)->update(['concession_id' => null]);

        // Allocate to new students
        if (!empty($request->student_ids)) {
            Student::whereIn('user_id', $request->student_ids)
                ->update(['concession_id' => $id]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Concession allocated successfully'
        ]);
    }
}

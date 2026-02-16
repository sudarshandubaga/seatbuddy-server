<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EnquiryController extends Controller
{
    /**
     * Display a listing of enquiries.
     */
    public function index()
    {
        $user = auth()->user()->load('library');
        return response()->json([
            'success' => true,
            'data' => Enquiry::with('slotPackage')->where('library_id', $user->library?->id)->latest()->get(),
        ]);
    }

    /**
     * Store a newly created enquiry.
     */
    public function store(Request $request)
    {
        $user = auth()->user()->load('library');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'dob' => 'nullable|date',
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'slot_package_id' => 'nullable|uuid|exists:slot_packages,id',
            'message' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $enquiry = Enquiry::create([
            'id' => Str::uuid(),
            'library_id' => $user->library?->id,
            ...$validated,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Enquiry created successfully',
            'data' => $enquiry,
        ], 201);
    }

    /**
     * Display the specified enquiry.
     */
    public function show(Enquiry $enquiry)
    {
        return response()->json([
            'success' => true,
            'data' => $enquiry,
        ]);
    }

    /**
     * Update the specified enquiry.
     */
    public function update(Request $request, Enquiry $enquiry)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'dob' => 'nullable|date',
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'slot_package_id' => 'nullable|uuid|exists:slot_packages,id',
            'message' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $enquiry->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Enquiry updated successfully',
            'data' => $enquiry,
        ]);
    }

    /**
     * Remove a single enquiry.
     */
    public function destroy(Enquiry $enquiry)
    {
        $enquiry->delete();

        return response()->json([
            'success' => true,
            'message' => 'Enquiry deleted successfully',
        ]);
    }

    /**
     * Remove multiple enquiries.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|uuid|exists:enquiries,id',
        ]);

        $user = auth()->user()->load('library');
        Enquiry::whereIn('id', $validated['ids'])
            ->where('library_id', $user->library?->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Selected enquiries deleted successfully',
        ]);
    }
}

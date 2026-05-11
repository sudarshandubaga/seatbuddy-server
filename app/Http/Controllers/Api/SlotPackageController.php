<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SlotPackage;

class SlotPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $authUser = $request->user()->load('library');
        $packages = SlotPackage::where('library_id', $authUser->library->id)
            ->withCount(['students as active_students' => function ($query) {
                $query->whereNotNull('seat_no');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $packages
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $authUser = $request->user()->load('library');

        $rules = [
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'price' => 'required|numeric|min:0',
            'icon' => 'nullable|string',
            'description' => 'nullable|string',
            'is_full_day' => 'nullable|boolean',
            'is_overnight' => 'nullable|boolean',
            'billing_cycle' => 'nullable|in:daily,weekly,fortnightly,monthly,quarterly,semi_annually,annually',
        ];

        if (!$request->is_full_day && !$request->is_overnight) {
            $rules['end_time'] .= '|after:start_time';
        }

        $request->validate($rules);

        $package = SlotPackage::create([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'price' => $request->price,
            'icon' => $request->icon,
            'description' => $request->description,
            'is_full_day' => $request->is_full_day ?? false,
            'is_overnight' => $request->is_overnight ?? false,
            'billing_cycle' => $request->billing_cycle ?? 'monthly',
            'library_id' => $authUser->library->id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Slot package created successfully',
            'data' => $package
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $authUser = $request->user()->load('library');
        $package = SlotPackage::where('id', $id)
            ->where('library_id', $authUser->library->id)
            ->firstOrFail();

        return response()->json([
            'status' => true,
            'data' => $package
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $authUser = $request->user()->load('library');
        $package = SlotPackage::where('id', $id)
            ->where('library_id', $authUser->library->id)
            ->firstOrFail();

        $rules = [
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'price' => 'required|numeric|min:0',
            'icon' => 'nullable|string',
            'description' => 'nullable|string',
            'is_full_day' => 'nullable|boolean',
            'is_overnight' => 'nullable|boolean',
            'billing_cycle' => 'nullable|in:daily,weekly,fortnightly,monthly,quarterly,semi_annually,annually',
        ];

        if (!$request->is_full_day && !$request->is_overnight) {
            $rules['end_time'] .= '|after:start_time';
        }

        $request->validate($rules);

        $package->update($request->only([
            'name',
            'start_time',
            'end_time',
            'price',
            'icon',
            'description',
            'is_full_day',
            'is_overnight',
            'billing_cycle'
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Slot package updated successfully',
            'data' => $package
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $authUser = $request->user()->load('library');
        $package = SlotPackage::where('id', $id)
            ->where('library_id', $authUser->library->id)
            ->firstOrFail();

        $package->delete();

        return response()->json([
            'status' => true,
            'message' => 'Slot package deleted successfully'
        ]);
    }
}

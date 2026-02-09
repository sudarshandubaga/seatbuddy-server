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
        $packages = SlotPackage::where('library_id', $request->user()->library_id)
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

        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'price' => 'required|numeric|min:0',
            'icon' => 'nullable|string',
            'description' => 'nullable|string'
        ]);

        $package = SlotPackage::create([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'price' => $request->price,
            'icon' => $request->icon,
            'description' => $request->description,
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
        $package = SlotPackage::where('id', $id)
            ->where('library_id', $request->user()->library_id)
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
        $package = SlotPackage::where('id', $id)
            ->where('library_id', $request->user()->library_id)
            ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'price' => 'required|numeric|min:0',
            'icon' => 'nullable|string',
            'description' => 'nullable|string'
        ]);

        $package->update($request->only([
            'name',
            'start_time',
            'end_time',
            'price',
            'icon',
            'description'
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
        $package = SlotPackage::where('id', $id)
            ->where('library_id', $request->user()->library_id)
            ->firstOrFail();

        $package->delete();

        return response()->json([
            'status' => true,
            'message' => 'Slot package deleted successfully'
        ]);
    }
}

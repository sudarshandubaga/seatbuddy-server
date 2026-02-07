<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SeatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        return response()->json(
            Seat::where('library_id', $user->library_id)->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'prefix' => 'nullable|string',
            'from' => 'required|integer',
            'to' => 'required|integer',
        ]);

        $requestedSeats = [];
        for ($i = $request->from; $i <= $request->to; $i++) {
            $requestedSeats[] = ($request->prefix ?? '') . $i;
        }

        $existingSeats = Seat::whereIn('seat_no', $requestedSeats)
            ->where('library_id', $user->library_id)
            ->pluck('seat_no')
            ->toArray();

        if (count($existingSeats) > 0) {
            return response()->json([
                'message' => 'The following seats already exist: ' . implode(', ', $existingSeats),
                'errors' => [
                    'seats' => $existingSeats
                ]
            ], 422);
        }

        $seats = [];
        foreach ($requestedSeats as $seatNo) {
            $seats[] = Seat::create([
                'library_id' => $user->library_id,
                'seat_no' => $seatNo,
            ]);
        }

        return response()->json([
            'message' => 'Seats created successfully',
            'data' => $seats
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Seat $seat)
    {
        return response()->json($seat);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Seat $seat)
    {
        $user = auth()->user();

        $request->validate([
            'seat_no' => [
                'required',
                'string',
                Rule::unique('seats')->where(function ($query) use ($user) {
                    return $query->where('library_id', $user->library_id);
                })->ignore($seat->id),
            ],
        ]);

        $seat->update($request->only('seat_no'));

        return response()->json([
            'message' => 'Seat updated successfully',
            'data' => $seat
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Seat $seat)
    {
        $seat->delete();

        return response()->json([
            'message' => 'Seat deleted successfully'
        ]);
    }
}

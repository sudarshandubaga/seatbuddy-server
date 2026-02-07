<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seat;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SeatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user()->load("library");

        $seats = Seat::where('library_id', $user->library->id)->get();

        // Fetch students with seat assignments for this library
        $students = Student::with('user')
            ->whereNotNull('seat_no')
            ->whereHas('user', function ($q) use ($user) {
                $q->where('library_id', $user->library->id);
            })
            ->get();

        // Map students to seats
        $seats->transform(function ($seat) use ($students) {
            $seatAllocations = $students->where('seat_no', (string) $seat->seat_no);

            $seat->allocations = $seatAllocations->map(function ($student) {
                return [
                    'slot' => 'F', // Default to Full Day as slot info is not yet on Student
                    'studentName' => $student->user->name,
                    'studentId' => $student->user->login_name ?? $student->user->id,
                ];
            })->values();

            return $seat;
        });

        return response()->json($seats);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user()->load("library");

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
            ->where('library_id', $user->library->id)
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
                'library_id' => $user->library->id,
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
        $user = auth()->user()->load("library");

        $request->validate([
            'seat_no' => [
                'required',
                'string',
                Rule::unique('seats')->where(function ($query) use ($user) {
                    return $query->where('library_id', $user->library->id);
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

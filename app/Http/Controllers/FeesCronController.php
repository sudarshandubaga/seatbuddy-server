<?php

namespace App\Http\Controllers;

use App\Models\Fees;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FeesCronController extends Controller
{
    public function store()
    {
        // run daily to insert fees
        $students = Student::with(['slotPackage'])
            ->whereNotNull('seat_no')
            ->where('day_of_billing', Carbon::now()->day)
            ->get();
        $count = 0;
        foreach ($students as $student) {
            if ($student->slotPackage) {
                // Check if fee already exists for this student today
                $exists = Fees::where('student_id', $student->id)
                    ->where('date', Carbon::now()->toDateString())
                    ->exists();

                if (!$exists) {
                    $fees = new Fees();
                    $fees->student_id = $student->id;
                    $fees->amount = $student->slotPackage->price;
                    $fees->date = Carbon::now()->toDateString();
                    $fees->save();
                    $count++;
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Fees generated successfully',
            'count' => $count
        ]);
    }
}

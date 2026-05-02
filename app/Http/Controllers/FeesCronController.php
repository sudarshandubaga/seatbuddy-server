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
        $students = Student::with(['slotPackage', 'library.user', 'user'])
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

                    // Create Notification Entry for Student
                    \App\Models\Notification::create([
                        'user_id' => $student->user_id,
                        'title' => 'Monthly Fee Generated',
                        'description' => 'Your subscription bill for ' . Carbon::now()->format('F Y') . ' of ₹' . number_format($student->slotPackage->price, 2) . ' is now available. Please clear it by this week.',
                        'purpose' => 'fees'
                    ]);

                    // Create Notification Entry for Library Manager
                    if ($student->library && $student->library->user_id) {
                        \App\Models\Notification::create([
                            'user_id' => $student->library->user_id,
                            'title' => 'Fee Generated: ' . $student->user->name,
                            'description' => 'A monthly fee of ₹' . number_format($student->slotPackage->price, 2) . ' has been generated for ' . $student->user->name . ' (Seat: ' . ($student->seat_no ?? 'N/A') . ').',
                            'purpose' => 'fees'
                        ]);
                    }

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

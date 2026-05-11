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
        $students = Student::with(['slotPackage', 'library.user', 'user', 'concession'])
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
                    $originalPrice = $student->slotPackage->price;
                    $finalPrice = $originalPrice;

                    // Apply Concession if exists
                    if ($student->concession) {
                        if ($student->concession->type === 'percentage') {
                            $discount = ($originalPrice * $student->concession->value) / 100;
                            $finalPrice = max(0, $originalPrice - $discount);
                        } elseif ($student->concession->type === 'fixed') {
                            $finalPrice = max(0, $originalPrice - $student->concession->value);
                        }
                    }

                    $fees = new Fees();
                    $fees->student_id = $student->id;
                    $fees->amount = $finalPrice;
                    $fees->date = Carbon::now()->toDateString();
                    $fees->save();

                    // Create Notification Entry for Student
                    \App\Models\Notification::create([
                        'user_id' => $student->user_id,
                        'title' => 'Monthly Fee Generated',
                        'description' => 'Your subscription bill for ' . Carbon::now()->format('F Y') . ' of ₹' . number_format($finalPrice, 2) . ' is now available. Please clear it by this week.',
                        'purpose' => 'fees'
                    ]);

                    // Create Notification Entry for Library Manager
                    if ($student->library && $student->library->user_id) {
                        \App\Models\Notification::create([
                            'user_id' => $student->library->user_id,
                            'title' => 'Fee Generated: ' . $student->user->name,
                            'description' => 'A monthly fee of ₹' . number_format($finalPrice, 2) . ' has been generated for ' . $student->user->name . ' (Seat: ' . ($student->seat_no ?? 'N/A') . ').',
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

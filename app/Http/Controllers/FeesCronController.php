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
        $today = Carbon::now()->startOfDay();
        
        // run daily to insert fees
        $allStudents = Student::with(['slotPackage', 'library.user', 'user', 'concession'])
            ->whereNotNull('seat_no')
            ->get();

        $students = $allStudents->filter(function($student) use ($today) {
            if (!$student->slotPackage) return false;
            
            $cycle = $student->slotPackage->billing_cycle;
            $joinDate = $student->join_date ? Carbon::parse($student->join_date)->startOfDay() : $today;
            $dayOfBilling = (int) ($student->day_of_billing ?? $joinDate->day);

            switch ($cycle) {
                case 'daily':
                    return true;
                case 'weekly':
                    return $today->diffInDays($joinDate) % 7 === 0;
                case 'fortnightly':
                    return $today->diffInDays($joinDate) % 15 === 0;
                case 'monthly':
                    return $today->day === $dayOfBilling;
                case 'quarterly':
                    return $today->day === $dayOfBilling && ($today->diffInMonths($joinDate->copy()->day(1)) % 3 === 0);
                case 'semi_annually':
                    return $today->day === $dayOfBilling && ($today->diffInMonths($joinDate->copy()->day(1)) % 6 === 0);
                case 'annually':
                    return $today->day === $dayOfBilling && ($today->diffInMonths($joinDate->copy()->day(1)) % 12 === 0);
                default:
                    return $today->day === $dayOfBilling;
            }
        });
            
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
                        'title' => 'Subscription Fee Generated',
                        'description' => 'Your subscription bill of ₹' . number_format($finalPrice, 2) . ' is now available. Please clear it by this week.',
                        'purpose' => 'fees'
                    ]);

                    // Create Notification Entry for Library Manager
                    if ($student->library && $student->library->user_id) {
                        \App\Models\Notification::create([
                            'user_id' => $student->library->user_id,
                            'title' => 'Fee Generated: ' . $student->user->name,
                            'description' => 'A subscription fee of ₹' . number_format($finalPrice, 2) . ' has been generated for ' . $student->user->name . ' (Seat: ' . ($student->seat_no ?? 'N/A') . ').',
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

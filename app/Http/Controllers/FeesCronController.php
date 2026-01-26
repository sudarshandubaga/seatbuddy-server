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
        $students = Student::where('day_of_billing', Carbon::now()->day)->get();
        foreach ($students as $student) {
            $fees = new Fees();
            $fees->student_id = $student->id;
            $fees->amount = $student->slot_package->price;
            $fees->date = Carbon::now()->toDateString();
            $fees->save();
        }
    }
}

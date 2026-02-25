<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasUuids;
    protected $fillable = [
        'user_id',
        'library_id',
        'father_name',
        'slot_package_id',
        'notes',
        'seat_no',
        'join_date',
        'day_of_billing',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function slotPackage()
    {
        return $this->belongsTo(SlotPackage::class);
    }

    public function library()
    {
        return $this->belongsTo(Library::class);
    }
    public function fees()
    {
        return $this->hasManyThrough(
            Fees::class,      // Final model
            Student::class,  // Intermediate model
            'user_id',       // Foreign key on students table
            'student_id',    // Foreign key on fees table
            'id',            // Local key on users table
            'id'             // Local key on students table
        );
    }

    public function getDueAmountAttribute()
    {
        return $this->fees()
            ->where('status', 'due')
            ->sum('amount');
    }
}

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
}

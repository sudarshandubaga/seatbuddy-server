<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    use HasUuids;

    protected $fillable = [
        'library_id',
        'name',
        'email',
        'phone',
        'dob',
        'gender',
        'slot_package_id',
        'message',
        'address',
    ];

    public function slotPackage()
    {
        return $this->belongsTo(SlotPackage::class);
    }
}

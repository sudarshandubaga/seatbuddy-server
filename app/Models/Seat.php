<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'library_id',
        'seat_no',
    ];

    public function library()
    {
        return $this->belongsTo(Library::class);
    }
}

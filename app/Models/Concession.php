<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Concession extends Model
{
    use HasUuids;

    protected $fillable = [
        'library_id',
        'name',
        'type',
        'value',
        'description',
    ];

    public function library()
    {
        return $this->belongsTo(Library::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}

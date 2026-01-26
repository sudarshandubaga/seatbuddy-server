<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasUuids;

    protected $fillable = [
        'library_id',
        'title',
        'description',
        'amount',
        'category',
        'date',
    ];
}

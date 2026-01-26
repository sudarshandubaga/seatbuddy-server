<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SubscriptionPlan extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'description' => 'array',
        'is_recommended' => 'boolean',
    ];
}

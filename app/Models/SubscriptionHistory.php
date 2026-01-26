<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SubscriptionHistory extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function library()
    {
        return $this->belongsTo(Library::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}

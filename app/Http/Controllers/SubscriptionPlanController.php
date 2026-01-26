<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        return SubscriptionPlan::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:subscription_plans,name',
            'regular_amount' => 'required|numeric',
            'trade_amount' => 'required|numeric',
            'validity' => 'required|integer', // in months
            'description' => 'nullable|array',
            'is_recommended' => 'boolean',
        ]);

        $plan = SubscriptionPlan::create($validated);

        return response()->json($plan, 201);
    }

    public function show(SubscriptionPlan $subscriptionPlan)
    {
        return $subscriptionPlan;
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $validated = $request->validate([
            'name' => 'string|unique:subscription_plans,name,' . $subscriptionPlan->id,
            'regular_amount' => 'numeric',
            'trade_amount' => 'numeric',
            'validity' => 'integer',
            'description' => 'nullable|array',
            'is_recommended' => 'boolean',
        ]);

        $subscriptionPlan->update($validated);

        return response()->json($subscriptionPlan);
    }

    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->delete();
        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionHistory;
use Illuminate\Http\Request;

class SubscriptionHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = SubscriptionHistory::with(['library', 'plan']);

        if ($request->has('library_id') && !empty($request->library_id)) {
            $query->where('library_id', $request->library_id);
        }

        return response()->json($query->latest()->paginate($request->get('per_page', 10)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'library_id' => 'required|exists:libraries,id',
            'plan_id' => 'required|exists:subscription_plans,id',
            'amount' => 'required|numeric',
            'is_paid' => 'boolean',
        ]);

        $history = \DB::transaction(function () use ($validated) {
            $data = $validated;
            $data['subscription_plan_id'] = $data['plan_id'];
            unset($data['plan_id']);

            $history = SubscriptionHistory::create($data);

            $plan = \App\Models\SubscriptionPlan::find($validated['plan_id']);
            $library = \App\Models\Library::find($validated['library_id']);

            // Calculate new validity
            $currentValidUpto = \Carbon\Carbon::parse($library->valid_upto);
            $now = \Carbon\Carbon::now();

            // If current validity is in the past, start from now. Otherwise key adding to it.
            if ($currentValidUpto->isPast()) {
                $newValidUpto = $now->addMonths($plan->validity);
            } else {
                $newValidUpto = $currentValidUpto->addMonths($plan->validity);
            }

            $library->update(['valid_upto' => $newValidUpto]);

            return $history;
        });

        return response()->json($history, 201);
    }
}

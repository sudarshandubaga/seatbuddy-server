<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Library;
use App\Models\SubscriptionHistory;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;
use Exception;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Create a Razorpay order for renewal or upgrade.
     */
    public function purchase(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'library_id' => 'required|exists:libraries,id',
        ]);

        try {
            $library = Library::findOrFail($validated['library_id']);
            $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

            return DB::transaction(function () use ($library, $plan) {
                // 1. Create Subscription History entry
                $history = SubscriptionHistory::create([
                    'library_id' => $library->id,
                    'subscription_plan_id' => $plan->id,
                    'amount' => $plan->trade_amount,
                    'is_paid' => false,
                ]);

                // 2. Generate Razorpay Order
                $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
                $order = $api->order->create([
                    'receipt' => $history->id,
                    'amount' => $plan->trade_amount * 100, // Amount in paise
                    'currency' => 'INR',
                ]);

                $history->update([
                    'razorpay_order_id' => $order['id'],
                ]);

                return response()->json([
                    'status' => 'success',
                    'order_id' => $order['id'],
                    'amount' => $plan->trade_amount * 100,
                    'key' => config('services.razorpay.key'),
                    'history_id' => $history->id,
                ]);
            });
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to initiate purchase: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify payment and update subscription status.
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'history_id' => 'required|exists:subscription_histories,id',
        ]);

        try {
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            
            $attributes = [
                'razorpay_order_id' => $validated['razorpay_order_id'],
                'razorpay_payment_id' => $validated['razorpay_payment_id'],
                'razorpay_signature' => $validated['razorpay_signature']
            ];

            $api->utility->verifyPaymentSignature($attributes);

            return DB::transaction(function () use ($validated) {
                $history = SubscriptionHistory::findOrFail($validated['history_id']);
                $library = Library::findOrFail($history->library_id);
                $plan = SubscriptionPlan::findOrFail($history->subscription_plan_id);

                // Update History
                $history->update([
                    'is_paid' => true,
                    'razorpay_payment_id' => $validated['razorpay_payment_id'],
                    'razorpay_signature' => $validated['razorpay_signature'],
                ]);

                // Calculate New Validity
                $currentValidUpto = Carbon::parse($library->valid_upto);
                $now = Carbon::now();

                // If expired, start from now. If active, extend from current expiry.
                if ($currentValidUpto->isPast()) {
                    $newValidUpto = $now->addMonths($plan->validity);
                } else {
                    $newValidUpto = $currentValidUpto->addMonths($plan->validity);
                }

                // Update Library
                $library->update([
                    'valid_upto' => $newValidUpto,
                    'subscription_plan_id' => $plan->id,
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Subscription updated successfully.',
                    'valid_upto' => $newValidUpto->toDateTimeString(),
                ]);
            });

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment verification failed: ' . $e->getMessage(),
            ], 400);
        }
    }
}

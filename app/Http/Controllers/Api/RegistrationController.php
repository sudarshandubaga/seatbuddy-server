<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Library;
use App\Models\SubscriptionHistory;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Razorpay\Api\Api;
use Exception;

class RegistrationController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'library_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|string|max:15',
            'code' => 'required|string|max:4',
            'user_suffix' => 'required|string|max:4',
            'password' => 'required|string|min:6',
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                // 1. Create User
                $user = User::create([
                    'name' => $validated['owner_name'],
                    'email' => $validated['email'],
                    'phone' => $validated['mobile'],
                    'login_name' => $validated['code'] . $validated['user_suffix'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'library',
                    'library_code' => $validated['code'],
                ]);

                // 2. Create Library (pending)
                $library = Library::create([
                    'name' => $validated['library_name'],
                    'address' => 'Pending', // Will be updated later or provided in registration
                    'latitude' => 0,
                    'longitude' => 0,
                    'phone' => $validated['mobile'],
                    'email' => $validated['email'],
                    'code' => $validated['code'],
                    'user_id' => $user->id,
                    'valid_upto' => now(), // Initial
                ]);

                // 3. Get Plan
                $plan = SubscriptionPlan::find($validated['plan_id']);

                // 4. Create Subscription History (pending payment)
                $history = SubscriptionHistory::create([
                    'library_id' => $library->id,
                    'subscription_plan_id' => $plan->id,
                    'amount' => $plan->trade_amount,
                    'is_paid' => false,
                ]);

                // 5. Create Razorpay Order if not a free trial (or if trade_amount > 0)
                if ($plan->trade_amount > 0) {
                    $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
                    $order = $api->order->create([
                        'receipt' => $history->id,
                        'amount' => $plan->trade_amount * 100, // in paise
                        'currency' => 'INR',
                    ]);

                    $history->update([
                        'razorpay_order_id' => $order['id'],
                    ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Registration successful. Please complete payment.',
                        'order_id' => $order['id'],
                        'amount' => $plan->trade_amount * 100,
                        'key' => env('RAZORPAY_KEY_ID'),
                        'user' => $user,
                        'history_id' => $history->id,
                    ]);
                }

                // If free trial or 0 amount, activate immediately
                $library->update([
                    'valid_upto' => now()->addMonths($plan->validity),
                    'subscription_plan_id' => $plan->id,
                ]);
                $history->update(['is_paid' => true]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Registration successful. Free trial activated.',
                    'user' => $user,
                ]);
            });
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        $validated = $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'history_id' => 'required|exists:subscription_histories,id',
        ]);

        try {
            $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));

            $attributes = [
                'razorpay_order_id' => $validated['razorpay_order_id'],
                'razorpay_payment_id' => $validated['razorpay_payment_id'],
                'razorpay_signature' => $validated['razorpay_signature']
            ];

            $api->utility->verifyPaymentSignature($attributes);

            $history = SubscriptionHistory::find($validated['history_id']);
            $history->update([
                'is_paid' => true,
                'razorpay_payment_id' => $validated['razorpay_payment_id'],
                'razorpay_signature' => $validated['razorpay_signature'],
            ]);

            $library = Library::find($history->library_id);
            $plan = SubscriptionPlan::find($history->subscription_plan_id);

            // Update library validity
            $newValidity = now()->addMonths($plan->validity);
            $library->update([
                'valid_upto' => $newValidity,
                'subscription_plan_id' => $plan->id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment verified and subscription activated.',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment verification failed: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function checkUniqueness(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:4',
            'user_suffix' => 'required|string|max:4',
        ]);

        $code = $request->code;
        $loginName = $code . $request->user_suffix;

        $codeExists = Library::where('code', $code)->exists();
        $loginNameExists = User::where('login_name', $loginName)->exists();

        if ($codeExists) {
            return response()->json([
                'status' => false,
                'message' => 'The Library Code is already taken.',
                'field' => 'code'
            ], 422);
        }

        if ($loginNameExists) {
            return response()->json([
                'status' => false,
                'message' => 'The User ID (Code + Suffix) is already taken.',
                'field' => 'user_id'
            ], 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'Available'
        ]);
    }
}

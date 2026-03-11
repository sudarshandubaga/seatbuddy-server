<?php

namespace App\Http\Controllers;

use App\Models\Library;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\SubscriptionHistory;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index()
    {
        return Library::with('user')->latest()->get();
    }

    public function store(Request $request)
    {
        $createNewUser = $request->boolean('create_new_user');

        $rules = [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'phone' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'valid_upto' => 'required|date',
            'code' => 'required|string|unique:libraries,code|max:4',
            'logo' => 'nullable|image|max:2048',
            'no_of_tables' => 'nullable|integer',
            'plan_id' => 'required|exists:subscription_plans,id',
        ];

        if ($createNewUser) {
            $rules['owner_name'] = 'required|string|max:255';
            $rules['password'] = 'required|string|min:6';
            $rules['user_suffix'] = 'required|string|max:4';
        } else {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $validated, $createNewUser) {
            $userId = $validated['user_id'] ?? null;

            if ($createNewUser) {
                $loginName = $validated['code'] . $validated['user_suffix'];
                
                // Double check login name uniqueness
                if (\App\Models\User::where('login_name', $loginName)->exists()) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'user_suffix' => ['The generated User ID (Code + Suffix) is already taken.'],
                    ]);
                }

                $user = \App\Models\User::create([
                    'name' => $validated['owner_name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'login_name' => $loginName,
                    'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
                    'role' => 'library',
                    'library_code' => $validated['code'],
                ]);
                $userId = $user->id;
            }

            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('libraries', 'public');
                $validated['logo'] = $path;
            }

            $library = \App\Models\Library::create([
                'name' => $validated['name'],
                'address' => $validated['address'],
                'latitude' => $validated['latitude'] ?? 0,
                'longitude' => $validated['longitude'] ?? 0,
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'valid_upto' => $validated['valid_upto'],
                'code' => $validated['code'],
                'user_id' => $userId,
                'logo' => $validated['logo'] ?? null,
                'no_of_tables' => $validated['no_of_tables'] ?? 0,
                'subscription_plan_id' => $validated['plan_id'],
            ]);

            // Create a sub record
            \App\Models\SubscriptionHistory::create([
                'library_id' => $library->id,
                'subscription_plan_id' => $validated['plan_id'],
                'amount' => \App\Models\SubscriptionPlan::find($validated['plan_id'])->trade_amount,
                'is_paid' => true,
            ]);

            return response()->json($library->load('user'), 201);
        });
    }

    public function show(Library $library)
    {
        return $library->load('user');
    }

    public function update(Request $request, Library $library)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'address' => 'string',
            'latitude' => 'numeric',
            'longitude' => 'numeric',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'valid_upto' => 'date',
            'code' => 'string|unique:libraries,code,' . $library->id,
            'user_id' => 'exists:users,id',
            'logo' => 'nullable|image|max:2048',
            'no_of_tables' => 'nullable|integer',
            'plan_id' => 'nullable|exists:subscription_plans,id',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('libraries', 'public');
            $validated['logo'] = $path;
        }

        if (isset($validated['plan_id'])) {
            $validated['subscription_plan_id'] = $validated['plan_id'];
            unset($validated['plan_id']);
        }

        $library->update($validated);

        return response()->json($library->load('user'));
    }

    public function destroy(Library $library)
    {
        $library->delete();
        return response()->noContent();
    }

    public function getSubscriptionPlans()
    {
        return SubscriptionPlan::all();
    }

    public function updateNoOfSeats(Request $request, Library $library)
    {
        $validated = $request->validate([
            'no_of_tables' => 'required|integer',
        ]);

        $library->update($validated);

        return response()->json($library);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    private function storeImage($base64Image)
    {
        if (!$base64Image || !preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            return $base64Image;
        }

        $image = substr($base64Image, strpos($base64Image, ',') + 1);
        $type = strtolower($type[1]);

        if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
            return $base64Image;
        }

        $image = str_replace(' ', '+', $image);
        $imageName = strtolower(Str::random(10)) . '.' . $type;

        $directory = public_path('uploads/students');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($directory . '/' . $imageName, base64_decode($image));

        return url('uploads/students/' . $imageName);
    }

    // 🔹 LIST
    public function index(Request $request)
    {
        $user = auth()->user()->load('library');
        $query = User::with(['student', 'student.slotPackage'])
            ->whereHas('student', function ($q) use ($user, $request) {
                if ($user->role === 'admin') {
                    if ($request->has('library_id')) {
                        $q->where('library_id', $request->library_id);
                    }
                } else {
                    $q->where('library_id', $user->library->id);
                }
                if ($request->has('unallocated') && $request->unallocated == 'true') {
                    $q->whereNull('seat_no');
                }
                if ($request->has('slot_package_id') && $request->slot_package_id) {
                    $q->where('slot_package_id', $request->slot_package_id);
                }
            })
            ->where(function ($q) use ($request) {
                if ($request->status === 'deactivated') {
                    $q->where('is_active', false);
                } else {
                    $q->where('is_active', true);
                }
            })
            ->where('role', 'student');

        return response()->json([
            'status' => true,
            'data' => $query->get()
        ]);
    }

    // 🔹 STORE
    public function store(Request $request)
    {
        $authUser = auth()->user()->load('library');

        $request->validate([
            'name' => 'required|string',
            'login_name' => 'required|unique:users,login_name',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'phone' => 'nullable',
            'father_name' => 'nullable',
            'slot_package_id' => 'nullable|exists:slot_packages,id',
            'notes' => 'nullable',
            'day_of_billing' => 'nullable',
            'join_date' => 'nullable|date',
            'address' => 'nullable',
            'image' => 'nullable',
            'gender' => 'nullable|in:male,female,other',
        ]);

        $libraryCode = $authUser->library->code;
        $loginName = $request->login_name;

        // Ensure login_name starts with library code
        if (!\Illuminate\Support\Str::startsWith($loginName, $libraryCode)) {
            $loginName = $libraryCode . $loginName;
        }

        // Secondary check for uniqueness after prefixing
        if (\App\Models\User::where('login_name', $loginName)->exists()) {
            return response()->json([
                'message' => 'The login name (with prefix) has already been taken.',
                'errors' => ['login_name' => ['The login name has already been taken.']]
            ], 422);
        }

        $user = User::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'login_name' => $loginName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'student',
            'library_code' => $libraryCode,
            'address' => $request->address,
            'image' => $this->storeImage($request->image),
            'gender' => $request->gender,
        ]);

        // Validate seat allocation conflicts with full-day slot packages
        if ($request->seat_no && $request->slot_package_id) {
            $slotPackage = \App\Models\SlotPackage::find($request->slot_package_id);
            if ($slotPackage && $slotPackage->is_full_day) {
                // Full-day slot: ensure no other non-null slot packages are on the same seat
                $conflictingStudent = Student::where('seat_no', $request->seat_no)
                    ->where('library_id', $authUser->library->id)
                    ->whereNotNull('slot_package_id')
                    ->first();
                if ($conflictingStudent) {
                    return response()->json([
                        'message' => 'Cannot allocate full-day slot. The seat already has a student with a reserved slot.',
                        'errors' => ['seat_no' => ['Seat already occupied by another slot allocation.']]
                    ], 422);
                }
            } else {
                // Non-full-day slot: ensure no full-day slot on the same seat
                $conflictingStudent = Student::where('seat_no', $request->seat_no)
                    ->where('library_id', $authUser->library->id)
                    ->whereHas('slotPackage', function ($q) {
                        $q->where('is_full_day', true);
                    })
                    ->first();
                if ($conflictingStudent) {
                    return response()->json([
                        'message' => 'Cannot allocate this slot. The seat already has a full-day slot allocated.',
                        'errors' => ['seat_no' => ['Seat already occupied by a full-day slot allocation.']]
                    ], 422);
                }
            }
        } elseif ($request->seat_no && !$request->slot_package_id) {
            // Unreserved (null slot_package_id): check if seat has a full-day slot
            $conflictingStudent = Student::where('seat_no', $request->seat_no)
                ->where('library_id', $authUser->library->id)
                ->whereHas('slotPackage', function ($q) {
                    $q->where('is_full_day', true);
                })
                ->first();
            if ($conflictingStudent) {
                return response()->json([
                    'message' => 'Cannot allocate unreserved slot. The seat already has a full-day slot allocated.',
                    'errors' => ['seat_no' => ['Seat already occupied by a full-day slot allocation.']]
                ], 422);
            }
        }

        $student = Student::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'library_id' => $authUser->library->id,
            'father_name' => $request->father_name,
            'slot_package_id' => $request->slot_package_id,
            'notes' => $request->notes,
            'day_of_billing' => $request->day_of_billing,
            'join_date' => $request->join_date,
            'seat_no' => $request->seat_no,
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\StudentCredentialsMail($user, $request->password));
        } catch (\Exception $e) {
            // Log failure or continue
        }

        return response()->json([
            'message' => 'Student created successfully',
            'data' => $student->load('user')
        ], 201);
    }

    // 🔹 SHOW
    public function show($id)
    {
        $student = Student::with('user')->findOrFail($id);

        return response()->json($student);
    }

    // 🔹 UPDATE
    public function update(Request $request, $id)
    {
        $student = Student::with('user')->findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required',
            'email' => 'sometimes|required|email|unique:users,email,' . $student->user_id,
            'password' => 'sometimes|nullable|min:6',
            'phone' => 'nullable',
            'father_name' => 'nullable',
            'notes' => 'nullable',
            'day_of_billing' => 'nullable',
            'join_date' => 'nullable|date',
            'seat_no' => 'nullable',
            'address' => 'nullable',
            'image' => 'nullable',
            'gender' => 'nullable|in:male,female,other',
        ]);

        // Validate seat allocation conflicts with full-day slot packages on update
        if ($request->seat_no && $request->slot_package_id) {
            $slotPackage = \App\Models\SlotPackage::find($request->slot_package_id);
            if ($slotPackage && $slotPackage->is_full_day) {
                // Full-day slot: ensure no other non-null slot packages are on the same seat (excluding current student)
                $conflictingStudent = Student::where('seat_no', $request->seat_no)
                    ->where('library_id', $student->library_id)
                    ->where('id', '!=', $student->id)
                    ->whereNotNull('slot_package_id')
                    ->first();
                if ($conflictingStudent) {
                    return response()->json([
                        'message' => 'Cannot allocate full-day slot. The seat already has a student with a reserved slot.',
                        'errors' => ['seat_no' => ['Seat already occupied by another slot allocation.']]
                    ], 422);
                }
            } else {
                // Non-full-day slot: ensure no full-day slot on the same seat (excluding current student)
                $conflictingStudent = Student::where('seat_no', $request->seat_no)
                    ->where('library_id', $student->library_id)
                    ->where('id', '!=', $student->id)
                    ->whereHas('slotPackage', function ($q) {
                        $q->where('is_full_day', true);
                    })
                    ->first();
                if ($conflictingStudent) {
                    return response()->json([
                        'message' => 'Cannot allocate this slot. The seat already has a full-day slot allocated.',
                        'errors' => ['seat_no' => ['Seat already occupied by a full-day slot allocation.']]
                    ], 422);
                }
            }
        } elseif ($request->seat_no && !$request->slot_package_id) {
            // Unreserved (null slot_package_id): check if seat has a full-day slot (excluding current student)
            $conflictingStudent = Student::where('seat_no', $request->seat_no)
                ->where('library_id', $student->library_id)
                ->where('id', '!=', $student->id)
                ->whereHas('slotPackage', function ($q) {
                    $q->where('is_full_day', true);
                })
                ->first();
            if ($conflictingStudent) {
                return response()->json([
                    'message' => 'Cannot allocate unreserved slot. The seat already has a full-day slot allocated.',
                    'errors' => ['seat_no' => ['Seat already occupied by a full-day slot allocation.']]
                ], 422);
            }
        }

        $userData = $request->only('name', 'email', 'phone', 'address', 'gender');
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        if ($request->has('image')) {
            $userData['image'] = $this->storeImage($request->image);
        }
        $student->user->update($userData);

        $student->update($request->only('father_name', 'notes', 'slot_package_id', 'day_of_billing', 'join_date', 'seat_no'));

        return response()->json([
            'message' => 'Student updated successfully',
            'data' => $student->load('user')
        ]);
    }

    // 🔹 DELETE
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->user()->delete(); // cascade delete student

        return response()->json([
            'status' => true,
            'message' => 'Student deleted successfully'
        ]);
    }

    public function toggleStatus($id)
    {
        $student = Student::with('user')->findOrFail($id);
        $student->user->update([
            'is_active' => !$student->user->is_active
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Student status updated',
            'is_active' => $student->user->is_active
        ]);
    }
}

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
                $q->where('library_id', $user->library->id);
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

        return response()->json($query->get());
    }

    // 🔹 STORE
    public function store(Request $request)
    {
        $authUser = auth()->user()->load('library');

        $request->validate([
            'name' => 'required|string',
            'login_name' => 'required|unique:users,login_name',
            'email' => 'required|email|unique:users,email',
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

        $user = User::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'login_name' => $request->login_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'student',
            'library_id' => $authUser->library->id,
            'address' => $request->address,
            'image' => $this->storeImage($request->image),
            'gender' => $request->gender,
        ]);

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

        $userData = $request->only('name', 'email', 'phone', 'address', 'gender');
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

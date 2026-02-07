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
    // 🔹 LIST
    public function index()
    {
        $user = auth()->user();
        return response()->json(
            User::with('student')->where('library_id', $user->library_id)->where('role', 'student')->get()
        );
    }

    // 🔹 STORE
    public function store(Request $request)
    {
        $authUser = auth()->user();

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
            'address' => 'nullable',
        ]);

        $user = User::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'login_name' => $request->login_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'student',
            'library_id' => $authUser->library_id,
            'address' => $request->address,
        ]);

        $student = Student::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'library_id' => $authUser->library_id,
            'father_name' => $request->father_name,
            'slot_package_id' => $request->slot_package_id,
            'notes' => $request->notes,
            'day_of_billing' => $request->day_of_billing,
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
        ]);

        $student->user->update($request->only('name', 'email', 'phone'));

        $student->update($request->only('father_name', 'notes', 'slot_package_id', 'day_of_billing'));

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
}

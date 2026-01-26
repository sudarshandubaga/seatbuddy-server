<?php

namespace App\Http\Controllers;

use App\Models\Library;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index()
    {
        return Library::with('user')->latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'valid_upto' => 'required|date',
            'code' => 'required|string|unique:libraries,code',
            'user_id' => 'required|exists:users,id',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('libraries', 'public');
            $validated['logo'] = $path;
        }

        $library = Library::create($validated);

        return response()->json($library, 201);
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
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('libraries', 'public');
            $validated['logo'] = $path;
        }

        $library->update($validated);

        return response()->json($library);
    }

    public function destroy(Library $library)
    {
        $library->delete();
        return response()->noContent();
    }
}

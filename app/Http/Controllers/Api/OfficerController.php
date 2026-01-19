<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Officer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class OfficerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Officer::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $officers = $query->paginate(15);
        return response()->json($officers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:officers,email',
            'password' => ['required', Password::min(8)],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $officer = Officer::create($validated);

        return response()->json($officer, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $officer = Officer::findOrFail($id);
        return response()->json($officer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $officer = Officer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:officers,email,' . $id,
            'password' => ['sometimes', Password::min(8)],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $officer->update($validated);

        return response()->json($officer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $officer = Officer::findOrFail($id);
        $officer->delete();

        return response()->json(['message' => 'Officer deleted successfully'], 200);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserInfo;
use Illuminate\Http\Request;

class UserInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = UserInfo::with('user');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('phone', 'like', "%{$search}%");
        }

        $userInfos = $query->paginate(15);
        return response()->json($userInfos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_user' => 'required|uuid|exists:users,id|unique:user_info,id_user',
            'phone' => 'required|string|max:255',
            'birthday' => 'required|date',
        ]);

        $userInfo = UserInfo::create($validated);

        return response()->json($userInfo->load('user'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $userInfo = UserInfo::with('user')->findOrFail($id);
        return response()->json($userInfo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $userInfo = UserInfo::findOrFail($id);

        $validated = $request->validate([
            'id_user' => 'sometimes|uuid|exists:users,id|unique:user_info,id_user,' . $id,
            'phone' => 'sometimes|string|max:255',
            'birthday' => 'sometimes|date',
        ]);

        $userInfo->update($validated);

        return response()->json($userInfo->load('user'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $userInfo = UserInfo::findOrFail($id);
        $userInfo->delete();

        return response()->json(['message' => 'User info deleted successfully'], 200);
    }
}

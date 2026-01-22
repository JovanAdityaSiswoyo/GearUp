<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Officer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        // Get data from all three tables
        $users = User::with('roles')->get()->map(function($user) {
            $user->user_type = 'user';
            return $user;
        });
        
        $admins = Admin::with('roles')->get()->map(function($admin) {
            $admin->user_type = 'admin';
            return $admin;
        });
        
        $officers = Officer::with('roles')->get()->map(function($officer) {
            $officer->user_type = 'officer';
            return $officer;
        });
        
        // Merge all collections
        $allUsers = $users->concat($admins)->concat($officers);
        
        // Filter by role/type
        if ($request->filled('role')) {
            $allUsers = $allUsers->filter(function($user) use ($request) {
                return $user->user_type === $request->role;
            });
        }
        
        // Search by name or email
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $allUsers = $allUsers->filter(function($user) use ($search) {
                return str_contains(strtolower($user->name), $search) || 
                       str_contains(strtolower($user->email), $search);
            });
        }
        
        // Sort by created_at descending
        $allUsers = $allUsers->sortByDesc('created_at');
        
        // Manual pagination
        $perPage = 5;
        $currentPage = $request->get('page', 1);
        $total = $allUsers->count();
        $users = new \Illuminate\Pagination\LengthAwarePaginator(
            $allUsers->forPage($currentPage, $perPage),
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'user_type' => 'required|in:user,admin,officer',
        ]);

        // Check email uniqueness across all three tables
        $emailExists = User::where('email', $validated['email'])->exists() ||
                      Admin::where('email', $validated['email'])->exists() ||
                      Officer::where('email', $validated['email'])->exists();
        
        if ($emailExists) {
            return back()->withErrors(['email' => 'The email has already been taken.'])->withInput();
        }

        $validated['password'] = Hash::make($validated['password']);
        $userType = $validated['user_type'];
        unset($validated['user_type']);
        
        // Remove phone for admin and officer
        if ($userType !== 'user') {
            unset($validated['phone']);
        }

        // Create in appropriate table
        if ($userType === 'admin') {
            $user = Admin::create($validated);
            $user->assignRole('admin');
        } elseif ($userType === 'officer') {
            $user = Officer::create($validated);
            $user->assignRole('officer');
        } else {
            $user = User::create($validated);
            $user->assignRole('user');
        }

        alert()->success('Success', ucfirst($userType) . ' created successfully!');
        return redirect()->route('admin.users.index');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:20',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        alert()->success('Success', 'User updated successfully!');
        return redirect()->route('admin.users.index');
    }

    public function destroy(Request $request, $id)
    {
        // Determine which table to delete from
        $type = $request->input('type', 'user');
        
        if ($type === 'admin') {
            $user = Admin::findOrFail($id);
        } elseif ($type === 'officer') {
            $user = Officer::findOrFail($id);
        } else {
            $user = User::findOrFail($id);
        }
        
        $user->delete();
        alert()->success('Deleted', ucfirst($type) . ' deleted successfully!');
        return redirect()->route('admin.users.index');
    }
}

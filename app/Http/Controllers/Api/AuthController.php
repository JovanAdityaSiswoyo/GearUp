<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        ActivityLog::create([
            'log_name' => 'auth',
            'description' => 'Register API berhasil: ' . ($user->email ?? 'unknown'),
            'subject_type' => get_class($user),
            'subject_id' => (string) $user->id,
            'causer_type' => get_class($user),
            'causer_id' => (string) $user->id,
            'event' => 'register',
            'properties' => [
                'guard' => 'sanctum',
                'email' => $user->email ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        ]);

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Login user and create token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        ActivityLog::create([
            'log_name' => 'auth',
            'description' => 'Login API berhasil: ' . ($user->email ?? 'unknown'),
            'subject_type' => get_class($user),
            'subject_id' => (string) $user->id,
            'causer_type' => get_class($user),
            'causer_id' => (string) $user->id,
            'event' => 'login',
            'properties' => [
                'guard' => 'sanctum',
                'email' => $user->email ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        ]);

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request)
    {
        $actor = $request->user();

        $token = $actor?->currentAccessToken();
        if ($token) {
            $token->delete();
        }

        if ($actor) {
            ActivityLog::create([
                'log_name' => 'auth',
                'description' => 'Logout API berhasil: ' . ($actor->email ?? 'unknown'),
                'subject_type' => get_class($actor),
                'subject_id' => (string) $actor->id,
                'causer_type' => get_class($actor),
                'causer_id' => (string) $actor->id,
                'event' => 'logout',
                'properties' => [
                    'guard' => 'sanctum',
                    'email' => $actor->email ?? null,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
            ]);
        }

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        return response()->json($request->user()->load('userInfo'));
    }
}

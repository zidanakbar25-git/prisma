<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login API menggunakan username dan password.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('username', $validated['username'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Username atau password salah.',
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Akun Anda sedang dinonaktifkan.',
            ], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Login API',
            'description' => 'Pengguna "' . $user->name . '" berhasil login melalui API.',
        ]);

        return response()->json([
            'message' => 'Login API berhasil.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'role' => $user->role,
                'is_active' => $user->is_active,
            ],
        ], 200);
    }

    /**
     * Logout API dengan mencabut token Sanctum saat ini.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Logout API',
            'description' => 'Pengguna "' . $user->name
                . '" berhasil logout melalui API.',
        ]);

        $currentToken = $user->currentAccessToken();

        if ($currentToken) {
            $currentToken->delete();
        }

        return response()->json([
            'message' => 'Logout API berhasil.',
        ]);
    }
}

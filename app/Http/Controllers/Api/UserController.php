<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Memastikan hanya Kabag yang dapat mengelola pengguna.
     */
    private function authorizeKabag(Request $request): ?JsonResponse
    {
        if ($request->user()->role !== 'kabag') {
            return response()->json([
                'message' => 'Hanya Kabag yang dapat mengelola pengguna.',
            ], 403);
        }

        return null;
    }

    /**
     * Menampilkan daftar pengguna.
     */
    public function index(Request $request): JsonResponse
    {
        if ($response = $this->authorizeKabag($request)) {
            return $response;
        }

        $users = User::orderBy('name')
            ->get()
            ->map(fn (User $user) => $this->formatUser($user));

        return response()->json([
            'message' => 'Data pengguna berhasil diambil.',
            'data' => $users,
        ]);
    }

    /**
     * Menampilkan detail pengguna.
     */
    public function show(Request $request, User $user): JsonResponse
    {
        if ($response = $this->authorizeKabag($request)) {
            return $response;
        }

        return response()->json([
            'message' => 'Detail pengguna berhasil diambil.',
            'data' => $this->formatUser($user),
        ]);
    }

    /**
     * Menambahkan pengguna baru.
     */
    public function store(Request $request): JsonResponse
    {
        if ($response = $this->authorizeKabag($request)) {
            return $response;
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'role' => ['required', Rule::in(['kabag', 'staff', 'intern'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $user = DB::transaction(function () use ($validated, $request) {
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'role' => $validated['role'],
                'password' => $validated['password'],
                'is_active' => true,
            ]);

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'Tambah Pengguna API',
                'description' => 'Menambahkan pengguna "'
                    . $user->name . '" dengan username '
                    . $user->username . ' melalui API.',
            ]);

            return $user;
        });

        return response()->json([
            'message' => 'Pengguna berhasil ditambahkan.',
            'data' => $this->formatUser($user),
        ], 201);
    }

    /**
     * Mengubah data pengguna.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        if ($response = $this->authorizeKabag($request)) {
            return $response;
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'role' => ['required', Rule::in(['kabag', 'staff', 'intern'])],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $oldName = $user->name;

        DB::transaction(function () use ($user, $validated, $request, $oldName) {
            $user->update([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'role' => $validated['role'],
            ]);

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'Edit Pengguna API',
                'description' => 'Mengubah data pengguna "'
                    . $oldName . '" melalui API.',
            ]);
        });

        return response()->json([
            'message' => 'Data pengguna berhasil diperbarui.',
            'data' => $this->formatUser($user->fresh()),
        ]);
    }

    /**
     * Mereset password pengguna.
     */
    public function resetPassword(Request $request, User $user): JsonResponse
    {
        if ($response = $this->authorizeKabag($request)) {
            return $response;
        }

        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->update([
            'password' => $validator->validated()['password'],
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'Reset Password API',
            'description' => 'Mereset password pengguna "'
                . $user->name . '" melalui API.',
        ]);

        return response()->json([
            'message' => 'Password pengguna berhasil direset.',
        ]);
    }

    /**
     * Mengaktifkan atau menonaktifkan akun.
     */
    public function toggleStatus(Request $request, User $user): JsonResponse
    {
        if ($response = $this->authorizeKabag($request)) {
            return $response;
        }

        if ($request->user()->id === $user->id) {
            return response()->json([
                'message' => 'Anda tidak dapat menonaktifkan akun sendiri.',
            ], 422);
        }

        $newStatus = !$user->is_active;

        DB::transaction(function () use ($user, $newStatus, $request) {
            $user->update([
                'is_active' => $newStatus,
            ]);

            // Hapus token API agar perubahan status langsung berlaku
            // untuk sesi API yang menggunakan Sanctum token.
            if (!$newStatus) {
                $user->tokens()->delete();
            }

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => $newStatus
                    ? 'Aktifkan Pengguna API'
                    : 'Nonaktifkan Pengguna API',
                'description' => ($newStatus
                    ? 'Mengaktifkan'
                    : 'Menonaktifkan')
                    . ' akun pengguna "' . $user->name . '" melalui API.',
            ]);
        });

        return response()->json([
            'message' => $newStatus
                ? 'Akun pengguna berhasil diaktifkan.'
                : 'Akun pengguna berhasil dinonaktifkan.',
            'data' => $this->formatUser($user->fresh()),
        ]);
    }

    /**
     * Menyaring data sensitif dari response.
     */
    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'role' => $user->role,
            'is_active' => (bool) $user->is_active,
            'created_at' => $user->created_at?->toISOString(),
            'updated_at' => $user->updated_at?->toISOString(),
        ];
    }
}

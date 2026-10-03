<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        abort_unless(
            auth()->user()->role === 'kabag',
            403
        );

        $users = User::orderBy('role')
            ->orderBy('name')
            ->get();

        return view(
            'users.index',
            compact('users')
        );
    }


    public function create()
    {
        abort_unless(
            auth()->user()->role === 'kabag',
            403
        );

        return view('users.create');
    }


    public function store(Request $request)
    {
        abort_unless(
            auth()->user()->role === 'kabag',
            403
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'username' => [
                'required',
                'string',
                'max:255',
                'unique:users,username',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
            ],

            'role' => [
                'required',
                'in:kabag,staff,intern',
            ],
        ], [
            'name.required' =>
                'Nama wajib diisi.',

            'name.max' =>
                'Nama maksimal 255 karakter.',

            'username.required' =>
                'Username wajib diisi.',

            'username.max' =>
                'Username maksimal 255 karakter.',

            'username.unique' =>
                'Username tersebut sudah digunakan.',

            'password.required' =>
                'Password wajib diisi.',

            'password.min' =>
                'Password minimal 8 karakter.',

            'role.required' =>
                'Role wajib dipilih.',

            'role.in' =>
                'Role yang dipilih tidak valid.',
        ]);

        $user = User::create([
            'name' =>
                $validated['name'],

            'username' =>
                $validated['username'],

            'password' =>
                $validated['password'],

            'role' =>
                $validated['role'],

            'is_active' =>
                true,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Audit Log
        |--------------------------------------------------------------------------
        */

        ActivityLog::create([
            'user_id' =>
                auth()->id(),

            'action' =>
                'Tambah Pengguna',

            'description' =>
                'Menambahkan pengguna "' .
                $user->name .
                '" dengan username "' .
                $user->username .
                '" dan role ' .
                $user->role .
                '.',
        ]);


        return redirect()
            ->route('users.index')
            ->with(
                'success',
                'Pengguna berhasil ditambahkan.'
            );
    }


    public function edit(User $user)
    {
        abort_unless(
            auth()->user()->role === 'kabag',
            403
        );

        return view(
            'users.edit',
            compact('user')
        );
    }


    public function update(
        Request $request,
        User $user
    ) {
        abort_unless(
            auth()->user()->role === 'kabag',
            403
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique(
                    'users',
                    'username'
                )->ignore($user->id),
            ],

            'role' => [
                'required',
                'in:kabag,staff,intern',
            ],
        ], [
            'name.required' =>
                'Nama wajib diisi.',

            'name.max' =>
                'Nama maksimal 255 karakter.',

            'username.required' =>
                'Username wajib diisi.',

            'username.max' =>
                'Username maksimal 255 karakter.',

            'username.unique' =>
                'Username tersebut sudah digunakan.',

            'role.required' =>
                'Role wajib dipilih.',

            'role.in' =>
                'Role yang dipilih tidak valid.',
        ]);


        $oldName = $user->name;
        $oldUsername = $user->username;
        $oldRole = $user->role;


        $user->update([
            'name' =>
                $validated['name'],

            'username' =>
                $validated['username'],

            'role' =>
                $validated['role'],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Audit Log
        |--------------------------------------------------------------------------
        */

        ActivityLog::create([
            'user_id' =>
                auth()->id(),

            'action' =>
                'Edit Pengguna',

            'description' =>
                'Mengubah data pengguna "' .
                $oldName .
                '" menjadi "' .
                $user->name .
                '".',
        ]);


        return redirect()
            ->route('users.index')
            ->with(
                'success',
                'Data pengguna berhasil diperbarui.'
            );
    }


    public function resetPassword(
        Request $request,
        User $user
    ) {
        abort_unless(
            auth()->user()->role === 'kabag',
            403
        );

        $validated = $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
            ],

            'password_confirmation' => [
                'required',
                'same:password',
            ],
        ], [
            'password.required' =>
                'Password baru wajib diisi.',

            'password.min' =>
                'Password minimal 8 karakter.',

            'password_confirmation.required' =>
                'Konfirmasi password wajib diisi.',

            'password_confirmation.same' =>
                'Konfirmasi password tidak sama.',
        ]);


        $user->update([
            'password' =>
                $validated['password'],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Audit Log
        |--------------------------------------------------------------------------
        */

        ActivityLog::create([
            'user_id' =>
                auth()->id(),

            'action' =>
                'Reset Password',

            'description' =>
                'Mereset password pengguna "' .
                $user->name .
                '".',
        ]);


        return redirect()
            ->route('users.index')
            ->with(
                'success',
                'Password pengguna berhasil direset.'
            );
    }


    public function toggleStatus(User $user)
    {
        abort_unless(
            auth()->user()->role === 'kabag',
            403
        );


        if ($user->id === auth()->id()) {

            return redirect()
                ->route('users.index')
                ->with(
                    'error',
                    'Anda tidak dapat menonaktifkan akun sendiri.'
                );
        }


        $oldStatus = $user->is_active;

        $user->update([
            'is_active' =>
                !$user->is_active,
        ]);


        $status = $user->is_active
            ? 'mengaktifkan'
            : 'menonaktifkan';


        /*
        |--------------------------------------------------------------------------
        | Audit Log
        |--------------------------------------------------------------------------
        */

        ActivityLog::create([
            'user_id' =>
                auth()->id(),

            'action' =>
                $user->is_active
                    ? 'Aktifkan Pengguna'
                    : 'Nonaktifkan Pengguna',

            'description' =>
                'Berhasil ' .
                $status .
                ' akun pengguna "' .
                $user->name .
                '".',
        ]);


        $message = $user->is_active
            ? 'Pengguna berhasil diaktifkan.'
            : 'Pengguna berhasil dinonaktifkan.';


        return redirect()
            ->route('users.index')
            ->with(
                'success',
                $message
            );
    }
}
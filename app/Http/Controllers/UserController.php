<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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

        User::create([
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

        return redirect()
            ->route('users.index')
            ->with(
                'success',
                'Pengguna berhasil ditambahkan.'
            );
    }
}
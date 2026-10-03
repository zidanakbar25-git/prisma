<?php

namespace App\Http\Controllers;

use App\Models\User;

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
}
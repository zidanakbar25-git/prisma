<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function kabag()
    {
        return view('dashboard.kabag');
    }

    public function staff()
    {
        return view('dashboard.staff');
    }

    public function intern()
    {
        return view('dashboard.intern');
    }
}
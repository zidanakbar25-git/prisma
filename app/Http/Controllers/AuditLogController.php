<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(
            auth()->user()->role === 'kabag',
            403
        );

        $query = ActivityLog::with('user')
            ->latest();

        // Filter berdasarkan user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter berdasarkan action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter tanggal
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query
            ->paginate(20)
            ->withQueryString();

        $users = \App\Models\User::orderBy('name')->get();

        $actions = ActivityLog::query()
            ->whereNotNull('action')
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view(
            'audit-logs.index',
            compact(
                'logs',
                'users',
                'actions'
            )
        );
    }
}
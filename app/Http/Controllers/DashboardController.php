<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard Kabag.
     */
    public function kabag(Request $request)
    {
        abort_unless($request->user()->role === 'kabag', 403);

        $today = now()->toDateString();
        $user = $request->user();

        $upcomingActivities = Activity::with(['creator', 'pics'])
            ->whereDate('activity_date', '>=', $today)
            ->orderBy('activity_date')
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        $tasks = Task::with(['creator', 'assignees'])
            ->orderBy('due_date')
            ->orderByDesc('created_at')
            ->get();

        $taskSummary = [
            'total' => $tasks->count(),
            'belum_mulai' => $tasks->sum(
                fn ($task) => $task->assignees
                    ->where('pivot.status', 'belum_mulai')
                    ->count()
            ),
            'sedang_dikerjakan' => $tasks->sum(
                fn ($task) => $task->assignees
                    ->where('pivot.status', 'sedang_dikerjakan')
                    ->count()
            ),
            'selesai' => $tasks->sum(
                fn ($task) => $task->assignees
                    ->where('pivot.status', 'selesai')
                    ->count()
            ),
        ];

        $activeUsersCount = User::where('is_active', true)->count();

        $recentLogs = ActivityLog::with('user')
            ->latest()
            ->limit(8)
            ->get();

        return view('dashboard.kabag', compact(
            'upcomingActivities',
            'tasks',
            'taskSummary',
            'activeUsersCount',
            'recentLogs'
        ));
    }

    /**
     * Dashboard Staff.
     */
    public function staff(Request $request)
    {
        abort_unless($request->user()->role === 'staff', 403);

        return $this->userDashboard($request);
    }

    /**
     * Dashboard Intern.
     */
    public function intern(Request $request)
    {
        abort_unless($request->user()->role === 'intern', 403);

        return $this->userDashboard($request);
    }

    /**
     * Data dashboard untuk Staff dan Intern.
     */
    private function userDashboard(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();

        $upcomingActivities = Activity::with(['creator', 'pics'])
            ->whereDate('activity_date', '>=', $today)
            ->orderBy('activity_date')
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        $assignedTasks = $user->assignedTasks()
            ->with(['creator', 'assignees'])
            ->orderBy('tasks.due_date')
            ->orderByDesc('tasks.created_at')
            ->get();

        $taskSummary = [
            'total' => $assignedTasks->count(),
            'belum_mulai' => $assignedTasks->filter(
                fn ($task) => $task->pivot->status === 'belum_mulai'
            )->count(),
            'sedang_dikerjakan' => $assignedTasks->filter(
                fn ($task) => $task->pivot->status === 'sedang_dikerjakan'
            )->count(),
            'selesai' => $assignedTasks->filter(
                fn ($task) => $task->pivot->status === 'selesai'
            )->count(),
        ];

        $recentLogs = ActivityLog::with('user')
            ->latest()
            ->limit(8)
            ->get();

        return view(
            $user->role === 'staff'
                ? 'dashboard.staff'
                : 'dashboard.intern',
            compact(
                'upcomingActivities',
                'assignedTasks',
                'taskSummary',
                'recentLogs'
            )
        );
    }
}

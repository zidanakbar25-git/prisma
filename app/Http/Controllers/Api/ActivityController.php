<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;

class ActivityController extends Controller
{
    /**
     * Menampilkan seluruh kegiatan.
     */
    public function index(): JsonResponse
    {
        $activities = Activity::with(['creator', 'pics'])
            ->orderBy('activity_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json([
            'message' => 'Data kegiatan berhasil diambil.',
            'data' => $activities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'activity_date' => $activity->activity_date->format('Y-m-d'),
                    'start_time' => $activity->start_time
                        ? date('H:i', strtotime($activity->start_time))
                        : null,
                    'end_time' => $activity->end_time
                        ? date('H:i', strtotime($activity->end_time))
                        : null,
                    'location' => $activity->location,
                    'description' => $activity->description,
                    'created_by' => $activity->creator ? [
                        'id' => $activity->creator->id,
                        'name' => $activity->creator->name,
                    ] : null,
                    'pics' => $activity->pics->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'role' => $user->role,
                        ];
                    })->values(),
                ];
            })->values(),
        ]);
    }
}
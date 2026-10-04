<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    /**
     * Menampilkan detail satu kegiatan.
     */
    public function show(Activity $activity): JsonResponse
    {
        $activity->load(['creator', 'pics']);

        return response()->json([
            'message' => 'Detail kegiatan berhasil diambil.',
            'data' => [
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
            ],
        ]);
    }

    /**
     * Membuat kegiatan baru.
     */
    public function store(Request $request): JsonResponse
    {
        if ($request->user()->role !== 'kabag') {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk membuat kegiatan.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'activity_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after_or_equal:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'pic_ids' => ['required', 'array', 'min:1'],
            'pic_ids.*' => ['integer', 'exists:users,id'],
        ], [
            'title.required' => 'Judul kegiatan wajib diisi.',
            'title.max' => 'Judul kegiatan maksimal 255 karakter.',
            'activity_date.required' => 'Tanggal kegiatan wajib diisi.',
            'start_time.required' => 'Jam mulai wajib diisi.',
            'start_time.date_format' => 'Format jam mulai tidak valid.',
            'end_time.date_format' => 'Format jam selesai tidak valid.',
            'end_time.after_or_equal' => 'Jam selesai tidak boleh lebih awal dari jam mulai.',
            'pic_ids.required' => 'Minimal pilih satu PIC.',
            'pic_ids.min' => 'Minimal pilih satu PIC.',
            'pic_ids.*.exists' => 'PIC yang dipilih tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $validPicIds = User::whereIn('id', $validated['pic_ids'])
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        if (count($validPicIds) !== count(array_unique($validated['pic_ids']))) {
            return response()->json([
                'message' => 'PIC yang dipilih harus merupakan pengguna aktif.',
            ], 422);
        }

        $activity = Activity::create([
            'title' => $validated['title'],
            'activity_date' => $validated['activity_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'location' => $validated['location'] ?? null,
            'description' => $validated['description'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        $activity->pics()->sync($validPicIds);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'Tambah Kegiatan API',
            'description' => 'Menambahkan kegiatan "' . $activity->title . '" melalui API pada tanggal ' . $activity->activity_date->format('d/m/Y') . '.',
        ]);

        $activity->load(['creator', 'pics']);

        return response()->json([
            'message' => 'Kegiatan berhasil ditambahkan.',
            'data' => $this->formatActivity($activity),
        ], 201);
    }

    /**
     * Mengubah kegiatan.
     */
    public function update(Request $request, Activity $activity): JsonResponse
    {
        if ($request->user()->role !== 'kabag') {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk mengubah kegiatan.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'activity_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after_or_equal:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'pic_ids' => ['required', 'array', 'min:1'],
            'pic_ids.*' => ['integer', 'exists:users,id'],
        ], [
            'title.required' => 'Judul kegiatan wajib diisi.',
            'title.max' => 'Judul kegiatan maksimal 255 karakter.',
            'activity_date.required' => 'Tanggal kegiatan wajib diisi.',
            'start_time.required' => 'Jam mulai wajib diisi.',
            'start_time.date_format' => 'Format jam mulai tidak valid.',
            'end_time.date_format' => 'Format jam selesai tidak valid.',
            'end_time.after_or_equal' => 'Jam selesai tidak boleh lebih awal dari jam mulai.',
            'pic_ids.required' => 'Minimal pilih satu PIC.',
            'pic_ids.min' => 'Minimal pilih satu PIC.',
            'pic_ids.*.exists' => 'PIC yang dipilih tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $validPicIds = User::whereIn('id', $validated['pic_ids'])
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        if (count($validPicIds) !== count(array_unique($validated['pic_ids']))) {
            return response()->json([
                'message' => 'PIC yang dipilih harus merupakan pengguna aktif.',
            ], 422);
        }

        $oldTitle = $activity->title;

        $activity->update([
            'title' => $validated['title'],
            'activity_date' => $validated['activity_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'location' => $validated['location'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        $activity->pics()->sync($validPicIds);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'Edit Kegiatan API',
            'description' => 'Mengubah kegiatan "' . $oldTitle . '" menjadi "' . $activity->title . '" melalui API.',
        ]);

        $activity->load(['creator', 'pics']);

        return response()->json([
            'message' => 'Kegiatan berhasil diperbarui.',
            'data' => $this->formatActivity($activity),
        ], 200);
    }

    /**
 * Menghapus kegiatan.
 */
public function destroy(Request $request, Activity $activity): JsonResponse
{
    if ($request->user()->role !== 'kabag') {
        return response()->json([
            'message' => 'Anda tidak memiliki akses untuk menghapus kegiatan.',
        ], 403);
    }

    $activityTitle = $activity->title;

    $activity->delete();

    ActivityLog::create([
        'user_id' => $request->user()->id,
        'action' => 'Hapus Kegiatan API',
        'description' => 'Menghapus kegiatan "' . $activityTitle . '" melalui API.',
    ]);

    return response()->json([
        'message' => 'Kegiatan berhasil dihapus.',
    ], 200);
}

    /**
     * Memformat data kegiatan untuk response JSON.
     */
    private function formatActivity(Activity $activity): array
    {
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
            })->values()->toArray(),
        ];
    }
}
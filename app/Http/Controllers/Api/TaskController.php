
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Menampilkan daftar To-Do sesuai role pengguna.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Task::with(['creator', 'assignees'])
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc');

        if ($user->role !== 'kabag') {
            $query->whereHas('assignees', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            });
        }

        $tasks = $query->get();

        return response()->json([
            'message' => 'Data To-Do berhasil diambil.',
            'data' => $tasks->map(function ($task) use ($user) {
                return $this->formatTask($task, $user);
            })->values(),
        ]);
    }

    /**
     * Menampilkan detail satu To-Do.
     */
    public function show(Request $request, Task $task): JsonResponse
    {
        $user = $request->user();

        $task->load(['creator', 'assignees']);

        if (
            $user->role !== 'kabag' &&
            !$task->assignees->contains('id', $user->id)
        ) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke To-Do ini.',
            ], 403);
        }

        return response()->json([
            'message' => 'Detail To-Do berhasil diambil.',
            'data' => $this->formatTask($task, $user),
        ]);
    }

    /**
     * Membuat To-Do baru.
     * Hanya Kabag yang dapat membuat To-Do.
     */
    public function store(Request $request): JsonResponse
    {
        if ($request->user()->role !== 'kabag') {
            return response()->json([
                'message' => 'Hanya Kabag yang dapat membuat To-Do.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:rendah,sedang,tinggi'],
            'due_date' => ['required', 'date'],
            'assignee_ids' => ['required', 'array', 'min:1'],
            'assignee_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:users,id',
            ],
        ], [
            'title.required' => 'Judul To-Do wajib diisi.',
            'title.max' => 'Judul To-Do maksimal 255 karakter.',
            'priority.required' => 'Prioritas wajib dipilih.',
            'priority.in' => 'Prioritas harus rendah, sedang, atau tinggi.',
            'due_date.required' => 'Tanggal tenggat wajib diisi.',
            'due_date.date' => 'Tanggal tenggat tidak valid.',
            'assignee_ids.required' => 'Pilih minimal satu penerima tugas.',
            'assignee_ids.min' => 'Pilih minimal satu penerima tugas.',
            'assignee_ids.*.distinct' => 'Penerima tugas tidak boleh duplikat.',
            'assignee_ids.*.exists' => 'Penerima tugas tidak ditemukan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $assigneeIds = array_map('intval', $validated['assignee_ids']);

        $validAssigneeIds = User::whereIn('id', $assigneeIds)
            ->where('is_active', true)
            ->whereIn('role', ['staff', 'intern'])
            ->pluck('id')
            ->all();

        if (count($validAssigneeIds) !== count($assigneeIds)) {
            return response()->json([
                'message' => 'Penerima harus merupakan Staff atau Intern aktif.',
            ], 422);
        }

        $task = DB::transaction(function () use (
            $validated,
            $validAssigneeIds,
            $request
        ) {
            $task = Task::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'priority' => $validated['priority'],
                'due_date' => $validated['due_date'],
                'created_by' => $request->user()->id,
            ]);

            $assignments = [];

            foreach ($validAssigneeIds as $assigneeId) {
                $assignments[$assigneeId] = [
                    'status' => 'belum_mulai',
                    'is_read' => false,
                ];
            }

            $task->assignees()->sync($assignments);

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'Tambah To-Do API',
                'description' => 'Membuat To-Do "' . $task->title
                    . '" melalui API dan menugaskannya kepada '
                    . count($validAssigneeIds) . ' pengguna.',
            ]);

            return $task;
        });

        $task->load(['creator', 'assignees']);

        return response()->json([
            'message' => 'To-Do berhasil dibuat dan ditugaskan.',
            'data' => $this->formatTask($task, $request->user()),
        ], 201);
    }

    /**
     * Mengubah To-Do.
     * Hanya Kabag yang dapat mengubah data dan penerima tugas.
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        if ($request->user()->role !== 'kabag') {
            return response()->json([
                'message' => 'Hanya Kabag yang dapat mengubah To-Do.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:rendah,sedang,tinggi'],
            'due_date' => ['required', 'date'],
            'assignee_ids' => ['required', 'array', 'min:1'],
            'assignee_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:users,id',
            ],
        ], [
            'title.required' => 'Judul To-Do wajib diisi.',
            'title.max' => 'Judul To-Do maksimal 255 karakter.',
            'priority.required' => 'Prioritas wajib dipilih.',
            'priority.in' => 'Prioritas tidak valid.',
            'due_date.required' => 'Tanggal tenggat wajib diisi.',
            'due_date.date' => 'Tanggal tenggat tidak valid.',
            'assignee_ids.required' => 'Pilih minimal satu penerima tugas.',
            'assignee_ids.min' => 'Pilih minimal satu penerima tugas.',
            'assignee_ids.*.distinct' => 'Penerima tugas tidak boleh duplikat.',
            'assignee_ids.*.exists' => 'Penerima tugas tidak ditemukan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $assigneeIds = array_map('intval', $validated['assignee_ids']);

        $validAssigneeIds = User::whereIn('id', $assigneeIds)
            ->where('is_active', true)
            ->whereIn('role', ['staff', 'intern'])
            ->pluck('id')
            ->all();

        if (count($validAssigneeIds) !== count($assigneeIds)) {
            return response()->json([
                'message' => 'Penerima harus merupakan Staff atau Intern aktif.',
            ], 422);
        }

        DB::transaction(function () use (
            $task,
            $validated,
            $validAssigneeIds,
            $request
        ) {
            $oldTitle = $task->title;

            $task->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'priority' => $validated['priority'],
                'due_date' => $validated['due_date'],
            ]);

            $currentAssignments = $task->assignees()
                ->pluck('users.id')
                ->all();

            $newAssigneeIds = array_diff(
                $validAssigneeIds,
                $currentAssignments
            );

            $assignments = [];

            foreach ($validAssigneeIds as $assigneeId) {
                // Pertahankan status penerima lama.
                // Penerima baru memulai dari belum_mulai.
                if (in_array($assigneeId, $currentAssignments)) {
                    continue;
                }

                $assignments[$assigneeId] = [
                    'status' => 'belum_mulai',
                    'is_read' => false,
                ];
            }

            // Sync tanpa atribut tambahan mempertahankan pivot
            // penerima lama dan menghapus penerima yang dilepas.
            $task->assignees()->syncWithoutDetaching($assignments);

            $task->assignees()->detach(
                array_diff($currentAssignments, $validAssigneeIds)
            );

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'Edit To-Do API',
                'description' => 'Mengubah To-Do "' . $oldTitle
                    . '" menjadi "' . $task->title . '" melalui API.',
            ]);
        });

        $task->load(['creator', 'assignees']);

        return response()->json([
            'message' => 'To-Do berhasil diperbarui.',
            'data' => $this->formatTask($task, $request->user()),
        ]);
    }

    /**
     * Menghapus To-Do.
     * Hanya Kabag yang dapat menghapus.
     */
    public function destroy(Request $request, Task $task): JsonResponse
    {
        if ($request->user()->role !== 'kabag') {
            return response()->json([
                'message' => 'Hanya Kabag yang dapat menghapus To-Do.',
            ], 403);
        }

        $title = $task->title;

        DB::transaction(function () use ($task, $title, $request) {
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'Hapus To-Do API',
                'description' => 'Menghapus To-Do "' . $title . '" melalui API.',
            ]);

            $task->delete();
        });

        return response()->json([
            'message' => 'To-Do berhasil dihapus.',
        ]);
    }

    /**
     * Mengubah status tugas milik pengguna yang login.
     */
    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => [
                'required',
                'in:belum_mulai,sedang_dikerjakan,selesai',
            ],
        ], [
            'status.required' => 'Status tugas wajib dipilih.',
            'status.in' => 'Status tugas tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        $assignment = $task->assignees()
            ->where('users.id', $user->id)
            ->first();

        if (!$assignment) {
            return response()->json([
                'message' => 'Anda tidak ditugaskan pada To-Do ini.',
            ], 403);
        }

        $task->assignees()->updateExistingPivot($user->id, [
            'status' => $request->input('status'),
            'is_read' => true,
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Ubah Status To-Do API',
            'description' => 'Mengubah status To-Do "' . $task->title
                . '" menjadi "' . $request->input('status') . '".',
        ]);

        $task->load(['creator', 'assignees']);

        return response()->json([
            'message' => 'Status To-Do berhasil diperbarui.',
            'data' => $this->formatTask($task, $user),
        ]);
    }

    /**
     * Format data To-Do untuk response API.
     */
    private function formatTask(Task $task, $user): array
    {
        $myAssignment = $task->assignees->firstWhere('id', $user->id);

        return [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'priority' => $task->priority,
            'due_date' => $task->due_date->format('Y-m-d'),

            'created_by' => $task->creator ? [
                'id' => $task->creator->id,
                'name' => $task->creator->name,
            ] : null,

            'assignees' => $task->assignees->map(function ($assignee) {
                return [
                    'id' => $assignee->id,
                    'name' => $assignee->name,
                    'role' => $assignee->role,
                    'status' => $assignee->pivot->status,
                    'is_read' => (bool) $assignee->pivot->is_read,
                ];
            })->values()->toArray(),

            'my_assignment' => $myAssignment ? [
                'status' => $myAssignment->pivot->status,
                'is_read' => (bool) $myAssignment->pivot->is_read,
            ] : null,
        ];
    }
}

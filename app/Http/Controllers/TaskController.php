<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'kabag') {

            $tasks = Task::with([
                'creator',
                'assignees',
            ])
                ->orderBy('due_date', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

        } else {

            $tasks = Task::with([
                'creator',
                'assignees',
            ])
                ->whereHas('assignees', function ($query) use ($user) {
                    $query->where(
                        'users.id',
                        $user->id
                    );
                })
                ->orderBy('due_date', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();


            foreach ($tasks as $task) {

                $assignment = $task->assignees
                    ->firstWhere(
                        'id',
                        $user->id
                    );

                if ($assignment) {

                    $assignment->pivot->was_unread =
                        !$assignment->pivot->is_read;
                }
            }


            $taskIds = $tasks->pluck('id');


            if ($taskIds->isNotEmpty()) {

                $user->assignedTasks()->updateExistingPivot(
                    $taskIds->all(),
                    [
                        'is_read' => true,
                    ]
                );
            }
        }


        return view(
            'tasks.index',
            compact('tasks')
        );
    }


    public function create()
    {
        abort_unless(
            auth()->user()->role === 'kabag',
            403
        );


        $users = User::whereIn(
            'role',
            [
                'staff',
                'intern',
            ]
        )
            ->where(
                'is_active',
                true
            )
            ->orderBy('name')
            ->get();


        return view(
            'tasks.create',
            compact('users')
        );
    }


    public function store(Request $request)
    {
        abort_unless(
            auth()->user()->role === 'kabag',
            403
        );


        $validated = $this->validateTask(
            $request
        );


        $validAssigneeIds =
            $this->getValidAssigneeIds(
                $validated['assignee_ids']
            );


        if (
            count($validAssigneeIds) !==
            count(
                array_unique(
                    $validated['assignee_ids']
                )
            )
        ) {

            return back()
                ->withErrors([
                    'assignee_ids' =>
                        'Penerima hanya dapat berupa Staff atau Intern yang aktif.',
                ])
                ->withInput();
        }


        $task = Task::create([
            'title' =>
                $validated['title'],

            'description' =>
                $validated['description'] ?? null,

            'priority' =>
                $validated['priority'],

            'due_date' =>
                $validated['due_date'],

            'created_by' =>
                auth()->id(),
        ]);


        $assignments = [];


        foreach ($validAssigneeIds as $userId) {

            $assignments[$userId] = [
                'status' =>
                    'belum_mulai',

                'is_read' =>
                    false,
            ];
        }


        $task->assignees()->sync(
            $assignments
        );


        /*
        |--------------------------------------------------------------------------
        | Audit Log
        |--------------------------------------------------------------------------
        */

        $assigneeNames = User::whereIn(
            'id',
            $validAssigneeIds
        )
            ->pluck('name')
            ->implode(', ');


        ActivityLog::create([
            'user_id' =>
                auth()->id(),

            'action' =>
                'Tambah To-Do',

            'description' =>
                'Menambahkan To-Do "' .
                $task->title .
                '" dan mengassign kepada ' .
                $assigneeNames .
                '.',
        ]);


        return redirect()
            ->route('tasks.index')
            ->with(
                'success',
                'To-Do berhasil ditambahkan.'
            );
    }


    public function edit(Task $task)
    {
        abort_unless(
            auth()->user()->role === 'kabag',
            403
        );


        $task->load(
            'assignees'
        );


        $users = User::whereIn(
            'role',
            [
                'staff',
                'intern',
            ]
        )
            ->where(
                'is_active',
                true
            )
            ->orderBy('name')
            ->get();


        return view(
            'tasks.edit',
            compact(
                'task',
                'users'
            )
        );
    }


    public function update(
        Request $request,
        Task $task
    ) {
        abort_unless(
            auth()->user()->role === 'kabag',
            403
        );


        $validated = $this->validateTask(
            $request
        );


        $validAssigneeIds =
            $this->getValidAssigneeIds(
                $validated['assignee_ids']
            );


        if (
            count($validAssigneeIds) !==
            count(
                array_unique(
                    $validated['assignee_ids']
                )
            )
        ) {

            return back()
                ->withErrors([
                    'assignee_ids' =>
                        'Penerima hanya dapat berupa Staff atau Intern yang aktif.',
                ])
                ->withInput();
        }


        $oldTitle = $task->title;


        $task->update([
            'title' =>
                $validated['title'],

            'description' =>
                $validated['description'] ?? null,

            'priority' =>
                $validated['priority'],

            'due_date' =>
                $validated['due_date'],
        ]);


        /*
         * Ambil assignment lama.
         * Status dan is_read milik penerima lama
         * akan dipertahankan.
         */

        $existingAssignments = $task->assignees()
            ->get()
            ->keyBy('id');


        $assignments = [];


        foreach ($validAssigneeIds as $userId) {

            if ($existingAssignments->has($userId)) {

                $oldAssignment =
                    $existingAssignments->get(
                        $userId
                    );


                $assignments[$userId] = [
                    'status' =>
                        $oldAssignment->pivot->status,

                    'is_read' =>
                        $oldAssignment->pivot->is_read,
                ];

            } else {

                $assignments[$userId] = [
                    'status' =>
                        'belum_mulai',

                    'is_read' =>
                        false,
                ];
            }
        }


        $task->assignees()->sync(
            $assignments
        );


        /*
        |--------------------------------------------------------------------------
        | Audit Log
        |--------------------------------------------------------------------------
        */

        ActivityLog::create([
            'user_id' =>
                auth()->id(),

            'action' =>
                'Edit To-Do',

            'description' =>
                'Mengubah To-Do "' .
                $oldTitle .
                '" menjadi "' .
                $task->title .
                '".',
        ]);


        return redirect()
            ->route('tasks.index')
            ->with(
                'success',
                'To-Do berhasil diperbarui.'
            );
    }


    public function destroy(Task $task)
    {
        abort_unless(
            auth()->user()->role === 'kabag',
            403
        );


        $taskTitle = $task->title;


        $task->delete();


        /*
        |--------------------------------------------------------------------------
        | Audit Log
        |--------------------------------------------------------------------------
        */

        ActivityLog::create([
            'user_id' =>
                auth()->id(),

            'action' =>
                'Hapus To-Do',

            'description' =>
                'Menghapus To-Do "' .
                $taskTitle .
                '".',
        ]);


        return redirect()
            ->route('tasks.index')
            ->with(
                'success',
                'To-Do berhasil dihapus.'
            );
    }


    public function updateStatus(
        Request $request,
        Task $task
    ) {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:belum_mulai,sedang_dikerjakan,selesai',
            ],
        ], [
            'status.required' =>
                'Status wajib dipilih.',

            'status.in' =>
                'Status yang dipilih tidak valid.',
        ]);


        $user = auth()->user();


        $assignment = $task->assignees()
            ->where(
                'users.id',
                $user->id
            )
            ->first();


        if (!$assignment) {

            abort(
                403,
                'Anda tidak memiliki tugas ini.'
            );
        }


        $oldStatus =
            $assignment->pivot->status;


        $task->assignees()->updateExistingPivot(
            $user->id,
            [
                'status' =>
                    $validated['status'],
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Audit Log
        |--------------------------------------------------------------------------
        */

        ActivityLog::create([
            'user_id' =>
                auth()->id(),

            'action' =>
                'Ubah Status To-Do',

            'description' =>
                'Mengubah status To-Do "' .
                $task->title .
                '" dari "' .
                $this->statusLabel($oldStatus) .
                '" menjadi "' .
                $this->statusLabel(
                    $validated['status']
                ) .
                '".',
        ]);


        return redirect()
            ->route('tasks.index')
            ->with(
                'success',
                'Status To-Do berhasil diperbarui.'
            );
    }


    private function validateTask(
        Request $request
    ) {
        return $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'priority' => [
                'required',
                'in:rendah,sedang,tinggi',
            ],

            'due_date' => [
                'required',
                'date',
            ],

            'assignee_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'assignee_ids.*' => [
                'integer',
                'exists:users,id',
            ],
        ], [
            'title.required' =>
                'Judul To-Do wajib diisi.',

            'title.max' =>
                'Judul To-Do maksimal 255 karakter.',

            'priority.required' =>
                'Prioritas wajib dipilih.',

            'priority.in' =>
                'Prioritas yang dipilih tidak valid.',

            'due_date.required' =>
                'Deadline wajib diisi.',

            'due_date.date' =>
                'Format deadline tidak valid.',

            'assignee_ids.required' =>
                'Minimal pilih satu penerima.',

            'assignee_ids.min' =>
                'Minimal pilih satu penerima.',

            'assignee_ids.*.exists' =>
                'Penerima yang dipilih tidak valid.',
        ]);
    }


    private function getValidAssigneeIds(
        array $assigneeIds
    ) {
        return User::whereIn(
            'id',
            $assigneeIds
        )
            ->whereIn(
                'role',
                [
                    'staff',
                    'intern',
                ]
            )
            ->where(
                'is_active',
                true
            )
            ->pluck('id')
            ->toArray();
    }


    private function statusLabel(
        string $status
    ): string {
        return match ($status) {

            'belum_mulai' =>
                'Belum Mulai',

            'sedang_dikerjakan' =>
                'Sedang Dikerjakan',

            'selesai' =>
                'Selesai',

            default =>
                $status,
        };
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuditLogController extends Controller
{
    /**
     * Menampilkan audit log dengan filter dan pagination.
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->user()->role !== 'kabag') {
            return response()->json([
                'message' => 'Hanya Kabag yang dapat melihat audit log.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'action' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:start_date',
            ],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ], [
            'user_id.integer' => 'ID pengguna harus berupa angka.',
            'user_id.exists' => 'Pengguna tidak ditemukan.',
            'start_date.date_format' => 'Format tanggal awal harus YYYY-MM-DD.',
            'end_date.date_format' => 'Format tanggal akhir harus YYYY-MM-DD.',
            'end_date.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
            'per_page.min' => 'Jumlah data per halaman minimal 1.',
            'per_page.max' => 'Jumlah data per halaman maksimal 100.',
            'page.min' => 'Nomor halaman minimal 1.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi filter audit log gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $query = ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if (!empty($validated['user_id'])) {
            $query->where('user_id', $validated['user_id']);
        }

        if (!empty($validated['action'])) {
            $query->where(
                'action',
                'like',
                '%' . $validated['action'] . '%'
            );
        }

        if (!empty($validated['start_date'])) {
            $query->whereDate(
                'created_at',
                '>=',
                $validated['start_date']
            );
        }

        if (!empty($validated['end_date'])) {
            $query->whereDate(
                'created_at',
                '<=',
                $validated['end_date']
            );
        }

        $logs = $query->paginate(
            (int) ($validated['per_page'] ?? 20)
        );

        return response()->json([
            'message' => 'Data audit log berhasil diambil.',
            'data' => $logs->getCollection()->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user' => $log->user ? [
                        'id' => $log->user->id,
                        'name' => $log->user->name,
                        'username' => $log->user->username,
                        'role' => $log->user->role,
                    ] : null,
                    'action' => $log->action,
                    'description' => $log->description,
                    'created_at' => $log->created_at?->toISOString(),
                ];
            })->values(),

            'pagination' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
                'from' => $logs->firstItem(),
                'to' => $logs->lastItem(),
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with(['creator', 'pics'])
            ->orderBy('activity_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        $users = User::where('is_active', true)
            ->orderBy('name')
            ->get();

        $calendarEvents = $activities->map(function ($activity) {

            $startDate = $activity->activity_date->format('Y-m-d');

            $startTime = $activity->start_time
                ? date('H:i:s', strtotime($activity->start_time))
                : '00:00:00';

            $start = $startDate . 'T' . $startTime;

            if ($activity->end_time) {
                $endTime = date(
                    'H:i:s',
                    strtotime($activity->end_time)
                );

                $end = $startDate . 'T' . $endTime;
            } else {
                $end = null;
            }

            return [
                'id' => $activity->id,
                'title' => $activity->title,
                'start' => $start,
                'end' => $end,
                'url' => route('activities.show', $activity),

                'date' => $startDate,

                'date_label' => $activity->activity_date
                    ->locale('id')
                    ->translatedFormat('l, d F Y'),

                'start_time' => $activity->start_time
                    ? date('H:i', strtotime($activity->start_time))
                    : null,

                'end_time' => $activity->end_time
                    ? date('H:i', strtotime($activity->end_time))
                    : null,

                'time_label' => $activity->start_time
                    ? (
                        date('H:i', strtotime($activity->start_time))
                        . ' - '
                        . (
                            $activity->end_time
                                ? date('H:i', strtotime($activity->end_time))
                                : 'Selesai'
                        )
                    )
                    : '-',

                'location' => $activity->location,

                'description' => $activity->description,

                'creator' => $activity->creator
                    ? $activity->creator->name
                    : '-',

                'pics' => $activity->pics
                    ->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'role' => $user->role,
                        ];
                    })
                    ->values()
                    ->toArray(),

                'extendedProps' => [
                    'location' => $activity->location,
                ],
            ];
        })->values();

        return view(
            'activities.index',
            compact(
                'activities',
                'calendarEvents',
                'users'
            )
        );
    }

    public function create()
    {
        $users = User::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('activities.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => ['required', 'string', 'max:255'],
                'activity_date' => ['required', 'date'],
                'start_time' => ['required', 'date_format:H:i'],
                'end_time' => [
                    'nullable',
                    'date_format:H:i',
                    'after_or_equal:start_time'
                ],
                'location' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'pic_ids' => ['required', 'array', 'min:1'],
                'pic_ids.*' => [
                    'integer',
                    'exists:users,id'
                ],
            ],
            [
                'title.required' => 'Judul kegiatan wajib diisi.',
                'activity_date.required' => 'Tanggal kegiatan wajib diisi.',
                'start_time.required' => 'Jam mulai wajib diisi.',
                'end_time.date_format' => 'Format jam selesai tidak valid.',
                'end_time.after_or_equal' =>
                    'Jam selesai tidak boleh lebih awal dari jam mulai.',
                'pic_ids.required' => 'Minimal pilih satu PIC.',
                'pic_ids.min' => 'Minimal pilih satu PIC.',
                'pic_ids.*.exists' => 'PIC yang dipilih tidak valid.',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->route('activities.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'create');
        }

        $validated = $validator->validated();

        $activity = Activity::create([
            'title' => $validated['title'],
            'activity_date' => $validated['activity_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'location' => $validated['location'] ?? null,
            'description' => $validated['description'] ?? null,
            'created_by' => auth()->id(),
        ]);

        $activity->pics()->sync($validated['pic_ids']);

        return redirect()
            ->route('activities.index')
            ->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function show(Activity $activity)
    {
        $activity->load(['creator', 'pics']);

        return view('activities.show', compact('activity'));
    }

    public function edit(Activity $activity)
    {
        $activity->load('pics');

        $users = User::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('activities.edit', compact('activity', 'users'));
    }

    public function update(Request $request, Activity $activity)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => ['required', 'string', 'max:255'],
                'activity_date' => ['required', 'date'],
                'start_time' => ['required', 'date_format:H:i'],
                'end_time' => [
                    'nullable',
                    'date_format:H:i',
                    'after_or_equal:start_time'
                ],
                'location' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'pic_ids' => ['required', 'array', 'min:1'],
                'pic_ids.*' => [
                    'integer',
                    'exists:users,id'
                ],
            ],
            [
                'title.required' => 'Judul kegiatan wajib diisi.',
                'activity_date.required' => 'Tanggal kegiatan wajib diisi.',
                'start_time.required' => 'Jam mulai wajib diisi.',
                'end_time.date_format' => 'Format jam selesai tidak valid.',
                'end_time.after_or_equal' =>
                    'Jam selesai tidak boleh lebih awal dari jam mulai.',
                'pic_ids.required' => 'Minimal pilih satu PIC.',
                'pic_ids.min' => 'Minimal pilih satu PIC.',
                'pic_ids.*.exists' => 'PIC yang dipilih tidak valid.',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->route('activities.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'edit')
                ->with('edit_id', $activity->id);
        }

        $validated = $validator->validated();

        $activity->update([
            'title' => $validated['title'],
            'activity_date' => $validated['activity_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'location' => $validated['location'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        $activity->pics()->sync($validated['pic_ids']);

        return redirect()
            ->route('activities.index')
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()
            ->route('activities.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }

    public function exportForm()
    {
        return view('activities.export');
    }

    public function exportPdf(Request $request)
    {
        $validated = $request->validate([
            'filter_type' => ['required', 'in:month,range'],
            'month' => ['nullable', 'date_format:Y-m'],
            'start_date' => ['nullable', 'date'],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date'
            ],
        ], [
            'filter_type.required' => 'Jenis periode wajib dipilih.',
            'filter_type.in' => 'Jenis periode tidak valid.',
            'month.date_format' => 'Format bulan tidak valid.',
            'start_date.date' => 'Tanggal mulai tidak valid.',
            'end_date.date' => 'Tanggal selesai tidak valid.',
            'end_date.after_or_equal' =>
                'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        $query = Activity::with(['creator', 'pics']);

        if ($validated['filter_type'] === 'month') {

            if (empty($validated['month'])) {
                return back()
                    ->withErrors([
                        'month' => 'Bulan wajib dipilih.'
                    ])
                    ->withInput();
            }

            $query->whereRaw(
                "DATE_FORMAT(activity_date, '%Y-%m') = ?",
                [$validated['month']]
            );
        }

        if ($validated['filter_type'] === 'range') {

            if (
                empty($validated['start_date']) ||
                empty($validated['end_date'])
            ) {
                return back()
                    ->withErrors([
                        'start_date' =>
                            'Tanggal mulai dan tanggal selesai wajib diisi.'
                    ])
                    ->withInput();
            }

            $query->whereBetween(
                'activity_date',
                [
                    $validated['start_date'],
                    $validated['end_date']
                ]
            );
        }

        $activities = $query
            ->orderBy('activity_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        if ($validated['filter_type'] === 'month') {

            $periodLabel = \Carbon\Carbon::createFromFormat(
                'Y-m',
                $validated['month']
            )->translatedFormat('F Y');

        } else {

            $startDate = \Carbon\Carbon::parse(
                $validated['start_date']
            );

            $endDate = \Carbon\Carbon::parse(
                $validated['end_date']
            );

            $periodLabel =
                $startDate->translatedFormat('d F Y')
                . ' - '
                . $endDate->translatedFormat('d F Y');
        }

        $pdf = Pdf::loadView(
            'activities.pdf',
            [
                'activities' => $activities,
                'periodLabel' => $periodLabel,
            ]
        );

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download(
            'kalender-kegiatan-' .
            now()->format('Y-m-d') .
            '.pdf'
        );
    }
}
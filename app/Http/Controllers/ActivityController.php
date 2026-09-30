<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Menampilkan daftar kegiatan.
     */
    public function index()
    {
        $activities = Activity::with(['creator', 'pics'])
            ->orderBy('activity_date', 'desc')
            ->orderBy('start_time', 'asc')
            ->get();

        return view('activities.index', compact('activities'));
    }

    /**
     * Menampilkan form tambah kegiatan.
     */
    public function create()
    {
        $users = User::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('activities.create', compact('users'));
    }

    /**
     * Menyimpan kegiatan baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'activity_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'pic_ids' => ['required', 'array', 'min:1'],
            'pic_ids.*' => ['integer', 'exists:users,id'],
        ], [
            'title.required' => 'Judul kegiatan wajib diisi.',
            'activity_date.required' => 'Tanggal kegiatan wajib diisi.',
            'start_time.required' => 'Jam mulai wajib diisi.',
            'end_time.date_format' => 'Format jam selesai tidak valid.',
            'pic_ids.required' => 'Minimal pilih satu PIC.',
            'pic_ids.min' => 'Minimal pilih satu PIC.',
            'pic_ids.*.exists' => 'PIC yang dipilih tidak valid.',
        ]);

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

    /**
     * Menampilkan detail kegiatan.
     */
    public function show(Activity $activity)
    {
        $activity->load(['creator', 'pics']);

        return view('activities.show', compact('activity'));
    }

    /**
     * Menampilkan form edit kegiatan.
     */
    public function edit(Activity $activity)
    {
        $activity->load('pics');

        $users = User::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('activities.edit', compact('activity', 'users'));
    }

    /**
     * Memperbarui kegiatan.
     */
    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'activity_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'pic_ids' => ['required', 'array', 'min:1'],
            'pic_ids.*' => ['integer', 'exists:users,id'],
        ], [
            'title.required' => 'Judul kegiatan wajib diisi.',
            'activity_date.required' => 'Tanggal kegiatan wajib diisi.',
            'start_time.required' => 'Jam mulai wajib diisi.',
            'end_time.date_format' => 'Format jam selesai tidak valid.',
            'pic_ids.required' => 'Minimal pilih satu PIC.',
            'pic_ids.min' => 'Minimal pilih satu PIC.',
            'pic_ids.*.exists' => 'PIC yang dipilih tidak valid.',
        ]);

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
            ->route('activities.show', $activity)
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    /**
     * Menghapus kegiatan.
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()
            ->route('activities.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }
}
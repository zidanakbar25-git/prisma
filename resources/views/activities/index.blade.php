@extends('layouts.app')

@section('content')

@php
    $isKabag = auth()->user()->role === 'kabag';

    $inputClass = 'mt-2 block w-full rounded-lg border border-[#E5E9F0] px-3 py-2.5 text-sm text-[#111827] outline-none transition focus:border-[#1B2F5B] focus:ring-1 focus:ring-[#1B2F5B]';
    $checkboxClass = 'h-4 w-4 rounded border-[#CBD5E1] text-[#1B2F5B] focus:ring-[#1B2F5B]';
@endphp

<div class="space-y-6">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-wrap items-start justify-between gap-4">

        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-[#111827]">
                Kalender Kegiatan
            </h1>

            <p class="mt-1.5 text-sm text-[#64748B]">
                Kelola dan pantau jadwal kegiatan Humas.
            </p>
        </div>

        <div class="flex items-center gap-3">

            <a
                href="{{ route('activities.export.form') }}"
                class="inline-flex h-10 items-center justify-center rounded-lg border border-[#E5E9F0] bg-white px-4 text-sm font-medium text-[#111827] transition hover:bg-[#F1F5F9]"
            >
                Export PDF
            </a>

            @if($isKabag)

                <button
                    type="button"
                    id="open-create-activity"
                    class="inline-flex h-10 items-center justify-center rounded-lg bg-[#1B2F5B] px-4 text-sm font-medium text-white transition hover:bg-[#26407A]"
                >
                    Tambah Kegiatan
                </button>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ERROR --}}
    {{-- ========================================================= --}}

    @if($errors->any())

        <div class="rounded-xl border border-[#FECACA] bg-[#FEF2F2] px-4 py-3">

            <p class="text-sm font-medium text-[#B91C1C]">
                Terdapat kesalahan pada formulir.
            </p>

            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-[#B91C1C]">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- VIEW TABS --}}
    {{-- ========================================================= --}}

    <div class="inline-flex items-center gap-1 rounded-xl bg-[#EEF2F6] p-1">

        <button
            type="button"
            class="flex h-9 items-center gap-2 rounded-lg bg-white px-4 text-sm font-semibold text-[#111827] shadow-sm"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <path d="M16 2v4M8 2v4M3 10h18"/>
            </svg>
            <span>Kalender</span>
        </button>

        <button
            type="button"
            class="flex h-9 items-center gap-2 rounded-lg px-4 text-sm text-[#64748B] transition hover:text-[#111827]"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
            </svg>
            <span>Daftar</span>
        </button>

        <button
            type="button"
            class="flex h-9 items-center gap-2 rounded-lg px-4 text-sm text-[#64748B] transition hover:text-[#111827]"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M3 12a9 9 0 1 0 3-6.7L3 8"/>
                <path d="M3 3v5h5M12 7v5l4 2"/>
            </svg>
            <span>Riwayat</span>
        </button>

    </div>


    {{-- ========================================================= --}}
    {{-- CALENDAR + ACTIVITY --}}
    {{-- ========================================================= --}}

    <div
        id="activity-calendar"
        class="grid grid-cols-1 gap-5 xl:grid-cols-[400px_minmax(0,1fr)]"
    >

        {{-- CALENDAR --}}
        <div class="rounded-2xl border border-[#E5E9F0] bg-white p-4">

            <div class="rounded-xl bg-[#F7F9FC] px-3 pb-3 pt-4">

                <div class="mb-3 flex items-center justify-between px-2">

                    <button
                        type="button"
                        id="previous-month"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-[#111827] transition hover:bg-white"
                        aria-label="Bulan sebelumnya"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M15 18l-6-6 6-6"/>
                        </svg>
                    </button>

                    <h2
                        id="calendar-month-title"
                        class="text-base font-semibold text-[#111827]"
                    ></h2>

                    <button
                        type="button"
                        id="next-month"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-[#111827] transition hover:bg-white"
                        aria-label="Bulan berikutnya"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M9 18l6-6-6-6"/>
                        </svg>
                    </button>

                </div>

                <div class="grid grid-cols-7">
                    @foreach(['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'] as $dayName)
                        <div class="py-2 text-center text-sm text-[#64748B]">{{ $dayName }}</div>
                    @endforeach
                </div>

                <div
                    id="calendar-grid"
                    class="grid grid-cols-7"
                ></div>

            </div>

            <div class="mt-4 border-t border-[#E5E9F0] pt-3">

                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-[#64748B]">

                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-[#DC2626]"></span>
                        Belum dibaca
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-[#1B2F5B]"></span>
                        Sudah dibaca
                    </div>

                </div>

            </div>

        </div>


        {{-- ACTIVITY LIST --}}
        <div class="min-w-0 rounded-2xl border border-[#E5E9F0] bg-white p-6">

            <h2
                id="selected-date-title"
                class="text-base font-semibold text-[#111827]"
            ></h2>

            <div
                id="activity-list"
                class="mt-4 min-h-[400px] space-y-3"
            ></div>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- CREATE ACTIVITY MODAL --}}
{{-- ========================================================= --}}

@if($isKabag)

<div
    id="create-activity-modal"
    class="fixed inset-0 z-50 hidden overflow-y-auto"
    aria-hidden="true"
>

    <div
        class="flex min-h-full items-center justify-center bg-black/40 px-4 py-8"
        data-modal-backdrop="create"
    >

        <div
            class="w-full max-w-2xl rounded-2xl border border-[#E5E9F0] bg-white shadow-xl"
            role="dialog"
            aria-modal="true"
            aria-labelledby="create-activity-title"
        >

            <div class="flex items-center justify-between border-b border-[#E5E9F0] px-6 py-4">

                <div>
                    <h2 id="create-activity-title" class="text-lg font-semibold text-[#111827]">
                        Tambah Kegiatan
                    </h2>
                    <p class="mt-1 text-sm text-[#64748B]">
                        Masukkan informasi kegiatan yang akan dijadwalkan.
                    </p>
                </div>

                <button
                    type="button"
                    data-close-modal="create"
                    class="text-sm font-medium text-[#64748B] transition hover:text-[#111827]"
                >
                    Tutup
                </button>

            </div>

            <form method="POST" action="{{ route('activities.store') }}">

                @csrf

                <div class="max-h-[70vh] overflow-y-auto px-6 py-6">

                    @if($errors->any() && session('open_modal') === 'create')

                        <div class="mb-5 rounded-xl border border-[#FECACA] bg-[#FEF2F2] px-4 py-3 text-sm text-[#B91C1C]">

                            <p class="font-medium">Periksa kembali data yang dimasukkan.</p>

                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>

                        </div>

                    @endif

                    <div>
                        <label for="create-title" class="block text-sm font-medium text-[#111827]">
                            Judul Kegiatan
                        </label>
                        <input
                            type="text"
                            id="create-title"
                            name="title"
                            value="{{ old('title') }}"
                            required
                            class="{{ $inputClass }}"
                            placeholder="Contoh: Rapat Koordinasi Humas"
                        >
                    </div>

                    <div class="mt-5">
                        <label for="create-activity-date" class="block text-sm font-medium text-[#111827]">
                            Tanggal
                        </label>
                        <input
                            type="date"
                            id="create-activity-date"
                            name="activity_date"
                            value="{{ old('activity_date') }}"
                            required
                            class="{{ $inputClass }}"
                        >
                    </div>

                    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <div>
                            <label for="create-start-time" class="block text-sm font-medium text-[#111827]">
                                Jam Mulai
                            </label>
                            <input
                                type="time"
                                id="create-start-time"
                                name="start_time"
                                value="{{ old('start_time') }}"
                                required
                                class="{{ $inputClass }}"
                            >
                        </div>

                        <div>
                            <label for="create-end-time" class="block text-sm font-medium text-[#111827]">
                                Jam Selesai
                            </label>
                            <input
                                type="time"
                                id="create-end-time"
                                name="end_time"
                                value="{{ old('end_time') }}"
                                class="{{ $inputClass }}"
                            >

                            <label class="mt-2 flex items-center gap-2 text-sm text-[#64748B]">
                                <input type="checkbox" id="create-no-end-time" class="{{ $checkboxClass }}">
                                <span>Kegiatan berlangsung sampai selesai</span>
                            </label>
                        </div>

                    </div>

                    <div class="mt-5">
                        <label for="create-location" class="block text-sm font-medium text-[#111827]">
                            Lokasi
                        </label>
                        <input
                            type="text"
                            id="create-location"
                            name="location"
                            value="{{ old('location') }}"
                            class="{{ $inputClass }}"
                            placeholder="Contoh: Ruang Rapat Humas"
                        >
                    </div>

                    <div class="mt-5">

                        <label class="block text-sm font-medium text-[#111827]">PIC</label>

                        <p class="mt-1 text-xs text-[#64748B]">
                            Pilih satu atau lebih pengguna sebagai PIC kegiatan.
                        </p>

                        <div class="mt-3 max-h-40 overflow-y-auto rounded-lg border border-[#E5E9F0]">

                            @foreach($users as $user)

                                <label class="flex items-center gap-3 border-b border-[#EEF2F6] px-3 py-2.5 last:border-b-0">

                                    <input
                                        type="checkbox"
                                        name="pic_ids[]"
                                        value="{{ $user->id }}"
                                        class="{{ $checkboxClass }}"
                                        {{ in_array($user->id, old('pic_ids', [])) ? 'checked' : '' }}
                                    >

                                    <div>
                                        <p class="text-sm font-medium text-[#111827]">{{ $user->name }}</p>
                                        <p class="text-xs text-[#64748B]">{{ ucfirst($user->role) }}</p>
                                    </div>

                                </label>

                            @endforeach

                        </div>

                    </div>

                    <div class="mt-5">
                        <label for="create-description" class="block text-sm font-medium text-[#111827]">
                            Deskripsi
                        </label>
                        <textarea
                            id="create-description"
                            name="description"
                            rows="4"
                            class="{{ $inputClass }} resize-none"
                            placeholder="Tambahkan keterangan kegiatan jika diperlukan."
                        >{{ old('description') }}</textarea>
                    </div>

                </div>

                <div class="flex items-center justify-end gap-3 border-t border-[#E5E9F0] px-6 py-4">

                    <button
                        type="button"
                        data-close-modal="create"
                        class="rounded-lg border border-[#E5E9F0] bg-white px-4 py-2.5 text-sm font-medium text-[#111827] transition hover:bg-[#F1F5F9]"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="rounded-lg bg-[#1B2F5B] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#26407A]"
                    >
                        Simpan Kegiatan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endif


{{-- ========================================================= --}}
{{-- DETAIL ACTIVITY MODAL --}}
{{-- ========================================================= --}}

<div
    id="detail-activity-modal"
    class="fixed inset-0 z-50 hidden overflow-y-auto"
    aria-hidden="true"
>

    <div
        class="flex min-h-full items-center justify-center bg-black/40 px-4 py-8"
        data-modal-backdrop="detail"
    >

        <div
            class="relative w-full max-w-2xl rounded-2xl border border-[#E2E8E4] bg-white shadow-xl"
            role="dialog"
            aria-modal="true"
            aria-labelledby="detail-activity-title"
        >

            {{-- Tombol close (ikon X) --}}
            <button
                type="button"
                data-close-modal="detail"
                aria-label="Tutup"
                class="absolute right-4 top-4 flex h-8 w-8 items-center justify-center rounded-lg text-[#68746D] transition hover:bg-[#F1F4F1] hover:text-[#24332A]"
            >
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>

            <div class="max-h-[75vh] overflow-y-auto px-6 pb-6 pt-6">

                {{-- Judul --}}
                <h2
                    id="detail-activity-title"
                    class="pr-10 text-2xl font-semibold tracking-tight text-[#24332A]"
                >
                    -
                </h2>

                <p class="mt-1 text-sm text-[#68746D]">
                    Rincian jadwal kegiatan dan tugas.
                </p>


                {{-- Info: tanggal, waktu, lokasi --}}
                <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-3 rounded-xl border border-[#E2E8E4] bg-[#F4F7F5] p-5 sm:grid-cols-2">

                    <div class="flex items-center gap-3 text-sm text-[#24332A]">
                        <svg class="shrink-0" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <path d="M16 2v4M8 2v4M3 10h18"/>
                        </svg>
                        <span id="detail-date">-</span>
                    </div>

                    <div class="flex items-center gap-3 text-sm text-[#24332A]">
                        <svg class="shrink-0" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 6v6l4 2"/>
                        </svg>
                        <span id="detail-time">-</span>
                    </div>

                    <div class="flex items-center gap-3 text-sm text-[#24332A]">
                        <svg class="shrink-0" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <span id="detail-location">-</span>
                    </div>

                </div>


                {{-- Ditugaskan kepada --}}
                <h3 class="mt-6 flex items-center gap-2 text-sm font-semibold text-[#24332A]">
                    <svg class="shrink-0" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    Ditugaskan kepada
                </h3>

                <div
                    id="detail-pics"
                    class="mt-3 flex flex-wrap gap-2"
                ></div>


                {{-- Deskripsi --}}
                <h3 class="mt-6 text-sm font-semibold text-[#24332A]">
                    Deskripsi Tugas
                </h3>

                <div class="mt-3 rounded-xl border border-[#E2E8E4] bg-white p-5">
                    <p
                        id="detail-description"
                        class="whitespace-pre-line text-sm leading-6 text-[#24332A]"
                    >
                        -
                    </p>
                </div>


                {{-- Dibuat oleh --}}
                <p class="mt-5 text-xs text-[#68746D]">
                    Dibuat oleh
                    <span id="detail-creator" class="font-medium text-[#24332A]">-</span>
                </p>

            </div>


            {{-- Footer --}}
            <div class="flex items-center justify-between border-t border-[#E2E8E4] px-6 py-4">

                @if(auth()->user()->role === 'kabag')

                    <form
                        id="delete-activity-form"
                        method="POST"
                        action=""
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?');"
                    >

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="rounded-lg border border-[#FECACA] px-4 py-2.5 text-sm font-medium text-[#DC2626] transition hover:bg-[#FEF2F2]"
                        >
                            Hapus
                        </button>

                    </form>

                    <button
                        type="button"
                        id="open-edit-from-detail"
                        class="rounded-lg bg-[#234936] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#315A45]"
                    >
                        Edit Kegiatan
                    </button>

                @else

                    <div></div>

                    <button
                        type="button"
                        data-close-modal="detail"
                        class="rounded-lg border border-[#E2E8E4] bg-white px-4 py-2.5 text-sm font-medium text-[#24332A] transition hover:bg-[#F1F4F1]"
                    >
                        Tutup
                    </button>

                @endif

            </div>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- EDIT ACTIVITY MODAL --}}
{{-- ========================================================= --}}

@if($isKabag)

<div
    id="edit-activity-modal"
    class="fixed inset-0 z-50 hidden overflow-y-auto"
    aria-hidden="true"
>

    <div
        class="flex min-h-full items-center justify-center bg-black/40 px-4 py-8"
        data-modal-backdrop="edit"
    >

        <div
            class="w-full max-w-2xl rounded-2xl border border-[#E5E9F0] bg-white shadow-xl"
            role="dialog"
            aria-modal="true"
            aria-labelledby="edit-activity-title"
        >

            <div class="flex items-center justify-between border-b border-[#E5E9F0] px-6 py-4">

                <div>
                    <h2 id="edit-activity-title" class="text-lg font-semibold text-[#111827]">
                        Edit Kegiatan
                    </h2>
                    <p class="mt-1 text-sm text-[#64748B]">
                        Perbarui informasi kegiatan.
                    </p>
                </div>

                <button
                    type="button"
                    data-close-modal="edit"
                    class="text-sm font-medium text-[#64748B] transition hover:text-[#111827]"
                >
                    Tutup
                </button>

            </div>

            <form id="edit-activity-form" method="POST" action="">

                @csrf
                @method('PUT')

                <div class="max-h-[70vh] overflow-y-auto px-6 py-6">

                    @if($errors->any() && session('open_modal') === 'edit')

                        <div class="mb-5 rounded-xl border border-[#FECACA] bg-[#FEF2F2] px-4 py-3 text-sm text-[#B91C1C]">

                            <p class="font-medium">Periksa kembali data yang dimasukkan.</p>

                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>

                        </div>

                    @endif

                    <div>
                        <label for="edit-title" class="block text-sm font-medium text-[#111827]">
                            Judul Kegiatan
                        </label>
                        <input
                            type="text"
                            id="edit-title"
                            name="title"
                            required
                            class="{{ $inputClass }}"
                        >
                    </div>

                    <div class="mt-5">
                        <label for="edit-activity-date" class="block text-sm font-medium text-[#111827]">
                            Tanggal
                        </label>
                        <input
                            type="date"
                            id="edit-activity-date"
                            name="activity_date"
                            required
                            class="{{ $inputClass }}"
                        >
                    </div>

                    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <div>
                            <label for="edit-start-time" class="block text-sm font-medium text-[#111827]">
                                Jam Mulai
                            </label>
                            <input
                                type="time"
                                id="edit-start-time"
                                name="start_time"
                                required
                                class="{{ $inputClass }}"
                            >
                        </div>

                        <div>
                            <label for="edit-end-time" class="block text-sm font-medium text-[#111827]">
                                Jam Selesai
                            </label>
                            <input
                                type="time"
                                id="edit-end-time"
                                name="end_time"
                                class="{{ $inputClass }}"
                            >

                            <label class="mt-2 flex items-center gap-2 text-sm text-[#64748B]">
                                <input type="checkbox" id="edit-no-end-time" class="{{ $checkboxClass }}">
                                <span>Kegiatan berlangsung sampai selesai</span>
                            </label>
                        </div>

                    </div>

                    <div class="mt-5">
                        <label for="edit-location" class="block text-sm font-medium text-[#111827]">
                            Lokasi
                        </label>
                        <input
                            type="text"
                            id="edit-location"
                            name="location"
                            class="{{ $inputClass }}"
                        >
                    </div>

                    <div class="mt-5">

                        <label class="block text-sm font-medium text-[#111827]">PIC</label>

                        <p class="mt-1 text-xs text-[#64748B]">
                            Pilih satu atau lebih pengguna sebagai PIC kegiatan.
                        </p>

                        <div class="mt-3 max-h-40 overflow-y-auto rounded-lg border border-[#E5E9F0]">

                            @foreach($users as $user)

                                <label class="flex items-center gap-3 border-b border-[#EEF2F6] px-3 py-2.5 last:border-b-0">

                                    <input
                                        type="checkbox"
                                        name="pic_ids[]"
                                        value="{{ $user->id }}"
                                        data-edit-pic
                                        class="{{ $checkboxClass }}"
                                    >

                                    <div>
                                        <p class="text-sm font-medium text-[#111827]">{{ $user->name }}</p>
                                        <p class="text-xs text-[#64748B]">{{ ucfirst($user->role) }}</p>
                                    </div>

                                </label>

                            @endforeach

                        </div>

                    </div>

                    <div class="mt-5">
                        <label for="edit-description" class="block text-sm font-medium text-[#111827]">
                            Deskripsi
                        </label>
                        <textarea
                            id="edit-description"
                            name="description"
                            rows="4"
                            class="{{ $inputClass }} resize-none"
                        ></textarea>
                    </div>

                </div>

                <div class="flex items-center justify-end gap-3 border-t border-[#E5E9F0] px-6 py-4">

                    <button
                        type="button"
                        data-close-modal="edit"
                        class="rounded-lg border border-[#E5E9F0] bg-white px-4 py-2.5 text-sm font-medium text-[#111827] transition hover:bg-[#F1F5F9]"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="rounded-lg bg-[#1B2F5B] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#26407A]"
                    >
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endif


{{-- ========================================================= --}}
{{-- JAVASCRIPT DATA --}}
{{-- ========================================================= --}}

<script id="calendar-events-data" type="application/json">
    @json($calendarEvents)
</script>

<script>
    window.activityModalState = {
        openModal: @json(session('open_modal')),
        editId: @json(session('edit_id')),
        hasOldInput: @json(session()->hasOldInput()),

        old: {
            title: @json(old('title')),
            activity_date: @json(old('activity_date')),
            start_time: @json(old('start_time')),
            end_time: @json(old('end_time')),
            location: @json(old('location')),
            description: @json(old('description')),
            pic_ids: @json(old('pic_ids', []))
        }
    };
</script>

@endsection
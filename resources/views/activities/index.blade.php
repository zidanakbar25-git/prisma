@extends('layouts.app')

@section('content')

<div class="space-y-7">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex items-start justify-between gap-6">

        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-[#24332A]">
                Kalender Kegiatan
            </h1>

            <p class="mt-1.5 text-sm text-[#6B7280]">
                Kelola dan pantau jadwal kegiatan Humas.
            </p>
        </div>


        <div class="flex items-center gap-3">

            <a
                href="{{ route('activities.export.form') }}"
                class="inline-flex h-10 items-center justify-center border border-[#D7DED8] bg-white px-4 text-sm font-medium text-[#24332A] transition hover:bg-[#F4F6F4]"
            >
                Export PDF
            </a>

            @if(auth()->user()->role === 'kabag')

                <button
                    type="button"
                    id="open-create-activity"
                    class="inline-flex h-10 items-center justify-center bg-[#234936] px-4 text-sm font-medium text-white transition hover:bg-[#315A45]"
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

        <div class="border border-[#E6CACA] bg-[#FCF4F4] px-4 py-3">

            <p class="text-sm font-medium text-[#8A3F3F]">
                Terdapat kesalahan pada formulir.
            </p>

            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-[#8A3F3F]">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- VIEW TABS --}}
    {{-- ========================================================= --}}

    <div class="inline-flex items-center border border-[#E1E6E2] bg-[#EEF2F3] p-1">

        <button
            type="button"
            class="flex h-9 items-center gap-2 bg-white px-4 text-sm font-medium text-[#24332A] shadow-sm"
        >
            <span>Kalender</span>
        </button>

        <button
            type="button"
            class="flex h-9 items-center gap-2 px-4 text-sm text-[#64716A] transition hover:text-[#24332A]"
        >
            <span>Daftar</span>
        </button>

        <button
            type="button"
            class="flex h-9 items-center gap-2 px-4 text-sm text-[#64716A] transition hover:text-[#24332A]"
        >
            <span>Riwayat</span>
        </button>

    </div>


    {{-- ========================================================= --}}
    {{-- CALENDAR + ACTIVITY --}}
    {{-- ========================================================= --}}

    <div
        id="activity-calendar"
        class="grid grid-cols-1 gap-5 xl:grid-cols-[340px_minmax(0,1fr)]"
    >


        {{-- ===================================================== --}}
        {{-- CALENDAR --}}
        {{-- ===================================================== --}}

        <div class="border border-[#DDE4DE] bg-white">

            {{-- Calendar Header --}}
            <div class="border-b border-[#E2E7E2] px-5 py-4">

                <div class="flex items-center justify-between">

                    <button
                        type="button"
                        id="previous-month"
                        class="flex h-8 w-8 items-center justify-center text-lg text-[#526058] transition hover:bg-[#F1F4F1] hover:text-[#24332A]"
                        aria-label="Bulan sebelumnya"
                    >
                        ‹
                    </button>


                    <h2
                        id="calendar-month-title"
                        class="text-base font-semibold text-[#24332A]"
                    ></h2>


                    <button
                        type="button"
                        id="next-month"
                        class="flex h-8 w-8 items-center justify-center text-lg text-[#526058] transition hover:bg-[#F1F4F1] hover:text-[#24332A]"
                        aria-label="Bulan berikutnya"
                    >
                        ›
                    </button>

                </div>

            </div>


            {{-- Calendar Body --}}
            <div class="p-4">

                {{-- Calendar Surface --}}
                <div class="bg-[#F7F8F9] px-3 pb-3 pt-4">

                    {{-- Day Names --}}
                    <div class="grid grid-cols-7">

                        <div class="py-2 text-center text-xs font-medium text-[#68746D]">
                            Min
                        </div>

                        <div class="py-2 text-center text-xs font-medium text-[#68746D]">
                            Sen
                        </div>

                        <div class="py-2 text-center text-xs font-medium text-[#68746D]">
                            Sel
                        </div>

                        <div class="py-2 text-center text-xs font-medium text-[#68746D]">
                            Rab
                        </div>

                        <div class="py-2 text-center text-xs font-medium text-[#68746D]">
                            Kam
                        </div>

                        <div class="py-2 text-center text-xs font-medium text-[#68746D]">
                            Jum
                        </div>

                        <div class="py-2 text-center text-xs font-medium text-[#68746D]">
                            Sab
                        </div>

                    </div>


                    {{-- Calendar Grid --}}
                    <div
                        id="calendar-grid"
                        class="grid grid-cols-7"
                    ></div>

                </div>


                {{-- Calendar Legend --}}
                <div class="mt-4 border-t border-[#E2E7E2] pt-3">

                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2">

                        <div class="flex items-center gap-2">

                            <span class="h-1.5 w-1.5 rounded-full bg-[#234936]"></span>

                            <span class="text-xs text-[#68746D]">
                                Ada kegiatan
                            </span>

                        </div>

                        <div class="flex items-center gap-2">

                            <span class="h-1.5 w-1.5 rounded-full bg-[#AAB4AD]"></span>

                            <span class="text-xs text-[#68746D]">
                                Hari ini
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- ACTIVITY LIST --}}
        {{-- ===================================================== --}}

        <div class="min-w-0 border border-[#DDE4DE] bg-white">

            {{-- List Header --}}
            <div class="border-b border-[#E2E7E2] px-6 py-5">

                <p class="text-xs font-medium uppercase tracking-wide text-[#7A837D]">
                    Agenda
                </p>

                <h2
                    id="selected-date-title"
                    class="mt-1.5 text-lg font-semibold text-[#24332A]"
                ></h2>

            </div>


            {{-- Activity Content --}}
            <div
                id="activity-list"
                class="min-h-[460px] p-6"
            ></div>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- CREATE ACTIVITY MODAL --}}
{{-- ========================================================= --}}

@if(auth()->user()->role === 'kabag')

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
            class="w-full max-w-2xl border border-[#DDE4DE] bg-white shadow-xl"
            role="dialog"
            aria-modal="true"
            aria-labelledby="create-activity-title"
        >

            <div class="flex items-center justify-between border-b border-[#E2E7E2] px-6 py-4">

                <div>

                    <h2
                        id="create-activity-title"
                        class="text-lg font-semibold text-[#24332A]"
                    >
                        Tambah Kegiatan
                    </h2>

                    <p class="mt-1 text-sm text-[#6B7280]">
                        Masukkan informasi kegiatan yang akan dijadwalkan.
                    </p>

                </div>

                <button
                    type="button"
                    data-close-modal="create"
                    class="text-sm font-medium text-[#6B7280] transition hover:text-[#24332A]"
                >
                    Tutup
                </button>

            </div>


            <form
                method="POST"
                action="{{ route('activities.store') }}"
            >

                @csrf

                <div class="max-h-[70vh] overflow-y-auto px-6 py-6">

                    @if($errors->any() && session('open_modal') === 'create')

                        <div class="mb-5 border border-[#E6CACA] bg-[#FCF4F4] px-4 py-3 text-sm text-[#8A3F3F]">

                            <p class="font-medium">
                                Periksa kembali data yang dimasukkan.
                            </p>

                            <ul class="mt-2 list-disc space-y-1 pl-5">

                                @foreach($errors->all() as $error)

                                    <li>
                                        {{ $error }}
                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endif


                    <div>

                        <label
                            for="create-title"
                            class="block text-sm font-medium text-[#24332A]"
                        >
                            Judul Kegiatan
                        </label>

                        <input
                            type="text"
                            id="create-title"
                            name="title"
                            value="{{ old('title') }}"
                            required
                            class="mt-2 block w-full border border-[#D5DED7] px-3 py-2.5 text-sm text-[#24332A] outline-none transition focus:border-[#234936] focus:ring-1 focus:ring-[#234936]"
                            placeholder="Contoh: Rapat Koordinasi Humas"
                        >

                    </div>


                    <div class="mt-5">

                        <label
                            for="create-activity-date"
                            class="block text-sm font-medium text-[#24332A]"
                        >
                            Tanggal
                        </label>

                        <input
                            type="date"
                            id="create-activity-date"
                            name="activity_date"
                            value="{{ old('activity_date') }}"
                            required
                            class="mt-2 block w-full border border-[#D5DED7] px-3 py-2.5 text-sm text-[#24332A] outline-none transition focus:border-[#234936] focus:ring-1 focus:ring-[#234936]"
                        >

                    </div>


                    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <div>

                            <label
                                for="create-start-time"
                                class="block text-sm font-medium text-[#24332A]"
                            >
                                Jam Mulai
                            </label>

                            <input
                                type="time"
                                id="create-start-time"
                                name="start_time"
                                value="{{ old('start_time') }}"
                                required
                                class="mt-2 block w-full border border-[#D5DED7] px-3 py-2.5 text-sm text-[#24332A] outline-none transition focus:border-[#234936] focus:ring-1 focus:ring-[#234936]"
                            >

                        </div>


                        <div>

                            <label
                                for="create-end-time"
                                class="block text-sm font-medium text-[#24332A]"
                            >
                                Jam Selesai
                            </label>

                            <input
                                type="time"
                                id="create-end-time"
                                name="end_time"
                                value="{{ old('end_time') }}"
                                class="mt-2 block w-full border border-[#D5DED7] px-3 py-2.5 text-sm text-[#24332A] outline-none transition focus:border-[#234936] focus:ring-1 focus:ring-[#234936]"
                            >

                            <label class="mt-2 flex items-center gap-2 text-sm text-[#6B7280]">

                                <input
                                    type="checkbox"
                                    id="create-no-end-time"
                                    class="h-4 w-4 border-[#C8D1CB] text-[#234936] focus:ring-[#234936]"
                                >

                                <span>
                                    Kegiatan berlangsung sampai selesai
                                </span>

                            </label>

                        </div>

                    </div>


                    <div class="mt-5">

                        <label
                            for="create-location"
                            class="block text-sm font-medium text-[#24332A]"
                        >
                            Lokasi
                        </label>

                        <input
                            type="text"
                            id="create-location"
                            name="location"
                            value="{{ old('location') }}"
                            class="mt-2 block w-full border border-[#D5DED7] px-3 py-2.5 text-sm text-[#24332A] outline-none transition focus:border-[#234936] focus:ring-1 focus:ring-[#234936]"
                            placeholder="Contoh: Ruang Rapat Humas"
                        >

                    </div>


                    <div class="mt-5">

                        <label class="block text-sm font-medium text-[#24332A]">
                            PIC
                        </label>

                        <p class="mt-1 text-xs text-[#7A837D]">
                            Pilih satu atau lebih pengguna sebagai PIC kegiatan.
                        </p>

                        <div class="mt-3 max-h-40 overflow-y-auto border border-[#D5DED7]">

                            @foreach($users as $user)

                                <label class="flex items-center gap-3 border-b border-[#EEF1EE] px-3 py-2.5 last:border-b-0">

                                    <input
                                        type="checkbox"
                                        name="pic_ids[]"
                                        value="{{ $user->id }}"
                                        class="h-4 w-4 border-[#C8D1CB] text-[#234936] focus:ring-[#234936]"
                                        {{ in_array($user->id, old('pic_ids', [])) ? 'checked' : '' }}
                                    >

                                    <div>

                                        <p class="text-sm font-medium text-[#24332A]">
                                            {{ $user->name }}
                                        </p>

                                        <p class="text-xs text-[#7A837D]">
                                            {{ ucfirst($user->role) }}
                                        </p>

                                    </div>

                                </label>

                            @endforeach

                        </div>

                    </div>


                    <div class="mt-5">

                        <label
                            for="create-description"
                            class="block text-sm font-medium text-[#24332A]"
                        >
                            Deskripsi
                        </label>

                        <textarea
                            id="create-description"
                            name="description"
                            rows="4"
                            class="mt-2 block w-full resize-none border border-[#D5DED7] px-3 py-2.5 text-sm text-[#24332A] outline-none transition focus:border-[#234936] focus:ring-1 focus:ring-[#234936]"
                            placeholder="Tambahkan keterangan kegiatan jika diperlukan."
                        >{{ old('description') }}</textarea>

                    </div>

                </div>


                <div class="flex items-center justify-end gap-3 border-t border-[#E2E7E2] px-6 py-4">

                    <button
                        type="button"
                        data-close-modal="create"
                        class="border border-[#D5DED7] bg-white px-4 py-2.5 text-sm font-medium text-[#24332A] transition hover:bg-[#F3F6F3]"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="bg-[#234936] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#315A45]"
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
            class="w-full max-w-xl border border-[#DDE4DE] bg-white shadow-xl"
            role="dialog"
            aria-modal="true"
            aria-labelledby="detail-activity-title"
        >

            <div class="flex items-start justify-between border-b border-[#E2E7E2] px-6 py-5">

                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-[#7A837D]">
                        Detail Kegiatan
                    </p>

                    <h2
                        id="detail-activity-title"
                        class="mt-1 text-xl font-semibold text-[#24332A]"
                    >
                        -
                    </h2>

                </div>

                <button
                    type="button"
                    data-close-modal="detail"
                    class="text-sm font-medium text-[#6B7280] transition hover:text-[#24332A]"
                >
                    Tutup
                </button>

            </div>


            <div class="px-6 py-6">

                <div class="space-y-5">

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-[#7A837D]">
                            Tanggal
                        </p>

                        <p
                            id="detail-date"
                            class="mt-1 text-sm text-[#24332A]"
                        >
                            -
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-[#7A837D]">
                            Waktu
                        </p>

                        <p
                            id="detail-time"
                            class="mt-1 text-sm text-[#24332A]"
                        >
                            -
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-[#7A837D]">
                            Lokasi
                        </p>

                        <p
                            id="detail-location"
                            class="mt-1 text-sm text-[#24332A]"
                        >
                            -
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-[#7A837D]">
                            PIC
                        </p>

                        <div
                            id="detail-pics"
                            class="mt-2 space-y-1"
                        ></div>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-[#7A837D]">
                            Deskripsi
                        </p>

                        <p
                            id="detail-description"
                            class="mt-1 whitespace-pre-line text-sm leading-6 text-[#24332A]"
                        >
                            -
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-[#7A837D]">
                            Dibuat Oleh
                        </p>

                        <p
                            id="detail-creator"
                            class="mt-1 text-sm text-[#24332A]"
                        >
                            -
                        </p>

                    </div>

                </div>

            </div>


            <div class="flex items-center justify-between border-t border-[#E2E7E2] px-6 py-4">

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
                            class="border border-[#E2CFCF] px-4 py-2.5 text-sm font-medium text-[#8A3F3F] transition hover:bg-[#FCF4F4]"
                        >
                            Hapus
                        </button>

                    </form>


                    <button
                        type="button"
                        id="open-edit-from-detail"
                        class="bg-[#234936] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#315A45]"
                    >
                        Edit Kegiatan
                    </button>

                @else

                    <div></div>

                    <button
                        type="button"
                        data-close-modal="detail"
                        class="border border-[#D5DED7] bg-white px-4 py-2.5 text-sm font-medium text-[#24332A] transition hover:bg-[#F3F6F3]"
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

@if(auth()->user()->role === 'kabag')

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
            class="w-full max-w-2xl border border-[#DDE4DE] bg-white shadow-xl"
            role="dialog"
            aria-modal="true"
            aria-labelledby="edit-activity-title"
        >

            <div class="flex items-center justify-between border-b border-[#E2E7E2] px-6 py-4">

                <div>

                    <h2
                        id="edit-activity-title"
                        class="text-lg font-semibold text-[#24332A]"
                    >
                        Edit Kegiatan
                    </h2>

                    <p class="mt-1 text-sm text-[#6B7280]">
                        Perbarui informasi kegiatan.
                    </p>

                </div>

                <button
                    type="button"
                    data-close-modal="edit"
                    class="text-sm font-medium text-[#6B7280] transition hover:text-[#24332A]"
                >
                    Tutup
                </button>

            </div>


            <form
                id="edit-activity-form"
                method="POST"
                action=""
            >

                @csrf
                @method('PUT')

                <div class="max-h-[70vh] overflow-y-auto px-6 py-6">

                    @if($errors->any() && session('open_modal') === 'edit')

                        <div class="mb-5 border border-[#E6CACA] bg-[#FCF4F4] px-4 py-3 text-sm text-[#8A3F3F]">

                            <p class="font-medium">
                                Periksa kembali data yang dimasukkan.
                            </p>

                            <ul class="mt-2 list-disc space-y-1 pl-5">

                                @foreach($errors->all() as $error)

                                    <li>
                                        {{ $error }}
                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endif


                    <div>

                        <label
                            for="edit-title"
                            class="block text-sm font-medium text-[#24332A]"
                        >
                            Judul Kegiatan
                        </label>

                        <input
                            type="text"
                            id="edit-title"
                            name="title"
                            required
                            class="mt-2 block w-full border border-[#D5DED7] px-3 py-2.5 text-sm text-[#24332A] outline-none transition focus:border-[#234936] focus:ring-1 focus:ring-[#234936]"
                        >

                    </div>


                    <div class="mt-5">

                        <label
                            for="edit-activity-date"
                            class="block text-sm font-medium text-[#24332A]"
                        >
                            Tanggal
                        </label>

                        <input
                            type="date"
                            id="edit-activity-date"
                            name="activity_date"
                            required
                            class="mt-2 block w-full border border-[#D5DED7] px-3 py-2.5 text-sm text-[#24332A] outline-none transition focus:border-[#234936] focus:ring-1 focus:ring-[#234936]"
                        >

                    </div>


                    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <div>

                            <label
                                for="edit-start-time"
                                class="block text-sm font-medium text-[#24332A]"
                            >
                                Jam Mulai
                            </label>

                            <input
                                type="time"
                                id="edit-start-time"
                                name="start_time"
                                required
                                class="mt-2 block w-full border border-[#D5DED7] px-3 py-2.5 text-sm text-[#24332A] outline-none transition focus:border-[#234936] focus:ring-1 focus:ring-[#234936]"
                            >

                        </div>


                        <div>

                            <label
                                for="edit-end-time"
                                class="block text-sm font-medium text-[#24332A]"
                            >
                                Jam Selesai
                            </label>

                            <input
                                type="time"
                                id="edit-end-time"
                                name="end_time"
                                class="mt-2 block w-full border border-[#D5DED7] px-3 py-2.5 text-sm text-[#24332A] outline-none transition focus:border-[#234936] focus:ring-1 focus:ring-[#234936]"
                            >

                            <label class="mt-2 flex items-center gap-2 text-sm text-[#6B7280]">

                                <input
                                    type="checkbox"
                                    id="edit-no-end-time"
                                    class="h-4 w-4 border-[#C8D1CB] text-[#234936] focus:ring-[#234936]"
                                >

                                <span>
                                    Kegiatan berlangsung sampai selesai
                                </span>

                            </label>

                        </div>

                    </div>


                    <div class="mt-5">

                        <label
                            for="edit-location"
                            class="block text-sm font-medium text-[#24332A]"
                        >
                            Lokasi
                        </label>

                        <input
                            type="text"
                            id="edit-location"
                            name="location"
                            class="mt-2 block w-full border border-[#D5DED7] px-3 py-2.5 text-sm text-[#24332A] outline-none transition focus:border-[#234936] focus:ring-1 focus:ring-[#234936]"
                        >

                    </div>


                    <div class="mt-5">

                        <label class="block text-sm font-medium text-[#24332A]">
                            PIC
                        </label>

                        <p class="mt-1 text-xs text-[#7A837D]">
                            Pilih satu atau lebih pengguna sebagai PIC kegiatan.
                        </p>

                        <div class="mt-3 max-h-40 overflow-y-auto border border-[#D5DED7]">

                            @foreach($users as $user)

                                <label class="flex items-center gap-3 border-b border-[#EEF1EE] px-3 py-2.5 last:border-b-0">

                                    <input
                                        type="checkbox"
                                        name="pic_ids[]"
                                        value="{{ $user->id }}"
                                        data-edit-pic
                                        class="h-4 w-4 border-[#C8D1CB] text-[#234936] focus:ring-[#234936]"
                                    >

                                    <div>

                                        <p class="text-sm font-medium text-[#24332A]">
                                            {{ $user->name }}
                                        </p>

                                        <p class="text-xs text-[#7A837D]">
                                            {{ ucfirst($user->role) }}
                                        </p>

                                    </div>

                                </label>

                            @endforeach

                        </div>

                    </div>


                    <div class="mt-5">

                        <label
                            for="edit-description"
                            class="block text-sm font-medium text-[#24332A]"
                        >
                            Deskripsi
                        </label>

                        <textarea
                            id="edit-description"
                            name="description"
                            rows="4"
                            class="mt-2 block w-full resize-none border border-[#D5DED7] px-3 py-2.5 text-sm text-[#24332A] outline-none transition focus:border-[#234936] focus:ring-1 focus:ring-[#234936]"
                        ></textarea>

                    </div>

                </div>


                <div class="flex items-center justify-end gap-3 border-t border-[#E2E7E2] px-6 py-4">

                    <button
                        type="button"
                        data-close-modal="edit"
                        class="border border-[#D5DED7] bg-white px-4 py-2.5 text-sm font-medium text-[#24332A] transition hover:bg-[#F3F6F3]"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="bg-[#234936] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#315A45]"
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
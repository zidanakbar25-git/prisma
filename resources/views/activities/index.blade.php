@extends('layouts.app')

@section('content')
    <div class="p-8">

        {{-- Header --}}
        <div class="flex items-start justify-between mb-7">

            <div>
                <h1 class="text-2xl font-semibold text-[#24332A]">
                    Kalender Kegiatan
                </h1>

                <p class="mt-1 text-sm text-[#6B7280]">
                    Jadwal dan agenda kegiatan Biro Humas.
                </p>
            </div>


            @if(auth()->user()->role === 'kabag')

                <a
                    href="{{ route('activities.create') }}"
                    class="px-4 py-2.5 text-sm font-medium text-white bg-[#234936] rounded-md hover:bg-[#315A45]"
                >
                    Tambah Kegiatan
                </a>

            @endif

        </div>


        {{-- Flash Message --}}
        @if(session('success'))

            <div class="mb-5 px-4 py-3 text-sm text-[#234936] bg-[#EEF5EF] border border-[#D5E4D7] rounded-md">
                {{ session('success') }}
            </div>

        @endif


        {{-- Main Calendar --}}
        <div
            id="activity-calendar"
            data-events='@json($calendarEvents)'
        >

            <div class="grid grid-cols-1 xl:grid-cols-[390px_minmax(0,1fr)] gap-6">


                {{-- ====================================================== --}}
                {{-- LEFT : CALENDAR --}}
                {{-- ====================================================== --}}

                <div class="bg-white border border-[#E2E7E2] rounded-lg p-5">

                    {{-- Calendar Header --}}
                    <div class="flex items-center justify-between mb-5">

                        <button
                            type="button"
                            id="previous-month"
                            class="w-9 h-9 flex items-center justify-center rounded-md text-[#24332A] hover:bg-[#F0F4F1]"
                            aria-label="Bulan sebelumnya"
                        >
                            <span class="text-xl leading-none">
                                ‹
                            </span>
                        </button>


                        <h2
                            id="calendar-month-title"
                            class="text-lg font-semibold text-[#24332A]"
                        >
                            {{ now()->translatedFormat('F Y') }}
                        </h2>


                        <button
                            type="button"
                            id="next-month"
                            class="w-9 h-9 flex items-center justify-center rounded-md text-[#24332A] hover:bg-[#F0F4F1]"
                            aria-label="Bulan berikutnya"
                        >
                            <span class="text-xl leading-none">
                                ›
                            </span>
                        </button>

                    </div>


                    {{-- Today --}}
                    <div class="flex justify-end mb-4">

                        <button
                            type="button"
                            id="today-button"
                            class="px-3 py-1.5 text-xs font-medium text-[#234936] border border-[#D5DDD6] rounded-md hover:bg-[#F7F8F6]"
                        >
                            Hari Ini
                        </button>

                    </div>


                    {{-- Day Names --}}
                    <div class="grid grid-cols-7 mb-2">

                        <div class="py-2 text-center text-xs font-medium text-[#8A938D]">
                            Min
                        </div>

                        <div class="py-2 text-center text-xs font-medium text-[#8A938D]">
                            Sen
                        </div>

                        <div class="py-2 text-center text-xs font-medium text-[#8A938D]">
                            Sel
                        </div>

                        <div class="py-2 text-center text-xs font-medium text-[#8A938D]">
                            Rab
                        </div>

                        <div class="py-2 text-center text-xs font-medium text-[#8A938D]">
                            Kam
                        </div>

                        <div class="py-2 text-center text-xs font-medium text-[#8A938D]">
                            Jum
                        </div>

                        <div class="py-2 text-center text-xs font-medium text-[#8A938D]">
                            Sab
                        </div>

                    </div>


                    {{-- Calendar Dates --}}
                    <div
                        id="calendar-grid"
                        class="grid grid-cols-7 gap-y-1"
                    ></div>


                    {{-- Legend --}}
                    <div class="mt-5 pt-4 border-t border-[#E2E7E2]">

                        <div class="flex items-center gap-2 text-xs text-[#6B7280]">

                            <span class="w-1.5 h-1.5 rounded-full bg-[#234936]"></span>

                            <span>
                                Ada kegiatan
                            </span>

                        </div>

                    </div>

                </div>


                {{-- ====================================================== --}}
                {{-- RIGHT : ACTIVITY LIST --}}
                {{-- ====================================================== --}}

                <div class="bg-white border border-[#E2E7E2] rounded-lg p-6">

                    {{-- Selected Date --}}
                    <div class="pb-5 border-b border-[#E2E7E2]">

                        <h2
                            id="selected-date-title"
                            class="text-lg font-semibold text-[#24332A]"
                        >
                            Kegiatan
                        </h2>

                        <p class="mt-1 text-sm text-[#6B7280]">
                            Daftar kegiatan pada tanggal yang dipilih.
                        </p>

                    </div>


                    {{-- Activity List --}}
                    <div
                        id="activity-list"
                        class="mt-5 space-y-4"
                    ></div>

                </div>

            </div>

        </div>

    </div>
@endsection
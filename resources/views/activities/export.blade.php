@extends('layouts.app')

@section('content')
    <div class="p-8 max-w-3xl">

        <div class="mb-6">

            <h1 class="text-2xl font-semibold text-[#24332A]">
                Export Kalender
            </h1>

            <p class="mt-1 text-sm text-[#6B7280]">
                Export daftar kegiatan Humas ke dalam format PDF.
            </p>

        </div>


        @if($errors->any())

            <div class="mb-5 px-4 py-3 text-sm text-red-700 bg-red-50 border border-red-200 rounded-md">

                <ul class="list-disc list-inside space-y-1">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        <div class="bg-white border border-[#E2E7E2] rounded-lg p-6">

            <form
                action="{{ route('activities.export.pdf') }}"
                method="GET"
            >

                <div class="space-y-6">


                    {{-- Jenis Periode --}}
                    <div>

                        <label class="block mb-3 text-sm font-medium text-[#24332A]">
                            Periode Export
                        </label>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">


                            {{-- Bulan --}}
                            <label
                                class="flex items-start gap-3 p-4 border border-[#D5DDD6] rounded-md cursor-pointer hover:bg-[#F7F8F6]"
                            >

                                <input
                                    type="radio"
                                    name="filter_type"
                                    value="month"
                                    checked
                                    class="mt-1 text-[#234936] focus:ring-[#315A45]"
                                >

                                <div>

                                    <p class="text-sm font-medium text-[#24332A]">
                                        Berdasarkan Bulan
                                    </p>

                                    <p class="mt-1 text-xs text-[#6B7280]">
                                        Export seluruh kegiatan dalam satu bulan.
                                    </p>

                                </div>

                            </label>


                            {{-- Range --}}
                            <label
                                class="flex items-start gap-3 p-4 border border-[#D5DDD6] rounded-md cursor-pointer hover:bg-[#F7F8F6]"
                            >

                                <input
                                    type="radio"
                                    name="filter_type"
                                    value="range"
                                    class="mt-1 text-[#234936] focus:ring-[#315A45]"
                                >

                                <div>

                                    <p class="text-sm font-medium text-[#24332A]">
                                        Rentang Tanggal
                                    </p>

                                    <p class="mt-1 text-xs text-[#6B7280]">
                                        Export kegiatan berdasarkan tanggal tertentu.
                                    </p>

                                </div>

                            </label>

                        </div>

                    </div>


                    {{-- Bulan --}}
                    <div id="month-section">

                        <label
                            for="month"
                            class="block mb-2 text-sm font-medium text-[#24332A]"
                        >
                            Pilih Bulan
                        </label>

                        <input
                            type="month"
                            id="month"
                            name="month"
                            value="{{ old('month', now()->format('Y-m')) }}"
                            class="w-full px-3 py-2.5 text-sm border border-[#D5DDD6] rounded-md focus:outline-none focus:ring-2 focus:ring-[#315A45] focus:border-[#315A45]"
                        >

                        @error('month')

                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- Range --}}
                    <div
                        id="range-section"
                        class="hidden"
                    >

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                            <div>

                                <label
                                    for="start_date"
                                    class="block mb-2 text-sm font-medium text-[#24332A]"
                                >
                                    Tanggal Mulai
                                </label>

                                <input
                                    type="date"
                                    id="start_date"
                                    name="start_date"
                                    value="{{ old('start_date') }}"
                                    class="w-full px-3 py-2.5 text-sm border border-[#D5DDD6] rounded-md focus:outline-none focus:ring-2 focus:ring-[#315A45] focus:border-[#315A45]"
                                >

                                @error('start_date')

                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>


                            <div>

                                <label
                                    for="end_date"
                                    class="block mb-2 text-sm font-medium text-[#24332A]"
                                >
                                    Tanggal Selesai
                                </label>

                                <input
                                    type="date"
                                    id="end_date"
                                    name="end_date"
                                    value="{{ old('end_date') }}"
                                    class="w-full px-3 py-2.5 text-sm border border-[#D5DDD6] rounded-md focus:outline-none focus:ring-2 focus:ring-[#315A45] focus:border-[#315A45]"
                                >

                                @error('end_date')

                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                        </div>

                    </div>


                    {{-- Buttons --}}
                    <div class="flex items-center justify-end gap-3 pt-5 border-t border-[#E2E7E2]">

                        <a
                            href="{{ route('activities.index') }}"
                            class="px-4 py-2.5 text-sm font-medium text-[#24332A] border border-[#D5DDD6] rounded-md hover:bg-[#F7F8F6]"
                        >
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="px-4 py-2.5 text-sm font-medium text-white bg-[#234936] rounded-md hover:bg-[#315A45]"
                        >
                            Export PDF
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const radios =
                    document.querySelectorAll(
                        'input[name="filter_type"]'
                    );

                const monthSection =
                    document.getElementById(
                        'month-section'
                    );

                const rangeSection =
                    document.getElementById(
                        'range-section'
                    );


                function updateSections() {

                    const selected =
                        document.querySelector(
                            'input[name="filter_type"]:checked'
                        ).value;


                    if (selected === 'month') {

                        monthSection.classList.remove(
                            'hidden'
                        );

                        rangeSection.classList.add(
                            'hidden'
                        );

                    } else {

                        monthSection.classList.add(
                            'hidden'
                        );

                        rangeSection.classList.remove(
                            'hidden'
                        );

                    }

                }


                radios.forEach(function (radio) {

                    radio.addEventListener(
                        'change',
                        updateSections
                    );

                });


                updateSections();

            }
        );

    </script>
@endsection
@extends('layouts.app')

@section('content')
    <div class="p-8">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">

            <div>
                <h1 class="text-2xl font-semibold text-[#24332A]">
                    Kalender Kegiatan
                </h1>

                <p class="mt-1 text-sm text-[#6B7280]">
                    Daftar kegiatan dan agenda Biro Humas.
                </p>
            </div>

            @if(auth()->user()->role === 'kabag')
                <a
                    href="{{ route('activities.create') }}"
                    class="px-4 py-2 text-sm font-medium text-white bg-[#234936] rounded-md hover:bg-[#315A45]"
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


        {{-- Error Message --}}
        @if(session('error'))
            <div class="mb-5 px-4 py-3 text-sm text-red-700 bg-red-50 border border-red-200 rounded-md">
                {{ session('error') }}
            </div>
        @endif


        {{-- Daftar Kegiatan --}}
        <div class="bg-white border border-[#E2E7E2] rounded-lg">

            @if($activities->count() > 0)

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="border-b border-[#E2E7E2] bg-[#F7F8F6]">

                            <tr>

                                <th class="px-6 py-4 text-left font-medium text-[#6B7280]">
                                    Kegiatan
                                </th>

                                <th class="px-6 py-4 text-left font-medium text-[#6B7280]">
                                    Tanggal
                                </th>

                                <th class="px-6 py-4 text-left font-medium text-[#6B7280]">
                                    Waktu
                                </th>

                                <th class="px-6 py-4 text-left font-medium text-[#6B7280]">
                                    Lokasi
                                </th>

                                <th class="px-6 py-4 text-left font-medium text-[#6B7280]">
                                    PIC
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-[#E2E7E2]">

                            @foreach($activities as $activity)

                                <tr class="hover:bg-[#F7F8F6]">

                                    {{-- Kegiatan --}}
                                    <td class="px-6 py-4 font-medium">

                                        <a
                                            href="{{ route('activities.show', $activity) }}"
                                            class="text-[#234936] hover:underline"
                                        >
                                            {{ $activity->title }}
                                        </a>

                                    </td>


                                    {{-- Tanggal --}}
                                    <td class="px-6 py-4 text-[#6B7280]">

                                        {{ $activity->activity_date->format('d M Y') }}

                                    </td>


                                    {{-- Waktu --}}
                                    <td class="px-6 py-4 text-[#6B7280]">

                                        {{ \Carbon\Carbon::parse($activity->start_time)->format('H:i') }}

                                        -

                                        @if($activity->end_time)

                                            {{ \Carbon\Carbon::parse($activity->end_time)->format('H:i') }}

                                        @else

                                            Selesai

                                        @endif

                                    </td>


                                    {{-- Lokasi --}}
                                    <td class="px-6 py-4 text-[#6B7280]">

                                        {{ $activity->location ?? '-' }}

                                    </td>


                                    {{-- PIC --}}
                                    <td class="px-6 py-4 text-[#6B7280]">

                                        @forelse($activity->pics as $pic)

                                            <span>
                                                {{ $pic->name }}
                                            </span>{{ !$loop->last ? ', ' : '' }}

                                        @empty

                                            -

                                        @endforelse

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="px-6 py-12 text-center">

                    <p class="text-sm text-[#6B7280]">
                        Belum ada kegiatan.
                    </p>

                    @if(auth()->user()->role === 'kabag')

                        <a
                            href="{{ route('activities.create') }}"
                            class="inline-block mt-4 px-4 py-2 text-sm font-medium text-white bg-[#234936] rounded-md hover:bg-[#315A45]"
                        >
                            Tambah Kegiatan
                        </a>

                    @endif

                </div>

            @endif

        </div>

    </div>
@endsection
@extends('layouts.app')

@section('content')
    <div class="p-8 max-w-4xl">

        <div class="flex items-center justify-between mb-6">

            <div>
                <h1 class="text-2xl font-semibold text-[#24332A]">
                    Detail Kegiatan
                </h1>

                <p class="mt-1 text-sm text-[#6B7280]">
                    Informasi lengkap kegiatan Humas.
                </p>
            </div>

            <div class="flex items-center gap-3">

                @if(auth()->user()->role === 'kabag')

                    <a
                        href="{{ route('activities.edit', $activity) }}"
                        class="px-4 py-2 text-sm font-medium text-white bg-[#234936] rounded-md hover:bg-[#315A45]"
                    >
                        Edit
                    </a>

                    <form
                        action="{{ route('activities.destroy', $activity) }}"
                        method="POST"
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?');"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="px-4 py-2 text-sm font-medium text-red-700 border border-red-200 rounded-md hover:bg-red-50"
                        >
                            Hapus
                        </button>
                    </form>

                @endif

                <a
                    href="{{ route('activities.index') }}"
                    class="px-4 py-2 text-sm font-medium text-[#24332A] border border-[#D5DDD6] rounded-md hover:bg-[#F7F8F6]"
                >
                    Kembali
                </a>

            </div>
        </div>


        <div class="bg-white border border-[#E2E7E2] rounded-lg">

            <div class="px-6 py-5 border-b border-[#E2E7E2]">

                <h2 class="text-lg font-semibold text-[#24332A]">
                    {{ $activity->title }}
                </h2>

            </div>


            <div class="p-6 space-y-5">

                {{-- Tanggal --}}
                <div>
                    <p class="text-xs font-medium text-[#6B7280] uppercase">
                        Tanggal
                    </p>

                    <p class="mt-1 text-sm text-[#24332A]">
                        {{ $activity->activity_date->format('d M Y') }}
                    </p>
                </div>


                {{-- Waktu --}}
                <div>
                    <p class="text-xs font-medium text-[#6B7280] uppercase">
                        Waktu
                    </p>

                    <p class="mt-1 text-sm text-[#24332A]">

                        {{ \Carbon\Carbon::parse($activity->start_time)->format('H:i') }}

                        -

                        @if($activity->end_time)

                            {{ \Carbon\Carbon::parse($activity->end_time)->format('H:i') }}

                        @else

                            Selesai

                        @endif

                    </p>
                </div>


                {{-- Lokasi --}}
                <div>
                    <p class="text-xs font-medium text-[#6B7280] uppercase">
                        Lokasi
                    </p>

                    <p class="mt-1 text-sm text-[#24332A]">
                        {{ $activity->location ?? '-' }}
                    </p>
                </div>


                {{-- PIC --}}
                <div>
                    <p class="text-xs font-medium text-[#6B7280] uppercase">
                        PIC
                    </p>

                    <div class="mt-1 space-y-1">

                        @forelse($activity->pics as $pic)

                            <p class="text-sm text-[#24332A]">
                                {{ $pic->name }}

                                <span class="text-[#6B7280]">
                                    ({{ ucfirst($pic->role) }})
                                </span>
                            </p>

                        @empty

                            <p class="text-sm text-[#6B7280]">
                                -
                            </p>

                        @endforelse

                    </div>
                </div>


                {{-- Dibuat Oleh --}}
                <div>
                    <p class="text-xs font-medium text-[#6B7280] uppercase">
                        Dibuat Oleh
                    </p>

                    <p class="mt-1 text-sm text-[#24332A]">
                        {{ $activity->creator->name }}
                    </p>
                </div>


                {{-- Deskripsi --}}
                <div>
                    <p class="text-xs font-medium text-[#6B7280] uppercase">
                        Deskripsi
                    </p>

                    <p class="mt-1 text-sm leading-6 text-[#24332A]">
                        {{ $activity->description ?? '-' }}
                    </p>
                </div>

            </div>

        </div>

    </div>
@endsection
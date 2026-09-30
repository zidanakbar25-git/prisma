@extends('layouts.app')

@section('content')
    <div class="p-8 max-w-4xl">

        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-[#24332A]">
                Tambah Kegiatan
            </h1>

            <p class="mt-1 text-sm text-[#6B7280]">
                Tambahkan kegiatan baru ke kalender Humas.
            </p>
        </div>

        <div class="bg-white border border-[#E2E7E2] rounded-lg p-6">

            <form action="{{ route('activities.store') }}" method="POST">
                @csrf

                <div class="space-y-5">

                    {{-- Judul --}}
                    <div>
                        <label
                            for="title"
                            class="block mb-2 text-sm font-medium text-[#24332A]"
                        >
                            Judul Kegiatan
                        </label>

                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title') }}"
                            placeholder="Contoh: Rapat Evaluasi Humas"
                            class="w-full px-3 py-2.5 text-sm border border-[#D5DDD6] rounded-md focus:outline-none focus:ring-2 focus:ring-[#315A45] focus:border-[#315A45]"
                        >

                        @error('title')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- Tanggal --}}
                    <div>
                        <label
                            for="activity_date"
                            class="block mb-2 text-sm font-medium text-[#24332A]"
                        >
                            Tanggal
                        </label>

                        <input
                            type="date"
                            id="activity_date"
                            name="activity_date"
                            value="{{ old('activity_date') }}"
                            class="w-full px-3 py-2.5 text-sm border border-[#D5DDD6] rounded-md focus:outline-none focus:ring-2 focus:ring-[#315A45] focus:border-[#315A45]"
                        >

                        @error('activity_date')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- Waktu --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>
                            <label
                                for="start_time"
                                class="block mb-2 text-sm font-medium text-[#24332A]"
                            >
                                Jam Mulai
                            </label>

                            <input
                                type="time"
                                id="start_time"
                                name="start_time"
                                value="{{ old('start_time') }}"
                                class="w-full px-3 py-2.5 text-sm border border-[#D5DDD6] rounded-md focus:outline-none focus:ring-2 focus:ring-[#315A45] focus:border-[#315A45]"
                            >

                            @error('start_time')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="end_time"
                                class="block mb-2 text-sm font-medium text-[#24332A]"
                            >
                                Jam Selesai
                            </label>

                            <input
                                type="time"
                                id="end_time"
                                name="end_time"
                                value="{{ old('end_time') }}"
                                class="w-full px-3 py-2.5 text-sm border border-[#D5DDD6] rounded-md focus:outline-none focus:ring-2 focus:ring-[#315A45] focus:border-[#315A45]"
                            >

                            <p class="mt-1 text-xs text-[#6B7280]">
                                Kosongkan jika kegiatan berlangsung sampai selesai.
                            </p>

                            @error('end_time')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>


                    {{-- Lokasi --}}
                    <div>
                        <label
                            for="location"
                            class="block mb-2 text-sm font-medium text-[#24332A]"
                        >
                            Lokasi
                        </label>

                        <input
                            type="text"
                            id="location"
                            name="location"
                            value="{{ old('location') }}"
                            placeholder="Contoh: Ruang Rapat Humas"
                            class="w-full px-3 py-2.5 text-sm border border-[#D5DDD6] rounded-md focus:outline-none focus:ring-2 focus:ring-[#315A45] focus:border-[#315A45]"
                        >

                        @error('location')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- PIC --}}
                    <div>
                        <label
                            class="block mb-2 text-sm font-medium text-[#24332A]"
                        >
                            PIC Kegiatan
                        </label>

                        <div class="border border-[#D5DDD6] rounded-md p-3 space-y-2">

                            @foreach($users as $user)
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="pic_ids[]"
                                        value="{{ $user->id }}"
                                        {{ in_array($user->id, old('pic_ids', [])) ? 'checked' : '' }}
                                        class="w-4 h-4 text-[#234936] border-gray-300 rounded focus:ring-[#315A45]"
                                    >

                                    <span class="text-sm text-[#24332A]">
                                        {{ $user->name }}
                                    </span>

                                    <span class="text-xs text-[#6B7280]">
                                        ({{ ucfirst($user->role) }})
                                    </span>
                                </label>
                            @endforeach

                        </div>

                        @error('pic_ids')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                        @error('pic_ids.*')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- Deskripsi --}}
                    <div>
                        <label
                            for="description"
                            class="block mb-2 text-sm font-medium text-[#24332A]"
                        >
                            Deskripsi
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            placeholder="Deskripsi kegiatan..."
                            class="w-full px-3 py-2.5 text-sm border border-[#D5DDD6] rounded-md focus:outline-none focus:ring-2 focus:ring-[#315A45] focus:border-[#315A45]"
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- Tombol --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E2E7E2]">

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
                            Simpan Kegiatan
                        </button>

                    </div>

                </div>
            </form>

        </div>
    </div>
@endsection
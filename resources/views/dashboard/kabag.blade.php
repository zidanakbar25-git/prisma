<x-layouts.app
    title="Dashboard"
    page-title="Dashboard"
>

    {{-- Header --}}
    <div class="mb-8">

        <h2 class="text-2xl font-semibold text-[#24332a]">
            Selamat datang, {{ auth()->user()->name }}
        </h2>

        <p class="mt-2 text-sm text-gray-500">
            Kelola informasi, kegiatan, dan tugas tim Humas.
        </p>

    </div>


    {{-- Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

        <div class="bg-white border border-[#e2e7e2] rounded-lg p-5">
            <p class="text-sm text-gray-500">
                Total Kegiatan
            </p>

            <p class="text-3xl font-semibold text-[#234936] mt-3">
                0
            </p>

            <p class="text-xs text-gray-400 mt-1">
                Seluruh kegiatan
            </p>
        </div>


        <div class="bg-white border border-[#e2e7e2] rounded-lg p-5">
            <p class="text-sm text-gray-500">
                Kegiatan Bulan Ini
            </p>

            <p class="text-3xl font-semibold text-[#234936] mt-3">
                0
            </p>

            <p class="text-xs text-gray-400 mt-1">
                September 2026
            </p>
        </div>


        <div class="bg-white border border-[#e2e7e2] rounded-lg p-5">
            <p class="text-sm text-gray-500">
                To-Do Aktif
            </p>

            <p class="text-3xl font-semibold text-[#234936] mt-3">
                0
            </p>

            <p class="text-xs text-gray-400 mt-1">
                Menunggu penyelesaian
            </p>
        </div>


        <div class="bg-white border border-[#e2e7e2] rounded-lg p-5">
            <p class="text-sm text-gray-500">
                Pengguna Aktif
            </p>

            <p class="text-3xl font-semibold text-[#234936] mt-3">
                3
            </p>

            <p class="text-xs text-gray-400 mt-1">
                Pengguna sistem
            </p>
        </div>

    </div>


    {{-- Content --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">


        {{-- Kegiatan Terdekat --}}
        <div class="bg-white border border-[#e2e7e2] rounded-lg">

            <div class="px-6 py-5 border-b border-[#e8ebe8]">

                <h3 class="font-semibold text-[#24332a]">
                    Kegiatan Terdekat
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Kegiatan yang akan berlangsung.
                </p>

            </div>


            <div class="p-6">

                <div class="py-8 text-center text-sm text-gray-400">
                    Belum ada kegiatan terdekat.
                </div>

            </div>

        </div>


        {{-- To-Do --}}
        <div class="bg-white border border-[#e2e7e2] rounded-lg">

            <div class="px-6 py-5 border-b border-[#e8ebe8]">

                <h3 class="font-semibold text-[#24332a]">
                    To-Do
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Tugas yang perlu diperhatikan.
                </p>

            </div>


            <div class="p-6">

                <div class="py-8 text-center text-sm text-gray-400">
                    Belum ada tugas.
                </div>

            </div>

        </div>

    </div>

</x-layouts.app>
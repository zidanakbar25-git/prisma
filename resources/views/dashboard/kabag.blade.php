
<x-layouts.app pageTitle="Dashboard Kabag">
    <div class="space-y-6">

        <div>
            <h1 class="text-2xl font-semibold text-[#24332A]">
                Dashboard
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Ringkasan kegiatan dan pengelolaan tim Humas.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="border border-[#E2E7E2] bg-white p-5">
                <p class="text-sm text-gray-500">Kegiatan Mendatang</p>
                <p class="mt-3 text-3xl font-semibold text-[#234936]">
                    {{ $upcomingActivities->count() }}
                </p>
                <p class="mt-2 text-xs text-gray-500">
                    Maksimal lima kegiatan ditampilkan
                </p>
            </div>

            <div class="border border-[#E2E7E2] bg-white p-5">
                <p class="text-sm text-gray-500">Total To-Do</p>
                <p class="mt-3 text-3xl font-semibold text-[#234936]">
                    {{ $taskSummary['total'] }}
                </p>
                <p class="mt-2 text-xs text-gray-500">
                    Tugas yang terdaftar
                </p>
            </div>

            <div class="border border-[#E2E7E2] bg-white p-5">
                <p class="text-sm text-gray-500">Pengguna Aktif</p>
                <p class="mt-3 text-3xl font-semibold text-[#234936]">
                    {{ $activeUsersCount }}
                </p>
                <p class="mt-2 text-xs text-gray-500">
                    Seluruh akun aktif
                </p>
            </div>

            <div class="border border-[#E2E7E2] bg-white p-5">
                <p class="text-sm text-gray-500">Penugasan Selesai</p>
                <p class="mt-3 text-3xl font-semibold text-[#234936]">
                    {{ $taskSummary['selesai'] }}
                </p>
                <p class="mt-2 text-xs text-gray-500">
                    Dari seluruh penugasan tim
                </p>
            </div>
        </div>

        <div class="border border-[#E2E7E2] bg-white p-5">
            <div class="mb-5">
                <h2 class="text-lg font-semibold text-[#24332A]">
                    Ringkasan Status To-Do Tim
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Jumlah penugasan berdasarkan status pengerjaan.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="border border-[#E2E7E2] p-4">
                    <p class="text-sm text-gray-500">Belum Mulai</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-700">
                        {{ $taskSummary['belum_mulai'] }}
                    </p>
                </div>

                <div class="border border-[#E2E7E2] p-4">
                    <p class="text-sm text-gray-500">Sedang Dikerjakan</p>
                    <p class="mt-2 text-2xl font-semibold text-amber-700">
                        {{ $taskSummary['sedang_dikerjakan'] }}
                    </p>
                </div>

                <div class="border border-[#E2E7E2] p-4">
                    <p class="text-sm text-gray-500">Selesai</p>
                    <p class="mt-2 text-2xl font-semibold text-[#234936]">
                        {{ $taskSummary['selesai'] }}
                    </p>
                </div>
            </div>

            <div class="mt-5">
                <a href="{{ route('tasks.index') }}"
                   class="text-sm font-medium text-[#234936] underline underline-offset-4">
                    Lihat semua To-Do
                </a>
            </div>
        </div>

        <div class="border border-[#E2E7E2] bg-white">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#E2E7E2] p-5">
                <div>
                    <h2 class="text-lg font-semibold text-[#24332A]">
                        Kegiatan Terdekat
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Jadwal kegiatan Humas berikutnya.
                    </p>
                </div>

                <a href="{{ route('activities.index') }}"
                   class="text-sm font-medium text-[#234936] underline underline-offset-4">
                    Buka kalender
                </a>
            </div>

            @if ($upcomingActivities->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-sm text-gray-500">
                        Belum ada kegiatan mendatang.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-[#F7F8F6] text-xs uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-5 py-3 font-medium">Kegiatan</th>
                                <th class="px-5 py-3 font-medium">Tanggal</th>
                                <th class="px-5 py-3 font-medium">Waktu</th>
                                <th class="px-5 py-3 font-medium">Lokasi</th>
                                <th class="px-5 py-3 font-medium">PIC</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E7E2]">
                            @foreach ($upcomingActivities as $activity)
                                <tr>
                                    <td class="px-5 py-4 font-medium text-[#24332A]">
                                        {{ $activity->title }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-gray-600">
                                        {{ $activity->activity_date->format('d/m/Y') }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-gray-600">
                                        {{ substr($activity->start_time, 0, 5) }}
                                        -
                                        {{ $activity->end_time ? substr($activity->end_time, 0, 5) : 'Selesai' }}
                                    </td>
                                    <td class="px-5 py-4 text-gray-600">
                                        {{ $activity->location ?: '-' }}
                                    </td>
                                    <td class="px-5 py-4 text-gray-600">
                                        {{ $activity->pics->pluck('name')->implode(', ') ?: '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="border border-[#E2E7E2] bg-white">
            <div class="border-b border-[#E2E7E2] p-5">
                <h2 class="text-lg font-semibold text-[#24332A]">
                    Aktivitas Terbaru
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Riwayat aktivitas pengguna dalam sistem.
                </p>
            </div>

            @if ($recentLogs->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-sm text-gray-500">
                        Belum ada aktivitas yang tercatat.
                    </p>
                </div>
            @else
                <div class="divide-y divide-[#E2E7E2]">
                    @foreach ($recentLogs as $log)
                        <div class="flex flex-col gap-2 px-5 py-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="font-medium text-[#24332A]">
                                    {{ $log->action }}
                                </p>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ $log->description ?: 'Tidak ada deskripsi.' }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $log->user?->name ?? 'Pengguna tidak tersedia' }}
                                </p>
                            </div>
                            <p class="whitespace-nowrap text-xs text-gray-500">
                                {{ $log->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-[#E2E7E2] p-5">
                    <a href="{{ route('audit-logs.index') }}"
                       class="text-sm font-medium text-[#234936] underline underline-offset-4">
                        Buka audit log
                    </a>
                </div>
            @endif
        </div>

    </div>
</x-layouts.app>

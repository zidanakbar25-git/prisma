
<x-layouts.app pageTitle="Dashboard Staff">
    <div class="space-y-6">

        <div>
            <h1 class="text-2xl font-semibold text-[#24332A]">
                Dashboard
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Ringkasan tugas dan jadwal kegiatan Humas.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="border border-[#E2E7E2] bg-white p-5">
                <p class="text-sm text-gray-500">Total Tugas</p>
                <p class="mt-3 text-3xl font-semibold text-[#234936]">
                    {{ $taskSummary['total'] }}
                </p>
            </div>

            <div class="border border-[#E2E7E2] bg-white p-5">
                <p class="text-sm text-gray-500">Belum Mulai</p>
                <p class="mt-3 text-3xl font-semibold text-gray-700">
                    {{ $taskSummary['belum_mulai'] }}
                </p>
            </div>

            <div class="border border-[#E2E7E2] bg-white p-5">
                <p class="text-sm text-gray-500">Sedang Dikerjakan</p>
                <p class="mt-3 text-3xl font-semibold text-amber-700">
                    {{ $taskSummary['sedang_dikerjakan'] }}
                </p>
            </div>

            <div class="border border-[#E2E7E2] bg-white p-5">
                <p class="text-sm text-gray-500">Selesai</p>
                <p class="mt-3 text-3xl font-semibold text-[#234936]">
                    {{ $taskSummary['selesai'] }}
                </p>
            </div>
        </div>

        <div class="border border-[#E2E7E2] bg-white">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#E2E7E2] p-5">
                <div>
                    <h2 class="text-lg font-semibold text-[#24332A]">
                        Tugas Saya
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Daftar To-Do yang ditugaskan kepada Anda.
                    </p>
                </div>

                <a href="{{ route('tasks.index') }}"
                   class="text-sm font-medium text-[#234936] underline underline-offset-4">
                    Buka To-Do
                </a>
            </div>

            @if ($assignedTasks->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-sm text-gray-500">
                        Belum ada tugas yang ditugaskan kepada Anda.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-[#F7F8F6] text-xs uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-5 py-3 font-medium">Tugas</th>
                                <th class="px-5 py-3 font-medium">Prioritas</th>
                                <th class="px-5 py-3 font-medium">Tenggat</th>
                                <th class="px-5 py-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E7E2]">
                            @foreach ($assignedTasks as $task)
                                <tr>
                                    <td class="px-5 py-4">
                                        <p class="font-medium text-[#24332A]">
                                            {{ $task->title }}
                                        </p>
                                        <p class="mt-1 max-w-md text-xs text-gray-500">
                                            {{ $task->description ?: 'Tidak ada deskripsi.' }}
                                        </p>
                                    </td>
                                    <td class="px-5 py-4 text-gray-600">
                                        {{ ucfirst($task->priority) }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-gray-600">
                                        {{ $task->due_date->format('d/m/Y') }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        @if ($task->pivot->status === 'selesai')
                                            <span class="font-medium text-[#234936]">Selesai</span>
                                        @elseif ($task->pivot->status === 'sedang_dikerjakan')
                                            <span class="font-medium text-amber-700">Sedang Dikerjakan</span>
                                        @else
                                            <span class="font-medium text-gray-600">Belum Mulai</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
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
                <div class="divide-y divide-[#E2E7E2]">
                    @foreach ($upcomingActivities as $activity)
                        <div class="flex flex-col gap-2 p-5 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="font-medium text-[#24332A]">
                                    {{ $activity->title }}
                                </p>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ $activity->activity_date->format('d/m/Y') }}
                                    ·
                                    {{ substr($activity->start_time, 0, 5) }}
                                    -
                                    {{ $activity->end_time ? substr($activity->end_time, 0, 5) : 'Selesai' }}
                                </p>
                                <p class="mt-1 text-sm text-gray-500">
                                    Lokasi: {{ $activity->location ?: '-' }}
                                </p>
                            </div>
                            <p class="text-sm text-gray-500">
                                PIC: {{ $activity->pics->pluck('name')->implode(', ') ?: '-' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="border border-[#E2E7E2] bg-white">
            <div class="border-b border-[#E2E7E2] p-5">
                <h2 class="text-lg font-semibold text-[#24332A]">
                    Aktivitas Terbaru
                </h2>
            </div>

            @forelse ($recentLogs as $log)
                <div class="border-b border-[#E2E7E2] p-5 last:border-b-0">
                    <p class="font-medium text-[#24332A]">
                        {{ $log->action }}
                    </p>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ $log->description ?: 'Tidak ada deskripsi.' }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ $log->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
            @empty
                <p class="p-8 text-center text-sm text-gray-500">
                    Belum ada aktivitas yang tercatat.
                </p>
            @endforelse
        </div>

    </div>
</x-layouts.app>

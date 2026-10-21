<x-layouts.app
    title="To-Do - PRISMA"
    pageTitle="To-Do"
>

    <div class="space-y-7">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-2xl font-semibold tracking-tight text-[#24332a]">
                    Daftar To-Do
                </h2>

                <p class="mt-1.5 text-sm leading-6 text-[#6b7280]">
                    Kelola dan pantau tugas kerja Humas.
                </p>
            </div>

            @if(auth()->user()->role === 'kabag')

                <a
                    href="{{ route('tasks.create') }}"
                    class="inline-flex shrink-0 items-center justify-center rounded-lg bg-[#234936] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#315a45] focus:outline-none focus:ring-2 focus:ring-[#8ca795] focus:ring-offset-2"
                >
                    Tambah To-Do
                </a>

            @endif

        </div>


        {{-- Filter --}}
        <div
            class="rounded-xl border border-[#e0e6e1] bg-white p-4 shadow-[0_1px_3px_rgba(36,51,42,0.04)] sm:p-5"
        >

            <div
                class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-5"
            >

                {{-- Search --}}
                <div>

                    <label
                        for="taskSearch"
                        class="mb-2 block text-sm font-semibold text-[#37443b]"
                    >
                        Cari To-Do
                    </label>

                    <input
                        type="text"
                        id="taskSearch"
                        placeholder="Cari judul tugas..."
                        class="h-11 w-full rounded-lg border border-[#d9dfda] bg-white px-3.5 text-sm text-[#24332a] outline-none transition placeholder:text-[#9aa49d] focus:border-[#557963] focus:ring-2 focus:ring-[#557963]/15"
                    >

                </div>


                {{-- Prioritas --}}
                <div>

                    <label
                        for="priorityFilter"
                        class="mb-2 block text-sm font-semibold text-[#37443b]"
                    >
                        Prioritas
                    </label>

                    <select
                        id="priorityFilter"
                        class="h-11 w-full rounded-lg border border-[#d9dfda] bg-white px-3.5 text-sm text-[#24332a] outline-none transition placeholder:text-[#9aa49d] focus:border-[#557963] focus:ring-2 focus:ring-[#557963]/15"
                    >

                        <option value="">
                            Semua Prioritas
                        </option>

                        <option value="rendah">
                            Rendah
                        </option>

                        <option value="sedang">
                            Sedang
                        </option>

                        <option value="tinggi">
                            Tinggi
                        </option>

                    </select>

                </div>


                {{-- Status --}}
                <div>

                    <label
                        for="statusFilter"
                        class="mb-2 block text-sm font-semibold text-[#37443b]"
                    >
                        Status
                    </label>

                    <select
                        id="statusFilter"
                        class="h-11 w-full rounded-lg border border-[#d9dfda] bg-white px-3.5 text-sm text-[#24332a] outline-none transition placeholder:text-[#9aa49d] focus:border-[#557963] focus:ring-2 focus:ring-[#557963]/15"
                    >

                        <option value="">
                            Semua Status
                        </option>

                        <option value="belum_mulai">
                            Belum Mulai
                        </option>

                        <option value="sedang_dikerjakan">
                            Sedang Dikerjakan
                        </option>

                        <option value="selesai">
                            Selesai
                        </option>

                    </select>

                </div>

            </div>

        </div>


        {{-- Daftar To-Do --}}
        <div
            id="taskList"
            class="space-y-4"
        >

            @forelse($tasks as $task)

                @php
                    $currentAssignment = $task->assignees
                        ->firstWhere('id', auth()->id());

                    $currentStatus = $currentAssignment
                        ? $currentAssignment->pivot->status
                        : null;

                    $statusLabels = [
                        'belum_mulai' => 'Belum Mulai',
                        'sedang_dikerjakan' => 'Sedang Dikerjakan',
                        'selesai' => 'Selesai',
                    ];

                    $priorityLabels = [
                        'rendah' => 'Rendah',
                        'sedang' => 'Sedang',
                        'tinggi' => 'Tinggi',
                    ];

                    $priorityClass = match ($task->priority) {
                        'tinggi' =>
                            'bg-[#f9eeee] text-[#9b4040] border-[#efd6d6]',

                        'sedang' =>
                            'bg-[#faf6e8] text-[#806b27] border-[#eadfba]',

                        default =>
                            'bg-[#edf5ef] text-[#315c40] border-[#d5e5d8]',
                    };
                @endphp


                <div
                    class="task-item group rounded-xl border border-[#e0e6e1] bg-white p-5 shadow-[0_1px_3px_rgba(36,51,42,0.035)] transition duration-150 hover:border-[#cbd8ce] hover:shadow-[0_5px_18px_rgba(36,51,42,0.06)] sm:p-6"
                    data-title="{{ strtolower($task->title) }}"
                    data-priority="{{ $task->priority }}"
                    data-status="{{ $currentStatus }}"
                >

                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                        {{-- Informasi utama --}}
                        <div class="min-w-0 flex-1">

                            {{-- Judul + Badge Baru --}}
                            <div class="flex items-center gap-3">

                                <h3
                                    class="text-base font-semibold leading-6 text-[#24332a] sm:text-[17px]"
                                >
                                    {{ $task->title }}
                                </h3>


                                @if(
                                    auth()->user()->role !== 'kabag' &&
                                    $currentAssignment &&
                                    ($currentAssignment->pivot->was_unread ?? false)
                                )

                                    <span
                                        class="inline-flex
                                               items-center
                                               rounded-md
                                               border border-[#d5e5d8]
                                               bg-[#edf5ef]
                                               px-2 py-1
                                               text-[10px]
                                               font-semibold
                                               tracking-wide
                                               text-[#315c40]"
                                    >
                                        BARU
                                    </span>

                                @endif

                            </div>


                            {{-- Deskripsi --}}
                            @if($task->description)

                                <p
                                    class="mt-2 max-w-3xl text-sm leading-6 text-[#68746d]"
                                >
                                    {{ $task->description }}
                                </p>

                            @endif


                            {{-- Metadata --}}
                            <div
                                class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-[#edf0ed] pt-3 text-xs text-[#6b7280]"
                            >

                                <span>
                                    Deadline:

                                    <strong
                                        class="font-medium
                                               text-[#37443b]"
                                    >
                                        {{ $task->due_date->format('d/m/Y') }}
                                    </strong>
                                </span>



                            </div>




                        </div>


                        {{-- Bagian kanan --}}
                        <div class="flex shrink-0 flex-col items-start gap-3 border-t border-[#edf0ed] pt-4 sm:flex-row sm:flex-wrap sm:items-center lg:flex-col lg:items-end lg:border-0 lg:pt-0"
                        >

                            {{-- Prioritas --}}
                            <span
                                class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold {{ $priorityClass }}"
                            >
                                Prioritas:
                                {{ $priorityLabels[$task->priority] }}
                            </span>


                            {{-- Status Staff / Intern --}}
                            @if(
                                auth()->user()->role !== 'kabag' &&
                                $currentAssignment
                            )

                                <form
                                    method="POST"
                                    action="{{ route('tasks.update-status', $task) }}"
                                >

                                    @csrf

                                    @method('PUT')


                                    <label
                                        for="status-{{ $task->id }}"
                                        class="mb-1.5 block text-xs font-semibold text-[#68746d]"
                                    >
                                        Status Saya
                                    </label>


                                    <select
                                        id="status-{{ $task->id }}"
                                        name="status"
                                        onchange="this.form.submit()"
                                        class="h-10 min-w-[190px] rounded-lg border border-[#d9dfda] bg-white px-3 text-sm text-[#24332a] outline-none transition focus:border-[#557963] focus:ring-2 focus:ring-[#557963]/15"
                                    >

                                        <option
                                            value="belum_mulai"
                                            {{ $currentStatus === 'belum_mulai' ? 'selected' : '' }}
                                        >
                                            Belum Mulai
                                        </option>

                                        <option
                                            value="sedang_dikerjakan"
                                            {{ $currentStatus === 'sedang_dikerjakan' ? 'selected' : '' }}
                                        >
                                            Sedang Dikerjakan
                                        </option>

                                        <option
                                            value="selesai"
                                            {{ $currentStatus === 'selesai' ? 'selected' : '' }}
                                        >
                                            Selesai
                                        </option>

                                    </select>

                                </form>

                            @endif


                            {{-- Tombol Edit + Hapus --}}
                            @if(auth()->user()->role === 'kabag')

                                <div
                                    class="flex items-center gap-2"
                                >

                                    {{-- Edit --}}
                                    <a
                                        href="{{ route('tasks.edit', $task) }}"
                                        class="inline-flex h-9 items-center justify-center rounded-lg border border-[#d9dfda] bg-white px-3.5 text-xs font-semibold text-[#37443b] transition hover:border-[#b9c9bd] hover:bg-[#f5f8f5] focus:outline-none focus:ring-2 focus:ring-[#557963]/20"
                                    >
                                        Edit
                                    </a>


                                    {{-- Hapus --}}
                                    <form
                                        method="POST"
                                        action="{{ route('tasks.destroy', $task) }}"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus To-Do ini?');"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="inline-flex h-9 items-center justify-center rounded-lg border border-[#efd6d6] bg-white px-3.5 text-xs font-semibold text-[#9b4040] transition hover:border-[#e6bcbc] hover:bg-[#fcf3f3] focus:outline-none focus:ring-2 focus:ring-[#b85a5a]/15"
                                        >
                                            Hapus
                                        </button>

                                    </form>

                                </div>

                            @endif

                        </div>

                    </div>

                    {{-- Penerima untuk Kabag: panel selebar kartu tugas --}}
                    @if(auth()->user()->role === 'kabag')

                        <div class="mt-5 w-full rounded-lg border border-[#e7ece8] bg-[#f5f8f5] px-4 py-4 sm:px-5">

                            <div class="mb-3 text-xs font-semibold uppercase tracking-[0.06em] text-[#68746d]">
                                Penerima
                            </div>

                            <div class="space-y-2">
                                @foreach($task->assignees as $assignee)
                                    @php
                                        $assigneeStatus = $assignee->pivot->status;
                                        $assigneeStatusClass = match ($assigneeStatus) {
                                            'selesai' => 'border-[#d5e5d8] bg-[#edf5ef] text-[#315c40]',
                                            'sedang_dikerjakan' => 'border-[#d9e4f2] bg-[#f0f5fb] text-[#365b86]',
                                            default => 'border-[#e2e7e2] bg-white text-[#68746d]',
                                        };
                                    @endphp

                                    <div class="flex flex-col gap-2 border-b border-[#e2e9e3] py-2 first:pt-0 last:border-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                                        <span class="text-sm font-medium text-[#37443b]">
                                            {{ $assignee->name }}
                                        </span>

                                        <span class="inline-flex w-fit items-center rounded-md border px-3 py-1.5 text-xs font-medium {{ $assigneeStatusClass }}">
                                            {{ $statusLabels[$assigneeStatus] ?? '-' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                        </div>

                    @endif

                </div>

            @empty

                {{-- Tidak ada data --}}
                <div
                    class="rounded-xl border border-dashed border-[#d8e1da] bg-white px-6 py-14 text-center"
                >

                    <p
                        class="text-sm
                               font-medium
                               text-[#37443b]"
                    >
                        Belum ada To-Do.
                    </p>

                    <p
                        class="mt-1
                               text-sm
                               text-[#6b7280]"
                    >
                        To-Do yang dibuat akan tampil di halaman ini.
                    </p>

                </div>

            @endforelse


            {{-- Hasil filter kosong --}}
            <div
                id="emptyFilterMessage"
                class="hidden
                       rounded-md
                       border border-[#e2e7e2]
                       bg-white
                       px-6 py-12
                       text-center"
            >

                <p
                    class="text-sm
                           font-medium
                           text-[#37443b]"
                >
                    Tidak ada To-Do yang sesuai.
                </p>

                <p
                    class="mt-1
                           text-sm
                           text-[#6b7280]"
                >
                    Coba ubah kata pencarian atau filter.
                </p>

            </div>

        </div>

    </div>


    {{-- Filter Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const searchInput =
                document.getElementById('taskSearch');

            const priorityFilter =
                document.getElementById('priorityFilter');

            const statusFilter =
                document.getElementById('statusFilter');

            const taskItems =
                document.querySelectorAll('.task-item');

            const emptyFilterMessage =
                document.getElementById('emptyFilterMessage');


            function filterTasks() {

                const search =
                    searchInput.value
                        .toLowerCase()
                        .trim();

                const priority =
                    priorityFilter.value;

                const status =
                    statusFilter.value;

                let visibleCount = 0;


                taskItems.forEach(function (item) {

                    const title =
                        item.dataset.title || '';

                    const itemPriority =
                        item.dataset.priority || '';

                    const itemStatus =
                        item.dataset.status || '';


                    const matchSearch =
                        title.includes(search);

                    const matchPriority =
                        !priority ||
                        itemPriority === priority;

                    const matchStatus =
                        !status ||
                        itemStatus === status;


                    const shouldShow =
                        matchSearch &&
                        matchPriority &&
                        matchStatus;


                    item.style.display =
                        shouldShow ? '' : 'none';


                    if (shouldShow) {
                        visibleCount++;
                    }

                });


                if (
                    taskItems.length > 0 &&
                    visibleCount === 0
                ) {
                    emptyFilterMessage.classList.remove('hidden');
                } else {
                    emptyFilterMessage.classList.add('hidden');
                }

            }


            searchInput.addEventListener(
                'input',
                filterTasks
            );


            priorityFilter.addEventListener(
                'change',
                filterTasks
            );


            statusFilter.addEventListener(
                'change',
                filterTasks
            );

        });
    </script>

</x-layouts.app>
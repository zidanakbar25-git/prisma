<x-layouts.app
    title="To-Do - PRISMA"
    pageTitle="To-Do"
>

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between gap-4">

            <div>
                <h2 class="text-xl font-semibold text-[#24332a]">
                    Daftar To-Do
                </h2>

                <p class="mt-1 text-sm text-[#6b7280]">
                    Kelola dan pantau tugas kerja Humas.
                </p>
            </div>

            @if(auth()->user()->role === 'kabag')

                <a
                    href="{{ route('tasks.create') }}"
                    class="shrink-0
                           rounded-md
                           bg-[#234936]
                           px-5 py-3
                           text-sm font-medium
                           text-white
                           transition
                           hover:bg-[#315a45]"
                >
                    Tambah To-Do
                </a>

            @endif

        </div>


        {{-- Filter --}}
        <div
            class="rounded-md
                   border border-[#e2e7e2]
                   bg-white
                   px-5 py-4"
        >

            <div
                class="grid grid-cols-1
                       gap-4
                       md:grid-cols-3"
            >

                {{-- Search --}}
                <div>

                    <label
                        for="taskSearch"
                        class="mb-2 block
                               text-sm font-medium
                               text-[#37443b]"
                    >
                        Cari To-Do
                    </label>

                    <input
                        type="text"
                        id="taskSearch"
                        placeholder="Cari judul tugas..."
                        class="w-full
                               rounded-md
                               border border-[#d9dfda]
                               bg-white
                               px-3 py-2.5
                               text-sm
                               text-[#24332a]
                               outline-none
                               focus:border-[#557963]
                               focus:ring-1
                               focus:ring-[#557963]"
                    >

                </div>


                {{-- Prioritas --}}
                <div>

                    <label
                        for="priorityFilter"
                        class="mb-2 block
                               text-sm font-medium
                               text-[#37443b]"
                    >
                        Prioritas
                    </label>

                    <select
                        id="priorityFilter"
                        class="w-full
                               rounded-md
                               border border-[#d9dfda]
                               bg-white
                               px-3 py-2.5
                               text-sm
                               text-[#24332a]
                               outline-none
                               focus:border-[#557963]
                               focus:ring-1
                               focus:ring-[#557963]"
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
                        class="mb-2 block
                               text-sm font-medium
                               text-[#37443b]"
                    >
                        Status
                    </label>

                    <select
                        id="statusFilter"
                        class="w-full
                               rounded-md
                               border border-[#d9dfda]
                               bg-white
                               px-3 py-2.5
                               text-sm
                               text-[#24332a]
                               outline-none
                               focus:border-[#557963]
                               focus:ring-1
                               focus:ring-[#557963]"
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
            class="space-y-3"
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
                    class="task-item
                           rounded-md
                           border border-[#e2e7e2]
                           bg-white
                           px-5 py-5"
                    data-title="{{ strtolower($task->title) }}"
                    data-priority="{{ $task->priority }}"
                    data-status="{{ $currentStatus }}"
                >

                    <div
                        class="flex flex-col
                               gap-5
                               lg:flex-row
                               lg:items-start
                               lg:justify-between"
                    >

                        {{-- Informasi utama --}}
                        <div class="min-w-0 flex-1">

                            {{-- Judul + Badge Baru --}}
                            <div class="flex items-center gap-3">

                                <h3
                                    class="text-base
                                           font-semibold
                                           text-[#24332a]"
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
                                    class="mt-2
                                           text-sm
                                           leading-6
                                           text-[#6b7280]"
                                >
                                    {{ $task->description }}
                                </p>

                            @endif


                            {{-- Metadata --}}
                            <div
                                class="mt-4
                                       flex flex-wrap
                                       items-center
                                       gap-x-5
                                       gap-y-2
                                       text-xs
                                       text-[#6b7280]"
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


                                <span>
                                    Dibuat oleh:

                                    <strong
                                        class="font-medium
                                               text-[#37443b]"
                                    >
                                        {{ $task->creator->name }}
                                    </strong>
                                </span>

                            </div>


                            {{-- Penerima untuk Kabag --}}
                            @if(auth()->user()->role === 'kabag')

                                <div class="mt-5">

                                    <div
                                        class="mb-2
                                               text-xs
                                               font-medium
                                               text-[#6b7280]"
                                    >
                                        Penerima
                                    </div>


                                    <div class="space-y-2">

                                        @foreach($task->assignees as $assignee)

                                            <div
                                                class="flex
                                                       max-w-xl
                                                       items-center
                                                       justify-between
                                                       gap-4
                                                       border-b
                                                       border-[#edf0ed]
                                                       pb-2
                                                       last:border-0
                                                       last:pb-0"
                                            >

                                                <span
                                                    class="text-sm
                                                           text-[#37443b]"
                                                >
                                                    {{ $assignee->name }}
                                                </span>


                                                <span
                                                    class="text-xs
                                                           text-[#6b7280]"
                                                >
                                                    {{
                                                        $statusLabels[
                                                            $assignee->pivot->status
                                                        ] ?? '-'
                                                    }}
                                                </span>

                                            </div>

                                        @endforeach

                                    </div>

                                </div>

                            @endif

                        </div>


                        {{-- Bagian kanan --}}
                        <div
                            class="flex
                                   shrink-0
                                   flex-col
                                   items-start
                                   gap-3
                                   lg:items-end"
                        >

                            {{-- Prioritas --}}
                            <span
                                class="inline-flex
                                       items-center
                                       rounded-md
                                       border
                                       px-3 py-1.5
                                       text-xs
                                       font-medium
                                       {{ $priorityClass }}"
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
                                        class="mb-1.5 block
                                               text-xs
                                               font-medium
                                               text-[#6b7280]"
                                    >
                                        Status Saya
                                    </label>


                                    <select
                                        id="status-{{ $task->id }}"
                                        name="status"
                                        onchange="this.form.submit()"
                                        class="min-w-[190px]
                                               rounded-md
                                               border border-[#d9dfda]
                                               bg-white
                                               px-3 py-2
                                               text-sm
                                               text-[#24332a]
                                               outline-none
                                               focus:border-[#557963]
                                               focus:ring-1
                                               focus:ring-[#557963]"
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
                                    class="flex
                                           items-center
                                           gap-2"
                                >

                                    {{-- Edit --}}
                                    <a
                                        href="{{ route('tasks.edit', $task) }}"
                                        class="rounded-md
                                               border border-[#d9dfda]
                                               bg-white
                                               px-3 py-2
                                               text-xs
                                               font-medium
                                               text-[#37443b]
                                               transition
                                               hover:bg-[#f7f8f6]"
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
                                            class="rounded-md
                                                   border border-[#efd6d6]
                                                   bg-white
                                                   px-3 py-2
                                                   text-xs
                                                   font-medium
                                                   text-[#9b4040]
                                                   transition
                                                   hover:bg-[#faf0f0]"
                                        >
                                            Hapus
                                        </button>

                                    </form>

                                </div>

                            @endif

                        </div>

                    </div>

                </div>

            @empty

                {{-- Tidak ada data --}}
                <div
                    class="rounded-md
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
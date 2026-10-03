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
                    class="shrink-0 rounded-md
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
            class="bg-white
                   border border-[#e2e7e2]
                   rounded-md
                   px-5 py-4"
        >

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- Search --}}
                <div>

                    <label
                        class="block mb-2
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
                        class="block mb-2
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
                        class="block mb-2
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
                @endphp


                <div
                    class="task-item
                           bg-white
                           border border-[#e2e7e2]
                           rounded-md
                           px-5 py-5"
                    data-title="{{ strtolower($task->title) }}"
                    data-priority="{{ $task->priority }}"
                    data-status="{{ $currentStatus }}"
                >

                    <div
                        class="flex flex-col
                               lg:flex-row
                               lg:items-start
                               lg:justify-between
                               gap-5"
                    >

                        {{-- Informasi tugas --}}
                        <div class="min-w-0 flex-1">

                            <div class="flex items-start gap-3">

                                <div class="min-w-0">

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
                   bg-[#edf5ef]
                   border border-[#d5e5d8]
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

                                </div>

                            </div>


                            {{-- Metadata --}}
                            <div
                                class="mt-4
                                       flex flex-wrap
                                       items-center
                                       gap-x-5 gap-y-2
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


                            {{-- Penerima --}}
                            @if(auth()->user()->role === 'kabag')

                                <div class="mt-4">

                                    <div
                                        class="text-xs
                                               font-medium
                                               text-[#6b7280]
                                               mb-2"
                                    >
                                        Penerima
                                    </div>

                                    <div class="space-y-2">

                                        @foreach($task->assignees as $assignee)

                                            <div
                                                class="flex
                                                       items-center
                                                       justify-between
                                                       gap-4
                                                       max-w-xl
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
                                                    {{ $statusLabels[$assignee->pivot->status] ?? '-' }}
                                                </span>

                                            </div>

                                        @endforeach

                                    </div>

                                </div>

                            @endif

                        </div>


                        {{-- Bagian kanan --}}
                        <div
                            class="shrink-0
                                   flex flex-col
                                   items-start
                                   lg:items-end
                                   gap-3"
                        >

                            {{-- Prioritas --}}
                            @php
                                $priorityClass = match ($task->priority) {
                                    'tinggi' =>
                                        'bg-[#f9eeee] text-[#9b4040] border-[#efd6d6]',

                                    'sedang' =>
                                        'bg-[#faf6e8] text-[#806b27] border-[#eadfba]',

                                    default =>
                                        'bg-[#edf5ef] text-[#315c40] border-[#d5e5d8]',
                                };
                            @endphp

                            <span
                                class="inline-flex
                                       items-center
                                       border
                                       rounded-md
                                       px-3 py-1.5
                                       text-xs
                                       font-medium
                                       {{ $priorityClass }}"
                            >
                                Prioritas:
                                {{ $priorityLabels[$task->priority] }}
                            </span>


                            {{-- Status Staff / Intern --}}
                            @if(auth()->user()->role !== 'kabag' && $currentAssignment)

                                <form
                                    method="POST"
                                    action="{{ route('tasks.update-status', $task) }}"
                                >

                                    @csrf
                                    @method('PUT')

                                    <label
                                        class="block mb-1.5
                                               text-xs
                                               font-medium
                                               text-[#6b7280]"
                                    >
                                        Status Saya
                                    </label>

                                    <select
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

                        </div>

                    </div>

                </div>

            @empty

                <div
                    class="bg-white
                           border border-[#e2e7e2]
                           rounded-md
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


            function filterTasks() {

                const search =
                    searchInput.value
                        .toLowerCase()
                        .trim();

                const priority =
                    priorityFilter.value;

                const status =
                    statusFilter.value;


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


                    item.style.display =
                        matchSearch &&
                        matchPriority &&
                        matchStatus
                            ? ''
                            : 'none';

                });

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
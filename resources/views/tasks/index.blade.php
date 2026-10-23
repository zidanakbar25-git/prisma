<x-layouts.app
    title="To-Do - PRISMA"
    pageTitle="To-Do"
>

    @php
        $isKabag = auth()->user()->role === 'kabag';

        // Modal mana yang harus dibuka lagi setelah validasi gagal
        $openModal = session('open_modal', old('_modal'));
        $editId = session('edit_id', old('_task_id'));

        if ($isKabag) {
            // Daftar penerima. Kalau controller sudah mengirim $users, itu yang dipakai.
            $users = $users ?? \App\Models\User::where('is_active', true)
                ->whereIn('role', ['staff', 'intern'])
                ->orderBy('name')
                ->get();

            // Data untuk mengisi popup Edit
            $taskData = $tasks->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'priority' => $item->priority,
                    'due_date' => $item->due_date->format('Y-m-d'),
                    'assignee_ids' => $item->assignees->pluck('id')->values()->all(),
                ];
            })->values();
        }

        $fieldClass = 'mt-2 block w-full rounded-lg border border-[#E5E9F0] bg-white px-3 py-2.5 text-sm text-[#24332A] outline-none transition placeholder:text-[#9aa49d] focus:border-[#234936] focus:ring-1 focus:ring-[#234936]';
    @endphp

    <div id="todo-page" class="space-y-7" data-page-anchor>

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

            @if($isKabag)

                <button
                    type="button"
                    id="open-create-task"
                    class="inline-flex shrink-0 items-center justify-center rounded-lg bg-[#234936] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#315a45] focus:outline-none focus:ring-2 focus:ring-[#8ca795] focus:ring-offset-2"
                >
                    Tambah To-Do
                </button>

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
                            @if($isKabag)

                                <div
                                    class="flex items-center gap-2"
                                >

                                    {{-- Edit (membuka popup) --}}
                                    <button
                                        type="button"
                                        data-edit-task="{{ $task->id }}"
                                        class="inline-flex h-9 items-center justify-center rounded-lg border border-[#d9dfda] bg-white px-3.5 text-xs font-semibold text-[#37443b] transition hover:border-[#b9c9bd] hover:bg-[#f5f8f5] focus:outline-none focus:ring-2 focus:ring-[#557963]/20"
                                    >
                                        Edit
                                    </button>


                                    {{-- Hapus --}}
                                    <form
                                        method="POST"
                                        action="{{ route('tasks.destroy', $task) }}"
                                        data-confirm-title="Hapus To-Do?"
                                        data-confirm-message="To-Do “{{ $task->title }}” akan dihapus dan tidak dapat dikembalikan."
                                        data-confirm-button="Hapus"
                                        data-confirm-tone="danger"
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
                    @if($isKabag)

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


    {{-- ========================================================= --}}
    {{-- POPUP TAMBAH / EDIT TO-DO (hanya Kabag) --}}
    {{-- ========================================================= --}}

    @if($isKabag)

        @foreach(['create', 'edit'] as $mode)

            @php
                $isEdit = $mode === 'edit';

                // Popup Tambah menampilkan kembali input lama setelah validasi gagal.
                // Popup Edit diisi ulang lewat JavaScript.
                $restore = !$isEdit && $openModal === 'create';
            @endphp

            <div
                id="{{ $mode }}-task-modal"
                class="fixed inset-0 z-50 hidden overflow-y-auto"
                aria-hidden="true"
            >

                <div
                    class="flex min-h-full items-center justify-center bg-black/40 px-4 py-8"
                    data-modal-backdrop="{{ $mode }}"
                >

                    <div
                        class="w-full max-w-2xl rounded-2xl border border-[#E5E9F0] bg-white shadow-xl"
                        role="dialog"
                        aria-modal="true"
                        aria-labelledby="{{ $mode }}-task-title"
                    >

                        {{-- Header popup --}}
                        <div class="flex items-center justify-between border-b border-[#E5E9F0] px-6 py-4">

                            <div>
                                <h2
                                    id="{{ $mode }}-task-title"
                                    class="text-lg font-semibold text-[#24332A]"
                                >
                                    {{ $isEdit ? 'Edit To-Do' : 'Tambah To-Do' }}
                                </h2>

                                <p class="mt-1 text-sm text-[#68746D]">
                                    {{ $isEdit
                                        ? 'Perbarui informasi dan penerima tugas.'
                                        : 'Buat tugas baru dan tentukan penerima tugas.' }}
                                </p>
                            </div>

                            <button
                                type="button"
                                data-close-modal="{{ $mode }}"
                                class="text-sm font-medium text-[#68746D] transition hover:text-[#24332A]"
                            >
                                Tutup
                            </button>

                        </div>


                        <form
                            id="{{ $mode }}-task-form"
                            method="POST"
                            action="{{ $isEdit
                                ? route('tasks.update', ['task' => '__ID__'])
                                : route('tasks.store') }}"
                            @if($isEdit)
                                data-action-template="{{ route('tasks.update', ['task' => '__ID__']) }}"
                            @endif
                        >

                            @csrf

                            @if($isEdit)
                                @method('PUT')
                                <input type="hidden" name="_task_id" id="edit-task-id" value="">
                            @endif

                            <input type="hidden" name="_modal" value="{{ $mode }}">


                            <div class="max-h-[70vh] overflow-y-auto px-6 py-6">

                                {{-- Error validasi --}}
                                @if($errors->any() && $openModal === $mode)

                                    <div class="mb-5 rounded-xl border border-[#FECACA] bg-[#FEF2F2] px-4 py-3 text-sm text-[#B91C1C]">

                                        <p class="font-medium">Periksa kembali data yang dimasukkan.</p>

                                        <ul class="mt-2 list-disc space-y-1 pl-5">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>

                                    </div>

                                @endif


                                {{-- Judul --}}
                                <div>
                                    <label for="{{ $mode }}-title" class="block text-sm font-medium text-[#24332A]">
                                        Judul To-Do
                                    </label>

                                    <input
                                        id="{{ $mode }}-title"
                                        type="text"
                                        name="title"
                                        value="{{ $restore ? old('title') : '' }}"
                                        maxlength="255"
                                        required
                                        class="{{ $fieldClass }}"
                                        placeholder="Contoh: Buat narasi kegiatan wisuda"
                                    >
                                </div>


                                {{-- Deskripsi --}}
                                <div class="mt-5">
                                    <label for="{{ $mode }}-description" class="block text-sm font-medium text-[#24332A]">
                                        Deskripsi
                                    </label>

                                    <textarea
                                        id="{{ $mode }}-description"
                                        name="description"
                                        rows="4"
                                        class="{{ $fieldClass }} resize-none"
                                        placeholder="Jelaskan pekerjaan yang perlu dilakukan..."
                                    >{{ $restore ? old('description') : '' }}</textarea>
                                </div>


                                {{-- Prioritas + Deadline --}}
                                <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">

                                    <div>
                                        <label for="{{ $mode }}-priority" class="block text-sm font-medium text-[#24332A]">
                                            Prioritas
                                        </label>

                                        <select
                                            id="{{ $mode }}-priority"
                                            name="priority"
                                            required
                                            class="{{ $fieldClass }}"
                                        >
                                            <option value="" disabled {{ $restore && old('priority') ? '' : 'selected' }}>
                                                Pilih prioritas
                                            </option>

                                            @foreach(['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi'] as $value => $label)
                                                <option
                                                    value="{{ $value }}"
                                                    {{ $restore && old('priority') === $value ? 'selected' : '' }}
                                                >
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="{{ $mode }}-due-date" class="block text-sm font-medium text-[#24332A]">
                                            Deadline
                                        </label>

                                        <input
                                            id="{{ $mode }}-due-date"
                                            type="date"
                                            name="due_date"
                                            value="{{ $restore ? old('due_date') : '' }}"
                                            required
                                            class="{{ $fieldClass }}"
                                        >
                                    </div>

                                </div>


                                {{-- Penerima --}}
                                <div class="mt-5">

                                    <div class="flex items-start justify-between gap-3">

                                        <div>
                                            <label class="block text-sm font-medium text-[#24332A]">
                                                Ditugaskan kepada
                                            </label>

                                            <p class="mt-1 text-xs text-[#68746D]">
                                                Pilih satu atau beberapa Staff/Intern.
                                            </p>
                                        </div>

                                        <button
                                            type="button"
                                            data-select-all="{{ $mode }}"
                                            class="shrink-0 rounded-lg border border-[#E5E9F0] bg-white px-3 py-1.5 text-sm font-medium text-[#24332A] shadow-sm transition hover:bg-[#F1F5F9]"
                                        >
                                            Pilih semua
                                        </button>

                                    </div>

                                    <div class="mt-3 max-h-52 overflow-y-auto rounded-lg border border-[#E5E9F0]">

                                        @forelse($users as $user)

                                            <label class="flex cursor-pointer items-center gap-3 border-b border-[#EEF2F6] px-3 py-2.5 last:border-b-0 hover:bg-[#F7F9F7]">

                                                <input
                                                    type="checkbox"
                                                    name="assignee_ids[]"
                                                    value="{{ $user->id }}"
                                                    data-assignee="{{ $mode }}"
                                                    {{ $restore && in_array($user->id, old('assignee_ids', [])) ? 'checked' : '' }}
                                                    class="h-4 w-4 rounded border-[#CBD5E1] text-[#234936] focus:ring-[#234936]"
                                                >

                                                <div>
                                                    <p class="text-sm font-medium text-[#24332A]">
                                                        {{ $user->name }}
                                                    </p>

                                                    <p class="text-xs text-[#68746D]">
                                                        {{ ucfirst($user->role) }}@if(!empty($user->username)) · {{ $user->username }}@endif
                                                    </p>
                                                </div>

                                            </label>

                                        @empty

                                            <p class="px-3 py-4 text-sm text-[#B91C1C]">
                                                Tidak ada Staff atau Intern aktif yang dapat menerima tugas.
                                            </p>

                                        @endforelse

                                    </div>

                                </div>

                            </div>


                            {{-- Footer popup --}}
                            <div class="flex items-center justify-end gap-3 border-t border-[#E5E9F0] px-6 py-4">

                                <button
                                    type="button"
                                    data-close-modal="{{ $mode }}"
                                    class="rounded-lg border border-[#E5E9F0] bg-white px-4 py-2.5 text-sm font-medium text-[#24332A] transition hover:bg-[#F1F5F9]"
                                >
                                    Batal
                                </button>

                                <button
                                    type="submit"
                                    class="rounded-lg bg-[#234936] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#315A45]"
                                >
                                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan To-Do' }}
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        @endforeach


        {{-- Data untuk JavaScript --}}
        <script id="task-data" type="application/json">
            @json($taskData)
        </script>

        <script>
            window.taskModalState = {
                openModal: @json($openModal),
                editId: @json($editId),

                old: {
                    title: @json(old('title')),
                    description: @json(old('description')),
                    priority: @json(old('priority')),
                    due_date: @json(old('due_date')),
                    assignee_ids: @json(old('assignee_ids', []))
                }
            };
        </script>

    @endif


    {{-- Script popup Tambah / Edit --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const createModal = document.getElementById('create-task-modal');
            const editModal = document.getElementById('edit-task-modal');

            // Staff / Intern tidak punya popup
            if (!createModal || !editModal) {
                return;
            }

            const dataEl = document.getElementById('task-data');
            const tasks = dataEl ? JSON.parse(dataEl.textContent || '[]') : [];

            const modalState = window.taskModalState || {};

            function getModal(name) {
                return name === 'create' ? createModal : name === 'edit' ? editModal : null;
            }

            /* ----------------------------------------------------- */
            /* Buka / tutup popup                                     */
            /* ----------------------------------------------------- */

            /* ----------------------------------------------------- */
            /* Posisikan popup di tengah area konten (tanpa sidebar)  */
            /* ----------------------------------------------------- */

            const pageEl = document.getElementById('todo-page');

            function centerModal(modal) {
                const box = modal.querySelector('[data-modal-backdrop]');

                if (!box || !pageEl) {
                    return;
                }

                // Titik tengah area konten, diukur dari tepi kiri layar
                const rect = pageEl.getBoundingClientRect();
                const contentCenter = rect.left + rect.width / 2;

                // Lebar layar yang tersedia untuk popup
                const width = modal.clientWidth;

                // Geser popup ke kanan (padding kiri) supaya tepat di titik tengah konten.
                // 16 = padding bawaan (px-4).
                box.style.paddingLeft =
                    Math.max(16, 2 * contentCenter - width + 16) + 'px';
            }

            window.addEventListener('resize', function () {
                [createModal, editModal].forEach(function (modal) {
                    if (!modal.classList.contains('hidden')) {
                        centerModal(modal);
                    }
                });
            });

            function openModal(modal) {
                modal.classList.remove('hidden');
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('overflow-hidden');

                // Ukur setelah scrollbar halaman hilang (overflow-hidden)
                centerModal(modal);

                refreshSelectAll();
            }

            function closeModal(modal) {
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');

                const anyOpen = [createModal, editModal].some(function (item) {
                    return !item.classList.contains('hidden');
                });

                if (!anyOpen) {
                    document.body.classList.remove('overflow-hidden');
                }
            }

            function closeAll() {
                [createModal, editModal].forEach(function (modal) {
                    modal.classList.add('hidden');
                    modal.setAttribute('aria-hidden', 'true');
                });

                document.body.classList.remove('overflow-hidden');
            }

            /* ----------------------------------------------------- */
            /* Popup Tambah                                           */
            /* ----------------------------------------------------- */

            function openCreate() {
                closeAll();
                openModal(createModal);

                const title = document.getElementById('create-title');

                if (title) {
                    title.focus();
                }
            }

            const openCreateButton = document.getElementById('open-create-task');

            if (openCreateButton) {
                openCreateButton.addEventListener('click', openCreate);
            }

            /* ----------------------------------------------------- */
            /* Popup Edit                                             */
            /* ----------------------------------------------------- */

            function setAssignees(mode, ids) {
                const selected = (ids || []).map(String);

                document
                    .querySelectorAll('[data-assignee="' + mode + '"]')
                    .forEach(function (box) {
                        box.checked = selected.includes(String(box.value));
                    });
            }

            function openEdit(taskId) {
                const task = tasks.find(function (item) {
                    return String(item.id) === String(taskId);
                });

                if (!task) {
                    return;
                }

                closeAll();

                const form = document.getElementById('edit-task-form');

                form.action = form.dataset.actionTemplate.replace('__ID__', task.id);

                document.getElementById('edit-task-id').value = task.id;
                document.getElementById('edit-title').value = task.title || '';
                document.getElementById('edit-description').value = task.description || '';
                document.getElementById('edit-priority').value = task.priority || '';
                document.getElementById('edit-due-date').value = task.due_date || '';

                setAssignees('edit', task.assignee_ids);

                openModal(editModal);

                document.getElementById('edit-title').focus();
            }

            document
                .querySelectorAll('[data-edit-task]')
                .forEach(function (button) {
                    button.addEventListener('click', function () {
                        openEdit(button.dataset.editTask);
                    });
                });

            /* ----------------------------------------------------- */
            /* Pilih semua penerima                                   */
            /* ----------------------------------------------------- */

            function refreshSelectAll() {
                document
                    .querySelectorAll('[data-select-all]')
                    .forEach(function (button) {
                        const boxes = Array.from(
                            document.querySelectorAll(
                                '[data-assignee="' + button.dataset.selectAll + '"]'
                            )
                        );

                        const allChecked = boxes.length > 0 && boxes.every(function (box) {
                            return box.checked;
                        });

                        button.textContent = allChecked ? 'Batal pilih semua' : 'Pilih semua';
                    });
            }

            document
                .querySelectorAll('[data-select-all]')
                .forEach(function (button) {
                    button.addEventListener('click', function () {
                        const boxes = Array.from(
                            document.querySelectorAll(
                                '[data-assignee="' + button.dataset.selectAll + '"]'
                            )
                        );

                        const allChecked = boxes.length > 0 && boxes.every(function (box) {
                            return box.checked;
                        });

                        boxes.forEach(function (box) {
                            box.checked = !allChecked;
                        });

                        refreshSelectAll();
                    });
                });

            document
                .querySelectorAll('[data-assignee]')
                .forEach(function (box) {
                    box.addEventListener('change', refreshSelectAll);
                });

            /* ----------------------------------------------------- */
            /* Tutup: tombol, klik area gelap, tombol Esc             */
            /* ----------------------------------------------------- */

            document
                .querySelectorAll('[data-close-modal]')
                .forEach(function (button) {
                    button.addEventListener('click', function () {
                        closeModal(getModal(button.dataset.closeModal));
                    });
                });

            document
                .querySelectorAll('[data-modal-backdrop]')
                .forEach(function (backdrop) {
                    backdrop.addEventListener('click', function (event) {
                        if (event.target !== backdrop) {
                            return;
                        }

                        closeModal(getModal(backdrop.dataset.modalBackdrop));
                    });
                });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeAll();
                }
            });

            /* ----------------------------------------------------- */
            /* Buka lagi popup setelah validasi gagal                 */
            /* ----------------------------------------------------- */

            if (modalState.openModal === 'create') {
                openCreate();
            }

            if (modalState.openModal === 'edit' && modalState.editId) {
                openEdit(modalState.editId);

                const old = modalState.old || {};
                const has = function (value) {
                    return value !== null && value !== undefined;
                };

                if (has(old.title)) {
                    document.getElementById('edit-title').value = old.title;
                }

                if (has(old.description)) {
                    document.getElementById('edit-description').value = old.description;
                }

                if (has(old.priority)) {
                    document.getElementById('edit-priority').value = old.priority;
                }

                if (has(old.due_date)) {
                    document.getElementById('edit-due-date').value = old.due_date;
                }

                setAssignees('edit', old.assignee_ids);
                refreshSelectAll();
            }

            refreshSelectAll();

        });
    </script>


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

@include('partials.confirm-dialog')

</x-layouts.app>
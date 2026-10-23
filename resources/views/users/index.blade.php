<x-layouts.app
    title="Pengguna - PRISMA"
    pageTitle="Pengguna"
>

    @php
        // Modal mana yang harus dibuka lagi setelah validasi gagal
        $openModal = session('open_modal', old('_modal'));
        $editId = session('edit_id', old('_user_id'));

        // Akun yang tampil di tabel (Kabag tidak ditampilkan)
        $listedUsers = $users->reject(fn ($item) => $item->role === 'kabag');

        // Data untuk mengisi popup Edit
        $userData = $listedUsers->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'username' => $item->username,
                'role' => $item->role,
            ];
        })->values();

        $fieldClass = 'mt-2 block w-full rounded-lg border border-[#E5E9F0] bg-white px-3 py-2.5 text-sm text-[#24332a] outline-none transition placeholder:text-[#9ca3af] focus:border-[#234936] focus:ring-1 focus:ring-[#234936]';
    @endphp

    <div id="users-page" class="space-y-6" data-page-anchor>

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold tracking-tight text-[#24332a]">
                    Manajemen Pengguna
                </h2>

                <p class="mt-1 text-sm text-[#6b7280]">
                    Kelola akun dan akses pengguna sistem Humas.
                </p>

                <p class="mt-2 text-xs font-medium text-[#7b857e]">
                    {{ $users->count() }} akun terdaftar
                </p>
            </div>

            <button
                type="button"
                id="open-create-user"
                class="inline-flex h-11 shrink-0 items-center justify-center rounded-lg bg-[#234936] px-5 text-sm font-medium text-white transition hover:bg-[#315a45] focus:outline-none focus:ring-2 focus:ring-[#8eaa96] focus:ring-offset-2"
            >
                <svg
                    class="mr-2 h-4 w-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    aria-hidden="true"
                >
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Tambah Pengguna
            </button>
        </div>

        {{-- Daftar pengguna --}}
        <div class="overflow-hidden rounded-xl border border-[#dfe6df] bg-white shadow-[0_2px_10px_rgba(35,73,54,0.04)]">

            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-left">

                    <thead>
                        <tr class="border-b border-[#e2e7e2] bg-[#f7f8f6]">
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-[#68746d]">
                                Nama
                            </th>

                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-[#68746d]">
                                Username
                            </th>

                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-[#68746d]">
                                Role
                            </th>

                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-[#68746d]">
                                Status
                            </th>

                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-[#68746d]">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#edf0ed]">

                        @forelse($listedUsers as $user)

                            @php
                                $roleLabels = [
                                    'kabag' => 'Kabag',
                                    'staff' => 'Staff',
                                    'intern' => 'Intern',
                                ];
                            @endphp

                            <tr class="transition-colors hover:bg-[#fafbf9]">

                                {{-- Nama --}}
                                <td class="px-6 py-5">
                                    <span class="text-sm font-semibold text-[#24332a]">
                                        {{ $user->name }}
                                    </span>
                                </td>

                                {{-- Username --}}
                                <td class="px-6 py-5">
                                    <span class="text-sm text-[#68746d]">
                                        {{ $user->username }}
                                    </span>
                                </td>

                                {{-- Role --}}
                                <td class="px-6 py-5">
                                    <span class="inline-flex items-center rounded-md bg-[#f0f3f0] px-2.5 py-1 text-xs font-medium text-[#46574b]">
                                        {{ $roleLabels[$user->role] ?? $user->role }}
                                    </span>
                                </td>

                                {{-- Toggle status --}}
                                <td class="px-6 py-5">
                                    @if($user->id !== auth()->id())

                                        <form
                                            method="POST"
                                            action="{{ route('users.toggle-status', $user) }}"
                                            data-confirm-title="{{ $user->is_active ? 'Nonaktifkan akun?' : 'Aktifkan akun?' }}"
                                            data-confirm-message="{{ $user->is_active ? 'Akun ' . $user->name . ' akan dinonaktifkan dan tidak dapat masuk ke sistem.' : 'Akun ' . $user->name . ' akan diaktifkan kembali dan dapat masuk ke sistem.' }}"
                                            data-confirm-button="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                            data-confirm-tone="{{ $user->is_active ? 'danger' : 'primary' }}"
                                        >
                                            @csrf
                                            @method('PUT')

                                            <button
                                                type="submit"
                                                role="switch"
                                                aria-checked="{{ $user->is_active ? 'true' : 'false' }}"
                                                aria-label="{{ $user->is_active ? 'Nonaktifkan akun ' . $user->name : 'Aktifkan akun ' . $user->name }}"
                                                title="{{ $user->is_active ? 'Klik untuk menonaktifkan akun' : 'Klik untuk mengaktifkan akun' }}"
                                                class="inline-flex h-7 w-12 items-center rounded-full p-1 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-[#8eaa96] focus:ring-offset-2 {{ $user->is_active ? 'bg-[#279B59]' : 'bg-[#cbd2cc]' }}"
                                            >
                                                <span class="sr-only">
                                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>

                                                <span
                                                    aria-hidden="true"
                                                    class="h-5 w-5 rounded-full bg-white shadow-sm transition-transform duration-200 {{ $user->is_active ? 'translate-x-5' : 'translate-x-0' }}"
                                                ></span>
                                            </button>
                                        </form>

                                    @else

                                        <div class="flex items-center gap-2">
                                            <span
                                                role="switch"
                                                aria-checked="{{ $user->is_active ? 'true' : 'false' }}"
                                                aria-label="Status akun Anda"
                                                title="Akun yang sedang digunakan tidak dapat dinonaktifkan"
                                                class="inline-flex h-7 w-12 cursor-not-allowed items-center rounded-full p-1 opacity-70 {{ $user->is_active ? 'bg-[#279B59]' : 'bg-[#cbd2cc]' }}"
                                            >
                                                <span
                                                    class="h-5 w-5 rounded-full bg-white shadow-sm {{ $user->is_active ? 'translate-x-5' : 'translate-x-0' }}"
                                                ></span>
                                            </span>

                                            <span class="text-xs text-[#7b857e]">
                                                Akun Anda
                                            </span>
                                        </div>

                                    @endif
                                </td>

                                {{-- Aksi: Edit (membuka popup) --}}
                                <td class="px-6 py-5">
                                    <div class="flex justify-end">
                                        <button
                                            type="button"
                                            data-edit-user="{{ $user->id }}"
                                            aria-label="Edit pengguna {{ $user->name }}"
                                            title="Edit pengguna"
                                            class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-[#dce4dd] bg-white text-[#3c5143] transition hover:border-[#9db6a3] hover:bg-[#f0f5f1] hover:text-[#234936] focus:outline-none focus:ring-2 focus:ring-[#8eaa96] focus:ring-offset-2"
                                        >
                                            <svg
                                                class="h-[18px] w-[18px]"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                aria-hidden="true"
                                            >
                                                <path d="M12 20h9"/>
                                                <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="px-6 py-14 text-center">
                                    <div class="mx-auto flex max-w-sm flex-col items-center">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#edf5ef] text-[#315c40]">
                                            <svg
                                                class="h-6 w-6"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.7"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                aria-hidden="true"
                                            >
                                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                                <circle cx="9" cy="7" r="4"/>
                                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                            </svg>
                                        </div>

                                        <p class="mt-3 text-sm font-semibold text-[#37443b]">
                                            Belum ada pengguna
                                        </p>

                                        <p class="mt-1 text-sm text-[#7b857e]">
                                            Pengguna yang terdaftar akan muncul di sini.
                                        </p>
                                    </div>
                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- POPUP TAMBAH / EDIT PENGGUNA --}}
    {{-- ========================================================= --}}

    @foreach(['create', 'edit'] as $mode)

        @php
            $isEdit = $mode === 'edit';

            // Popup Tambah menampilkan kembali input lama setelah validasi gagal.
            // Popup Edit diisi ulang lewat JavaScript.
            $restore = !$isEdit && $openModal === 'create';
        @endphp

        <div
            id="{{ $mode }}-user-modal"
            class="fixed inset-0 z-50 hidden overflow-y-auto"
            aria-hidden="true"
        >

            <div
                class="flex min-h-full items-center justify-center bg-black/40 px-4 py-8"
                data-modal-backdrop="{{ $mode }}"
            >

                <div
                    class="w-full max-w-lg rounded-2xl border border-[#E5E9F0] bg-white shadow-xl"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="{{ $mode }}-user-title"
                >

                    {{-- Header popup --}}
                    <div class="flex items-center justify-between border-b border-[#E5E9F0] px-6 py-4">

                        <div>
                            <h2
                                id="{{ $mode }}-user-title"
                                class="text-lg font-semibold text-[#24332A]"
                            >
                                {{ $isEdit ? 'Edit Pengguna' : 'Tambah Pengguna' }}
                            </h2>

                            <p class="mt-1 text-sm text-[#68746D]">
                                {{ $isEdit
                                    ? 'Perbarui data akun pengguna.'
                                    : 'Tambahkan akun pengguna baru ke dalam sistem.' }}
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
                        id="{{ $mode }}-user-form"
                        method="POST"
                        action="{{ $isEdit
                            ? route('users.update', ['user' => '__ID__'])
                            : route('users.store') }}"
                        @if($isEdit)
                            data-action-template="{{ route('users.update', ['user' => '__ID__']) }}"
                        @endif
                    >

                        @csrf

                        @if($isEdit)
                            @method('PUT')
                            <input type="hidden" name="_user_id" id="edit-user-id" value="">
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


                            {{-- Nama --}}
                            <div>
                                <label for="{{ $mode }}-name" class="block text-sm font-medium text-[#24332A]">
                                    Nama
                                </label>

                                <input
                                    id="{{ $mode }}-name"
                                    type="text"
                                    name="name"
                                    value="{{ $restore ? old('name') : '' }}"
                                    required
                                    placeholder="Masukkan nama lengkap"
                                    class="{{ $fieldClass }}"
                                >
                            </div>


                            {{-- Username --}}
                            <div class="mt-5">
                                <label for="{{ $mode }}-username" class="block text-sm font-medium text-[#24332A]">
                                    Username
                                </label>

                                <input
                                    id="{{ $mode }}-username"
                                    type="text"
                                    name="username"
                                    value="{{ $restore ? old('username') : '' }}"
                                    required
                                    autocomplete="off"
                                    placeholder="Masukkan username"
                                    class="{{ $fieldClass }}"
                                >
                            </div>


                            {{-- Password (hanya saat tambah) --}}
                            @unless($isEdit)

                                <div class="mt-5">
                                    <label for="create-password" class="block text-sm font-medium text-[#24332A]">
                                        Password
                                    </label>

                                    <input
                                        id="create-password"
                                        type="password"
                                        name="password"
                                        required
                                        autocomplete="new-password"
                                        placeholder="Minimal 8 karakter"
                                        class="{{ $fieldClass }}"
                                    >

                                    <p class="mt-2 text-xs text-[#68746D]">
                                        Password minimal 8 karakter.
                                    </p>
                                </div>

                            @endunless


                            {{-- Role --}}
                            <div class="mt-5">
                                <label for="{{ $mode }}-role" class="block text-sm font-medium text-[#24332A]">
                                    Role
                                </label>

                                <select
                                    id="{{ $mode }}-role"
                                    name="role"
                                    required
                                    class="{{ $fieldClass }}"
                                >
                                    <option value="" disabled {{ $restore && old('role') ? '' : 'selected' }}>
                                        Pilih role
                                    </option>

                                    @foreach(['kabag' => 'Kabag', 'staff' => 'Staff', 'intern' => 'Intern'] as $value => $label)
                                        <option
                                            value="{{ $value }}"
                                            {{ $restore && old('role') === $value ? 'selected' : '' }}
                                        >
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
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
                                {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Pengguna' }}
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    @endforeach


    {{-- Data untuk JavaScript --}}
    <script id="user-data" type="application/json">
        @json($userData)
    </script>

    <script>
        window.userModalState = {
            openModal: @json($openModal),
            editId: @json($editId),

            old: {
                name: @json(old('name')),
                username: @json(old('username')),
                role: @json(old('role'))
            }
        };
    </script>


    {{-- Script popup Tambah / Edit --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const createModal = document.getElementById('create-user-modal');
            const editModal = document.getElementById('edit-user-modal');

            if (!createModal || !editModal) {
                return;
            }

            const dataEl = document.getElementById('user-data');
            const users = dataEl ? JSON.parse(dataEl.textContent || '[]') : [];

            const modalState = window.userModalState || {};

            function getModal(name) {
                return name === 'create' ? createModal : name === 'edit' ? editModal : null;
            }

            /* ----------------------------------------------------- */
            /* Posisikan popup di tengah area konten (tanpa sidebar)  */
            /* ----------------------------------------------------- */

            const pageEl = document.getElementById('users-page');

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

                // 16 = padding bawaan (px-4)
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

            /* ----------------------------------------------------- */
            /* Buka / tutup popup                                     */
            /* ----------------------------------------------------- */

            function openModal(modal) {
                modal.classList.remove('hidden');
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('overflow-hidden');

                // Ukur setelah scrollbar halaman hilang (overflow-hidden)
                centerModal(modal);
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

                const name = document.getElementById('create-name');

                if (name) {
                    name.focus();
                }
            }

            const openCreateButton = document.getElementById('open-create-user');

            if (openCreateButton) {
                openCreateButton.addEventListener('click', openCreate);
            }

            /* ----------------------------------------------------- */
            /* Popup Edit                                             */
            /* ----------------------------------------------------- */

            function openEdit(userId) {
                const user = users.find(function (item) {
                    return String(item.id) === String(userId);
                });

                if (!user) {
                    return;
                }

                closeAll();

                const form = document.getElementById('edit-user-form');

                form.action = form.dataset.actionTemplate.replace('__ID__', user.id);

                document.getElementById('edit-user-id').value = user.id;
                document.getElementById('edit-name').value = user.name || '';
                document.getElementById('edit-username').value = user.username || '';
                document.getElementById('edit-role').value = user.role || '';

                openModal(editModal);

                document.getElementById('edit-name').focus();
            }

            document
                .querySelectorAll('[data-edit-user]')
                .forEach(function (button) {
                    button.addEventListener('click', function () {
                        openEdit(button.dataset.editUser);
                    });
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

                if (has(old.name)) {
                    document.getElementById('edit-name').value = old.name;
                }

                if (has(old.username)) {
                    document.getElementById('edit-username').value = old.username;
                }

                if (has(old.role)) {
                    document.getElementById('edit-role').value = old.role;
                }
            }

        });
    </script>

@include('partials.confirm-dialog')

</x-layouts.app>
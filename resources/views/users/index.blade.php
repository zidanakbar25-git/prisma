
<x-layouts.app
    title="Pengguna - PRISMA"
    pageTitle="Pengguna"
>
    <div class="space-y-6">

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

            <a
                href="{{ route('users.create') }}"
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
            </a>
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

                        @forelse($users->reject(fn ($user) => $user->role === 'kabag') as $user)

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
                                            onsubmit="return confirm('{{ $user->is_active ? 'Apakah Anda yakin ingin menonaktifkan akun ini?' : 'Apakah Anda yakin ingin mengaktifkan akun ini?' }}');"
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

                                {{-- Aksi: Edit --}}
                                <td class="px-6 py-5">
                                    <div class="flex justify-end">
                                        <a
                                            href="{{ route('users.edit', $user) }}"
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
                                        </a>
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
</x-layouts.app>

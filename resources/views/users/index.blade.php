<x-layouts.app
    title="Pengguna - PRISMA"
    pageTitle="Pengguna"
>

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between gap-4">

            <div>
                <h2 class="text-xl font-semibold text-[#24332a]">
                    Manajemen Pengguna
                </h2>

                <p class="mt-1 text-sm text-[#6b7280]">
                    Kelola akun pengguna sistem Humas.
                </p>
            </div>


            <a
                href="{{ route('users.create') }}"
                class="shrink-0
                       rounded-md
                       bg-[#234936]
                       px-5 py-3
                       text-sm font-medium
                       text-white
                       transition
                       hover:bg-[#315a45]"
            >
                Tambah Pengguna
            </a>

        </div>


        {{-- Daftar pengguna --}}
        <div
            class="overflow-hidden
                   rounded-md
                   border border-[#e2e7e2]
                   bg-white"
        >

            <div class="overflow-x-auto">

                <table class="w-full text-left">

                    <thead>

                        <tr
                            class="border-b
                                   border-[#e2e7e2]
                                   bg-[#f7f8f6]"
                        >

                            <th
                                class="px-5 py-4
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wide
                                       text-[#6b7280]"
                            >
                                Nama
                            </th>

                            <th
                                class="px-5 py-4
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wide
                                       text-[#6b7280]"
                            >
                                Username
                            </th>

                            <th
                                class="px-5 py-4
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wide
                                       text-[#6b7280]"
                            >
                                Role
                            </th>

                            <th
                                class="px-5 py-4
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wide
                                       text-[#6b7280]"
                            >
                                Status
                            </th>

                            <th
                                class="px-5 py-4
                                       text-right
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wide
                                       text-[#6b7280]"
                            >
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($users as $user)

                            <tr
                                class="border-b
                                       border-[#edf0ed]
                                       last:border-0
                                       hover:bg-[#fafbf9]"
                            >

                                {{-- Nama --}}
                                <td class="px-5 py-4">

                                    <div
                                        class="text-sm
                                               font-medium
                                               text-[#24332a]"
                                    >
                                        {{ $user->name }}
                                    </div>

                                </td>


                                {{-- Username --}}
                                <td class="px-5 py-4">

                                    <span
                                        class="text-sm
                                               text-[#6b7280]"
                                    >
                                        {{ $user->username }}
                                    </span>

                                </td>


                                {{-- Role --}}
                                <td class="px-5 py-4">

                                    @php
                                        $roleLabels = [
                                            'kabag' => 'Kabag',
                                            'staff' => 'Staff',
                                            'intern' => 'Intern',
                                        ];
                                    @endphp

                                    <span
                                        class="text-sm
                                               text-[#37443b]"
                                    >
                                        {{ $roleLabels[$user->role] ?? $user->role }}
                                    </span>

                                </td>


                                {{-- Status --}}
                                <td class="px-5 py-4">

                                    @if($user->is_active)

                                        <span
                                            class="inline-flex
                                                   items-center
                                                   rounded-md
                                                   border
                                                   border-[#d5e5d8]
                                                   bg-[#edf5ef]
                                                   px-3 py-1
                                                   text-xs
                                                   font-medium
                                                   text-[#315c40]"
                                        >
                                            Aktif
                                        </span>

                                    @else

                                        <span
                                            class="inline-flex
                                                   items-center
                                                   rounded-md
                                                   border
                                                   border-[#e1e3e1]
                                                   bg-[#f2f3f2]
                                                   px-3 py-1
                                                   text-xs
                                                   font-medium
                                                   text-[#6b7280]"
                                        >
                                            Nonaktif
                                        </span>

                                    @endif

                                </td>


                                {{-- Aksi --}}
                                <td class="px-5 py-4">

                                    <div
                                        class="flex
                                               items-center
                                               justify-end
                                               gap-2"
                                    >

                                        {{-- Edit --}}
                                        <a
                                            href="{{ route('users.edit', $user) }}"
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


                                        {{-- Reset Password --}}
                                        <a
                                            href="{{ route('users.reset-password', $user) }}"
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
                                            Reset Password
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="px-6 py-12
                                           text-center"
                                >

                                    <p
                                        class="text-sm
                                               font-medium
                                               text-[#37443b]"
                                    >
                                        Belum ada pengguna.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</x-layouts.app>
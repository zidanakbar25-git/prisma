<x-layouts.app
    title="Tambah Pengguna - PRISMA"
    pageTitle="Tambah Pengguna"
>

    <div class="max-w-2xl">

        <div class="mb-6">

            <h2 class="text-xl font-semibold text-[#24332a]">
                Tambah Pengguna
            </h2>

            <p class="mt-1 text-sm text-[#6b7280]">
                Tambahkan akun pengguna baru ke dalam sistem.
            </p>

        </div>


        @if($errors->any())

            <div
                class="mb-6
                       rounded-md
                       border border-[#efd6d6]
                       bg-[#faf0f0]
                       px-4 py-3"
            >

                <ul class="space-y-1 text-sm text-[#9b4040]">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        <form
            method="POST"
            action="{{ route('users.store') }}"
            class="space-y-6"
        >

            @csrf


            {{-- Data Pengguna --}}
            <div
                class="rounded-md
                       border border-[#e2e7e2]
                       bg-white
                       p-6"
            >

                <div class="space-y-5">

                    {{-- Nama --}}
                    <div>

                        <label
                            for="name"
                            class="mb-2 block
                                   text-sm font-medium
                                   text-[#37443b]"
                        >
                            Nama
                        </label>

                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            placeholder="Masukkan nama lengkap"
                            class="w-full
                                   rounded-md
                                   border border-[#d9dfda]
                                   bg-white
                                   px-3 py-2.5
                                   text-sm
                                   text-[#24332a]
                                   outline-none
                                   placeholder:text-[#9ca3af]
                                   focus:border-[#557963]
                                   focus:ring-1
                                   focus:ring-[#557963]"
                        >

                    </div>


                    {{-- Username --}}
                    <div>

                        <label
                            for="username"
                            class="mb-2 block
                                   text-sm font-medium
                                   text-[#37443b]"
                        >
                            Username
                        </label>

                        <input
                            id="username"
                            name="username"
                            type="text"
                            value="{{ old('username') }}"
                            required
                            placeholder="Masukkan username"
                            class="w-full
                                   rounded-md
                                   border border-[#d9dfda]
                                   bg-white
                                   px-3 py-2.5
                                   text-sm
                                   text-[#24332a]
                                   outline-none
                                   placeholder:text-[#9ca3af]
                                   focus:border-[#557963]
                                   focus:ring-1
                                   focus:ring-[#557963]"
                        >

                    </div>


                    {{-- Password --}}
                    <div>

                        <label
                            for="password"
                            class="mb-2 block
                                   text-sm font-medium
                                   text-[#37443b]"
                        >
                            Password
                        </label>

                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            placeholder="Minimal 8 karakter"
                            class="w-full
                                   rounded-md
                                   border border-[#d9dfda]
                                   bg-white
                                   px-3 py-2.5
                                   text-sm
                                   text-[#24332a]
                                   outline-none
                                   placeholder:text-[#9ca3af]
                                   focus:border-[#557963]
                                   focus:ring-1
                                   focus:ring-[#557963]"
                        >

                        <p
                            class="mt-2
                                   text-xs
                                   text-[#6b7280]"
                        >
                            Password minimal 8 karakter.
                        </p>

                    </div>


                    {{-- Role --}}
                    <div>

                        <label
                            for="role"
                            class="mb-2 block
                                   text-sm font-medium
                                   text-[#37443b]"
                        >
                            Role
                        </label>

                        <select
                            id="role"
                            name="role"
                            required
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
                                Pilih role
                            </option>

                            <option
                                value="kabag"
                                {{ old('role') === 'kabag' ? 'selected' : '' }}
                            >
                                Kabag
                            </option>

                            <option
                                value="staff"
                                {{ old('role') === 'staff' ? 'selected' : '' }}
                            >
                                Staff
                            </option>

                            <option
                                value="intern"
                                {{ old('role') === 'intern' ? 'selected' : '' }}
                            >
                                Intern
                            </option>

                        </select>

                    </div>

                </div>

            </div>


            {{-- Tombol --}}
            <div class="flex items-center justify-between">

                <a
                    href="{{ route('users.index') }}"
                    class="rounded-md
                           border border-[#d9dfda]
                           bg-white
                           px-5 py-2.5
                           text-sm font-medium
                           text-[#37443b]
                           transition
                           hover:bg-[#f7f8f6]"
                >
                    Batal
                </a>


                <button
                    type="submit"
                    class="rounded-md
                           bg-[#234936]
                           px-5 py-2.5
                           text-sm font-medium
                           text-white
                           transition
                           hover:bg-[#315a45]"
                >
                    Simpan Pengguna
                </button>

            </div>

        </form>

    </div>

</x-layouts.app>
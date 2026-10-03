<x-layouts.app
    title="Tambah To-Do - PRISMA"
    pageTitle="Tambah To-Do"
>
    <div class="max-w-3xl">

        {{-- Header --}}
        <div class="mb-7">

            <a
                href="{{ route('tasks.index') }}"
                class="text-sm
                       text-[#68746D]
                       hover:text-[#234936]"
            >
                ← Kembali ke To-Do
            </a>

            <h2
                class="mt-4
                       text-xl
                       font-semibold
                       text-[#24332A]"
            >
                Tambah To-Do
            </h2>

            <p
                class="mt-1
                       text-sm
                       text-[#68746D]"
            >
                Buat tugas baru dan tentukan penerima tugas.
            </p>

        </div>


        {{-- Form --}}
        <form
            method="POST"
            action="{{ route('tasks.store') }}"
            class="border
                   border-[#E2E7E2]
                   bg-white
                   rounded-md
                   p-6"
        >
            @csrf


            {{-- Title --}}
            <div>

                <label
                    for="title"
                    class="block
                           text-sm
                           font-medium
                           text-[#24332A]"
                >
                    Judul To-Do
                </label>

                <input
                    id="title"
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    maxlength="255"
                    required
                    autofocus
                    class="mt-2
                           block
                           w-full
                           rounded-md
                           border
                           border-[#D8DED9]
                           bg-white
                           px-4
                           py-3
                           text-sm
                           text-[#24332A]
                           outline-none
                           transition
                           focus:border-[#234936]
                           focus:ring-1
                           focus:ring-[#234936]"
                    placeholder="Contoh: Buat narasi kegiatan wisuda"
                >

                @error('title')
                    <p
                        class="mt-2
                               text-sm
                               text-[#A04444]"
                    >
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Description --}}
            <div class="mt-6">

                <label
                    for="description"
                    class="block
                           text-sm
                           font-medium
                           text-[#24332A]"
                >
                    Deskripsi
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    class="mt-2
                           block
                           w-full
                           resize-none
                           rounded-md
                           border
                           border-[#D8DED9]
                           bg-white
                           px-4
                           py-3
                           text-sm
                           text-[#24332A]
                           outline-none
                           transition
                           focus:border-[#234936]
                           focus:ring-1
                           focus:ring-[#234936]"
                    placeholder="Jelaskan pekerjaan yang perlu dilakukan..."
                >{{ old('description') }}</textarea>

                @error('description')
                    <p
                        class="mt-2
                               text-sm
                               text-[#A04444]"
                    >
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Priority + Deadline --}}
            <div
                class="mt-6
                       grid
                       grid-cols-1
                       gap-6
                       md:grid-cols-2"
            >

                {{-- Priority --}}
                <div>

                    <label
                        for="priority"
                        class="block
                               text-sm
                               font-medium
                               text-[#24332A]"
                    >
                        Prioritas
                    </label>

                    <select
                        id="priority"
                        name="priority"
                        required
                        class="mt-2
                               block
                               w-full
                               rounded-md
                               border
                               border-[#D8DED9]
                               bg-white
                               px-4
                               py-3
                               text-sm
                               text-[#24332A]
                               outline-none
                               transition
                               focus:border-[#234936]
                               focus:ring-1
                               focus:ring-[#234936]"
                    >
                        <option
                            value=""
                            disabled
                            {{ old('priority') ? '' : 'selected' }}
                        >
                            Pilih prioritas
                        </option>

                        <option
                            value="rendah"
                            {{ old('priority') === 'rendah' ? 'selected' : '' }}
                        >
                            Rendah
                        </option>

                        <option
                            value="sedang"
                            {{ old('priority') === 'sedang' ? 'selected' : '' }}
                        >
                            Sedang
                        </option>

                        <option
                            value="tinggi"
                            {{ old('priority') === 'tinggi' ? 'selected' : '' }}
                        >
                            Tinggi
                        </option>
                    </select>

                    @error('priority')
                        <p
                            class="mt-2
                                   text-sm
                                   text-[#A04444]"
                        >
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Deadline --}}
                <div>

                    <label
                        for="due_date"
                        class="block
                               text-sm
                               font-medium
                               text-[#24332A]"
                    >
                        Deadline
                    </label>

                    <input
                        id="due_date"
                        type="date"
                        name="due_date"
                        value="{{ old('due_date') }}"
                        required
                        class="mt-2
                               block
                               w-full
                               rounded-md
                               border
                               border-[#D8DED9]
                               bg-white
                               px-4
                               py-3
                               text-sm
                               text-[#24332A]
                               outline-none
                               transition
                               focus:border-[#234936]
                               focus:ring-1
                               focus:ring-[#234936]"
                    >

                    @error('due_date')
                        <p
                            class="mt-2
                                   text-sm
                                   text-[#A04444]"
                        >
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>


            {{-- Assignees --}}
            <div class="mt-6">

                <div class="flex items-end justify-between gap-4">

                    <div>

                        <label
                            class="block
                                   text-sm
                                   font-medium
                                   text-[#24332A]"
                        >
                            Ditugaskan kepada
                        </label>

                        <p
                            class="mt-1
                                   text-xs
                                   text-[#8A948D]"
                        >
                            Pilih satu atau beberapa Staff/Intern.
                        </p>

                    </div>

                </div>


                <div
                    class="mt-3
                           overflow-hidden
                           rounded-md
                           border
                           border-[#D8DED9]"
                >

                    @forelse($users as $user)

                        <label
                            class="flex
                                   cursor-pointer
                                   items-center
                                   gap-3
                                   border-b
                                   border-[#EEF1EE]
                                   px-4
                                   py-3
                                   last:border-b-0
                                   hover:bg-[#F7F9F7]"
                        >

                            <input
                                type="checkbox"
                                name="assignee_ids[]"
                                value="{{ $user->id }}"
                                {{ in_array($user->id, old('assignee_ids', [])) ? 'checked' : '' }}
                                class="h-4
                                       w-4
                                       rounded
                                       border-[#BFC9C1]
                                       text-[#234936]
                                       focus:ring-[#234936]"
                            >

                            <div>

                                <p
                                    class="text-sm
                                           font-medium
                                           text-[#24332A]"
                                >
                                    {{ $user->name }}
                                </p>

                                <p
                                    class="mt-0.5
                                           text-xs
                                           capitalize
                                           text-[#8A948D]"
                                >
                                    {{ $user->role }}
                                    · {{ $user->username }}
                                </p>

                            </div>

                        </label>

                    @empty

                        <div class="px-4 py-5">

                            <p
                                class="text-sm
                                       text-[#A04444]"
                            >
                                Tidak ada Staff atau Intern aktif yang dapat menerima tugas.
                            </p>

                        </div>

                    @endforelse

                </div>

                @error('assignee_ids')
                    <p
                        class="mt-2
                               text-sm
                               text-[#A04444]"
                    >
                        {{ $message }}
                    </p>
                @enderror

                @error('assignee_ids.*')
                    <p
                        class="mt-2
                               text-sm
                               text-[#A04444]"
                    >
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Actions --}}
            <div
                class="mt-8
                       flex
                       items-center
                       justify-end
                       gap-3
                       border-t
                       border-[#EEF1EE]
                       pt-5"
            >

                <a
                    href="{{ route('tasks.index') }}"
                    class="rounded-md
                           px-5
                           py-3
                           text-sm
                           font-medium
                           text-[#68746D]
                           transition
                           hover:bg-[#F1F4F1]
                           hover:text-[#24332A]"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="rounded-md
                           bg-[#234936]
                           px-5
                           py-3
                           text-sm
                           font-medium
                           text-white
                           transition
                           hover:bg-[#315A45]"
                >
                    Simpan To-Do
                </button>

            </div>

        </form>

    </div>
</x-layouts.app>
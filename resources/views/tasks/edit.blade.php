<x-layouts.app
    title="Edit To-Do - PRISMA"
    pageTitle="Edit To-Do"
>

    <div class="max-w-3xl">

        <div class="mb-6">

            <h2 class="text-xl font-semibold text-[#24332a]">
                Edit To-Do
            </h2>

            <p class="mt-1 text-sm text-[#6b7280]">
                Perbarui informasi dan penerima tugas.
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
            action="{{ route('tasks.update', $task) }}"
            class="space-y-6"
        >

            @csrf
            @method('PUT')


            {{-- Informasi tugas --}}
            <div
                class="rounded-md
                       border border-[#e2e7e2]
                       bg-white
                       p-6"
            >

                <div class="space-y-5">

                    {{-- Judul --}}
                    <div>

                        <label
                            for="title"
                            class="mb-2 block
                                   text-sm font-medium
                                   text-[#37443b]"
                        >
                            Judul To-Do
                        </label>

                        <input
                            id="title"
                            name="title"
                            type="text"
                            value="{{ old('title', $task->title) }}"
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

                    </div>


                    {{-- Deskripsi --}}
                    <div>

                        <label
                            for="description"
                            class="mb-2 block
                                   text-sm font-medium
                                   text-[#37443b]"
                        >
                            Deskripsi
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            class="w-full
                                   rounded-md
                                   border border-[#d9dfda]
                                   bg-white
                                   px-3 py-2.5
                                   text-sm
                                   text-[#24332a]
                                   outline-none
                                   resize-y
                                   focus:border-[#557963]
                                   focus:ring-1
                                   focus:ring-[#557963]"
                        >{{ old('description', $task->description) }}</textarea>

                    </div>


                    {{-- Prioritas + Deadline --}}
                    <div
                        class="grid grid-cols-1
                               md:grid-cols-2
                               gap-5"
                    >

                        {{-- Prioritas --}}
                        <div>

                            <label
                                for="priority"
                                class="mb-2 block
                                       text-sm font-medium
                                       text-[#37443b]"
                            >
                                Prioritas
                            </label>

                            <select
                                id="priority"
                                name="priority"
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

                                <option
                                    value="rendah"
                                    {{ old('priority', $task->priority) === 'rendah' ? 'selected' : '' }}
                                >
                                    Rendah
                                </option>

                                <option
                                    value="sedang"
                                    {{ old('priority', $task->priority) === 'sedang' ? 'selected' : '' }}
                                >
                                    Sedang
                                </option>

                                <option
                                    value="tinggi"
                                    {{ old('priority', $task->priority) === 'tinggi' ? 'selected' : '' }}
                                >
                                    Tinggi
                                </option>

                            </select>

                        </div>


                        {{-- Deadline --}}
                        <div>

                            <label
                                for="due_date"
                                class="mb-2 block
                                       text-sm font-medium
                                       text-[#37443b]"
                            >
                                Deadline
                            </label>

                            <input
                                id="due_date"
                                name="due_date"
                                type="date"
                                value="{{ old('due_date', $task->due_date->format('Y-m-d')) }}"
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

                        </div>

                    </div>

                </div>

            </div>


            {{-- Penerima --}}
            <div
                class="rounded-md
                       border border-[#e2e7e2]
                       bg-white
                       p-6"
            >

                <div class="mb-4">

                    <h3
                        class="text-sm
                               font-semibold
                               text-[#24332a]"
                    >
                        Penerima To-Do
                    </h3>

                    <p
                        class="mt-1
                               text-xs
                               text-[#6b7280]"
                    >
                        Pilih Staff atau Intern yang akan menerima tugas.
                    </p>

                </div>


                <div class="space-y-3">

                    @foreach($users as $user)

                        @php
                            $isSelected = $task->assignees
                                ->contains('id', $user->id);
                        @endphp

                        <label
                            class="flex
                                   items-center
                                   gap-3
                                   rounded-md
                                   border border-[#e2e7e2]
                                   px-4 py-3
                                   cursor-pointer
                                   hover:bg-[#f7f8f6]"
                        >

                            <input
                                type="checkbox"
                                name="assignee_ids[]"
                                value="{{ $user->id }}"
                                {{ in_array(
                                    $user->id,
                                    old(
                                        'assignee_ids',
                                        $isSelected ? [$user->id] : []
                                    )
                                ) ? 'checked' : '' }}
                                class="h-4 w-4
                                       rounded
                                       border-[#cbd5cf]
                                       text-[#234936]
                                       focus:ring-[#557963]"
                            >

                            <div>

                                <div
                                    class="text-sm
                                           font-medium
                                           text-[#37443b]"
                                >
                                    {{ $user->name }}
                                </div>

                                <div
                                    class="mt-0.5
                                           text-xs
                                           capitalize
                                           text-[#6b7280]"
                                >
                                    {{ $user->role }}
                                </div>

                            </div>

                        </label>

                    @endforeach

                </div>

            </div>


            {{-- Tombol --}}
            <div class="flex items-center justify-between">

                <a
                    href="{{ route('tasks.index') }}"
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
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</x-layouts.app>
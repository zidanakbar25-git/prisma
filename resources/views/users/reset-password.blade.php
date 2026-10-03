<x-layouts.app
    title="Reset Password - PRISMA"
    pageTitle="Reset Password"
>

    <div class="max-w-2xl">

        <div class="mb-6">

            <h2 class="text-xl font-semibold text-[#24332a]">
                Reset Password
            </h2>

            <p class="mt-1 text-sm text-[#6b7280]">
                Atur password baru untuk akun
                <strong>{{ $user->name }}</strong>.
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
            action="{{ route('users.reset-password.store', $user) }}"
        >

            @csrf

            @method('PUT')


            <div
                class="rounded-md
                       border border-[#e2e7e2]
                       bg-white
                       p-6"
            >

                <div class="space-y-5">

                    {{-- Password Baru --}}
                    <div>

                        <label
                            for="password"
                            class="mb-2 block
                                   text-sm font-medium
                                   text-[#37443b]"
                        >
                            Password Baru
                        </label>

                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autofocus
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

                    </div>


                    {{-- Konfirmasi --}}
                    <div>

                        <label
                            for="password_confirmation"
                            class="mb-2 block
                                   text-sm font-medium
                                   text-[#37443b]"
                        >
                            Konfirmasi Password
                        </label>

                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            required
                            placeholder="Ulangi password baru"
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

                </div>

            </div>


            {{-- Tombol --}}
            <div class="mt-6 flex items-center justify-between">

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
                    Reset Password
                </button>

            </div>

        </form>

    </div>

</x-layouts.app>
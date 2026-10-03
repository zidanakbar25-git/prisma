<x-layouts.app title="Backup Database - PRISMA" pageTitle="Backup Database">

    <div class="space-y-6">

        {{-- Header --}}
        <div>
            <h2 class="text-xl font-semibold text-[#24332a]">
                Backup Database
            </h2>

            <p class="mt-1 text-sm text-[#6b7280]">
                Buat salinan database sistem secara manual untuk keperluan
                pemulihan data.
            </p>
        </div>


        {{-- Backup Card --}}
        <div class="max-w-2xl">

            <div
                class="border border-[#e2e7e2]
                       bg-white
                       rounded-md
                       p-6"
            >

                <div>

                    <h3 class="text-base font-semibold text-[#24332a]">
                        Backup Database
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-[#6b7280]">
                        Sistem akan membuat file backup berformat
                        <span class="font-medium text-[#374151]">
                            .sql
                        </span>
                        yang berisi struktur dan data database saat ini.
                    </p>

                </div>


                {{-- Information --}}
                <div
                    class="mt-6
                           border border-[#e2e7e2]
                           bg-[#f7f8f6]
                           rounded-md
                           p-4"
                >

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>

                            <div
                                class="text-xs
                                       uppercase
                                       tracking-wide
                                       text-[#6b7280]"
                            >
                                Database
                            </div>

                            <div
                                class="mt-1
                                       text-sm
                                       font-medium
                                       text-[#24332a]"
                            >
                                {{ config('database.connections.mysql.database') }}
                            </div>

                        </div>


                        <div>

                            <div
                                class="text-xs
                                       uppercase
                                       tracking-wide
                                       text-[#6b7280]"
                            >
                                Format
                            </div>

                            <div
                                class="mt-1
                                       text-sm
                                       font-medium
                                       text-[#24332a]"
                            >
                                SQL
                            </div>

                        </div>

                    </div>

                </div>


                {{-- Action --}}
                <div class="mt-6">

                    <a
                        href="{{ route('backup.download') }}"
                        class="inline-flex
                               items-center
                               justify-center
                               px-5 py-2.5
                               bg-[#315a45]
                               text-white
                               rounded-md
                               text-sm
                               font-medium
                               hover:bg-[#234936]
                               transition"
                    >
                        Buat & Download Backup
                    </a>

                </div>


                {{-- Notice --}}
                <div
                    class="mt-5
                           text-xs
                           leading-5
                           text-[#6b7280]"
                >
                    Proses backup dapat membutuhkan beberapa saat tergantung
                    ukuran database.
                </div>

            </div>

        </div>

    </div>

</x-layouts.app>
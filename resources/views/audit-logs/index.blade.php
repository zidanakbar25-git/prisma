<x-layouts.app title="Audit Log - PRISMA" pageTitle="Audit Log">

    <div class="space-y-6">

        {{-- Header --}}
        <div>
            <h2 class="text-xl font-semibold text-[#24332a]">
                Audit Log
            </h2>

            <p class="mt-1 text-sm text-[#6b7280]">
                Riwayat aktivitas pengguna dalam sistem.
            </p>
        </div>


        {{-- Filter --}}
        <div class="border border-[#e2e7e2] bg-white rounded-md p-5">

            <form
                method="GET"
                action="{{ route('audit-logs.index') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-4"
            >

                {{-- User --}}
                <div>

                    <label
                        for="user_id"
                        class="block text-sm font-medium text-[#374151] mb-2"
                    >
                        Pengguna
                    </label>

                    <select
                        id="user_id"
                        name="user_id"
                        class="w-full border border-[#d9dfda]
                               rounded-md px-3 py-2.5
                               text-sm text-[#24332a]
                               bg-white
                               focus:outline-none
                               focus:ring-2
                               focus:ring-[#3b6650]/20
                               focus:border-[#3b6650]"
                    >

                        <option value="">
                            Semua Pengguna
                        </option>

                        @foreach($users as $user)

                            <option
                                value="{{ $user->id }}"
                                @selected(request('user_id') == $user->id)
                            >
                                {{ $user->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Action --}}
                <div>

                    <label
                        for="action"
                        class="block text-sm font-medium text-[#374151] mb-2"
                    >
                        Aktivitas
                    </label>

                    <select
                        id="action"
                        name="action"
                        class="w-full border border-[#d9dfda]
                               rounded-md px-3 py-2.5
                               text-sm text-[#24332a]
                               bg-white
                               focus:outline-none
                               focus:ring-2
                               focus:ring-[#3b6650]/20
                               focus:border-[#3b6650]"
                    >

                        <option value="">
                            Semua Aktivitas
                        </option>

                        @foreach($actions as $action)

                            <option
                                value="{{ $action }}"
                                @selected(request('action') === $action)
                            >
                                {{ $action }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Date --}}
                <div>

                    <label
                        for="date"
                        class="block text-sm font-medium text-[#374151] mb-2"
                    >
                        Tanggal
                    </label>

                    <input
                        type="date"
                        id="date"
                        name="date"
                        value="{{ request('date') }}"
                        class="w-full border border-[#d9dfda]
                               rounded-md px-3 py-2.5
                               text-sm text-[#24332a]
                               bg-white
                               focus:outline-none
                               focus:ring-2
                               focus:ring-[#3b6650]/20
                               focus:border-[#3b6650]"
                    >

                </div>


                {{-- Buttons --}}
                <div class="flex items-end gap-2">

                    <button
                        type="submit"
                        class="px-4 py-2.5
                               bg-[#315a45]
                               text-white
                               rounded-md
                               text-sm
                               font-medium
                               hover:bg-[#234936]
                               transition"
                    >
                        Terapkan
                    </button>

                    <a
                        href="{{ route('audit-logs.index') }}"
                        class="px-4 py-2.5
                               border border-[#d9dfda]
                               text-[#4b5563]
                               rounded-md
                               text-sm
                               font-medium
                               hover:bg-[#f7f8f6]
                               transition"
                    >
                        Reset
                    </a>

                </div>

            </form>

        </div>


        {{-- Log Table --}}
        <div class="border border-[#e2e7e2] bg-white rounded-md overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full text-left">

                    <thead class="bg-[#f7f8f6] border-b border-[#e2e7e2]">

                        <tr>

                            <th
                                class="px-5 py-4
                                       text-xs font-semibold
                                       uppercase tracking-wide
                                       text-[#6b7280]"
                            >
                                Waktu
                            </th>

                            <th
                                class="px-5 py-4
                                       text-xs font-semibold
                                       uppercase tracking-wide
                                       text-[#6b7280]"
                            >
                                Pengguna
                            </th>

                            <th
                                class="px-5 py-4
                                       text-xs font-semibold
                                       uppercase tracking-wide
                                       text-[#6b7280]"
                            >
                                Aktivitas
                            </th>

                            <th
                                class="px-5 py-4
                                       text-xs font-semibold
                                       uppercase tracking-wide
                                       text-[#6b7280]"
                            >
                                Keterangan
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-[#e8ece8]">

                        @forelse($logs as $log)

                            <tr class="hover:bg-[#fafbf9] transition">

                                {{-- Waktu --}}
                                <td class="px-5 py-4 align-top">

                                    <div class="text-sm text-[#24332a]">
                                        {{ $log->created_at->format('d/m/Y') }}
                                    </div>

                                    <div class="text-xs text-[#6b7280] mt-1">
                                        {{ $log->created_at->format('H:i:s') }}
                                    </div>

                                </td>


                                {{-- User --}}
                                <td class="px-5 py-4 align-top">

                                    @if($log->user)

                                        <div class="text-sm font-medium text-[#24332a]">
                                            {{ $log->user->name }}
                                        </div>

                                        <div class="text-xs text-[#6b7280] mt-1">
                                            {{ $log->user->username }}
                                        </div>

                                    @else

                                        <span class="text-sm text-[#6b7280]">
                                            Pengguna tidak tersedia
                                        </span>

                                    @endif

                                </td>


                                {{-- Action --}}
                                <td class="px-5 py-4 align-top">

                                    <span
                                        class="inline-flex
                                               items-center
                                               px-2.5 py-1
                                               rounded-md
                                               bg-[#edf5ef]
                                               text-[#315c40]
                                               text-xs
                                               font-medium"
                                    >
                                        {{ $log->action }}
                                    </span>

                                </td>


                                {{-- Description --}}
                                <td
                                    class="px-5 py-4
                                           align-top
                                           text-sm
                                           text-[#4b5563]"
                                >
                                    {{ $log->description ?: '-' }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="4"
                                    class="px-5 py-12
                                           text-center
                                           text-sm
                                           text-[#6b7280]"
                                >
                                    Belum ada aktivitas yang tercatat.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            @if($logs->hasPages())

                <div class="px-5 py-4 border-t border-[#e2e7e2]">

                    {{ $logs->links() }}

                </div>

            @endif

        </div>

    </div>

</x-layouts.app>
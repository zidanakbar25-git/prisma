<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'PRISMA' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="bg-[#f7f8f6] text-[#1f2933]">

    {{-- Sidebar --}}
    <aside
        class="fixed left-0 top-0
               w-[260px] h-screen
               bg-[#234936] text-white
               flex flex-col">

        {{-- Logo --}}
        <div class="px-7 py-6 shrink-0">

            <div class="text-xl font-semibold tracking-wide">
                PRISMA
            </div>

            <div class="text-xs text-[#b9cbbf] mt-1">
                Sistem Informasi Humas
            </div>

        </div>


        {{-- Navigation --}}
        <nav class="flex-1 px-4 py-3 overflow-y-auto">

            {{-- Dashboard --}}
            <a
                href="{{ auth()->user()->role === 'kabag'
                    ? route('dashboard.kabag')
                    : (auth()->user()->role === 'staff'
                        ? route('dashboard.staff')
                        : route('dashboard.intern')) }}"
                class="block px-4 py-3 rounded-md
                       {{ request()->routeIs('dashboard.*')
                            ? 'bg-[#3b6650] text-white font-medium'
                            : 'text-[#dce8df] hover:bg-[#315a45]' }}
                       text-sm transition mb-1">
                Dashboard
            </a>


            {{-- Kalender Kegiatan --}}
            <a
                href="{{ route('activities.index') }}"
                class="block px-4 py-3 rounded-md
                       {{ request()->routeIs('activities.*')
                            ? 'bg-[#3b6650] text-white font-medium'
                            : 'text-[#dce8df] hover:bg-[#315a45]' }}
                       text-sm transition mb-1">
                Kalender Kegiatan
            </a>


            {{-- To-Do --}}
            @php
            $unreadTaskCount = 0;

            if (auth()->user()->role !== 'kabag') {
            $unreadTaskCount = auth()->user()
            ->assignedTasks()
            ->wherePivot('is_read', false)
            ->count();
            }
            @endphp

            <a
                href="{{ route('tasks.index') }}"
                class="flex items-center justify-between
                       px-4 py-3 rounded-md
                       {{ request()->routeIs('tasks.*')
                            ? 'bg-[#3b6650] text-white font-medium'
                            : 'text-[#dce8df] hover:bg-[#315a45]' }}
                       text-sm transition">
                <span>To-Do</span>

                @if($unreadTaskCount > 0)

                <span
                    class="text-[11px]
                               font-medium
                               bg-white
                               text-[#234936]
                               px-2 py-0.5
                               rounded-full">
                    {{ $unreadTaskCount }}
                </span>

                @endif

            </a>


            {{-- Manajemen --}}
            @if(auth()->user()->role === 'kabag')

            <div class="mt-7 mb-2 px-4">

                <span
                    class="text-[11px] uppercase
                               tracking-wider
                               text-[#9fb8a7]">
                    Manajemen
                </span>

            </div>


            {{-- Pengguna --}}
            <a
                href="{{ route('users.index') }}"
                class="block px-4 py-3 rounded-md
                           {{ request()->routeIs('users.*')
                                ? 'bg-[#3b6650] text-white font-medium'
                                : 'text-[#dce8df] hover:bg-[#315a45]' }}
                           text-sm transition mb-1">
                Pengguna
            </a>


            {{-- Audit Log --}}
            <a
                href="{{ route('audit-logs.index') }}"
                class="block px-4 py-3 rounded-md
           {{ request()->routeIs('audit-logs.*')
                ? 'bg-[#3b6650] text-white font-medium'
                : 'text-[#dce8df] hover:bg-[#315a45]' }}
           text-sm transition mb-1">
                Audit Log
            </a>

            {{-- Backup Database --}}
            <a
                href="{{ route('backup.index') }}"
                class="block px-4 py-3 rounded-md
           {{ request()->routeIs('backup.*')
                ? 'bg-[#3b6650] text-white font-medium'
                : 'text-[#dce8df] hover:bg-[#315a45]' }}
           text-sm transition">
                Backup Database
            </a>

            @endif

        </nav>


        {{-- User / Logout --}}
        <div class="px-5 pb-6 shrink-0">

            <div class="border-t border-[#3c5d4a] pt-5">

                <div class="px-1">

                    <div class="text-sm font-medium">
                        {{ auth()->user()->name }}
                    </div>

                    <div class="text-xs text-[#a9beaf] mt-1 capitalize">
                        {{ auth()->user()->role }}
                    </div>

                </div>


                <form
                    method="POST"
                    action="{{ route('logout') }}"
                    class="mt-5">

                    @csrf

                    <button
                        type="submit"
                        class="text-sm text-[#dce8df]
                               hover:text-white
                               transition">
                        Keluar
                    </button>

                </form>

            </div>

        </div>

    </aside>


    {{-- Main Area --}}
    <div class="ml-[260px] min-h-screen">

        {{-- Topbar --}}
        <header
            class="h-[72px]
                   bg-white
                   border-b border-[#e3e7e3]
                   flex items-center
                   px-8">

            <h1 class="text-lg font-semibold text-[#24332a]">
                {{ $pageTitle ?? 'Dashboard' }}
            </h1>

        </header>


        {{-- Content --}}
        <main class="p-8">

            @if(session('success'))

            <div
                class="mb-6
                           px-4 py-3
                           bg-[#edf5ef]
                           border border-[#d5e5d8]
                           text-[#315c40]
                           rounded-md
                           text-sm">
                {{ session('success') }}
            </div>

            @endif


            @if(session('error'))

            <div
                class="mb-6
                           px-4 py-3
                           bg-[#faf0f0]
                           border border-[#efd6d6]
                           text-[#9b4040]
                           rounded-md
                           text-sm">
                {{ session('error') }}
            </div>

            @endif


            {{ $slot ?? '' }}

            @yield('content')

        </main>

    </div>

    @livewireScripts

</body>

</html>
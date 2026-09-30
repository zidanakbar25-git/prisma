<x-layouts.app
    title="Dashboard Staff"
    page-title="Dashboard">

    <div>
        <h1 class="text-2xl font-bold text-gray-800">
            Dashboard Staff
        </h1>

        <p class="text-gray-500 mt-1">
            Selamat datang, {{ auth()->user()->name }}.
        </p>
    </div>

</x-layouts.app>
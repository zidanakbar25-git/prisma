<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Masuk - PRISMA</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f5f7f4]">

    <div class="min-h-screen flex items-center justify-center px-6">

        <div class="w-full max-w-[420px]">



            {{-- Login Card --}}
            <div class="bg-white border border-[#e0e6e1]
                        rounded-lg shadow-sm">

                <div class="px-8 py-8">

                    <div class="mb-7">

                        <h2 class="text-xl font-semibold text-[#24332a]">
                            Masuk
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Silakan masuk menggunakan akun Anda.
                        </p>

                    </div>


                    {{-- Success Message --}}
                    @if(session('success'))

                        <div class="mb-5 px-4 py-3
                                    bg-[#edf5ef]
                                    border border-[#d5e5d8]
                                    rounded-md
                                    text-sm text-[#315c40]">

                            {{ session('success') }}

                        </div>

                    @endif


                    {{-- Error Message --}}
                    @if($errors->any())

                        <div class="mb-5 px-4 py-3
                                    bg-[#faf0f0]
                                    border border-[#efd6d6]
                                    rounded-md
                                    text-sm text-[#9b4040]">

                            {{ $errors->first() }}

                        </div>

                    @endif


                    <form
                        method="POST"
                        action="{{ route('login.process') }}"
                    >

                        @csrf


                        {{-- Username --}}
                        <div class="mb-5">

                            <label
                                for="username"
                                class="block text-sm font-medium
                                       text-[#37433b] mb-2"
                            >
                                Username
                            </label>

                            <input
                                type="text"
                                id="username"
                                name="username"
                                value="{{ old('username') }}"
                                autocomplete="username"
                                autofocus
                                required

                                class="w-full h-11
                                       px-3
                                       border border-[#d7ded8]
                                       rounded-md
                                       text-sm text-gray-800
                                       outline-none
                                       transition

                                       focus:border-[#4d765d]
                                       focus:ring-2
                                       focus:ring-[#4d765d]/10

                                       placeholder:text-gray-400"

                                placeholder="Masukkan username"
                            >

                        </div>


                        {{-- Password --}}
                        <div class="mb-6">

                            <label
                                for="password"
                                class="block text-sm font-medium
                                       text-[#37433b] mb-2"
                            >
                                Password
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                autocomplete="current-password"
                                required

                                class="w-full h-11
                                       px-3
                                       border border-[#d7ded8]
                                       rounded-md
                                       text-sm text-gray-800
                                       outline-none
                                       transition

                                       focus:border-[#4d765d]
                                       focus:ring-2
                                       focus:ring-[#4d765d]/10

                                       placeholder:text-gray-400"

                                placeholder="Masukkan password"
                            >

                        </div>


                        {{-- Submit --}}
                        <button
                            type="submit"

                            class="w-full h-11
                                   bg-[#315a45]
                                   hover:bg-[#284d3a]
                                   text-white
                                   rounded-md
                                   text-sm font-medium
                                   transition duration-150"
                        >
                            Masuk
                        </button>

                    </form>

                </div>


                {{-- Footer Card --}}
                <div class="px-8 py-4
                            border-t border-[#edf0ed]
                            text-center">

                    <p class="text-xs text-gray-400">
                        Sistem Informasi Manajemen Humas
                    </p>

                </div>

            </div>


            

        </div>

    </div>

</body>
</html>
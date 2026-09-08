<h1>Dashboard Staff</h1>

<p>
    Selamat datang,
    {{ auth()->user()->name }}
</p>

<p>
    Role: {{ auth()->user()->role }}
</p>

<form method="POST" action="{{ route('logout') }}">
    @csrf

    <button type="submit">
        Logout
    </button>
</form>
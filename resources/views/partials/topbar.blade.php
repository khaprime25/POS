<header class="topbar">

    <div class="page-header">
        @yield('page-title', 'Dashboard')
    </div>

    <div class="topbar-actions">

        <!-- User -->
        <div class="user-box">
            <i class="fa-solid fa-user"></i>
            <span>
                {{ auth()->user()->name }}
            </span>
            <span class="role-badge">
                {{ ucfirst(auth()->user()->role) }}
            </span>
        </div>

        <!-- LOGOUT -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                Logout
            </button>
        </form>
    </div>

</header>
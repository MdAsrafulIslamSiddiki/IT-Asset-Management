<header class="nav">
    <div class="row">
        <div class="brand">
            <div class="logo"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M21 2l-9 9"></path>
                    <path d="M12.5 5.5l6 6"></path>
                    <circle cx="7.5" cy="16.5" r="4.5"></circle>
                </svg></div>
            <span>IT Asset Management</span>
        </div>
        <nav class="tabs">

            <a class="tab {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                Dashboard
            </a>

            <a class="tab {{ request()->routeIs('employees.index') ? 'active' : '' }}"
                href="{{ route('employees.index') }}">
                Employees
            </a>

            <a class="tab {{ request()->routeIs('asset-management.index') ? 'active' : '' }}" href="{{ route('asset-management.index') }}">
                Assets
            </a>

            <a class="tab {{ request()->routeIs('license') ? 'active' : '' }}" href="{{ route('license') }}">
                Licenses
            </a>

            <a class="tab {{ request()->is('search') ? 'active' : '' }}" href="{{ url('search') }}">
                Search
            </a>

            <a class="tab {{ request()->routeIs('report') ? 'active' : '' }}" href="{{ route('report') }}">
                Reports
            </a>

        </nav>

        <div class="spacer"></div>
        <div class="welcome">Welcome, Admin <div class="avatar">A</div>
        </div>
    </div>
</header>

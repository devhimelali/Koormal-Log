<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('assets/logos/koormal-logo.png') }}" alt="logo" height="36">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/logos/koormal-logo.png') }}" alt="" height="36">
            </span>
        </a>
        <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('assets/logos/koormal-logo.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/logos/koormal-logo.png') }}" alt="" height="22">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-3xl header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">

                <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link menu-link @if (Route::current()->getName() == 'admin.dashboard') active @endif"
                        aria-expanded="false">
                        <i class="ph-gauge"></i>
                        <span data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('supervisors-shift-log.index') }}"
                        class="nav-link menu-link @if (Route::current()->getName() == 'supervisors-shift-log.index') active @endif"
                        aria-expanded="false">
                        <i class="ph ph-clock-counter-clockwise"></i>
                        <span data-key="t-dashboard">Supervisors Shift Log</span>
                    </a>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>

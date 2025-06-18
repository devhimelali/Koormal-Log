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
                    <a href="{{ route('supervisors-shift-log.index', ['date' => date('d-m-Y')]) }}"
                       class="nav-link menu-link @if (Route::current()->getName() == 'supervisors-shift-log.index') active @endif"
                       aria-expanded="false">
                        <i class="ph ph-clock-counter-clockwise"></i>
                        <span data-key="t-dashboard">Supervisors Shift Log</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('notes.index') }}"
                       class="nav-link menu-link @if (Route::current()->getName() == 'notes.index') active @endif"
                       aria-expanded="false">
                        <i class="ph ph-file-text"></i>
                        <span data-key="t-dashboard">Notes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('opportune-jobs.index') }}"
                       class="nav-link menu-link @if (Route::current()->getName() == 'opportune-jobs.index') active @endif"
                       aria-expanded="false">
                        <i class="ph ph-briefcase"></i>
                        <span data-key="t-opportune-jobs">Opportune Jobs</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="javascript: void(0);"
                       class="nav-link menu-link handover-completion-menu-btn @if (Route::current()->getName() == 'handover-completions.index') active @endif"
                       aria-expanded="false">
                        <i class="ph ph-clipboard-text"></i>
                        <span data-key="t-opportune-jobs">Handover Completion</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>

<script>
    $(document).ready(function () {
        $('.handover-completion-menu-btn').on('click', function () {
            Swal.fire({
                title: 'Select Shift',
                icon: 'question',
                html: `
            <p>Which shift do you want to complete handover for?</p>
            <button class="swal2-confirm btn-option btn btn-primary" data-value="day_shift">Day Shift</button>
            <button class="swal2-confirm btn-option btn btn-secondary" data-value="night_shift">Night Shift</button>
        `,
                showConfirmButton: false,
                didOpen: () => {
                    document.querySelectorAll('.btn-option').forEach(button => {
                        button.addEventListener('click', () => {
                            const selected = button.getAttribute('data-value');
                            Swal.close();

                            let shift = '';
                            if (selected === 'day_shift') shift = 'day';
                            else if (selected === 'night_shift') shift = 'night';
                            const baseUrl = "{{ route('handover-completions.index') }}";
                            const redirectUrl = `${baseUrl}?shift=${shift}`;
                            window.location.href = redirectUrl;
                        });
                    });
                }
            });
        })
    })
</script>
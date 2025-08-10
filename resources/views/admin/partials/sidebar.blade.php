<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="{{ route('redirect') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('assets/logos/koormal-logo.png') }}" alt="logo" height="36">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/logos/koormal-logo.png') }}" alt="" height="36">
            </span>
        </a>
        <a href="{{ route('redirect') }}" class="logo logo-light">
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
                    <a href="{{ route('supervisors-shift-log.index', [
                        'role' => $role,
                        'date' => date('d-m-Y'),
                    ]) }}"
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
                    <a href="{{ route('handover-completion-questions.index') }}"
                        class="nav-link menu-link @if (Route::current()->getName() == 'handover-completion-questions.index') active @endif"
                        aria-expanded="false">
                        <i class="ph ph-question"></i>
                        <span data-key="t-handover-completion-questions">Handover Completion Questions</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="javascript: void(0);"
                        class="nav-link menu-link handover-completion-menu-btn @if (Route::current()->getName() == 'handover-completions.index') active @endif"
                        aria-expanded="false">
                        <i class="ph ph-clipboard-text"></i>
                        <span data-key="t-handover-completions">Handover Completions</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#sidebarCompany"
                        class="nav-link menu-link {{ in_array(Route::current()->getName(), ['crews.index', 'labours.index']) ? 'active' : 'collapsed' }}"
                        data-bs-toggle="collapse" role="button"
                        aria-expanded="{{ in_array(Route::current()->getName(), ['crews.index', 'labours.index']) ? 'true' : 'false' }}"
                        aria-controls="sidebarCompany">
                        <i class="ph ph-watch"></i>
                        <span data-key="t-company-information">Shift Crews</span>
                    </a>
                    <div class="menu-dropdown collapse {{ in_array(Route::current()->getName(), ['crews.index', 'labours.index']) ? 'show' : '' }}"
                        id="sidebarCompany" style="">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('crews.index') }}"
                                    class="nav-link {{ Route::current()->getName() == 'crews.index' ? 'active' : '' }}"
                                    data-key="t-crews">Crews</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('labours.index') }}"
                                    class="nav-link {{ Route::current()->getName() == 'labours.index' ? 'active' : '' }}"
                                    data-key="t-labour">Labour</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="{{ route('work-order-moves.index') }}"
                        class="nav-link menu-link @if (Route::current()->getName() == 'work-order-moves.index') active @endif"
                        aria-expanded="false">
                        <i class="ph ph-arrow-up-right"></i>
                        <span data-key="t-move-work-orders">Move Work Orders</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('schedule-compliance.index') }}"
                        class="nav-link menu-link @if (Route::current()->getName() == 'schedule-compliance.index') active @endif"
                        aria-expanded="false">
                        <i class="ph ph-calendar"></i>
                        <span data-key="t-schedule-compliance">Schedule Compliance</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('supervisor-completion-compliance.index') }}"
                        class="nav-link menu-link @if (Route::current()->getName() == 'supervisor-completion-compliance.index') active @endif"
                        aria-expanded="false">
                        <i class="ph ph-calendar-check"></i>
                        <span data-key="t-supervisor-completion-compliance">Supervisor Completion Compliance</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>

<script>
    $(document).ready(function() {
        $('.handover-completion-menu-btn').on('click', function() {
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
                            const selected = button.getAttribute(
                                'data-value');
                            Swal.close();

                            let shift = '';
                            if (selected === 'day_shift') shift = 'day';
                            else if (selected === 'night_shift') shift =
                                'night';
                            const baseUrl =
                                "{{ route('handover-completions.index') }}";
                            const redirectUrl = `${baseUrl}?shift=${shift}`;
                            window.location.href = redirectUrl;
                        });
                    });
                }
            });
        })
    })
</script>

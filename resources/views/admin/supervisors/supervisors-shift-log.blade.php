@extends('layouts.app')
@section('title', 'Supervisors Shift Log')
@section('content')
    <div class="my-4 p-4 border bg-white">
        <div class="row align-items-center text-center text-md-start mb-5">
            <!-- Left: Koormal logo and filter -->
            <div
                class="col-xl-2 mb-3 mb-lg-0 d-flex d-lg-block flex-column flex-sm-row gap-md-5 gap-lg-0 align-items-center">
                <div class="py-3 text-center">
                    <img src="{{ asset('assets/logos/koormal-logo.png') }}" style="width: 170px;" alt="Koormal Logo"
                        class="mb-2">
                </div>
                <div class="w-100 d-flex flex-sm-row flex-md-column gap-2 gap-md-0">
                    <select name="filter" class="form-select w-75 mt-2" id="filter">
                        <option value="both">Both</option>
                        <option value="day">Day</option>
                        <option value="night">Night</option>
                    </select>
                    <select name="export" class="form-select w-75 mt-2" id="export">
                        <option value="">Export</option>
                        <option value="pdf">PDF</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>
            </div>

            <!-- Center: Title and Shift Labour -->
            <div class="col-xl-8 text-center">
                @php
                    use Carbon\Carbon;
                    $selectedDate = request()->get('date', Carbon::now()->format('d-m-Y'));
                    $logDate = Carbon::createFromFormat('d-m-Y', $selectedDate);
                    $today = now()->startOfDay();
                    $isPast = $logDate->lt($today);
                @endphp

                <!-- Center: Title and Shift Labour -->
                <h4 class="fw-bold fst-italic mb-4">
                    SUPERVISORS SHIFT LOG –
                    <input type="text" id="flatpickr-date" class="form-control d-inline-block w-75 mt-3 mt-md-0"
                        value="{{ $selectedDate }}" placeholder="Select Date"
                        style="font-weight: bold;font-size: 18px;font-style: italic;">
                </h4>
                <div class="row">
                    <!-- Supervisor Shift -->
                    <div class="col-md-5 col-xl-4 px-0 mb-3 mb-md-0">
                        <!-- Date Picker Input (you can hide it if needed) -->
                        <div class="border border-success rounded p-2 mb-3">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <p class="mb-0">
                                    <strong>
                                        <u>
                                            Supervisor for Day Shift
                                        </u>
                                    </strong>
                                </p>
                                @if ($isEditable || $role == 'admin')
                                    <button class="btn btn-sm btn-success addCompletion" style="line-height: 1;"
                                        data-shift="day">Handover
                                        Complete
                                    </button>
                                @endif
                            </div>
                            <div class="supervisor-editable" data-shift="day"
                                contenteditable="{{ $isEditable || $role == 'admin' ? 'true' : 'false' }}">
                                {{ implode(', ', $supervisors_day) }}
                            </div>
                        </div>

                        <div class="border border-success rounded p-2">

                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <p class="mb-0">
                                    <strong>
                                        <u>
                                            Supervisor for Night Shift
                                        </u>
                                    </strong>
                                </p>
                                @if ($isEditable || $role == 'admin')
                                    <button class="btn btn-sm btn-success addCompletion" style="line-height: 1;"
                                        data-shift="night">Handover Complete
                                    </button>
                                @endif
                            </div>
                            <div class="supervisor-editable" data-shift="night"
                                contenteditable="{{ $isEditable || $role == 'admin' ? 'true' : 'false' }}">
                                {{ implode(', ', $supervisors_night) }}
                            </div>
                        </div>
                    </div>
                    <!-- Labour Shift -->
                    <div class="col-md-7 col-xl-8">
                        <!-- Date Picker Input (you can hide it if needed) -->
                        <div class="border border-success rounded p-2 mb-3">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <strong><u>Labour for Day Shift</u></strong><br>
                                @if ($isEditable || $role == 'admin')
                                    <button class="btn btn-sm btn-primary loadCrew" data-shift="day"
                                        style="line-height: 1;">
                                        Load a Crew
                                    </button>
                                @endif
                            </div>
                            <div class="editable" data-shift="day"
                                contenteditable="{{ $isEditable || $role == 'admin' ? 'true' : 'false' }}">
                                {{ $day_labours?->name }}
                            </div>
                        </div>

                        <div class="border border-success rounded p-2">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <strong><u>Labour for Night Shift</u></strong><br>
                                @if ($isEditable || $role == 'admin')
                                    <button class="btn btn-sm btn-primary loadCrew" data-shift="night"
                                        style="line-height: 1;">
                                        Load a Crew
                                    </button>
                                @endif
                            </div>

                            <div class="editable" data-shift="night"
                                contenteditable="{{ $isEditable || $role == 'admin' ? 'true' : 'false' }}">
                                {{ $night_labours?->name }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right: 4EMUS logo and buttons -->
            <div
                class="col-xl-2 mb-3 mt-2 mt-lg-0 mb-md-0 d-flex flex-lg-column flex-md-row align-items-center justify-content-between gap-md-5 gap-lg-0">
                <div class="py-3 text-center">
                    <img src="{{ asset('assets/logos/4emus-logo.png') }}" style="width: 180px;" alt="4EMUS Logo"
                        class="mb-2">
                </div>
                <div class="text-center">
                    @hasrole('admin')
                        <button class="btn btn-sm btn-success w-75" data-bs-toggle="modal"
                            data-bs-target="#supervisorsShiftLogModal" style="line-height: 1;">Upload Excel
                            Sheet
                        </button>
                    @endhasrole
                    @if ($role === 'admin' || ($role === 'supervisor' && !$isPast))
                        <button id="addJobBtn" class="btn btn-sm btn-primary mt-1 w-75" style="line-height: 1;">Add a Job
                        </button>
                    @endif

                    <a href="#" class="btn btn-sm btn-secondary mt-1 w-75 supervisor-note-btn" style="line-height: 1;"
                        data-type="day_shift">
                        Supervisor's Notes – Day Shift
                    </a>

                    <a href="#" class="btn btn-sm btn-warning mt-1 w-75 supervisor-note-btn" style="line-height: 1;"
                        data-type="night_shift">
                        Supervisor's Notes – Night Shift
                    </a>
                </div>
            </div>
        </div>

        {!! $dataTable->table(['class' => 'cell-border ', 'id' => 'jobTable'], true) !!}
    </div>

    <!-- Modal -->
    <div class="modal fade" id="supervisorsShiftLogModal" tabindex="-1" aria-labelledby="supervisorsShiftLogModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary-subtle pb-3">
                    <h1 class="modal-title fs-5" id="supervisorsShiftLogModalLabel">Upload Excel Sheet</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('supervisors-shift-log.csv.import') }}" method="POST"
                    enctype="multipart/form-data" id="csvImportForm">
                    <div class="modal-body p-3 mb-3">
                        @csrf
                        <label for="csv_file" class="form-label fw-semibold">Upload File <span
                                class="text-danger">*</span></label>
                        <input type="file" name="csv_file" class="form-control" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="csvImportBtn">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Opportune Job Modal -->
    @include('components.admin.supervisors.modal.opportune-job')

    <!-- Handover Completion Modal -->
    @include('components.admin.supervisors.modal.handover-completion')

    <!-- Load Crew Modal -->
    @include('components.admin.supervisors.modal.load-crew')

    <!-- Move Work Order Modal -->
    @include('components.admin.supervisors.modal.move-work-order')

@endsection
@push('scripts')
    <!-- DataTables JS and dependencies (if not already included globally) -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    {{-- sweetalert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Flatpickr assets -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    {{--
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script> --}}

    {!! $dataTable->scripts() !!}


    <script>
        let currentStep = 1;

        // Helper: Parse dd-mm-yyyy to JS Date
        function parseDMY(dateStr) {
            const [day, month, year] = dateStr.split('-');
            return new Date(year, month - 1, day); // month is 0-indexed
        }

        // Helper: Format Date as dd-mm-yyyy
        function formatDate(date) {
            const d = String(date.getDate()).padStart(2, '0');
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const y = date.getFullYear();
            return `${d}-${m}-${y}`;
        }

        // Initialize date picker
        $('#to_date').flatpickr({
            dateFormat: 'd-m-Y',
        });

        // Open modal and initialize step 1
        $('body').on('click', '.move-work-order-number-btn', function() {
            const today = new Date();
            today.setHours(0, 0, 0, 0); // remove time
            const id = $(this).data('id');
            const shift = $(this).data('shift');
            const date = $(this).data('date'); // dd-mm-yyyy
            const wo_number = $(this).data('wo-number');
            const fromDate = parseDMY(date);

            if (fromDate < today) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Cannot move a work order to a day before today’s date - ' + date,
                });
                return;
            }

            currentStep = 1;

            $('#wo_number').val(wo_number);
            $('#from_date').val(date);
            $('#from_shift').val(shift);
            $('#shift_log_id').val(id);
            $('#workorder_number_display').text(wo_number);
            $('#moveWorkOrderModal').modal('show');

            showStep(currentStep);
        });

        // Show the current step
        function showStep(step) {
            $('.form-step').addClass('d-none');
            $('.step-' + step).removeClass('d-none');

            $('#prevStep').toggleClass('d-none', step === 1);
            $('#nextStep').toggleClass('d-none', step >= 4);
            $('#submitMove').toggleClass('d-none', step < 4);

            if (step === 4) {
                const workorder = $('#workorder_number_display').text();
                const date = $('#to_date').val();
                const shift = $('input[name="to_shift"]:checked').val();
                $('#confirmation_text').text(
                    `Are you sure you want to move Work Order ${workorder} to ${date} (${shift})?`
                );
            }
        }

        // Handle next step
        $('#nextStep').on('click', function() {
            if (currentStep === 1 && !$('#reason').val().trim()) {
                notify('error', 'Please enter a reason.');
                $('#reason').addClass('is-invalid');
                return;
            }

            if (currentStep === 2) {
                const toDateStr = $('#to_date').val();
                const fromDateStr = $('#from_date').val();
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                if (!toDateStr) {
                    notify('error', 'Please select a date.');
                    $('#to_date').addClass('is-invalid');
                    return;
                }

                const toDate = parseDMY(toDateStr);
                const fromDate = parseDMY(fromDateStr);

                // ❌ Cannot move to past
                if (toDate < today) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Date',
                        text: `Cannot move a work order to a day before today’s date - ${toDateStr}`
                    });
                    return;
                }

                // ❌ If from future, allow only move back to today
                if (fromDate > today && toDate < fromDate && toDate.getTime() !== today.getTime()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Move',
                        text: `Future work orders can only be moved back to today's date - ${formatDate(today)}`
                    });
                    return;
                }
            }

            if (currentStep === 3 && !$('input[name="to_shift"]:checked').val()) {
                notify('error', 'Please select a shift.');
                $('input[name="to_shift"]').addClass('is-invalid');
                return;
            }

            currentStep++;
            showStep(currentStep);
        });

        // Handle previous step
        $('#prevStep').on('click', function() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });

        // Submit form
        $('#moveWorkOrderForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('work-order-moves.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#moveWorkOrderModal').modal('hide');
                    location.reload(); // Optional: Reload the table/list
                    notify('success', 'Work Order moved successfully!');
                },
                error: function(xhr) {
                    notify('error', 'An error occurred while moving the work order.');
                }
            });
        });

        // let currentStep = 1;
        // $('#to_date').flatpickr({
        //     dateFormat: 'd-m-Y',
        // });

        // // Open modal and initialize step 1
        // $('body').on('click', '.move-work-order-number-btn', function() {
        //     const today = new Date();
        //     const id = $(this).data('id');
        //     const shift = $(this).data('shift');
        //     const date = $(this).data('date');
        //     const wo_number = $(this).data('wo-number');
        //     const fromDate = parseDMY(date);

        //     if (fromDate < today) {
        //         Swal.fire({
        //             icon: 'error',
        //             title: 'Error',
        //             text: 'Cannot move a work order to a day before today’s date ' + date,
        //         });
        //         return;
        //     }

        //     currentStep = 1;

        //     $('#wo_number').val(wo_number);
        //     $('#from_date').val(date);
        //     $('#from_shift').val(shift);
        //     $('#shift_log_id').val(id);
        //     $('#workorder_number_display').text(wo_number);
        //     $('#moveWorkOrderModal').modal('show');

        //     showStep(currentStep);
        // });

        // // Show the current step
        // function showStep(step) {
        //     $('.form-step').addClass('d-none');
        //     $('.step-' + step).removeClass('d-none');

        //     $('#prevStep').toggleClass('d-none', step === 1);
        //     $('#nextStep').toggleClass('d-none', step >= 4);
        //     $('#submitMove').toggleClass('d-none', step < 4);

        //     if (step === 4) {
        //         const workorder = $('#workorder_number_display').text();
        //         const date = $('#to_date').val();
        //         const shift = $('input[name="to_shift"]:checked').val();
        //         $('#confirmation_text').text(
        //             `Are you sure you want to move Work Order ${workorder} to ${date} (${shift})?`);
        //     }
        // }

        // // Handle next step
        // $('#nextStep').on('click', function() {
        //     if (currentStep === 1 && !$('#reason').val().trim()) {
        //         notify('error', 'Please enter a reason.');
        //         $('#reason').addClass('is-invalid');
        //         return;
        //     }
        //     if (currentStep === 2 && !$('#to_date').val()) {
        //         notify('error', 'Please select a date.');
        //         $('#to_date').addClass('is-invalid');
        //         return;
        //     }
        //     if (currentStep === 3 && !$('input[name="to_shift"]:checked').val()) {
        //         notify('error', 'Please select a shift.');
        //         $('input[name="to_shift"]').addClass('is-invalid');
        //         return;
        //     }
        //     currentStep++;
        //     showStep(currentStep);
        // });

        // // Handle previous step
        // $('#prevStep').on('click', function() {
        //     if (currentStep > 1) {
        //         currentStep--;
        //         showStep(currentStep);
        //     }
        // });

        // // Submit form
        // $('#moveWorkOrderForm').on('submit', function(e) {
        //     e.preventDefault();

        //     $.ajax({
        //         url: "{{ route('work-order-moves.store') }}",
        //         type: 'POST',
        //         data: $(this).serialize(),
        //         success: function(response) {
        //             $('#moveWorkOrderModal').modal('hide');
        //             location.reload(); // Optional: Reload the table/list
        //             notify('success', 'Work Order moved successfully!');
        //         },
        //         error: function(xhr) {
        //             notify('error', 'An error occurred while moving the work order.');
        //         }
        //     });
        // });

        function setupShiftToggle(selector = '#shiftSelector') {
            const $container = $(selector);

            // On page load: set the initial states based on checked input
            $container.find('input[name="to_shift"]').each(function() {
                let label = $(this).closest('label');
                if ($(this).is(':checked')) {
                    activateLabel(label);
                } else {
                    deactivateLabel(label);
                }
            });

            // On change: toggle active classes and button styles
            $container.on('change', 'input[name="to_shift"]', function() {
                $container.find('input[name="to_shift"]').each(function() {
                    let label = $(this).closest('label');
                    if ($(this).is(':checked')) {
                        activateLabel(label);
                    } else {
                        deactivateLabel(label);
                    }
                });
            });

            function activateLabel($label) {
                if ($label.find('input').val() === 'Day') {
                    $label.removeClass('btn-outline-primary').addClass('btn-primary');
                } else {
                    $label.removeClass('btn-outline-secondary').addClass('btn-secondary');
                }
                $label.addClass('active');
            }

            function deactivateLabel($label) {
                if ($label.find('input').val() === 'Day') {
                    $label.removeClass('btn-primary').addClass('btn-outline-primary');
                } else {
                    $label.removeClass('btn-secondary').addClass('btn-outline-secondary');
                }
                $label.removeClass('active');
            }
        }

        // Initialize after DOM is ready or modal shown
        $(function() {
            setupShiftToggle();
        });

        $('#moveWorkOrderModal').on('hidden.bs.modal', function() {
            // Reset the form fields
            $('#moveWorkOrderForm')[0].reset();

            // Reset step
            currentStep = 1;
            showStep(currentStep);

            // Clear the displayed workorder number
            $('#workorder_number_display').text('');

            // Reset shift toggle buttons styles
            setupShiftToggle();

            $('#reason').removeClass('is-invalid');
            $('#to_date').removeClass('is-invalid');
            $('input[name="to_shift"]').removeClass('is-invalid');
        });


        function js_nl2br(str) {
            if (typeof str !== "string") return str;
            return str.replace(/\n/g, '<br>');
        }

        $(document).ready(function() {

            $('#job_id').on('change', function() {
                let jobName = $(this).find(':selected').data('tooltip');
                let formattedJobName = js_nl2br(jobName);
                $('#jobDetails').html('<span class="fw-bold text-danger">Work Description: </span>' +
                    formattedJobName);
            });

        });

        $('#csvImportForm').submit(function(e) {
            e.preventDefault();

            let log_date = $('#flatpickr-date').val();
            let formData = new FormData(this);
            formData.append('log_date', log_date);
            let url = $(this).attr('action');
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    $('#csvImportBtn').attr('disabled', true);
                    $('#csvImportBtn').html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...'
                    );
                },
                success: function(res) {
                    if (res.status == 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        $('#supervisorsShiftLogModal').modal('hide');
                        $('#jobTable').DataTable().ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                },
                error: function(xhr, status, error) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        notify('error', value);
                    });
                },
                complete: function() {
                    $('#csvImportBtn').attr('disabled', false);
                    $('#csvImportBtn').html('Import');
                }
            });
        });

        $('#filter').on('change', function() {
            reloadTableWithFilters();
        });

        document.addEventListener("DOMContentLoaded", function() {
            flatpickr("#flatpickr-date", {
                dateFormat: "d-m-Y",
                defaultDate: "{{ $selectedDate ?? now()->format('d-m-Y') }}",
                onChange: function(selectedDates, dateStr, instance) {
                    window.location.href =
                        "{{ route('supervisors-shift-log.index', ['role' => $role]) }}?date=" +
                        dateStr;
                }
            });
        });

        $(document).on('draw.dt', function() {
            $('#jobTable tbody').sortable({
                items: "tr",
                handle: ".drag-handle",
                helper: fixHelper,
                cancel: '[contenteditable]',
                start: function(e, ui) {
                    ui.placeholder.height(ui.item.height());
                },
                update: function() {
                    updateLineNumbers();
                    let order = [];
                    $('#jobTable tbody tr').each(function(index) {
                        order.push({
                            id: $(this).data('id'),
                            position: index + 1
                        });
                    });
                    $.ajax({
                        url: '{{ route('supervisors-shift-log.reorder') }}',
                        method: 'POST',
                        data: {
                            order: order,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log('Order updated successfully');
                        },
                        error: function(xhr) {
                            console.error('Failed to update order');
                        }
                    });
                }
            });
        });

        $('#export').on('change', function() {
            let selectedValue = $(this).val();
            if (selectedValue) {
                let date = $('#flatpickr-date').val();
                let filter = $('#filter').val();
                let url =
                    `{{ route('supervisors-shift-log.export') }}?export=${selectedValue}&${filter ? 'shift=' + encodeURIComponent(filter) : ''}&date=${date}`;
                window.open(url, '_blank');
                $('#export').val('');
            }

        })

        $('#jobTable').on('blur', '[contenteditable="true"]', function() {
            let td = $(this);
            let field = td.data('field');
            let value = td.text().trim();
            let rowId = td.closest('tr').attr('id'); // Example: row_12
            let id = rowId.replace('row_', '');
            editField(td, field, value, id);

        });

        $('#addJobBtn').on('click', function() {
            Swal.fire({
                title: 'Add a Job',
                text: 'Choose how you want to add a job:',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Add Blank New Job',
                cancelButtonText: 'Add Opportune Job From List',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect or show form for blank job
                    addJob();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // Redirect or show modal for opportune job
                    $('#addOpportuneJobModal').modal('show');
                }
            });
        })

        $('#addOpportuneJobForm').on('submit', function(e) {
            e.preventDefault();
            let log_date = $('#flatpickr-date').val();
            let formData = new FormData(this);
            formData.append('log_date', log_date);
            let url = $(this).attr('action');
            let method = $(this).attr('method');
            $.ajax({
                url: url,
                method: method,
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.status == 'success') {
                        $('#addOpportuneJobModal').modal('hide');
                        $('#jobTable').DataTable().ajax.reload();
                        notify('success', response.message);
                        $('#addOpportuneJobForm')[0].reset();
                    }
                },
                error: function(xhr, status, error) {
                    if (xhr.status == 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            notify('error', value);
                            let input = $('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.next('.invalid-feedback').text(value);
                        });
                    }
                }
            });
        });


        $(document).ready(function() {
            $('.editable').on('blur', function() {
                let content = $(this).text().trim();
                let shift = $(this).data('shift');
                let date = $('#flatpickr-date').val();

                $.ajax({
                    url: "{{ route('labour-shift.update') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        shift: shift,
                        labour: content,
                        date: date
                    },
                    success: function(res) {
                        if (res.status == 'success') {
                            notify('success', res.message);
                        }
                    },
                    error: function() {}
                });
            });
        });

        $(document).ready(function() {
            $('.supervisor-editable').on('blur', function() {
                let content = $(this).text().trim();
                let shift = $(this).data('shift');
                let date = $('#flatpickr-date').val();

                $.ajax({
                    url: "{{ route('supervisor-shift.update') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        shift: shift,
                        supervisor: content,
                        date: date
                    },
                    success: function(res) {
                        if (res.status == 'success') {
                            notify('success', res.message);
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    }
                });
            });
        });

        $('#jobTable').on('change', '.shift_name', function() {
            let select = $(this);
            let field = select.data('field'); // should be "shift_name"
            let value = select.val();
            let rowId = select.closest('tr').attr('id'); // Example: row_12
            let id = rowId.replace('row_', '');
            editField(select, field, value, id, true);
        });

        $('#jobTable').on('change', '.complete_progress', function() {
            let select = $(this);
            let field = select.data('field');
            let value = select.val();
            let rowId = select.closest('tr').attr('id');
            let id = rowId.replace('row_', '');
            editField(select, field, value, id, true);
        });


        $(document).on('click', '.deleteRowBtn', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "This file will be permanently deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteNewRow(id);
                }
            });
            updateLineNumbers();
        });

        function deleteNewRow(id) {
            const deleteUrl = `{{ route('supervisors-shift-log.destroy', ['id' => '__ID__']) }}`.replace('__ID__', id);
            $.ajax({
                url: deleteUrl,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}',
                },
                success: function() {
                    reloadTableWithFilters();
                    notify('success', 'Work Order deleted successfully');
                },
                error: function() {
                    reloadTableWithFilters();
                }
            });
        }

        function addJob() {
            $.ajax({
                url: '{{ route('supervisors-shift-log.store') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    date: $('#flatpickr-date').val(),
                },
                success: function(response) {
                    notify('success', response.message);
                    reloadTableWithFilters();
                },
                error: function() {

                }
            });
        }

        function reloadTableWithFilters() {
            let shift = $('#filter').val();
            let date = $('#flatpickr-date').val();

            let url = '{{ route('supervisors-shift-log.index', ['role' => $role]) }}';
            let params = [];

            if (shift) params.push('shift=' + encodeURIComponent(shift));
            if (date) params.push('date=' + encodeURIComponent(date));

            if (params.length > 0) {
                url += '?' + params.join('&');
            }

            $('#jobTable').DataTable().ajax.url(url).load();
        }


        const fixHelper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        };

        function editField(td, field, value, id, table_reload = false) {
            $.ajax({
                url: `{{ route('supervisors-shift-log.update', ':id') }}`.replace(':id', id),
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    field: field,
                    value: value
                },
                success: function(res) {
                    td.css('background-color', '#d4edda');
                    setTimeout(() => td.css('background-color', ''), 1000);
                    if (table_reload) {
                        $('#jobTable').DataTable().ajax.reload(null, false);
                    }
                },
                error: function() {
                    td.css('background-color', '#f8d7da');
                }
            });
        }

        function updateLineNumbers() {
            $('#jobTable tbody tr').each(function(index) {
                $(this).find('.line-no-text').text(index + 1);
            });
        }

        implementAutoAjaxLoading();

        $('.supervisor-note-btn').on('click', function(e) {
            e.preventDefault();

            const logDate = $('#flatpickr-date').val();
            const noteType = $(this).data('type');

            if (!logDate) {
                alert('Please select a date.');
                return;
            }

            const url = "{{ route('supervisor-notes.create') }}" +
                '?note_type=' + encodeURIComponent(noteType) +
                '&log_date=' + encodeURIComponent(logDate);

            window.location.href = url;
        });

        $(document).on('click', '#delete-selected', function() {
            Swal.fire({
                title: 'Are you sure you want to delete all work orders from this shift log?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let log_date = $('#flatpickr-date').val();

                    $.ajax({
                        url: "{{ route('bulk-delete-supervisor-shift') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            log_date: log_date
                        },
                        success: function(res) {
                            if (res.status == 'success') {
                                notify('success', res.message);
                                $('#jobTable').DataTable().ajax.reload(null, false);
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr)
                            if (xhr.status == 404) {
                                notify('error', xhr.responseJSON.message ||
                                    'No data found for the given date.')
                            } else if (xhr.status == 422) {
                                let errors = xhr.responseJSON.errors;
                                let firstError = Object.values(errors)[0][0];
                                notify('error', firstError || 'Validation failed.')
                            } else {
                                notify('error', xhr.responseJSON.message ||
                                    'Something went wrong. Please try again.')
                            }
                        }
                    });
                }
            });
        });

        $('.addCompletion').on('click', function() {
            let shift = $(this).data('shift');
            let date = $('#flatpickr-date').val();
            let url = "{{ route('handover-completions.create') }}?shift=" + shift + "&date=" + date;
            $.ajax({
                url: url,
                method: 'GET',
                success: function(res) {
                    $('#handoverCompletionModal .modal-content').html(res);
                    $('#handoverCompletionModal').modal('show');
                }
            })
        })

        $('body').on('click', '.loadCrew', function() {
            let shift = $(this).data('shift');
            let date = $('#flatpickr-date').val();
            let url = "{{ route('load-crew.index') }}?shift=" + shift + "&date=" + date;
            $.ajax({
                url: url,
                method: 'GET',
                success: function(res) {
                    $('#loadCrewModal .modal-content').html(res);
                    $('#loadCrewModal').modal('show');
                }
            })
        })

        $('body').on('change', '#crew_id', function() {
            let crew_id = $(this).val();

            $.ajax({
                url: "{{ route('get-labour-by-crew', ':crew_id') }}".replace(':crew_id', crew_id),
                method: 'GET',
                beforeSend: function() {
                    $('.labour-container').html(
                        '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                    );
                },
                success: function(res) {
                    $('.labour-container').html('');
                    let html = '';
                    html += '<label for="labour" class="form-label">Labour</label>';
                    html +=
                        '<select name="labours[]" id="labour" class="form-select" multiple required>';
                    html += '<option value="">Select Labour</option>';
                    $.each(res.data, function(index, value) {
                        html += '<option value="' + value.id + '">' + value.name + '</option>';
                    });
                    html += '</select>';
                    $('.labour-container').html(html);

                    // Initialize Choices.js
                    if (typeof Choices !== 'undefined') {
                        new Choices('#labour', {
                            removeItemButton: true,
                            placeholderValue: 'Select Labour',
                            searchPlaceholderValue: 'Search Labour',
                            noResultsText: 'No Labour Found'
                        });
                    }
                },
                error: function(xhr) {
                    $('.labour-container').html('');
                    notify('error', xhr.responseJSON?.message ||
                        'Something went wrong. Please try again.');
                }
            });
        });
    </script>
@endpush
@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
    <style>
        /* Table Structure */
        #jobTable {
            /* width: 100% !important; */
            margin: 0 auto;
            border-collapse: separate;
            border-spacing: 0;
        }

        /* Column Widths */
        .line-number {
            width: 40px !important;
        }

        .col-shift {
            width: 70px !important;
            min-width: 70px !important;
        }

        .col-wo {
            width: 100px !important;
        }

        .col-asset {
            width: 151px !important;
        }

        .col-desc {
            width: 300px !important;
            min-width: 300px;
        }

        .col-labour {
            width: 200px !important;
            min-width: 200px;
        }

        .col-note {
            width: 150px !important;
            min-width: 150px;
        }

        .col-req {
            width: 80px !important;
        }

        /* Table Cell Styling */
        #jobTable tbody tr td {
            vertical-align: middle !important;
            padding: 8px 10px !important;
            border: 1px solid #dee2e6;
        }

        /* Header Styling */
        thead {
            background-color: #c0c2c3;
        }

        /* Special Row Styles */
        .row-complete {
            background-color: #ffef3bc2 !important;
        }

        .row-night {
            background-color: #939393a8 !important;
        }

        /* Drag Handle */
        .drag-handle {
            cursor: move;
        }

        /* Labour Container */
        .labour-container {
            position: relative;
            z-index: 1;
        }

        /* Choices.js Overrides */
        .choices__list--dropdown .choices__list {
            margin: 0;
        }

        .choices__list.choices__list--dropdown.is-active {
            padding: 2px;
        }

        .choices__list--multiple .choices__item {
            padding: 1px 6px !important;
        }

        .choices__input {
            margin-bottom: 0 !important;
        }

        .choices__inner {
            padding: 3px 10px;
        }

        .choices__list--dropdown .choices__item {
            font-size: 12px !important;
        }

        /* DataTables Overrides */
        .dataTables_scrollBody {
            overflow-x: auto !important;
            overflow-y: hidden;
        }

        .dataTables_wrapper .dropdown-menu {
            z-index: 1001 !important;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {

            .col-desc,
            .col-labour,
            .col-note {
                width: 150px !important;
                min-width: 150px;
            }

            #jobTable tbody tr td {
                padding: 6px 8px !important;
                font-size: 12px;
            }

            .col-req {
                width: 80px !important;
            }
        }

        /* Flatpickr Override */
        .flatpickr-months {
            background-color: #ffffff;
        }

        .btn-group-toggle .btn input[type="radio"]:checked+span {
            font-weight: bold;
        }

        .btn-group-toggle .btn.active {
            background-color: #007bff;
            color: #fff;
        }
    </style>
@endpush

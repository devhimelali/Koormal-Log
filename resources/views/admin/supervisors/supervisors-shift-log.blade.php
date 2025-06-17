@extends('layouts.app')
@section('title', 'Supervisors Shift Log')
@section('content')
    <div class="my-4 p-4 border bg-white">
        <div class="row align-items-center text-center text-md-start my-5">
            <!-- Left: Koormal logo and filter -->
            <div class="col-md-2 mb-3 mb-md-0 d-flex flex-column align-items-center">
                <div class="py-3 text-center">
                    <img src="{{ asset('assets/logos/koormal-logo.png') }}" style="width: 180px;" alt="Koormal Logo"
                         class="mb-2">
                </div>
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

            <!-- Center: Title and Shift Labour -->
            <div class="col-md-8 text-center">
                @php
                    $selectedDate = request()->get('date', \Carbon\Carbon::now()->format('d-m-Y'));
                @endphp

                        <!-- Center: Title and Shift Labour -->
                <h4 class="fw-bold fst-italic mb-4">
                    SUPERVISORS SHIFT LOG –
                    <input type="text" id="flatpickr-date" class="form-control d-inline-block w-auto"
                           value="{{ $selectedDate }}"
                           placeholder="Select Date" style="font-weight: bold;font-size: 18px;font-style: italic;">
                </h4>
                <div class="row">
                    <!-- Supervisor Shift -->
                    <div class="col-md-6">
                        <!-- Date Picker Input (you can hide it if needed) -->
                        <div class="border border-success rounded p-2 mb-3">
                            <strong><u>Supervisor for Dayshift</u></strong><br>
                            <div class="supervisor-editable" data-shift="day" contenteditable="true">
                                {{ implode(', ', $supervisors_day) }}
                            </div>
                        </div>

                        <div class="border border-success rounded p-2">
                            <strong><u>Supervisor for Nightshift</u></strong><br>
                            <div class="supervisor-editable" data-shift="night" contenteditable="true">
                                {{ implode(', ', $supervisors_night) }}
                            </div>
                        </div>
                    </div>
                    <!-- Labour Shift -->
                    <div class="col-md-6">
                        <!-- Date Picker Input (you can hide it if needed) -->
                        <div class="border border-success rounded p-2 mb-3">
                            <strong><u>Labour for Dayshift</u></strong><br>
                            <div class="editable" data-shift="day" contenteditable="true">
                                {{ implode(', ', $labours_day) }}
                            </div>
                        </div>

                        <div class="border border-success rounded p-2">
                            <strong><u>Labour for Nightshift</u></strong><br>
                            <div class="editable" data-shift="night" contenteditable="true">
                                {{ implode(', ', $labours_night) }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right: 4EMUS logo and buttons -->
            <div class="col-md-2 mb-3 mb-md-0 d-flex flex-column align-items-center">
                <div class="py-3 text-center">
                    <img src="{{ asset('assets/logos/4emus-logo.png') }}" style="width: 180px;" alt="4EMUS Logo"
                         class="mb-2">
                </div>
                <button class="btn btn-sm btn-success w-75" data-bs-toggle="modal"
                        data-bs-target="#supervisorsShiftLogModal">Upload Excel
                    Sheet
                </button>
                <button id="addJobBtn" class="btn btn-sm btn-primary mt-1 w-75">Add a Job</button>

                <a href="#" class="btn btn-sm btn-secondary mt-1 w-75 supervisor-note-btn" data-type="day_shift">
                    Supervisor's Notes – Dayshift
                </a>

                <a href="#" class="btn btn-sm btn-warning mt-1 w-75 supervisor-note-btn" data-type="night_shift">
                    Supervisor's Notes – Nightshift
                </a>
            </div>
        </div>

        {!! $dataTable->table(['class' => 'cell-border w-100', 'id' => 'jobTable'], true) !!}
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

    <div class="modal fade" id="addOpportuneJobModal" tabindex="-1" aria-labelledby="addOpportuneJobModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary-subtle pb-3">
                    <h1 class="modal-title fs-5" id="addOpportuneJobModalLabel">Add a Job from Opportune Job List</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('store-shift-log-from-opportune-jobs') }}" method="POST"
                      id="addOpportuneJobForm">
                    <div class="modal-body p-3 mb-3">
                        @csrf
                        <div class="mb-2">
                            <label for="shift_name" class="form-label">Shift <span class="text-danger">*</span></label>
                            <select class="form-select" name="shift_name" id="shift_name">
                                <option value="">Select a Shift</option>
                                <option value="day">Day shift</option>
                                <option value="night">Night Shift</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="job_id" class="form-label">Select a Job</label>
                            <select class="form-select" name="job_id" id="job_id">
                                <option value="">Select a Job</option>
                                @foreach($opportuneJobs as $job)
                                    <option value="{{ $job->id }}">{{ $job->wo_number }} - {{ $job->asset_no }}
                                        - {{ $job->department }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-subtle-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary" id="addOpportuneJobSubmitBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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

    {!! $dataTable->scripts() !!}


    <script>

        $('#csvImportForm').submit(function (e) {
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
                beforeSend: function () {
                    $('#csvImportBtn').attr('disabled', true);
                    $('#csvImportBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
                },
                success: function (res) {
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
                error: function (xhr, status, error) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        notify('error', value);
                    });
                },
                complete: function () {
                    $('#csvImportBtn').attr('disabled', false);
                    $('#csvImportBtn').html('Import');
                }
            });
        });

        $('#filter').on('change', function () {
            reloadTableWithFilters();
        });

        document.addEventListener("DOMContentLoaded", function () {
            flatpickr("#flatpickr-date", {
                dateFormat: "d-m-Y",
                defaultDate: "{{ $selectedDate ?? now()->format('d-m-Y') }}",
                onChange: function (selectedDates, dateStr, instance) {
                    window.location.href = "{{ route('supervisors-shift-log.index') }}?date=" + dateStr;
                }
            });
        });

        $(document).on('draw.dt', function () {
            $('#jobTable tbody').sortable({
                items: "tr",
                handle: ".drag-handle",
                helper: fixHelper,
                cancel: '[contenteditable]',
                start: function (e, ui) {
                    ui.placeholder.height(ui.item.height());
                },
                update: function () {
                    updateLineNumbers();
                    let order = [];
                    $('#jobTable tbody tr').each(function (index) {
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
                        success: function (response) {
                            console.log('Order updated successfully');
                        },
                        error: function (xhr) {
                            console.error('Failed to update order');
                        }
                    });
                }
            });
        });

        $('#export').on('change', function () {
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

        $('#jobTable').on('blur', '[contenteditable="true"]', function () {
            let td = $(this);
            let field = td.data('field');
            let value = td.text().trim();
            let rowId = td.closest('tr').attr('id'); // Example: row_12
            let id = rowId.replace('row_', '');
            editField(td, field, value, id);

        });

        $('#addJobBtn').on('click', function () {
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

        $('#addOpportuneJobForm').on('submit', function (e) {
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
                success: function (response) {
                    if (response.status == 'success') {
                        $('#addOpportuneJobModal').modal('hide');
                        $('#jobTable').DataTable().ajax.reload();
                        notify('success', response.message);
                        $('#addOpportuneJobForm')[0].reset();
                    }
                },
                error: function (xhr, status, error) {
                    if (xhr.status == 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            notify('error', value);
                            let input = $('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.next('.invalid-feedback').text(value);
                        });
                    }
                }
            });
        });


        $(document).ready(function () {
            $('.editable').on('blur', function () {
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
                    success: function (res) {
                        if (res.status == 'success') {
                            notify('success', res.message);
                        }
                    },
                    error: function () {
                    }
                });
            });
        });

        $(document).ready(function () {
            $('.supervisor-editable').on('blur', function () {
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
                    success: function (res) {
                        if (res.status == 'success') {
                            notify('success', res.message);
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr);
                    }
                });
            });
        });

        $('#jobTable').on('change', '.shift_name', function () {
            let select = $(this);
            let field = select.data('field'); // should be "shift_name"
            let value = select.val();
            let rowId = select.closest('tr').attr('id'); // Example: row_12
            let id = rowId.replace('row_', '');
            editField(select, field, value, id, true);
        });

        $('#jobTable').on('change', '.complete_progress', function () {
            let select = $(this);
            let field = select.data('field');
            let value = select.val();
            let rowId = select.closest('tr').attr('id');
            let id = rowId.replace('row_', '');
            editField(select, field, value, id, true);
        });


        $(document).on('click', '.deleteRowBtn', function () {
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
                success: function () {
                    reloadTableWithFilters();
                    notify('success', 'Work Order deleted successfully');
                },
                error: function () {
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
                success: function (response) {
                    notify('success', response.message);
                    reloadTableWithFilters();
                },
                error: function () {

                }
            });
        }

        function reloadTableWithFilters() {
            let shift = $('#filter').val();
            let date = $('#flatpickr-date').val();

            let url = '{{ route('supervisors-shift-log.index') }}';
            let params = [];

            if (shift) params.push('shift=' + encodeURIComponent(shift));
            if (date) params.push('date=' + encodeURIComponent(date));

            if (params.length > 0) {
                url += '?' + params.join('&');
            }

            $('#jobTable').DataTable().ajax.url(url).load();
        }


        const fixHelper = function (e, ui) {
            ui.children().each(function () {
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
                success: function (res) {
                    td.css('background-color', '#d4edda');
                    setTimeout(() => td.css('background-color', ''), 1000);
                    if (table_reload) {
                        $('#jobTable').DataTable().ajax.reload(null, false);
                    }
                },
                error: function () {
                    td.css('background-color', '#f8d7da');
                }
            });
        }

        function updateLineNumbers() {
            $('#jobTable tbody tr').each(function (index) {
                $(this).find('.line-no-text').text(index + 1);
            });
        }

        implementAutoAjaxLoading();

        $('.supervisor-note-btn').on('click', function (e) {
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

        $(document).on('click', '#delete-selected', function () {
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
                        success: function (res) {
                            if (res.status == 'success') {
                                notify('success', res.message);
                                $('#jobTable').DataTable().ajax.reload(null, false);
                            }
                        },
                        error: function (xhr) {
                            console.log(xhr)
                            if (xhr.status == 404) {
                                notify('error', xhr.responseJSON.message || 'No data found for the given date.')
                            } else if (xhr.status == 422) {
                                let errors = xhr.responseJSON.errors;
                                let firstError = Object.values(errors)[0][0];
                                notify('error', firstError || 'Validation failed.')
                            } else {
                                notify('error', xhr.responseJSON.message || 'Something went wrong. Please try again.')
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        .drag-handle {
            cursor: move;
        }

        .line-no-text {
            display: inline-block;
            width: 20px;
            text-align: center;
        }

        #jobTable tbody tr td {
            vertical-align: middle;
        }

        .row-complete {
            background-color: #ffef3bc2 !important;
        }

        .row-night {
            background-color: #939393a8 !important;
        }

        .line-number {
            width: 40px;
            text-align: center;
        }

        .flatpickr-months {
            background-color: #ffffff;
        }

        thead {
            background-color: #e0e0e0;
        }

        table th,
        table td {
            padding: 2px 3px;
            /* very tight */
            text-align: left;
            vertical-align: top;
            font-size: 10px;
            /* very compact */
            line-height: 1.4;
        }

        table.dataTable tbody th,
        table.dataTable tbody td {
            padding: 6px !important;
        }

        .form-select {
            padding: .525rem 24px .525rem .9rem !important;
        }

        .dataTables_scrollHeadInner {
            width: 100% !important;
        }
    </style>
@endpush

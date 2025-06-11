@extends('layouts.app')
@section('content')
    <div class="my-4 p-4 border bg-white">
        <div class="row align-items-center text-center text-md-start my-5">
            <!-- Left: Koormal logo and filter -->
            <div class="col-md-3 mb-3 mb-md-0 d-flex flex-column align-items-center">
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
            <div class="col-md-6 text-center">
                @php
                    $selectedDate = request()->get('date', \Carbon\Carbon::now()->format('d-m-Y'));
                @endphp

                <!-- Center: Title and Shift Labour -->
                <h4 class="fw-bold fst-italic mb-4">
                    SUPERVISORS SHIFT LOG –
                    <input type="text" id="flatpickr-date" class="form-control d-inline-block w-auto"
                        value="{{ $selectedDate }}"
                        placeholder="Select Date"style="font-weight: bold;font-size: 18px;font-style: italic;">
                </h4>

                <!-- Date Picker Input (you can hide it if needed) -->


                {{-- <div class="border border-success rounded p-2 mb-3">
                    <strong><u>Labour for Dayshift</u></strong><br>
                    Alex Herbertson, Bill Smith, Steven Jones, Frank Reid, Mark Thomas
                </div>
                <div class="border border-success rounded p-2">
                    <strong><u>Labour for Nightshift</u></strong><br>
                    John Winters, Albert Cummins, Ralph Grieves, Mark Riley
                </div> --}}
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
                {{-- <div class="row mt-2 g-3">
                    <div class="col-md-6 text-start">
                        <label for="day_shit_supervisor" class="form-label"> Day Shift Supervisor</label>
                        <input type="text" class="form-control" placeholder="Day Shift Supervisor">
                    </div>
                    <div class="col-md-6 text-start">
                        <label for="night_shit_supervisor" class="form-label"> Night Shift Supervisor</label>
                        <input type="text" class="form-control" placeholder="Night Shift Supervisor">
                    </div>
                </div> --}}


            </div>

            <!-- Right: 4EMUS logo and buttons -->
            <div class="col-md-3 mb-3 mb-md-0 d-flex flex-column align-items-center">
                <div class="py-3 text-center">
                    <img src="{{ asset('assets/logos/4emus-logo.png') }}" style="width: 180px;" alt="4EMUS Logo"
                        class="mb-2">
                </div>
                <button class="btn btn-warning mt-3 w-75" data-bs-toggle="modal"
                    data-bs-target="#supervisorsShiftLogModal">Upload Excel
                    Sheet</button>
                <button id="addJobBtn" onclick="addJob()" class="btn btn-warning mt-2 w-75">Add a Job</button>
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
                <form action="{{ route('supervisors-shift-log.csv.import') }}" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-3 mb-3">
                        @csrf
                        <label for="csv_file" class="form-label fw-semibold">Upload File <span
                                class="text-danger">*</span></label>
                        <input type="file" name="csv_file" class="form-control" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import</button>
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
        $('#filter').on('change', function() {
            reloadTableWithFilters();
        });

        document.addEventListener("DOMContentLoaded", function() {
            flatpickr("#flatpickr-date", {
                dateFormat: "d-m-Y",
                defaultDate: "{{ $selectedDate ?? now()->format('d-m-Y') }}",
                onChange: function(selectedDates, dateStr, instance) {
                    window.location.href = "{{ route('supervisors-shift-log.index') }}?date=" + dateStr;
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
                    notify('success', 'Shift log deleted successfully');
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

            let url = '{{ route('supervisors-shift-log.index') }}';
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
    </style>
@endpush

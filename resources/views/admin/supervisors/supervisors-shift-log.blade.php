@extends('layouts.app')
@section('title', 'Supervisor Shift Log Information')
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
                    <option {{ $shift == 'both' ? 'selected' : '' }} value="both">Both</option>
                    <option {{ $shift == 'day' ? 'selected' : '' }} value="day">Day</option>
                    <option {{ $shift == 'night' ? 'selected' : '' }} value="night">Night</option>
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
                <button id="addJobBtn" class="btn btn-warning mt-2 w-75">Add a Job</button>
            </div>
        </div>

        <table class="table table-bordered" id="jobTable">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Shift</th>
                    <th>WO Number</th>
                    <th>Asset No</th>
                    <th>Work Description</th>
                    <th>Labour Assigned</th>
                    <th>% Complete</th>
                    <th style="width: 101px">Move</th>
                    <th style="width: 150px">Action</th>
                </tr>
            </thead>
            <tbody id="sortableRows">
                @foreach ($jobs as $index => $job)
                    @php
                        $isEditable = $job->is_excel_upload === 0 ? 'contenteditable=true' : '';
                        $shift = match ($job->shift_name) {
                            'night' => 'background-color: #939393a8;',
                            default => '',
                        };
                        if ($job->mark_as_complete == 1) {
                            $shift = 'background-color: #ffef3bc2;';
                        }

                    @endphp

                    <tr style="{{ $shift }}" data-id="{{ $job->id }}">
                        <td class="line-no text-center">
                            <span class="line-no-text"> {{ $index + 1 }}</span>
                        </td>
                        <td style="width: 95px">
                            <select data-field="shift_name" class="form-control"
                                style="text-transform: capitalize; width: 68px; {{ $shift }}">
                                <option value="">Select Shift</option>
                                <option value="day" {{ $job->shift_name == 'day' ? 'selected' : '' }}>Day
                                </option>
                                <option value="night" {{ $job->shift_name == 'night' ? 'selected' : '' }}>Night</option>
                            </select>
                        </td>

                        <td {!! $isEditable !!} data-field="wo_number">{{ $job->wo_number }}</td>
                        <td {!! $isEditable !!} data-field="asset_no">{{ $job->asset_no }}</td>
                        <td {!! $isEditable !!} data-field="work_description">{{ $job->work_description }}</td>
                        <td contenteditable=true data-field="labour">{{ $job->labour }}</td>
                        <td>
                            <input data-field="progress" type="number" class="form-control text-center complete_progress"
                                min="0" max="100" value="{{ $job->progress }}" style="width: 88px;"
                                oninput="this.value = Math.max(0, Math.min(100, this.value))">
                        </td>


                        <td class="line-no text-center">
                            <span class="drag-handle me-2 btn btn-secondary btn-sm" style="cursor: move;">
                                <i class="bi bi-arrows-move me-2"></i> Move
                            </span>
                        </td>
                        <td class="handle text-center">
                            <div class="btn-group">
                                <a href="{{ route('supervisors-shift-log.show', $job->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-info-circle"></i> More
                                </a>
                                <button class="btn btn-sm btn-danger deleteRowBtn">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
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
                    enctype="multipart/form-data">
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

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Flatpickr assets -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            flatpickr("#flatpickr-date", {
                dateFormat: "d-m-Y",
                defaultDate: "{{ $selectedDate }}",
                onChange: function(selectedDates, dateStr, instance) {
                    let filter = dateStr;
                    let params = new URLSearchParams(window.location.search);
                    params.set('date', filter);
                    window.location.href =
                        `{{ route('supervisors-shift-log.index') }}?${params.toString()}`;
                }
            });
        });
        $(function() {
            $("#sortableRows").sortable({
                items: "tr",
                handle: ".drag-handle",
                helper: fixHelper,
                cancel: '[contenteditable]', // ✅ Prevents blocking inputs
                start: function(e, ui) {
                    ui.placeholder.height(ui.item.height());
                },
                update: function() {
                    updateLineNumbers();
                    saveRowOrder();
                }
            });

            $("#sortableRows .drag-handle").disableSelection();

            $('#addJobBtn').on('click', function() {
                const newRow = `
                        <tr data-id="new">
                            <td class="line-no text-center">
                                <span class="line-no-text"></span>
                            </td>
                             <td style="width: 95px">
                                <select data-field="shift_name" class="form-control"
                                    style="text-transform: capitalize; width: 68px;">
                                    <option value="">Select Shift</option>
                                    <option value="day">Day
                                    </option>
                                    <option value="night">Night</option>
                                </select>
                            </td>
                            <td contenteditable="true" data-field="wo_number">WO####</td>
                            <td contenteditable="true" data-field="asset_no">Asset-XXX</td>
                            <td contenteditable="true" data-field="work_description">Work Description</td>
                            <td contenteditable="true" data-field="labour">Labour Name</td>
                            <td>
                                <input data-field="progress" type="number" class="form-control text-center complete_progress"
                                    min="0" max="100" value="0" style="width: 88px;"
                                    oninput="this.value = Math.max(0, Math.min(100, this.value))">
                            </td>
                            <td class="line-no text-center">
                                <span class="drag-handle me-2 btn btn-secondary btn-sm" style="cursor: move;">
                                    <i class="bi bi-arrows-move me-2"></i> Move
                                </span>
                            </td>
                            <td class="handle text-center">
                                <div class="btn-group">
                                    <a href="" class="btn btn-sm btn-info moreInfo">
                                        <i class="bi bi-info-circle"></i> More
                                    </a>
                                    <button class="btn btn-sm btn-danger deleteRowBtn"><i class="bi bi-trash"></i> Delete</button>
                                </div>
                            </td>
                        </tr>
                    `;

                $('#sortableRows').append(newRow);
                updateLineNumbers();
                saveNewRow();
            });

            $(document).on('click', '.deleteRowBtn', function() {
                let id = $(this).closest('tr').data('id');
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
                        deleteNewRow(id, $(this).closest('tr'));
                    }
                });
                updateLineNumbers();
            });
        });

        function updateLineNumbers() {
            $('#sortableRows tr').each(function(index) {
                $(this).find('.line-no-text').text(index + 1);
            });
        }

        function fixHelper(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        }


        function saveNewRow() {
            const newRow = $('#sortableRows tr[data-id="new"]').first();
            if (newRow.length === 0) return;

            let rowData = {};
            newRow.find('[data-field]').each(function() {
                const field = $(this).data('field');
                const value = $(this).text().trim();
                rowData[field] = value;
            });

            $.ajax({
                url: '{{ route('supervisors-shift-log.store') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ...rowData
                },
                success: function(response) {
                    if (response.id) {
                        newRow.attr('data-id', response.id);
                        newRow.find('.moreInfo').attr('href',
                            `{{ route('supervisors-shift-log.show', ':id') }}`.replace(':id', response.id));
                        newRow.find('[contenteditable]').css('background-color', '#d4edda');
                        setTimeout(() => newRow.find('[contenteditable]').css('background-color', ''), 1000);
                        updateLineNumbers();
                        notify('success', 'Row saved successfully');
                    }
                },
                error: function() {
                    newRow.find('[contenteditable]').css('background-color', '#f8d7da');
                    setTimeout(() => newRow.find('[contenteditable]').css('background-color', ''), 1500);
                    notify('error', 'Error saving row');
                }
            });
        }

        function deleteNewRow(id, tr) {
            if (id === 'new' || !id) {
                tr.remove();
                updateLineNumbers();
                return;
            }
            const deleteUrl = `{{ route('supervisors-shift-log.destroy', ['id' => '__ID__']) }}`.replace('__ID__', id);
            $.ajax({
                url: deleteUrl,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}',
                },
                success: function() {
                    tr.remove();
                    updateLineNumbers();
                    notify('success', 'Row deleted successfully');
                },
                error: function() {
                    notify('error', 'Error deleting row');
                }
            });
        }

        function saveRowOrder() {
            let order = [];

            $('#sortableRows tr').each(function(index) {
                const id = $(this).data('id');
                if (id && id !== 'new') {
                    order.push({
                        id: id,
                        position: index + 1
                    });
                }
            });

            $.ajax({
                url: '{{ route('supervisors-shift-log.reorder') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order: order
                },
                success: function(res) {
                    notify('success', res.message);
                },
                error: function(err) {
                    notify('error', err.message);
                }
            });
        }

        // Attach events
        $('#jobTable').on('change', 'select[data-field], input.complete_progress', function() {
            let el = $(this);
            let value = el.val();
            let field = el.data('field');

            if (field === 'shift_name') {
                let row = el.closest('tr');

                // Reset background
                row.css('background-color', '');
                el.css('background-color', '');

                if (value === 'night') {
                    row.css('background-color', '#939393a8');
                }
            }
            handler(this);
        });

        $('#jobTable').on('blur', '[contenteditable="true"]', function() {


            handler(this);
        });

        function handler(el) {
            let $el = $(el);
            let tr = $el.closest('tr');
            let id = tr.data('id');
            let field = $el.data('field');
            let value = $el.is('select') || $el.is('input') ? $el.val() : $el.text().trim();

            $.ajax({
                url: `{{ route('supervisors-shift-log.update', ':id') }}`.replace(':id', id),
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    field: field,
                    value: value
                },
                success: function(res) {
                    $el.css('background-color', '#d4edda');
                    notify('success', res.message);
                    setTimeout(() => $el.css('background-color', ''), 1000);
                },
                error: function() {
                    $el.css('background-color', '#f8d7da');
                    setTimeout(() => $el.css('background-color', ''), 1500);
                    notify('error', 'Error updating field');
                }
            });
        }

        $('#filter').on('change', function() {
            let filter = $(this).val();
            let params = new URLSearchParams(window.location.search);
            params.set('shift', filter);
            window.location.href = `{{ route('supervisors-shift-log.index') }}?${params.toString()}`;
        });

        $('#export').on('change', function() {
            // Get the selected value
            let selectedValue = $(this).val();

            // Check if a value is selected
            if (selectedValue) {
                let params = new URLSearchParams(window.location.search);
                let url = `{{ route('supervisors-shift-log.export') }}?export=${selectedValue}&${params}`;
                window.open(url, '_blank');
            }

        })
        $(document).ready(function() {
            $('.editable').on('blur', function() {
                let content = $(this).text().trim();
                let shift = $(this).data('shift');
                let date = $('#flatpickr-date').val();

                $.ajax({
                    url: '{{ route('labour-shift.update') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        shift: shift,
                        labour: content,
                        date: date
                    },
                    success: function(res) {},
                    error: function() {}
                });
            });
        });


        implementAutoAjaxLoading();
    </script>
@endsection

@section('page-style')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">
    <style>
        .line-number {
            width: 40px;
            text-align: center;
        }

        .flatpickr-months {
            background-color: #ffffff;
        }
    </style>
@endsection

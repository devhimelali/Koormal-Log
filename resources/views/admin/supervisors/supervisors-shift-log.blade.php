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
                    <option {{ $filter == 'both' ? 'selected' : '' }} value="both">Both</option>
                    <option {{ $filter == 'day' ? 'selected' : '' }} value="day">Day</option>
                    <option {{ $filter == 'night' ? 'selected' : '' }} value="night">Night</option>
                </select>
            </div>

            <!-- Center: Title and Shift Labour -->
            <div class="col-md-6 text-center">
                <h4 class="fw-bold fst-italic mb-4">SUPERVISORS SHIFT LOG – <span>(DATE)</span></h4>
                <div class="border border-success rounded p-2 mb-3">
                    <strong><u>Labour for Dayshift</u></strong><br>
                    Alex Herbertson, Bill Smith, Steven Jones, Frank Reid, Mark Thomas
                </div>
                <div class="border border-success rounded p-2">
                    <strong><u>Labour for Nightshift</u></strong><br>
                    John Winters, Albert Cummins, Ralph Grieves, Mark Riley
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
                    <th style="width: 101px">Move</th>
                    <th style="width: 150px">Action</th>
                </tr>
            </thead>
            <tbody id="sortableRows">
                @foreach ($jobs as $index => $job)
                    @php
                        $isEditable = $job->is_excel_upload === 0 ? 'contenteditable=true' : '';
                    @endphp

                    <tr data-id="{{ $job->id }}">
                        <td class="line-no text-center">
                            <span class="line-no-text"> {{ $index + 1 }}</span>
                        </td>
                        <td contenteditable=true data-field="shift_name">{{ $job->shift_name }}</td>
                        <td {!! $isEditable !!} data-field="wo_number">{{ $job->wo_number }}</td>
                        <td {!! $isEditable !!} data-field="asset_no">{{ $job->asset_no }}</td>
                        <td {!! $isEditable !!} data-field="work_description">{{ $job->work_description }}</td>
                        <td contenteditable=true data-field="labour">{{ $job->labour }}</td>
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
                        <div class="mt-3">
                            <label for="shift_name" class="form-label fw-semibold">Select Shift</label>
                            <select name="shift_name" class="form-select" id="shift_name">
                                <option value="day">Day</option>
                                <option value="night">Night</option>
                            </select>
                        </div>
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
    <script>
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
                            <td contenteditable="true" data-field="shift_name">Day</td>
                            <td contenteditable="true" data-field="wo_number">WO####</td>
                            <td contenteditable="true" data-field="asset_no">Asset-XXX</td>
                            <td contenteditable="true" data-field="work_description">Work Description</td>
                            <td contenteditable="true" data-field="labour">Labour Name</td>
                            <td class="line-no text-center">
                                <span class="drag-handle me-2 btn btn-secondary btn-sm" style="cursor: move;">
                                    <i class="bi bi-arrows-move me-2"></i> Move
                                </span>
                            </td>
                            <td class="handle text-center">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-info"><i class="bi bi-info-circle"></i> More</button>
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
                deleteNewRow(id);
                $(this).closest('tr').remove();
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

        $(document).ready(function() {
            $('#jobTable').on('blur', '[contenteditable="true"]', function() {
                let td = $(this);
                let tr = td.closest('tr');
                let id = tr.data('id');
                let field = td.data('field');
                let value = td.text().trim();

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
                        notify('success', res.message);
                        setTimeout(() => td.css('background-color', ''), 1000);
                    },
                    error: function() {
                        td.css('background-color', '#f8d7da');
                        setTimeout(() => td.css('background-color', ''), 1500);
                        notify('error', 'Error updating field');
                    }
                });
            });
        });
        $('#filter').on('change', function() {
            let filter = $(this).val();
            window.location.href = `{{ route('supervisors-shift-log.index') }}?filter=${filter}`;
        });
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
    </style>
@endsection

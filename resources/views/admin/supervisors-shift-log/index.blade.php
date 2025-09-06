@extends('layouts.app')
@section('title', 'Supervisors Shift Log')
@section('content')
    <div class="my-4 p-4 border bg-white">
        <div class="row align-items-center text-center text-md-start my-5">
            <!-- Left: Koormal logo and filter -->
            <div class="col-md-3 mb-3 mb-md-0 d-flex flex-column align-items-center">
                <div class="py-3 text-center">
                    <img src="{{ asset('assets/logos/koormal-logo.png') }}" style="width: 180px;" alt="Koormal Logo"
                         class="mb-2">
                </div>
                <select name="filter" class="form-select w-75 mt-2">
                    <option value="both">Both</option>
                    <option value="day">Day</option>
                    <option value="night">Night</option>
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
                <button class="btn btn-warning mt-3 w-75" data-bs-toggle="modal" data-bs-target="#importModal">Upload Excel Sheet</button>
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
                <tr>
                    <td class="line-no text-center">
                        <span class="line-no-text">{{ $index + 1 }}</span>
                    </td>
                    <td contenteditable="true">{{ $job['shift'] }}</td>
                    <td contenteditable="true">{{ $job['wo_number'] }}</td>
                    <td contenteditable="true">{{ $job['asset_no'] }}</td>
                    <td contenteditable="true">{{ $job['description'] }}</td>
                    <td contenteditable="true">{{ $job['labour'] }}</td>
                    <td class="line-no text-center">
                            <span class="drag-handle me-2 btn btn-secondary btn-sm" style="cursor: move;">
                                <i class="bi bi-arrows-move me-2"></i> Move
                            </span>
                    </td>
                    <td class="handle text-center">
                        <div class="btn-group">
                            <a href="{{ route('supervisors-shift-log.show', $job['id']) }}" class="btn btn-sm btn-info">
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

    <!-- Import Modal Start -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="exampleModalLabel" style="display: none;"
         aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header px-4 pt-4">
                    <h5 class="modal-title" id="modalLabel">Import Supervisor Shift Log Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            id="close-modal"></button>
                </div>

                <form class="tablelist-form" action="{{ route('supervisors-shift-log.bulk-import') }}" method="POST"
                      id="bulkImportForm"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="import_file" class="form-label">Upload Import File</label>
                            <input type="file" id="import_file" name="file" class="form-control">
                            <p class="text-danger mt-1 mb-0">File should be in CSV/XLSX format</p>
                        </div>
                    </div>
                    <div class="modal-footer" style="display: block;">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-ghost-danger" data-bs-dismiss="modal"><i
                                        class="bi bi-x-lg align-baseline me-1"></i> Close
                            </button>
                            <button type="submit" class="btn btn-primary" id="importSubmitBtn">Import</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- modal-content -->
        </div>
        <!-- modal-dialog -->
    </div>
    <!-- Import Modal End -->
@endsection

@section('page-script')
    <script>
        $(function () {
            $("#sortableRows").sortable({
                items: "tr",
                handle: ".drag-handle",
                helper: fixHelper,
                cancel: '[contenteditable]', // ✅ Prevents blocking inputs
                start: function (e, ui) {
                    ui.placeholder.height(ui.item.height());
                },
                update: function () {
                    updateLineNumbers();
                }
            });

            $("#sortableRows .drag-handle").disableSelection();

            $('#addJobBtn').on('click', function () {
                const newRow = `
                        <tr>
                            <td class="line-no text-center">
                                <span class="line-no-text"></span>
                            </td>
                            <td contenteditable="true">Day</td>
                            <td contenteditable="true">WO####</td>
                            <td contenteditable="true">Asset-XXX</td>
                            <td contenteditable="true">Work Description</td>
                            <td contenteditable="true">Labour Name</td>
                            <td class="line-no text-center">
                                <span class="drag-handle me-2 btn btn-secondary btn-sm" style="cursor: move;">
                                    <i class="bi bi-arrows-move me-2"></i> Move
                                </span>
                            </td>
                            <td class="handle text-center">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-info"><i class="bi bi-info-circle"></i> More</button>
                                    <button class="btn btn-sm btn-secondary deleteRowBtn"><i class="bi bi-trash"></i> Delete</button>
                                </div>
                            </td>
                        </tr>
                    `;

                $('#sortableRows').append(newRow);
                updateLineNumbers();
            });

            $(document).on('click', '.deleteRowBtn', function () {
                $(this).closest('tr').remove();
                updateLineNumbers();
            });
        });

        function updateLineNumbers() {
            $('#sortableRows tr').each(function (index) {
                $(this).find('.line-no-text').text(index + 1);
            });
        }

        function fixHelper(e, ui) {
            ui.children().each(function () {
                $(this).width($(this).width());
            });
            return ui;
        }
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

        /* [contenteditable="true"] {
                                                                outline: none;
                                                                border-bottom: 1px dashed #ccc;
                                                            } */
    </style>
@endsection

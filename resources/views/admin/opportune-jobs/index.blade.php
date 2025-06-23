@extends('layouts.app')
@section('title', 'Opportune Jobs')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    Opportune Jobs
                </h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{route('admin.dashboard')}}">
                                Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            Opportune Jobs
                        </li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <div class="row mb-5">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">Opportune Jobs List</h5>
                    </div>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-secondary d-flex align-items-center" data-bs-toggle="modal"
                                data-bs-target="#bulkImportModal">
                            <i class="bx bx-upload me-2"></i>
                            Import Opportune Jobs
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="datatable">
                            <thead class="table-light">
                            <tr>
                                <th scope="col">S.No</th>
                                <th scope="col" style="min-width: 80px; width: 80px;">Wo Order</th>
                                <th scope="col" style="min-width: 80px; width: 80px;">Asset No</th>
                                <th scope="col" style="min-width: 200px; width: 200px;">Work-order Description</th>
                                <th scope="col" style="min-width: 200px; width: 200px;">Asset Description</th>
                                <th scope="col">Department</th>
                                <th scope="col">Actions</th>
                            </tr>
                            </thead>
                            <tbody style="vertical-align: middle">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="bulkImportModal" tabindex="-1" aria-labelledby="supervisorsShiftLogModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary-subtle pb-3">
                    <h1 class="modal-title fs-5" id="supervisorsShiftLogModalLabel">Upload Excel Sheet</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('bulk-import-opportune-jobs') }}" method="POST"
                      enctype="multipart/form-data" id="bulkImportForm">
                    <div class="modal-body p-3 mb-3">
                        @csrf
                        <label for="csv_file" class="form-label fw-semibold">Upload File <span
                                    class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-subtle-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary" id="csvImportBtn">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" style="display: none;"
         aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Opportune Job</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            id="close-modal"></button>
                </div>

                <form class="tablelist-form" action="" method="POST" id="deleteForm">
                    @csrf
                    <input type="hidden" name="_method" value="DELETE">
                    <div class="modal-body p-4">
                        <p id="deleteMessage">Are you sure you want to delete this opportune job?</p>
                    </div>
                    <div class="modal-footer" style="display: block;">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-ghost-danger" data-bs-dismiss="modal"><i
                                        class="bi bi-x-lg align-baseline me-1"></i> Close
                            </button>
                            <button type="submit" class="btn btn-danger" id="deleteBtn">Delete</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('page-script')
    {{--    <script src="{{ asset('assets/libs/datatables/jquery.dataTables.min.js') }}"></script>--}}
    {{--    <script src="{{ asset('assets/libs/datatables/dataTables.bootstrap5.min.js') }}"></script>--}}
    <script>
        $(document).ready(function () {

            let table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('opportune-jobs.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'wo_number', name: 'wo_number'},
                    {data: 'asset_no', name: 'asset_no'},
                    {data: 'work_description', name: 'work_description'},
                    {data: 'asset_description', name: 'asset_description'},
                    {data: 'department', name: 'department'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
            });

            $('body').on('click', '.delete', function () {
                let id = $(this).data('id');
                let deleteUrl = "{{ route('opportune-jobs.destroy', ':id') }}".replace(':id', id);
                $('#deleteForm').attr('action', deleteUrl);
                $('#deleteModal').modal('show');
            });

            $('#deleteForm').submit(function (e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form.serialize(),
                    beforeSend: function () {
                        $('#deleteBtn').prop('disabled', true);
                        $('#deleteBtn').html(
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...'
                        );
                    },
                    success: function (response) {
                        $('#deleteModal').modal('hide');
                        table.ajax.reload();
                        notify('success', response.message);
                    },
                    error: handleAjaxErrors,
                    complete: function () {
                        $('#deleteBtn').prop('disabled', false);
                        $('#deleteBtn').html('Delete');
                    }
                });
            });

            $('#bulkImportForm').submit(function (e) {
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
                    success: function (response) {
                        if (response.status == 'success') {
                            notify('success', response.message);
                            table.ajax.reload();
                            $('#bulkImportForm')[0].reset();
                            $('#bulkImportModal').modal('hide');
                        }
                    },
                    error: function (xhr, status, error) {
                        if (xhr.status == 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                notify('error', value[0]);
                                let input = $('[name="' + key + '"]');
                                input.addClass('is-invalid');
                                input.next('.invalid-feedback').text(value[0]);
                            });
                        } else if (xhr.status === 429) {
                            notify('error', 'Too many failed attempts. Please try again later.');
                        } else if (xhr.status === 500) {
                            if (xhr.responseJSON.message) {
                                notify('error', xhr.responseJSON.message);
                            } else {
                                notify('error', 'Something went wrong!');
                            }
                        }
                    },
                    complete: function () {
                        $('#csvImportBtn').attr('disabled', false);
                        $('#csvImportBtn').html('Import');
                    }
                });
            });
        });
    </script>
@endsection

@section('page-style')
    <style>
        div#datatable_length {
            margin-bottom: 10px;
        }

        div#datatable_info {
            margin-top: 15px;
            margin-bottom: 10px;
        }

        div#datatable_paginate {
            margin-top: 15px;
            margin-bottom: 10px;
        }
    </style>
@endsection
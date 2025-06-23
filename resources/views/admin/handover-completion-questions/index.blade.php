@extends('layouts.app')
@section('title', 'Handover Completion Questions')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Handover Completion Questions</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Handover Completion Questions</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Completion Question Lists</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addOrEditHandoverCompletionModal">
                        Add New
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="datatable">
                            <thead class="table-light">
                            <tr>
                                <th scope="col" style="max-width: 50px; width: 50px;">S.No</th>
                                <th scope="col">Question</th>
                                <th scope="col" style="max-width: 100px; width: 100px;">Sorting Order</th>
                                <th scope="col" style="max-width: 180px; width: 180px;">Actions</th>
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
    <div class="modal fade" id="addOrEditHandoverCompletionModal" tabindex="-1"
         aria-labelledby="addOrEditHandoverCompletionModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOrEditHandoverCompletionModalLabel">Add a new question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('handover-completion-questions.store') }}" id="handoverCompletionAddForm"
                      method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="POST" id="method">
                    <div class="modal-body">
                        <div class="mb-2">
                            <label for="question" class="form-label">Question <span
                                        class="text-danger">*</span></label>
                            <textarea class="form-control" id="question" name="question"
                                      placeholder="Enter Question" cols="30" rows="5"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="sort_by" class="form-label">Sorting Order Number <span
                                        class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="sort_by" name="sort_by" value="1"
                                   placeholder="Enter Sorting Order">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="handoverCompletionAddBtn">Save</button>
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
                    <h5 class="modal-title" id="deleteModalLabel">Delete Handover Completion Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            id="close-modal"></button>
                </div>

                <form class="tablelist-form" action="" method="POST" id="deleteForm">
                    @csrf
                    <input type="hidden" name="_method" value="DELETE">
                    <div class="modal-body p-4">
                        <p id="deleteMessage">Are you sure you want to delete this handover completion question?</p>
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
    <script>
        $(document).ready(function () {
            let table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('handover-completion-questions.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'question', name: 'question'},
                    {data: 'sort_by', name: 'sort_by'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[2, 'asc']]
            });

            $('#handoverCompletionAddForm').submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function () {
                        ajaxBeforeSend('#handoverCompletionAddForm', '#addOrEditAssetSubmitBtn');
                    },
                    success: function (response) {
                        if (response.status == 'success') {
                            notify('success', response.message);
                            table.ajax.reload();
                            $('#handoverCompletionAddForm')[0].reset();
                            $('#addOrEditHandoverCompletionModal').modal('hide');
                        }
                    },
                    error: handleAjaxErrors,
                    complete: function () {
                        ajaxComplete('#addOrEditAssetSubmitBtn')
                    }
                });
            });

            $('#addOrEditHandoverCompletionModal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
                $('#method').val('POST');
                $('#addOrEditHandoverCompletionModal .modal-title').html('Add a new question');
                $('#handoverCompletionAddForm').attr('action', "{{ route('handover-completion-questions.store') }}");
                $('#handoverCompletionAddForm').find('.is-invalid').removeClass('is-invalid');
            });

            $('body').on('click', '.edit', function () {
                var id = $(this).data('id');
                $('#loader').show();
                var editUrl = "{{ route('handover-completion-questions.edit', ':id') }}".replace(':id', id);
                $.get(editUrl, function (data) {
                    $('#loader').hide();
                    $('#addOrEditHandoverCompletionModal .modal-title').html('Edit question');
                    $(
                        '#handoverCompletionAddForm').attr('action',
                        "{{ route('handover-completion-questions.update', ':id') }}"
                            .replace(':id', id));
                    $('#method').val('PUT');
                    $('#handoverCompletionAddForm #question').val(data.data.question);
                    $('#handoverCompletionAddForm #sort_by').val(data.data.sort_by);
                    $('#addOrEditHandoverCompletionModal').modal('show');
                }).fail(function () {
                    $('#loader').hide();
                    notify('error', 'Something went wrong. Please try again.');
                });
            });

            $('body').on('click', '.delete', function () {
                let id = $(this).data('id');
                let deleteUrl = "{{ route('handover-completion-questions.destroy', ':id') }}".replace(':id', id);
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

            implementAutoAjaxLoading();
        })
    </script>
@endsection
@section('page-style')
    <style>
        div#datatable_length {
            margin-bottom: 10px;
        }

        div#datatable_filter {
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
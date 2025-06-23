@extends('layouts.app')
@section('title', 'Handover Completions')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Handover Completions</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Handover Completions</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Handover Completion Lists</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="datatable">
                            <thead class="table-light">
                            <tr>
                                <th scope="col" style="max-width: 50px; width: 50px;">S.No</th>
                                <th scope="col">Shift</th>
                                <th scope="col">Date</th>
                                <th scope="col">Supervisor Name</th>
                                <th scope="col" style="max-width: 120px; width: 120px;">Actions</th>
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
    <div class="modal fade" id="handoverCompletionDetailsModal" tabindex="-1"
         aria-labelledby="handoverCompletionModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
        </div>
    </div>
@endsection
@section('page-script')
    <script>
        $(document).ready(function () {
            let shift = '{{ request()->shift }}';
            let table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('handover-completions.index') }}" + "?shift=" + shift,
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'shift', name: 'shift'},
                    {data: 'log_date', name: 'log_date'},
                    {data: 'supervisor_name', name: 'supervisor_name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
            });

            $('body').on('click', '.viewDetails', function () {
                let url = $(this).data('href');
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (response) {
                        $('#handoverCompletionDetailsModal .modal-content').html(response);
                        $('#handoverCompletionDetailsModal').modal('show');
                    }
                });
            })

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
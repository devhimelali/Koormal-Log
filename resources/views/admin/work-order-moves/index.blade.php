@extends('layouts.app')
@section('title', 'Work Order Moves')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Work Order Moves</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Work Order Moves</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Work Order Move Lists</h4>
                    {{--                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal"--}}
                    {{--                            data-bs-target="#addOrEditCrewModal">--}}
                    {{--                        Add New--}}
                    {{--                    </button>--}}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="datatable">
                            <thead class="table-light">
                            <tr>
                                <th scope="col" style="max-width: 40px; width: 40px;">S.No</th>
                                <th scope="col" style="min-width: 80px; width: 80px;">Work Order No</th>
                                <th scope="col" style="min-width: 250px;">Reason</th>
                                <th scope="col" style="min-width: 180px; width: 180px;">From Date → To Date</th>
                                <th scope="col" style="min-width: 110px; width: 110px;">From Shift → To Shift</th>
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
@endsection
@section('page-script')
    <script>
        $(document).ready(function () {
            let table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('work-order-moves.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'wo_number', name: 'wo_number'},
                    {data: 'reason', name: 'reason'},
                    {data: 'from_date_to_date', name: 'from_date_to_date'},
                    {data: 'from_shift_to_shift', name: 'from_shift_to_shift'},
                ],
            });
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
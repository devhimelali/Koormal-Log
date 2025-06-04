@extends('layouts.app')

@section('content')
    {{-- Breadcrumb --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Booking</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Booking</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Booking Table --}}
    <div class="row mb-5">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">Booking List</h5>
                    </div>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#bookingModal">
                            Add Labour
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        {!! $dataTable->table(
                            [
                                'class' => 'table table-striped mt-2',
                                'style' => 'width: 100%',
                            ],
                            true,
                        ) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('labours.store') }}" id="labourAddForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Add Labour</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="text" class="form-control" id="date" name="date"
                                placeholder="Select date">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name[]"
                                placeholder="Enter names separated by comma or enter">
                            <small class="text-muted">Write names and press enter</small>
                        </div>
                        <div class="mb-3">
                            <label for="select_shift" class="form-label">Shift</label>
                            <select name="shift" class="form-select" id="select_shift">
                                <option value="day">Day</option>
                                <option value="night">Night</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Labour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-style')
    <!-- Tagify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
    <style>
        div#dataTableBuilder_filter {
            margin-bottom: 16px;
        }

        .table.dataTable>thead>tr>th,
        table.dataTable>thead>tr>td {
            padding: 10px;
            border-top: 1.5px solid rgba(90, 90, 90, 0.3);
        }
    </style>
@endsection

@push('scripts')
    <!-- Tagify JS -->
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <!-- Flatpickr assets -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    {!! $dataTable->scripts() !!}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let nameInput = document.querySelector('#name');
            new Tagify(nameInput);
            flatpickr("#date", {
                dateFormat: "d-m-Y",
            });
        });

        $('#labourAddForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            let url = $(this).attr('action');
            let method = $(this).attr('method');
            $.ajax({
                url: url,
                method: method,
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#bookingModal').modal('hide');
                    $('#labourAddForm')[0].reset();
                    notify('success', 'Labour added successfully');
                    window.LaravelDataTables["dataTableBuilder"].ajax.reload();
                }
            });
        });
    </script>
@endpush

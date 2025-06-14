@extends('layouts.app')
@section('title', 'Notes')
@section('content')
    <div class="my-4 p-4 border bg-white">
        <div class="d-flex justify-content-between">
            <h2>Notes</h2>
            <button class="btn btn-success mb-3" id="addNoteBtn">Add Note</button>
        </div>
        {!! $dataTable->table(['class' => 'table table-bordered'], true) !!}
    </div>
    @include('admin.notes.modal')
@endsection
@push('scripts')
    {{-- sweetalert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {!! $dataTable->scripts() !!}
    <script>
        let saveUrl = "{{ route('notes.store') }}";
        let updateUrl = "";

        $('#addNoteBtn').click(function() {
            $('#noteForm')[0].reset();
            $('#noteModalLabel').text('Add Note');
            $('#noteModal').modal('show');
            $('#note_id').val('');
            saveUrl = "{{ route('notes.store') }}";
        });

        // Edit
        $('body').on('click', '.editBtn', function() {
            let id = $(this).data('id');
            let url = "{{ route('notes.edit', ':id') }}".replace(':id', id);
            $.get(url, function(data) {
                $('#noteModalLabel').text('Edit Note');
                $('#noteModal').modal('show');
                $('#note_id').val(data.id);
                $('#note').val(data.note);
                saveUrl = "{{ route('notes.update', ':id') }}".replace(':id', id);
            });
        });

        // Save or Update
        $('#noteForm').submit(function(e) {
            e.preventDefault();
            let formData = {
                note: $('#note').val(),
                _token: '{{ csrf_token() }}',
                _method: $('#note_id').val() ? 'PUT' : 'POST'
            };

            $.ajax({
                url: saveUrl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#noteModal').modal('hide');
                    $('#notes-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    alert(xhr.responseJSON.message);
                }
            });
        });



        $(document).on('click', '.deleteBtn', function() {
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
                    let url = "{{ route('notes.destroy', ':id') }}".replace(':id', id);
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            $('#notes-table').DataTable().ajax.reload();
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
        #notes-table tbody tr td {
            vertical-align: middle;
        }

        div.dataTables_wrapper div.dataTables_filter input {
            margin-bottom: 15px !important;
        }

        thead {
            background-color: #e0e0e0;
        }

        .text-wrap {
            white-space: normal !important;
            word-break: break-word;
        }
    </style>
@endpush

@extends('layouts.app')
@section('title', 'Supervisor Completion Compliance')

@section('content')
    <form id="supervisorCompletionComplianceForm">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <label for="supervisor" class="form-label fw-semibold">Supervisor Name</label>
                        <input type="text" name="supervisor" id="supervisor" class="form-control shadow-sm"
                               placeholder="Enter supervisor name">
                    </div>
                    <div class="col-md-5">
                        <label for="date_range" class="form-label fw-semibold">Date Range</label>
                        <input type="text" name="date_range" id="date_range" class="form-control shadow-sm"
                               placeholder="Select date range">
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" id="loadPdf" class="btn btn-danger shadow-sm mt-2 mt-md-0">
                            <i class="bi bi-filetype-pdf me-1"></i> Export PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(document).ready(function () {
            // Date range picker
            flatpickr('#date_range', {
                mode: 'range',
                dateFormat: 'd-m-Y',
            });

            // Handle form submit
            $('#supervisorCompletionComplianceForm').on('submit', function (e) {
                e.preventDefault();

                let supervisor = $('#supervisor').val().trim();
                let dateRange = $('#date_range').val().trim();

                if (!supervisor || !dateRange) {
                    alert('Please enter supervisor name and select a date range.');
                    return;
                }

                // Extract start and end date from date range
                let dates = dateRange.split(' to ');
                let start_date = dates[0] || '';
                let end_date = dates[1] || '';

                // AJAX request
                $.ajax({
                    url: "{{ route('supervisor-completion-compliance.pdf') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        supervisor: supervisor,
                        start_date: start_date,
                        end_date: end_date
                    },
                    xhrFields: {
                        responseType: 'blob' // Important for downloading PDF
                    },
                    beforeSend: function () {
                        $('#loadPdf').attr('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Generating...');
                    },
                    success: function (data) {
                        // Create a blob URL and open in new tab
                        let blob = new Blob([data], { type: 'application/pdf' });
                        let link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.target = '_blank';
                        link.click();


                    },
                    error: function () {
                        notify('error', 'Something went wrong while generating the PDF.');
                    },
                    complete: function () {
                        $('#loadPdf').attr('disabled', false).html('<i class="bi bi-filetype-pdf me-1"></i> Export PDF');
                    }
                });
            });
        });
    </script>
    <style>
        .spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection

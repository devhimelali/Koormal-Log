<div class="modal-header bg-primary-subtle py-2">
    <div>
        <h1 class="modal-title fs-5" id="handoverCompletionModalLabel">Handover Completion</h1>
        <span class="text-danger">If you answer No to any of the questions please add an explanation in your handover
            notes.</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('handover-completions.store') }}" method="POST" id="handoverCompletionForm">
    @csrf
    <input type="hidden" name="shift" id="handoverCompletionShift" value="{{ $shift }}">
    <input type="hidden" name="date" id="handoverCompletionDate" value="{{ $date }}">
    <div class="modal-body p-3 mb-3">
        <div class="row">
            @foreach ($questions as $question)
                @php
                    $selectedAnswer = $handoverCompletion->answers[$question->question] ?? 'No';
                @endphp
                <div class="col-md-6 mb-3">
                    <label>{{ $question->question }}</label>
                    <select name="answers[{{ $question->question }}]" class="form-select form-select-sm w-50">
                        <option value="No" {{ $selectedAnswer === 'No' ? 'selected' : '' }}>No</option>
                        <option value="Yes" {{ $selectedAnswer === 'Yes' ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
            @endforeach
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-subtle-danger" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-secondary" id="handoverCompletionSubmitBtn">Save</button>
    </div>
</form>

<script>
    $('#handoverCompletionForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let url = $(this).attr('action');
        let method = $(this).attr('method');

        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false, // ✅ FormData support
            contentType: false, // ✅ FormData support
            beforeSend: function() {
                $('#handoverCompletionForm').find('.invalid-feedback').remove();
                $('#handoverCompletionForm').find('.is-invalid').removeClass('is-invalid');
                $('#handoverCompletionSubmitBtn').attr('disabled', true);
                $('#handoverCompletionSubmitBtn').html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...'
                );
            },
            success: function(res) {
                if (res.status == 'success') {
                    $('#handoverCompletionModal').modal('hide');
                    location.reload();
                }
            },
            error: function(xhr) {
                if (xhr.status == 422) {
                    let errors = xhr.responseJSON.errors;
                    let firstError = Object.values(errors)[0][0];
                    notify('error', firstError || 'Validation failed.')
                } else {
                    notify('error', xhr.responseJSON.message ||
                        'Something went wrong. Please try again.')
                }
            },
            complete: function() {
                $('#handoverCompletionSubmitBtn').attr('disabled', false);
                $('#handoverCompletionSubmitBtn').html('Submit');
            }
        });
    });
</script>

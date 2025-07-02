<div class="modal-header bg-primary-subtle pb-2">
    <div>
        <h1 class="modal-title fs-5" id="modalLabel">Load Crew</h1>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('load-labour.store') }}" method="POST" id="loadLabourForm">
    @csrf
    <input type="hidden" name="shift" id="loadLabourShift" value="{{ $shift }}">
    <input type="hidden" name="date" id="loadLabourDate" value="{{ $date }}">
    <div class="modal-body p-3 mb-3">
        <div class="mb-2">
            <label for="labour_ids" class="form-label">Select a Crew</label>
            <select class="form-select" name="labour_ids[]" id="labour_ids" multiple>
                <option value="">Select a Crew</option>
                @foreach ($labours as $labour)
                    <option value="{{ $labour->id }}">{{ $labour->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-secondary" id="loadLabourSubmitBtn">Save</button>
        <button type="button" class="btn btn-subtle-danger" data-bs-dismiss="modal">Close</button>
    </div>
</form>

<script>
    $('#loadLabourForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let url = $(this).attr('action');
        let method = $(this).attr('method');

        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#loadLabourForm').find('.invalid-feedback').remove();
                $('#loadLabourForm').find('.is-invalid').removeClass('is-invalid');
                $('#loadLabourSubmitBtn').attr('disabled', true);
                $('#loadLabourSubmitBtn').html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...'
                );
            },
            success: function(res) {
                if (res.status == 'success') {
                    $('#loadCrewModal').modal('hide');
                    window.location.reload();
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
                $('#loadLabourSubmitBtn').attr('disabled', false);
                $('#loadLabourSubmitBtn').html('Submit');
            }
        });
    });
</script>

<div class="modal-header bg-primary-subtle pb-2">
    <div>
        <h1 class="modal-title fs-5" id="loadCrewModalLabel">Load Crew</h1>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{route('load-crew.store')}}" method="POST" id="loadCrewForm">
    @csrf
    <input type="hidden" name="shift" id="loadCrewShift" value="{{$shift}}">
    <input type="hidden" name="date" id="loadCrewDate" value="{{$date}}">
    <div class="modal-body p-3 mb-3">
        <div class="mb-2">
            <label for="crew_id" class="form-label">Select a Crew</label>
            <select class="form-select" name="crew_id" id="crew_id">
                <option value="">Select a Crew</option>
                @foreach($crews as $crew)
                    <option value="{{ $crew->id }}">{{ $crew->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="labour-container"></div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-secondary" id="loadCrewSubmitBtn">Save</button>
        <button type="button" class="btn btn-subtle-danger" data-bs-dismiss="modal">Close</button>
    </div>
</form>

<script>
    $('#loadCrewForm').on('submit', function (e) {
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
            beforeSend: function () {
                $('#loadCrewForm').find('.invalid-feedback').remove();
                $('#loadCrewForm').find('.is-invalid').removeClass('is-invalid');
                $('#loadCrewSubmitBtn').attr('disabled', true);
                $('#loadCrewSubmitBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
            },
            success: function (res) {
                if (res.status == 'success') {
                    $('#loadCrewModal').modal('hide');
                    $('#jobTable').DataTable().ajax.reload(null, false);
                }
            },
            error: function (xhr) {
                if (xhr.status == 422) {
                    let errors = xhr.responseJSON.errors;
                    let firstError = Object.values(errors)[0][0];
                    notify('error', firstError || 'Validation failed.')
                } else {
                    notify('error', xhr.responseJSON.message || 'Something went wrong. Please try again.')
                }
            },
            complete: function () {
                $('#loadCrewSubmitBtn').attr('disabled', false);
                $('#loadCrewSubmitBtn').html('Submit');
            }
        });
    });

</script>
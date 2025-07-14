<!-- Copy Labour & Supervisors Modal -->
<div id="copyDaysModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
    style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light py-2">
                <h5 class="modal-title" id="myModalLabel">Copy Labour & Supervisor Assignments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <form action="{{ route('labour-supervisor.copy') }}" method="post" class="copyDaysForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <label for="copy-days-shift" class="form-label">Shift <span class="text-danger">*</span></label>
                        <select class="form-select" name="shift" id="copy-days-shift" required>
                            <option value="">Select shift</option>
                            <option value="day">Day Shift</option>
                            <option value="night">Night Shift</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-2">
                        <label for="copy-for" class="form-label">Assignment Type <span
                                class="text-danger">*</span></label>
                        <select class="form-select" name="copy_for" id="copy-for" required>
                            <option value="">Select assignment type</option>
                            <option value="supervisor">Supervisor Only</option>
                            <option value="labour">Labour Only</option>
                            <option value="both">Both</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-2">
                        <label for="copy-days-date" class="form-label">Start Date <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="copy_days_date" id="copy-days-date" required
                            placeholder="Select starting date">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-2">
                        <label for="end_date" class="form-label">End Date<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="end_date" id="end_date" required
                            placeholder="Select ending date">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-2">
                        <label for="names" class="form-label">Names <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="names" id="names" required
                            placeholder="Enter names separated by comma or enter. e.g., John, Jane">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary" id="copyDaysSubmitBtn">Apply Copy</button>
                    <button type="button" class="btn btn-subtle-danger" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    $('.copyDaysForm').on('submit', function(e) {
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
            success: function(res) {
                notify('success', res.message);
                $('#copyDaysModal').modal('hide');
                $('.copyDaysForm')[0].reset();
                location.reload();
            },
            error: function(xhr, status, err) {
                if (xhr.status == 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        notify('error', value);
                        let input = $('[name="' + key + '"]');
                        input.addClass('is-invalid');
                        input.next('.invalid-feedback').text(value);
                    });
                } else {
                    notify('error', xhr.responseJSON.message);
                }
            }
        });
    })

    // modal hide reset the form
    $('#copyDaysModal').on('hidden.bs.modal', function() {
        $('.copyDaysForm')[0].reset();
    })
</script>

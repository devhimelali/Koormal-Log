<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-2">Update Password</h4>
    </div>
    <hr class="m-0">
    <div class="card-body">
        <form action="{{ route('user-password.update') }}" method="POST" id="updatePasswordForm">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label" for="current_password">Current Password <span
                            class="text-danger">*</span></label>
                    <div class="position-relative auth-pass-inputgroup">
                        <input type="password" class="form-control pe-5 password-input "
                               placeholder="Enter your current password" id="current_password" name="current_password">
                        <button
                            class="top-0 btn btn-link position-absolute end-0 text-decoration-none text-muted password-addon"
                            type="button"><i
                                class="align-middle ri-eye-fill"></i></button>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label" for="password">Password <span
                            class="text-danger">*</span></label>
                    <div class="position-relative auth-pass-inputgroup">
                        <input type="password" class="form-control pe-5 password-input "
                               placeholder="Enter password" id="password" name="password">
                        <button
                            class="top-0 btn btn-link position-absolute end-0 text-decoration-none text-muted password-addon"
                            type="button"><i
                                class="align-middle ri-eye-fill"></i></button>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label" for="password-confirmation">Confirm Password <span
                            class="text-danger">*</span></label>
                    <div class="position-relative auth-pass-inputgroup">
                        <input type="password" class="form-control pe-5 password-input "
                               placeholder="Enter confirm password" id="password-confirmation"
                               name="password_confirmation">
                        <button
                            class="top-0 btn btn-link position-absolute end-0 text-decoration-none text-muted password-addon"
                            type="button" id="password-addon"><i
                                class="align-middle ri-eye-fill"></i></button>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-secondary" id="updatePasswordSubmitBtn">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#updatePasswordForm').on('submit', function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            let url = $(this).attr('action');
            let method = $(this).attr('method');

            $.ajax({
                url: url,
                method: method,
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('').removeClass('d-block');
                    $('#updatePasswordSubmitBtn').attr('disabled', true);
                    $('#updatePasswordSubmitBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
                },
                success: function (response) {
                    notify('success', 'Password updated successfully');
                    $('#updatePasswordForm')[0].reset();
                },
                error: function (xhr, status, error) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        notify('error', value);
                        let input = $('[name="' + key + '"]');
                        input.addClass('is-invalid');
                        input.closest('.auth-pass-inputgroup').find('.invalid-feedback').text(value).addClass('d-block');
                    });
                },
                complete: function () {
                    $('#updatePasswordSubmitBtn').attr('disabled', false);
                    $('#updatePasswordSubmitBtn').html('Save');
                }
            });
        })
    });
</script>

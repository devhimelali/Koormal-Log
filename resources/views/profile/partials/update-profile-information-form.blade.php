<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-2">Update Profile Information</h4>
    </div>
    <hr class="m-0">
    <div class="card-body">
        <form action="{{ route('user-profile-information.update') }}" method="POST" id="profileInfoUpdateForm">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name"
                           value="{{ $user->name }}">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-12 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="{{ $user->email }}">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-secondary" id="profileInfoUpdateSubmitBtn">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#profileInfoUpdateForm').on('submit', function (e) {
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
                    $('#profileInfoUpdateSubmitBtn').attr('disabled', true);
                    $('#profileInfoUpdateSubmitBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
                },
                success: function (response) {
                    notify('success', 'Profile information updated successfully');
                },
                error: function (xhr, status, error) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        notify('error', value);
                        let input = $('[name="' + key + '"]');
                        input.addClass('is-invalid');
                        input.next('.invalid-feedback').text(value);
                    });
                },
                complete: function () {
                    $('#profileInfoUpdateSubmitBtn').attr('disabled', false);
                    $('#profileInfoUpdateSubmitBtn').html('Save');
                }
            });
        })
    });
</script>

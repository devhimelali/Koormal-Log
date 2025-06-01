@extends('layouts.auth')
@section('title', 'Reset Password')
@section('content')
    <div class="col-lg-5">
        <div class="mb-0 card">
            <div class="row g-0 align-items-center">
                <div class="mx-auto col-xxl-12">
                    <div class="mb-0 border-0 shadow-none card">
                        <div class="card-body p-sm-3 m-lg-4">
                            <div class="text-center">
                                <h5 class="fs-3xl">Create new password</h5>
                                <p class="mb-3 text-muted">Your new password must be different from previous used
                                    password.
                                </p>
                            </div>

                            <div class="p-2">
                                <form method="POST" action="{{ route('password.update') }}" id="resetPasswordForm">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                               value="{{ old('email', $request->email) }}" readonly>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="password">Password</label>
                                        <div class="position-relative auth-pass-inputgroup">
                                            <input type="password" class="form-control pe-5 password-input"
                                                   placeholder="Enter password" id="password" name="password">
                                            <button
                                                class="top-0 btn btn-link position-absolute end-0 text-decoration-none text-muted password-addon"
                                                type="button" id="password-addon"><i
                                                    class="align-middle ri-eye-fill"></i></button>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="password-confirmation">Confirm Password</label>
                                        <div class="mb-3 position-relative auth-pass-inputgroup">
                                            <input type="password" class="form-control pe-5 password-input"
                                                   placeholder="Confirm password" id="password-confirmation"
                                                   name="password_confirmation">
                                            <button
                                                class="top-0 btn btn-link position-absolute end-0 text-decoration-none text-muted password-addon"
                                                type="button"><i class="align-middle ri-eye-fill"></i></button>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <button class="btn btn-primary w-100" type="submit" id="submitBtn">Reset
                                            Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="mt-4 text-center">
                                <p class="mb-0">Wait, I remember my password...
                                    <a href="{{ route('login') }}"
                                       class="fw-semibold text-primary text-decoration-underline">
                                        Click here </a>
                                </p>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-script')
    <script>
        $(document).ready(function () {
            $('#resetPasswordForm').on('submit', function (e) {
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: formData,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').text('');
                        $('#submitBtn').attr('disabled', true);
                        $('#submitBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
                    },
                    success: function (response) {
                        notify('success', 'Password reset successfully');
                        setTimeout(() => {
                            window.location.href = "{{route('login')}}";
                        }, 1000);
                    },
                    error: function (xhr, status, error) {
                        if (xhr.status == 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                notify('error', value);
                                let input = $('[name="' + key + '"]');
                                input.addClass('is-invalid');
                                if (input.closest('.auth-pass-inputgroup').length) {
                                    input.closest('.auth-pass-inputgroup').find('.invalid-feedback').text(value);
                                } else {
                                    input.next('.invalid-feedback').text(value);
                                }
                            });
                        } else if (xhr.status === 429) {
                            notify('error', 'Too many failed attempts. Please try again later.');
                        } else if (xhr.status === 500) {
                            notify('error', 'Something went wrong on our end. Please try again later.');
                        } else {
                            notify('error', error);
                        }
                    },
                    complete: function () {
                        $('#submitBtn').attr('disabled', false);
                        $('#submitBtn').html('Sign Up');
                    }
                });
            })
        });
    </script>
@endsection

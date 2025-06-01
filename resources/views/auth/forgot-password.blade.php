@extends('layouts.auth')
@section('title', 'Forgot Password')
@section('content')
    <div class="col-lg-5">
        <div class="mb-0 card">
            <div class="row g-0 align-items-center">
                <!--end col-->
                <div class="mx-auto col-xxl-12">
                    <div class="mb-0 border-0 shadow-none card">
                        <div class="card-body p-sm-2 m-lg-4">
                            <div class="mt-2 text-center">
                                <h5 class="fs-3xl">Forgot Password?</h5>
                                <div class="pb-4">
                                    <img src="{{ asset('assets/images/auth/email.png') }}" alt="" class="avatar-md">
                                </div>
                            </div>

                            <div class="mx-2 mb-2 text-center border-0 alert alert-warning infoBox" role="alert">
                                Enter your email and instructions will be sent to you!
                            </div>
                            <div class="p-2">
                                <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="form-label" for="email">Email</label>
                                        <input type="email" class="form-control password-input" id="email"
                                               name="email" value="{{ old('email') }}" placeholder="Enter Email">
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <button class="btn btn-primary w-100" type="submit" id="submitBtn">Send Reset
                                            Link
                                        </button>
                                    </div>
                                </form><!-- end form -->
                            </div>
                            <div class="mt-4 text-center">
                                <p class="mb-0">Wait, I remember my password...
                                    <a href="{{ route('login') }}"
                                       class="fw-semibold text-primary text-decoration-underline">
                                        Click here
                                    </a>
                                </p>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div>
                <!--end col-->
            </div>
            <!--end row-->
        </div>
    </div>
@endsection
@section('page-script')
    <script>
        $(document).ready(function () {
            $('#forgotPasswordForm').on('submit', function (e) {
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
                        notify('success', 'Password reset link sent successfully');
                        $('.infoBox').addClass('d-none');
                        $('#forgotPasswordForm')[0].reset();
                    },
                    error: function (xhr, status, error) {
                        if (xhr.status == 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                notify('error', value);
                                let input = $('[name="' + key + '"]');
                                input.addClass('is-invalid');
                                input.next('.invalid-feedback').text(value);
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
                        $('#submitBtn').html('Send Reset Link');
                    }
                });
            })
        });
    </script>
@endsection

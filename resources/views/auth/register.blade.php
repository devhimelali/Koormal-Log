@extends('layouts.auth')
@section('title', 'Register')
@section('content')
    <div class="col-lg-5">
        <div class="mb-0 card">
            <div class="row g-0 align-items-center">
                <div class="mx-auto col-xxl-12">
                    <div class="mb-0 border-0 shadow-none card">
                        <div class="card-body p-sm-4 m-lg-4">
                            <div class="text-center">
                                <h5 class="fs-3xl">Register</h5>
                                <p class="text-muted">Create a new account by filling out the form below.</p>
                            </div>
                            <div class="p-2 mt-4">
                                <form action="{{ route('register') }}" method="post" id="registerForm">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name <span
                                                class="text-danger">*</span></label>
                                        <div class="position-relative ">
                                            <input type="text" class="form-control password-input" id="name"
                                                   placeholder="Ex: John Doe" name="name" value="{{ old('name') }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <div class="position-relative ">
                                            <input type="email" class="form-control password-input" id="username"
                                                   placeholder="Ex: user@example.com" name="email"
                                                   value="{{ old('email') }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="password">Password <span
                                                class="text-danger">*</span></label>
                                        <div class="mb-3 position-relative auth-pass-inputgroup">
                                            <input type="password" class="form-control pe-5 password-input "
                                                   placeholder="Enter password" id="password" name="password">
                                            <button
                                                class="top-0 btn btn-link position-absolute end-0 text-decoration-none text-muted password-addon"
                                                type="button" id="password-addon"><i
                                                    class="align-middle ri-eye-fill"></i></button>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="password-confirmation">Confirm Password <span
                                                class="text-danger">*</span></label>
                                        <div class="mb-3 position-relative auth-pass-inputgroup">
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

                                    <div class="mt-4">
                                        <button class="btn btn-primary w-100" type="submit" id="submitBtn">
                                            Sign Up
                                        </button>
                                    </div>
                                </form>

                                <div class="mt-4 text-center">
                                    <p class="mb-0">Don't have an account ?
                                        <a href="{{ route('login') }}"
                                           class="fw-semibold text-secondary text-decoration-underline">
                                            SignIn
                                        </a>
                                    </p>
                                </div>
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
            $('#registerForm').on('submit', function (e) {
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
                        notify('success', 'Account created successfully');
                        setTimeout(() => {
                            window.location.href = "{{route('redirect')}}";
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

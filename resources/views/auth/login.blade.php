@extends('layouts.auth')
@section('title', 'Login')
@section('content')
    <div class="col-lg-5">
        <div class="card mb-0">
            <div class="row g-0 align-items-center">
                <!--end col-->
                <div class="col-xxl-12 mx-auto">
                    <div class="card mb-0 border-0 shadow-none">
                        <div class="card-body p-sm-5">
                            <div class="text-center">
                                <h5 class="fs-3xl">Welcome Back</h5>
                                <p class="text-muted">Sign in to continue to {{env('APP_NAME')}}</p>
                            </div>
                            <div class="p-2 mt-2">
                                <form action="{{route('login')}}" method="post" id="loginForm">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <div class="position-relative ">
                                            <input type="email" class="form-control  password-input"
                                                   name="email" id="email" placeholder="Enter email">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="float-end">
                                            <a href="{{route('password.request')}}" class="text-muted">Forgot
                                                password?</a>
                                        </div>
                                        <label class="form-label" for="password-input">Password <span
                                                class="text-danger">*</span></label>
                                        <div class="position-relative auth-pass-inputgroup mb-3">
                                            <input type="password" class="form-control pe-5 password-input "
                                                   placeholder="Enter password" name="password" id="password-input">
                                            <button
                                                class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                type="button" id="password-addon"><i
                                                    class="ri-eye-fill align-middle"></i></button>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember"
                                               id="remember">
                                        <label class="form-check-label" for="remember">Remember
                                            me</label>
                                    </div>

                                    <div class="mt-4">
                                        <button class="btn btn-primary w-100" type="submit" id="submitBtn">Sign In
                                        </button>
                                    </div>
                                </form>

                                <div class="text-center mt-4">
                                    <p class="mb-0">Don't have an account ?
                                        <a href="{{route('register')}}"
                                           class="fw-semibold text-secondary text-decoration-underline">
                                            SignUp</a>
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
            $('#loginForm').on('submit', function (e) {
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
                        console.log(response);
                        notify('success', 'Logged in successfully');
                        if (response.two_factor) {
                            setTimeout(() => {
                                window.location.href = "{{ route('two-factor.login') }}";
                            }, 1000);
                        } else {
                            setTimeout(() => {
                                window.location.href = "{{ route('redirect') }}";
                            }, 1000);
                        }
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
                        $('#submitBtn').html('Sign In');
                    }
                });
            })
        });
    </script>
@endsection

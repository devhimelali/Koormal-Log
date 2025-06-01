@extends('layouts.auth')
@section('title', 'Verify Email')
@section('content')
    <div class="col-lg-5">
        <div class="mb-0 card">
            <div class="row g-0 align-items-center">
                <!--end col-->
                <div class="mx-auto col-xxl-12">
                    <div class="mb-0 border-0 shadow-none card">
                        <div class="card-body p-sm-2 m-lg-4">
                            <div class="mt-2 text-center">
                                <h5 class="fs-3xl">Verify Email</h5>
                                <div class="pb-4">
                                    <img src="{{ asset('assets/images/auth/email.png') }}" alt="" class="avatar-md">
                                </div>
                            </div>

                            <div class="mx-2 mb-2 text-center border-0 alert alert-warning infoBox" role="alert">
                                Before continuing, could you verify your email address by clicking on the link we just
                                emailed to you? If you didn't receive the email, we will gladly send you another.
                            </div>
                            <div class="p-2">
                                <form method="POST" action="{{ route('verification.send') }}" id="verifyEmailForm">
                                    @csrf

                                    <div class="mt-4 text-center">
                                        <button class="btn btn-primary w-100" type="submit" id="submitBtn">
                                            Resend Verification Email
                                        </button>
                                    </div>
                                </form><!-- end form -->
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
            $('#verifyEmailForm').on('submit', function (e) {
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
                        $('#submitBtn').attr('disabled', true);
                        $('#submitBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
                    },
                    success: function (response) {
                        notify('success', 'A new verification link has been sent to the email address you provided during registration.');
                    },
                    error: function (xhr, status, error) {
                        if (xhr.status == 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                notify('error', value);
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
                        $('#submitBtn').html('Resend Verification Email');
                    }
                });
            })
        });
    </script>
@endsection

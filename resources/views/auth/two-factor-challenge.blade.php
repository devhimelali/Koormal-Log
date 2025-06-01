@extends('layouts.auth')
@section('title', 'Two Factor Authentication')
@section('content')
    <div class="col-lg-5">
        <div class="card mb-0">
            <div class="row g-0 align-items-center">
                <div class="col-xxl-12 mx-auto">
                    <div class="card mb-0 border-0 shadow-none">
                        <div class="card-body m-lg-4">
                            <div class="p-2">
                                <div class="text-muted text-center mb-2 pb-2 mx-lg-3">
                                    <div class="mb-4">
                                        <img src="{{asset('assets/images/auth/permission.png')}}" alt=""
                                             class="avatar-md">
                                    </div>
                                    <h4>Two Factor Authentication</h4>
                                </div>

                                <form action="{{ route('two-factor.login') }}" method="POST" id="confirmPasswordForm">
                                    @csrf
                                    <div class="row">
                                        <div class="mb-3">
                                            <label class="form-label" for="code">Code</label>
                                            <input type="text" class="form-control password-input" name="code"
                                                   id="code"
                                                   placeholder="Enter your 2FA code">
                                            @error('code')
                                            <span class="text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary w-100" id="submitBtn">Continue
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

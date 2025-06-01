@extends('layouts.app')
@section('title', 'Profile')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Profiles</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('redirect') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">User Profile</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            @include('profile.partials.update-profile-information-form')
        </div>
        <div class="col-md-6">
            @include('profile.partials.update-password-form')
        </div>
        <div class="col-md-6">
            @include('profile.partials.two-factor-authentication-form')
        </div>
    </div>
@endsection
@section('page-script')
    <script src="{{asset('assets/js/pages/password-addon.init.js')}}"></script>
    <script>
        @if(session('status') == 'two-factor-authentication-enabled')
        notify('success', "Two factor authentication has been enabled.");
        @elseif(session('status') == 'two-factor-authentication-disabled')
        notify('error', "Two factor authentication has been disabled.");
        @endif
    </script>
@endsection

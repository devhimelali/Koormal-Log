<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-2">Two Factor Authentication</h4>
        <p class="text-muted">Add additional security to your account using two factor authentication.</p>
    </div>
    <div class="card-body">
        <div class="text-muted">
            <p>
                When two factor authentication is enabled, you will be prompted for a secure, random token during
                authentication. You may retrieve this token from your phone's Google Authenticator application.
            </p>
        </div>
        @if(auth()->user()->two_factor_confirmed_at)
            <ul>
                @foreach(auth()->user()->recoveryCodes() as $code)
                    <li>{{$code}}</li>
                @endforeach
            </ul>
            <form action="{{route('two-factor.disable')}}" method="post">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Disable</button>
            </form>
        @elseif(auth()->user()->two_factor_secret)
            {!! auth()->user()->twoFactorQrCodeSvg() !!}
            <form action="{{route('two-factor.confirm')}}" method="post">
                @csrf
                <div class="mb-3 mt-3">
                    <label for="code" class="form-label">Code</label>
                    <input type="text" class="form-control" name="code" id="code">
                </div>
                <button type="submit" class="btn btn-warning">Confirm</button>
            </form>
        @else
            <form action="{{route('two-factor.enable')}}" method="post">
                @csrf
                <button type="submit" class="btn btn-secondary">Enable</button>
            </form>
        @endif
    </div>
</div>


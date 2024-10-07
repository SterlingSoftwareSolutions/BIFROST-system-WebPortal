@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Set Up Two-Factor Authentication (2FA)</h2>

    <p>Scan the following QR code with Google Authenticator:</p>
    <div>
        {!! $QR_Image !!}
    </div>

    <p>Alternatively, you can enter the following key manually: <strong>{{ $secret }}</strong></p>

    <form method="POST" action="{{ route('2fa.enable') }}">
        @csrf
        <div>
            <label for="2fa_code">Enter the code displayed in your app:</label>
            <input type="text" name="2fa_code" required>
        </div>

        <button type="submit">Enable 2FA</button>
    </form>
</div>
@endsection

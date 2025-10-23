@extends('layouts.app')

@section('title', 'Email Verification Required')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>📧 Email Verification Required</h4>
                </div>

                <div class="card-body">
                    <div class="alert alert-warning">
                        <strong>⏳ Account Creation Pending</strong><br>
                        Your account will be created only after you verify your email address.
                    </div>

                    <div class="verification-info">
                        <h5>📬 Check Your Email</h5>
                        <p>We've sent a verification email with a secure link. Please:</p>
                        <ol>
                            <li>Check your inbox (and spam/junk folder)</li>
                            <li>Click the verification link in the email</li>
                            <li>Your account will be automatically created and you'll be logged in</li>
                        </ol>
                    </div>

                    <div class="alert alert-info">
                        <strong>🔐 Security Notice:</strong> The verification link expires in 24 hours. If it expires, you'll need to register again.
                    </div>

                    <div class="resend-section">
                        <h6>Didn't receive the email?</h6>
                        <form method="POST" action="{{ route('verification.resend.pending') }}" style="display: inline;">
                            @csrf
                            <div class="form-group">
                                <label for="email">Enter your email address:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                📤 Resend Verification Email
                            </button>
                        </form>
                    </div>

                    <div class="mt-4">
                        <h6>What happens after verification?</h6>
                        <ul class="list-unstyled">
                            <li>✅ Your account will be created automatically</li>
                            <li>✅ You'll be logged in immediately</li>
                            <li>✅ Full access to all studio services</li>
                            <li>✅ Booking system and instrument rentals</li>
                            <li>✅ Email notifications for your bookings</li>
                        </ul>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="{{ route('home') }}" class="btn btn-secondary">
                            🏠 Return to Home
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary ml-2">
                            📝 Register Again
                        </a>
                    </div>

                    <div class="mt-3">
                        <small class="text-muted">
                            <strong>Note:</strong> No account has been created yet. Your information is temporarily stored and will only become a real account after email verification.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.verification-info {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.verification-info h5 {
    color: #495057;
    margin-bottom: 15px;
}

.verification-info ol {
    margin-bottom: 0;
}

.verification-info ol li {
    margin-bottom: 8px;
    padding-left: 5px;
}

.resend-section {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.resend-section h6 {
    color: #856404;
    margin-bottom: 15px;
}

.list-unstyled li {
    padding: 5px 0;
    border-bottom: 1px solid #f0f0f0;
}

.list-unstyled li:last-child {
    border-bottom: none;
}
</style>
@endsection
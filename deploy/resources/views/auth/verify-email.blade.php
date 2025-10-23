@extends('layouts.app')

@section('title', 'Verify Your Email')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>📧 Verify Your Email Address</h4>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Almost there!</strong> Before you can access all features of Lemon Hub Studio, please verify your email address.
                    </div>

                    <p>We've sent a verification email to <strong>{{ auth()->user()->email }}</strong>.</p>
                    
                    <p>Please check your inbox and click the verification link to complete your registration.</p>

                    <div class="verification-actions">
                        <form method="POST" action="{{ route('verification.send') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                📤 Resend Verification Email
                            </button>
                        </form>

                        <a href="{{ route('home') }}" class="btn btn-secondary ml-2">
                            🏠 Return to Home
                        </a>
                    </div>

                    <div class="mt-4">
                        <h6>What happens after verification?</h6>
                        <ul>
                            <li>✅ Full access to booking system</li>
                            <li>✅ Instrument rental services</li>
                            <li>✅ Account management features</li>
                            <li>✅ Email notifications for bookings</li>
                        </ul>
                    </div>

                    <div class="mt-3">
                        <small class="text-muted">
                            <strong>Note:</strong> If you don't see the email in your inbox, please check your spam/junk folder. 
                            The verification link expires after 60 minutes for security reasons.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.card-header {
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    color: white;
    border-radius: 10px 10px 0 0;
}

.verification-actions {
    margin: 20px 0;
}

.btn {
    border-radius: 5px;
    padding: 10px 20px;
    font-weight: 500;
}

.btn-primary {
    background-color: #ff6b35;
    border-color: #ff6b35;
}

.btn-primary:hover {
    background-color: #e55a2b;
    border-color: #e55a2b;
}

.alert-info {
    border-left: 4px solid #17a2b8;
}

ul li {
    margin-bottom: 5px;
}
</style>
@endsection
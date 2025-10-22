<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Lemon Hub Studio</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: url('/images/studio-bg.jpg') no-repeat center center/cover; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; position: relative; }
        body::before { content: ""; position: absolute; inset: 0; background: rgba(0,0,0,0.7); z-index: 1; }
        .container { background: #fff; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); width: 100%; max-width: 500px; overflow: hidden; position: relative; z-index: 2; }
        .header { background: linear-gradient(135deg, #FFD700 0%, #E0BC3A 50%, #F4C200 100%); color: #1a1a1a; padding: 32px; text-align: center; position: relative; }
        .header h1 { font-size: 24px; font-weight: 600; margin-bottom: 8px; }
        .header p { opacity: 0.9; }
        .back-button { position: absolute; top: 12px; right: 12px; background: rgba(255,255,255,0.9); color: #1a1a1a; border: none; border-radius: 8px; padding: 8px 12px; font-size: 14px; cursor: pointer; text-decoration: none; box-shadow: 0 2px 6px rgba(0,0,0,0.15); }
        .back-button:hover { background: #fff; }

        /* Back button in body (same style as login) */
        .page-back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            background: linear-gradient(135deg, #FFD700 0%, #F4C200 50%, #E6B800 100%);
            color: #1a1a1a;
            border: none;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            z-index: 9999;
        }
        .page-back-button:hover {
            background: linear-gradient(135deg, #F4C200 0%, #E6B800 50%, #D4A700 100%);
        }

        .content { padding: 40px; }
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 16px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #333; }
        .form-group input { width: 100%; padding: 14px; border: 2px solid #e1e5e9; border-radius: 10px; font-size: 16px; background: #f8f9fa; }
        .form-group input:focus { outline: none; border-color: #FFD700; background: #fff; }
        .btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #FFD700 0%, #F4C200 50%, #E6B800 100%); color: #1a1a1a; border: none; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(255,215,0,0.25); }
        .btn:hover { transform: translateY(-2px); background: linear-gradient(135deg, #F4C200 0%, #E6B800 50%, #D4A700 100%); }
    </style>
</head>
<body>
<a href="{{ route('login') }}" class="page-back-button">Back</a>
<div class="container">
    <div class="header">
        <h1>Reset Password</h1>
        <p>Set a new password for your account.</p>
    </div>
    <div class="content">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email', $email ?? '') }}" required>
            </div>
            <div class="form-group">
                <label for="password">New Password</label>
                <div class="password-wrapper" style="position: relative;">
                    <input type="password" id="password" name="password" required pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$" title="At least 8 characters with uppercase, lowercase, number, and special character." style="width: 100%; padding-right: 40px;">
                    <button type="button" id="togglePassword" aria-label="Show password" class="toggle-visibility" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: transparent; border: none; cursor: pointer; padding: 4px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </button>
                </div>
                <div style="color:#666; font-size:13px; margin-top:8px;">Password must be at least 8 characters and include uppercase, lowercase, number, and special character</div>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <div class="password-wrapper" style="position: relative;">
                    <input type="password" id="password_confirmation" name="password_confirmation" required style="width: 100%; padding-right: 40px;">
                    <button type="button" id="toggleConfirmPassword" aria-label="Show password" class="toggle-visibility" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: transparent; border: none; cursor: pointer; padding: 4px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn">Update Password</button>
        </form>
    </div>
</div>
</body>
</html>
<script>
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const togglePasswordBtn = document.getElementById('togglePassword');
    const toggleConfirmPasswordBtn = document.getElementById('toggleConfirmPassword');

    const eyeIcon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
    const eyeOffIcon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.53 21.53 0 0 1 5.06-6.28"></path><path d="M9.88 9.88A3 3 0 0 0 12 15a3 3 0 0 0 2.12-.88"></path><path d="M1 1l22 22"></path></svg>';

    function attachToggle(btn, input) {
        if (!btn) return;
        btn.addEventListener('click', () => {
            const showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
            btn.innerHTML = showing ? eyeIcon : eyeOffIcon;
        });
    }

    attachToggle(togglePasswordBtn, passwordInput);
    attachToggle(toggleConfirmPasswordBtn, confirmPasswordInput);
</script>
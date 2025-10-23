<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Lemon Hub Studio</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('/images/studio-bg.jpg') no-repeat center center/cover;
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            padding: 20px; position: relative;
        }
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
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            z-index: 9999;
        }
        .page-back-button:hover {
            background: linear-gradient(135deg, #F4C200 0%, #E6B800 50%, #D4A700 100%);
        }

        .content { padding: 40px; }
        .description { color: #666; font-size: 14px; margin-bottom: 20px; text-align: center; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #333; }
        .form-group input { width: 100%; padding: 14px; border: 2px solid #e1e5e9; border-radius: 10px; font-size: 16px; background: #f8f9fa; }
        .form-group input:focus { outline: none; border-color: #FFD700; background: #fff; }
        .btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #FFD700 0%, #F4C200 50%, #E6B800 100%); color: #1a1a1a; border: none; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(255,215,0,0.25); }
        .btn:hover { transform: translateY(-2px); background: linear-gradient(135deg, #F4C200 0%, #E6B800 50%, #D4A700 100%); }
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 16px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
<a href="{{ route('login') }}" class="page-back-button">Back</a>
<div class="container">
    <div class="header">
        <h1>Forgot Password</h1>
        <p>Enter your email to receive a reset link.</p>
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

        <div class="description">We will email you a link to reset your password.</div>
        <div class="description">Note: Your new password must include uppercase, lowercase, number, and special character.</div>
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>
            <button type="submit" class="btn">Send Reset Link</button>
        </form>
    </div>
</div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Lemon Hub Studio</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('/images/studio-bg.jpg') no-repeat center center/cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
            animation: slideUp 0.6s ease-out;
            position: relative;
            z-index: 2;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, #FFD700 0%, #E0BC3A 50%, #F4C200 100%);
            color: #1a1a1a;
            padding: 40px;
            text-align: center;
        }

        .login-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .login-header p {
            opacity: 0.9;
            font-size: 16px;
        }

        .login-content {
            padding: 50px 40px;
        }

        .login-description {
            margin-bottom: 30px;
            color: #666;
            font-size: 14px;
            line-height: 1.5;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input:focus {
            outline: none;
            border-color: #FFD700;
            background: white;
        }

        .form-group input.error {
            border-color: #e74c3c;
        }

        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            font-size: 18px;
            user-select: none;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #FFD700;
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .remember-me input {
            margin-right: 10px;
            width: auto;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #FFD700 0%, #F4C200 50%, #E6B800 100%);
            color: #1a1a1a;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(255, 215, 0, 0.25);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #F4C200 0%, #E6B800 50%, #D4A700 100%);
            box-shadow: 0 8px 24px rgba(255, 215, 0, 0.35);
        }

        .register-link {
            text-align: center;
            color: #666;
        }

        .register-link a {
            color: #E6B800;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 10px;
                max-width: 95%;
            }
            
            .login-content {
                padding: 40px 25px;
            }

            .login-header {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>🍋 Lemon Hub Studio</h1>
            <p>Welcome back! Please sign in to your account</p>
        </div>
        
        <div class="login-content">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="login-description">
                Sign in with your credentials. The system will automatically detect if you're a user or administrator.
            </div>
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" required>
                        <span class="password-toggle" onclick="togglePassword('password')">👁</span>
                    </div>
                </div>
                
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                
                <button type="submit" class="login-btn">Sign In</button>
            </form>

            <div class="register-link">
                Don't have an account? <a href="{{ route('register') }}">Create one here</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = passwordInput.nextElementSibling;
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = '👁';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = '👁';
            }
        }

        // Clear form errors on input
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('error');
            });
        });
    </script>
</body>
</html>
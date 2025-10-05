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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            animation: slideUp 0.6s ease-out;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
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
            padding: 40px 30px;
        }

        .tab-container {
            display: flex;
            margin-bottom: 30px;
            border-radius: 10px;
            background: #f8f9fa;
            padding: 5px;
        }

        .tab-button {
            flex: 1;
            padding: 12px;
            background: transparent;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            color: #666;
        }

        .tab-button.active {
            background: white;
            color: #667eea;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
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
            border-color: #667eea;
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
            color: #667eea;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
            margin-bottom: 20px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
        }

        .register-link {
            text-align: center;
            color: #666;
        }

        .register-link a {
            color: #667eea;
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

        .login-description {
            margin-bottom: 20px;
            color: #666;
            font-size: 14px;
            line-height: 1.5;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 10px;
            }
            
            .login-content {
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

            <div class="tab-container">
                <button class="tab-button active" onclick="switchTab('user')">User Login</button>
                <button class="tab-button" onclick="switchTab('admin')">Admin Login</button>
            </div>

            <div class="tab-content">
                <!-- User Login Tab -->
                <div id="user-tab" class="tab-panel active">
                    <div class="login-description">
                        Sign in to access your bookings, manage your account, and book studio sessions.
                    </div>
                    
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <input type="hidden" name="login_type" value="user">
                        
                        <div class="form-group">
                            <label for="user_email">Email Address</label>
                            <input type="email" id="user_email" name="email" value="{{ old('email') }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="user_password">Password</label>
                            <div class="password-container">
                                <input type="password" id="user_password" name="password" required>
                                <span class="password-toggle" onclick="togglePassword('user_password')">👁</span>
                            </div>
                        </div>
                        
                        <div class="remember-me">
                            <input type="checkbox" id="user_remember" name="remember">
                            <label for="user_remember">Remember me</label>
                        </div>
                        
                        <button type="submit" class="login-btn">Sign In</button>
                    </form>
                </div>
                
                <!-- Admin Login Tab -->
                <div id="admin-tab" class="tab-panel">
                    <div class="login-description">
                        Sign in as an administrator to manage bookings, approve requests, and access admin dashboard.
                    </div>
                    
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <input type="hidden" name="login_type" value="admin">
                        
                        <div class="form-group">
                            <label for="admin_email">Email Address</label>
                            <input type="email" id="admin_email" name="email" value="{{ old('email') }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="admin_password">Password</label>
                            <div class="password-container">
                                <input type="password" id="admin_password" name="password" required>
                                <span class="password-toggle" onclick="togglePassword('admin_password')">👁</span>
                            </div>
                        </div>
                        
                        <div class="remember-me">
                            <input type="checkbox" id="admin_remember" name="remember">
                            <label for="admin_remember">Remember me</label>
                        </div>
                        
                        <button type="submit" class="login-btn">Sign In as Admin</button>
                    </form>
                </div>
            </div>

            <div class="register-link">
                Don't have an account? <a href="{{ route('register') }}">Create one here</a>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabType) {
            // Remove active class from all tabs and panels
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding panel
            event.target.classList.add('active');
            document.getElementById(tabType + '-tab').classList.add('active');
        }

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
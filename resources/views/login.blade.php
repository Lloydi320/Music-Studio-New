<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Lemon Hub Studio</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        :root {
            --studio-bg-image: url('{{ asset('images/studio.jpg') }}');
        }
        
        /* stylelint-disable */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
            background-image: var(--studio-bg-image);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            width: 100%;
            max-width: 800px;
            min-height: 500px;
            position: relative;
            z-index: 1;
            margin: 20px;
            display: flex;
        }
        
        .login-left {
            background: linear-gradient(135deg, #FFD700, #FFA500);
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            text-align: center;
            color: #333;
        }
        
        .login-left img {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }
        
        .login-left h1 {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .login-left p {
            font-size: 16px;
            font-style: italic;
            opacity: 0.8;
        }
        
        .login-right {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h2 {
            color: #333;
            margin: 0 0 5px;
            font-size: 28px;
            font-weight: 600;
        }
        
        .login-header p {
            color: #888;
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .login-tabs {
            display: flex;
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
        }
        
        .tab-button {
            flex: 1;
            padding: 15px 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #666;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-bottom: 2px solid transparent;
        }
        
        .tab-button.active {
            color: #FFD700;
            border-bottom-color: #FFD700;
        }
        
        .tab-content {
            flex: 1;
        }
        
        .tab-panel {
            display: none;
        }
        
        .tab-panel.active {
            display: block;
        }
        
        .login-description {
            text-align: center;
            margin-bottom: 25px;
            color: #666;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .google-login-btn {
            width: 100%;
            padding: 15px;
            background: #4285f4;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }
        
        .google-login-btn:hover {
            background: #3367d6;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(66, 133, 244, 0.3);
        }
        
        .google-icon {
            width: 18px;
            height: 18px;
        }
        
        .back-home {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
        }
        
        .back-home a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .back-home a:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 400px;
                min-height: auto;
            }
            
            .login-left {
                padding: 30px 20px;
            }
            
            .login-left h1 {
                font-size: 24px;
            }
            
            .login-right {
                padding: 30px 20px;
            }
        }
        
        @media (max-width: 480px) {
            .login-container {
                margin: 10px;
            }
            
            .tab-button {
                font-size: 13px;
                padding: 12px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="back-home">
        <a href="/">
            <span>←</span>
            Back to Home
        </a>
    </div>
    
    <div class="login-container">
        <!-- Left Panel - Branding -->
        <div class="login-left">
            <img src="{{ asset('images/studio-logo.png') }}" alt="Lemon Hub Studio Logo">
            <h1>LEMON<br>HUB STUDIO</h1>
            <p>Professional Music Experience</p>
        </div>
        
        <!-- Right Panel - Login Form -->
        <div class="login-right">
            <div class="login-header">
A                <h2 id="header-title">Welcome</h2>
                <p id="header-subtitle">Please log in your account</p>
            </div>
            
            <div class="login-tabs">
                <button class="tab-button active" onclick="switchTab('user')">
                    👤 User Login
                </button>
                <button class="tab-button" onclick="switchTab('admin')">
                    🔐 Admin Login
                </button>
            </div>
            
            <div class="tab-content">
                <!-- User Login Tab -->
                <div id="user-tab" class="tab-panel active">
                    <div class="login-description">
                        Sign in as a regular user to book studio sessions, rent instruments, and leave feedback.
                    </div>
                    
                    <a href="{{ route('google.login', ['type' => 'user']) }}" class="google-login-btn">
                        <svg class="google-icon" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Continue with Google
                    </a>
                </div>
                
                <!-- Admin Login Tab -->
                <div id="admin-tab" class="tab-panel">
                    <div class="login-description">
                        Sign in as an administrator to manage bookings, approve requests, and access admin dashboard.
                    </div>
                    
                    <a href="{{ route('google.login', ['type' => 'admin']) }}" class="google-login-btn">
                        <svg class="google-icon" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Continue with Google
                    </a>
                </div>
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
            
            // Update header text based on selected tab
            const headerTitle = document.getElementById('header-title');
            const headerSubtitle = document.getElementById('header-subtitle');
            
            if (tabType === 'admin') {
                headerTitle.textContent = 'Welcome';
                headerSubtitle.textContent = 'Please log in to admin dashboard';
            } else {
                headerTitle.textContent = 'Welcome';
                headerSubtitle.textContent = 'Please log in  your account';
            }
        }
        
        function switchToNextTab(nextTabType) {
            // Add a small delay to show the tab switch before redirect
            setTimeout(() => {
                switchTab(nextTabType);
            }, 100);
        }
    </script>
</body>
</html>
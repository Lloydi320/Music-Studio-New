<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Feedback | Lemon Hub Studio</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #ffd700;
      --secondary-color: #dbb411;
      --accent-color: #f4d03f;
      --success-color: #28a745;
      --warning-color: #ffc107;
      --error-color: #dc3545;
      --text-primary: #ffffff;
      --text-secondary: #cccccc;
      --bg-primary: #1a1a1a;
      --bg-secondary: #111;
      --border-color: #333;
      --shadow-sm: 0 1px 2px 0 rgb(255 215 0 / 0.1);
      --shadow-md: 0 4px 6px -1px rgb(255 215 0 / 0.15), 0 2px 4px -2px rgb(255 215 0 / 0.1);
      --shadow-lg: 0 10px 15px -3px rgb(255 215 0 / 0.2), 0 4px 6px -4px rgb(255 215 0 / 0.15);
      --shadow-xl: 0 20px 25px -5px rgb(255 215 0 / 0.25), 0 8px 10px -6px rgb(255 215 0 / 0.2);
      --radius-sm: 0.375rem;
      --radius-md: 0.5rem;
      --radius-lg: 0.75rem;
      --radius-xl: 1rem;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #111 0%, #000 100%);
      min-height: 100vh;
      color: var(--text-primary);
    }
    
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('{{ asset('images/studio-bg-original.jpg') }}') no-repeat center center/cover
      filter: blur(8px);
      z-index: -2;
    }
    
    body::after {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.8);
      backdrop-filter: blur(10px);
      z-index: -1;
    }
    
    .modern-feedback-container {
      margin-top: 80px;
      padding: 2rem;
      min-height: calc(100vh - 80px);
      max-width: 95vw;
      width: 100%;
      margin-left: auto;
      margin-right: auto;
      backdrop-filter: blur(5px);
      background: rgba(0, 0, 0, 0.3);
      border-radius: 15px;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .feedback-header {
      text-align: center;
      margin-bottom: 3rem;
      color: var(--text-primary);
      position: relative;
      padding: 2rem;
      border-radius: var(--radius-xl);
      background: 
        linear-gradient(135deg, 
          rgba(255, 215, 0, 0.05) 0%, 
          rgba(0, 100, 200, 0.08) 25%, 
          rgba(255, 140, 66, 0.06) 50%, 
          rgba(138, 43, 226, 0.04) 75%, 
          transparent 100%
        ),
        rgba(42, 42, 42, 0.7);
      border: 1px solid rgba(255, 215, 0, 0.2);
      box-shadow: var(--shadow-lg);
      backdrop-filter: blur(15px);
    }
    
    .feedback-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(45deg, 
        rgba(0, 100, 200, 0.1) 0%, 
        transparent 25%, 
        rgba(255, 140, 66, 0.08) 50%, 
        transparent 75%, 
        rgba(138, 43, 226, 0.06) 100%
      );
      border-radius: var(--radius-xl);
      z-index: -1;
      opacity: 0.7;
    }
    
    .feedback-header h1 {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 1rem;
      color: #ffd700;
      position: relative;
      text-shadow: 0 0 30px rgba(255, 215, 0, 0.3);
    }
    
    .feedback-header h1::before {
      content: '';
      position: absolute;
      top: -15px;
      left: -15px;
      right: -15px;
      bottom: -15px;
      background: radial-gradient(ellipse at center, 
        rgba(255, 215, 0, 0.1) 0%, 
        rgba(0, 100, 200, 0.08) 30%, 
        rgba(255, 140, 66, 0.06) 60%, 
        transparent 100%
      );
      border-radius: var(--radius-lg);
      z-index: -1;
      opacity: 0.6;
      filter: blur(10px);
    }
    
    .feedback-header p {
      font-size: 1.2rem;
      opacity: 0.9;
      max-width: 600px;
      margin: 0 auto;
      background: linear-gradient(90deg, 
        var(--text-primary) 0%, 
        #cccccc 50%, 
        var(--text-primary) 100%
      );
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .feedback-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
      align-items: start;
    }
    
    @media (max-width: 768px) {
      .feedback-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
      
      .feedback-header {
        margin-bottom: 2rem;
        padding: 1.5rem;
      }
      
      .feedback-header h1 {
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
      }
      
      .feedback-header p {
        font-size: 1rem;
      }
      
      .modern-feedback-container {
        padding: 0.75rem;
        max-width: 100vw;
        margin-top: 70px;
        border-radius: 10px;
      }
      
      .card-content {
        padding: 1.5rem;
        max-height: 60vh;
      }
      
      .card-header {
        padding: 1rem 1.5rem;
      }
      
      .form-group {
        margin-bottom: 1.25rem;
      }
      
      .form-input {
        padding: 0.875rem 1rem;
        font-size: 16px; /* Prevents zoom on iOS */
        border-radius: 8px;
      }
      
      .form-textarea {
        min-height: 100px;
        resize: vertical;
      }
      
      .submit-btn {
        padding: 1rem 1.5rem;
        font-size: 1.1rem;
        border-radius: 8px;
        margin-top: 0.5rem;
      }
      
      .card-title {
        font-size: 1.25rem;
        flex-wrap: wrap;
        gap: 0.5rem;
      }
      
      .refresh-btn {
         padding: 0.5rem 0.75rem;
         font-size: 0.9rem;
         margin-left: 0 !important;
         margin-top: 0.5rem;
       }
       
       .rating-input {
         font-size: 2.5rem;
         gap: 0.25rem;
         justify-content: center;
         margin: 1rem 0;
       }
       
       .rating-input span {
         padding: 0.25rem;
         min-width: 44px; /* Minimum touch target size */
         min-height: 44px;
         display: flex;
         align-items: center;
         justify-content: center;
         border-radius: 4px;
         transition: all 0.2s ease;
       }
       
       .rating-input span:hover,
       .rating-input span:active {
         background: rgba(255, 215, 0, 0.2);
         transform: scale(1.1);
       }
       
       .rating-text {
         text-align: center;
         font-size: 0.9rem;
       }
     }
    
    @media (min-width: 1400px) {
      .feedback-grid {
        grid-template-columns: 1.2fr 0.8fr;
        gap: 3rem;
      }
      
      .modern-feedback-container {
        max-width: 98vw;
        padding: 3rem;
      }
    }
    
    .modern-card {
      background: rgba(26, 26, 26, 0.95);
      backdrop-filter: blur(20px);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-xl);
      border: 1px solid rgba(255, 215, 0, 0.2);
      overflow: hidden;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .modern-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 25px 50px -12px rgb(255 215 0 / 0.25);
      border-color: var(--primary-color);
      background: rgba(26, 26, 26, 0.98);
    }
    
    .card-header {
      padding: 1.5rem 2rem;
      border-bottom: 1px solid var(--border-color);
      background: linear-gradient(135deg, var(--bg-secondary), var(--bg-primary));
    }
    
    .card-title {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--text-primary);
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    
    .card-content {
      padding: 2rem;
      max-height: 70vh;
      overflow-y: auto;
    }
    
    .feedback-entry {
      background: rgba(0, 0, 0, 0.4);
      border-radius: var(--radius-lg);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      border: 1px solid rgba(255, 215, 0, 0.3);
      transition: all 0.2s ease;
      position: relative;
      overflow: hidden;
    }
    
    .feedback-entry::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 4px;
      height: 100%;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    }
    
    .feedback-entry:hover {
      transform: translateX(4px);
      box-shadow: var(--shadow-md);
      background: rgba(0, 0, 0, 0.6);
      border-color: var(--primary-color);
    }
    
    .feedback-meta {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 1rem;
    }
    
    .user-info {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    
    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      font-size: 0.9rem;
    }
    
    .user-details h4 {
      font-size: 1rem;
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 0.25rem;
    }
    
    .user-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
      padding: 0.25rem 0.5rem;
      border-radius: var(--radius-sm);
      font-size: 0.75rem;
      font-weight: 500;
    }
    
    .badge-authenticated {
      background: #dbeafe;
      color: #1e40af;
    }
    
    .badge-guest {
      background: #f3f4f6;
      color: #374151;
    }
    
    .rating-display {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 0.25rem;
    }
    
    .stars {
      font-size: 1.25rem;
      color: var(--accent-color);
    }
    
    .rating-text {
      font-size: 0.75rem;
      color: var(--text-secondary);
      font-weight: 500;
    }
    
    .comment-box {
      background: var(--bg-primary);
      border-radius: var(--radius-md);
      padding: 1rem;
      margin: 1rem 0;
      border-left: 3px solid var(--accent-color);
      font-style: italic;
      color: var(--text-secondary);
      line-height: 1.6;
    }
    
    .feedback-photo {
      margin-top: 1rem;
    }
    
    .feedback-photo img {
      width: 100%;
      max-width: 200px;
      border-radius: var(--radius-md);
      box-shadow: var(--shadow-md);
      cursor: pointer;
      transition: transform 0.2s ease;
    }
    
    .feedback-photo img:hover {
      transform: scale(1.05);
    }
    
    .feedback-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid var(--border-color);
    }
    
    .timestamp {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.875rem;
      color: var(--text-secondary);
    }
    
    .verified-badge {
      display: flex;
      align-items: center;
      gap: 0.25rem;
      padding: 0.25rem 0.5rem;
      background: #dcfce7;
      color: #166534;
      border-radius: var(--radius-sm);
      font-size: 0.75rem;
      font-weight: 500;
    }
    
    .form-group {
      margin-bottom: 1.5rem;
    }
    
    .form-label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--text-primary);
      font-size: 0.875rem;
    }
    
    .form-input {
      width: 100%;
      padding: 0.75rem 1rem;
      border: 2px solid var(--border-color);
      border-radius: var(--radius-md);
      font-size: 1rem;
      transition: all 0.2s ease;
      background: rgba(0, 0, 0, 0.3);
      color: var(--text-primary);
      font-family: inherit;
    }
    
    .form-input:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2);
      background: rgba(0, 0, 0, 0.5);
    }
    
    .form-input::placeholder {
      color: var(--text-secondary);
    }
    
    .form-textarea {
      resize: vertical;
      min-height: 120px;
      line-height: 1.6;
    }
    
    .rating-input {
      display: flex;
      gap: 0.5rem;
      font-size: 2rem;
      cursor: pointer;
      margin: 0.5rem 0;
    }
    
    .rating-input span {
      color: #d1d5db;
      transition: color 0.2s ease;
    }
    
    .rating-input span:hover,
    .rating-input span.active {
      color: var(--accent-color);
    }
    
    .submit-btn {
      width: 100%;
      padding: 0.875rem 1.5rem;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: #000;
      border: none;
      border-radius: var(--radius-md);
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }
    
    .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
      background: linear-gradient(135deg, var(--accent-color), var(--primary-color));
    }
    
    .submit-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }
    
    .refresh-btn {
      background: linear-gradient(135deg, var(--accent-color), #ffed4e);
      color: #000;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: var(--radius-md);
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .refresh-btn:hover {
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    }
    
    .loading-state, .empty-state {
      text-align: center;
      padding: 3rem 2rem;
      color: var(--text-secondary);
      background: rgba(0, 0, 0, 0.2);
      border-radius: var(--radius-lg);
      border: 1px solid rgba(255, 215, 0, 0.2);
    }
    
    .loading-icon, .empty-icon {
      font-size: 3rem;
      margin-bottom: 1rem;
      color: var(--primary-color);
    }
    
    .loading-icon {
      animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    
    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }
  </style>
</head>
<body class="feedback-page">

  <header class="navbar">
    <div class="logo">
      <img src="{{ asset('images/studio-logo.png') }}" alt="Lemon Hub Studio Logo">
      <span>LEMON HUB STUDIO</span>
    </div>
    
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle mobile menu">
      <span></span>
      <span></span>
      <span></span>
    </button>
    
    <nav class="nav-container">
      <ul class="nav-links">
        <li><a href="/">Home</a></li>
        <li><a href="/services">About Us & Our Services</a></li>
        <li><a href="#" id="contactLink">Contact</a></li>
        <li><a href="#" id="feedbackLink" class="active">Feedbacks</a></li>
        <li><a href="/map">Map</a></li>
        @if(Auth::check())
        <li><a href="#" id="rescheduleBookingLink">Rescheduling</a></li>
        @endif
        @if(Auth::check() && Auth::user()->isAdmin())
        <li><a href="/admin/calendar" style="color: #ff6b35; font-weight: bold;">📅 Admin Calendar</a></li>
        @endif
        @if(Auth::check())
        <li>
          <form action="/logout" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" style="background: none; border: none; color: #fff; padding: 15px 20px; font-size: 1.1rem; cursor: pointer; width: 100%; text-align: left; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
               Sign Out
            </button>
          </form>
        </li>
        @endif
      </ul>
    </nav>
    @if(Auth::check())
        <div class="modern-user-profile" id="userProfile">
            <div class="profile-trigger" onclick="toggleUserDropdown()">
                <div class="profile-avatar">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="User Avatar">
                    @else
                        <div class="avatar-placeholder">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    @if(Auth::user()->is_admin)
                        <div class="admin-indicator"></div>
                    @endif
                </div>
                <div class="profile-info">
                    <div class="profile-name">{{ Auth::user()->name }}</div>
                    @if(Auth::user()->is_admin)
                        <div class="profile-role">Admin</div>
                    @else
                        <div class="profile-role">Member</div>
                    @endif
                </div>
                <svg class="dropdown-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7 10l5 5 5-5z"/>
                </svg>
            </div>
            
            <div class="user-dropdown" id="userDropdown">
                <div class="dropdown-header">
                    <div class="dropdown-avatar">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="User Avatar">
                        @else
                            <div class="avatar-placeholder large">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="dropdown-user-info">
                        <h4>{{ Auth::user()->name }}</h4>
                        <p>{{ Auth::user()->email }}</p>
                        @if(Auth::user()->is_admin)
                            <span class="user-badge">Admin</span>
                        @endif
                    </div>
                </div>
                
                <div class="dropdown-menu">
                    @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                            </svg>
                            Admin Dashboard
                        </a>
                    @endif
                    
                    <a href="{{ route('home') }}" class="dropdown-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                        </svg>
                        Calendar
                    </a>
                    
                    <a href="{{ route('booking') }}" class="dropdown-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        Book Session
                    </a>
                    
                    <a href="{{ route('services') }}" class="dropdown-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        About Us & Services
                    </a>
                    
                    <a href="#" id="contactLink" class="dropdown-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                        Contact
                    </a>
                    
                    <a href="{{ route('feedback') }}" class="dropdown-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 12h-2v-2h2v2zm0-4h-2V6h2v4z"/>
                        </svg>
                        Feedback
                    </a>
                    
                    <div class="dropdown-divider"></div>
                    
                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button type="submit" class="dropdown-item logout-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.59L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <a href="{{ route('google.login') }}" class="login-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Login with Google
        </a>
    @endif
  </header>

  <main class="modern-feedback-container">
    <div class="feedback-header">
      <h1><i class="fas fa-comments"></i> Customer Feedback</h1>
      <p>Share your experience and help us improve our services. Your feedback matters to us!</p>
    </div>
    
    <div class="feedback-grid">
      <div class="modern-card">
        <div class="card-header">
          <div class="card-title">
            <i class="fas fa-star"></i>
            Recent Feedbacks
            <button id="refreshFeedbacks" class="refresh-btn" style="margin-left: auto;">
              <i class="fas fa-sync-alt"></i>
              Refresh
            </button>
          </div>
        </div>
        <div class="card-content">
          <div id="feedbackEntries">
            <div class="loading-state">
              <div class="loading-icon"><i class="fas fa-spinner fa-spin"></i></div>
              <p>Loading feedback from database...</p>
            </div>
          </div>
        </div>
      </div>

      <div class="modern-card">
        <div class="card-header">
          <div class="card-title">
            <i class="fas fa-edit"></i>
            Share Your Experience
          </div>
        </div>
        <div class="card-content">
          <form id="feedbackForm">
            <div class="form-group">
              <label for="name" class="form-label">
                <i class="fas fa-user"></i> Your Name
              </label>
              <input type="text" id="name" class="form-input" required 
                     @if(Auth::check()) value="{{ Auth::user()->name }}" @endif 
                     placeholder="Enter your full name" />
            </div>

            <div class="form-group">
              <label class="form-label">
                <i class="fas fa-star"></i> Rating
              </label>
              <div class="rating-input" id="ratingStars">
                <span data-value="1">★</span>
                <span data-value="2">★</span>
                <span data-value="3">★</span>
                <span data-value="4">★</span>
                <span data-value="5">★</span>
              </div>
              <small class="rating-text">Click to rate your experience</small>
            </div>

            <div class="form-group">
              <label for="comment" class="form-label">
                <i class="fas fa-comment"></i> Your Feedback
              </label>
              <textarea id="comment" class="form-input form-textarea" required
                        placeholder="Tell us about your experience with our services..."></textarea>
            </div>

            <div class="form-group">
              <label for="photo" class="form-label">
                <i class="fas fa-camera"></i> Upload Photo (Optional)
              </label>
              <input type="file" id="photo" class="form-input" accept="image/*" />
              <small class="rating-text">Share a photo from your experience</small>
            </div>

            <button type="submit" class="submit-btn">
              <i class="fas fa-paper-plane"></i>
              Submit Feedback
            </button>
          </form>
        </div>
      </div>
    </div>
  </main>

  <script>
    // Prevent multiple form submissions
    let isSubmitting = false;
    
    // Global variables for feedback form
    let selectedRating = 0;
    
    // Function to open photo modal
    function openPhotoModal(photoUrl) {
      const modal = document.createElement('div');
      modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10000;
        cursor: pointer;
      `;
      
      const modalImg = document.createElement('img');
      modalImg.src = photoUrl;
      modalImg.style.cssText = `
        max-width: 90%;
        max-height: 90%;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
      `;
      
      modal.appendChild(modalImg);
      document.body.appendChild(modal);
      
      modal.addEventListener('click', () => {
        document.body.removeChild(modal);
      });
    }

    // Function to load feedback from database
    function loadFeedbacks() {
      console.log('🔄 Loading feedback from database...');
      const container = document.getElementById('feedbackEntries');
      
      if (!container) {
        console.error('❌ Container element not found!');
        return;
      }
      
      // Show loading state
      container.innerHTML = `
        <div class="loading-state">
          <div class="loading-icon"><i class="fas fa-spinner fa-spin"></i></div>
          <p>Loading feedback from database...</p>
        </div>
      `;
      
      const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      
      fetch('/api/feedbacks', {
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': token
        }
      })
      .then(res => res.json())
      .then(data => {
        container.innerHTML = '';
        if (!data.feedbacks || !data.feedbacks.length) {
          container.innerHTML = `
            <div class="empty-state">
              <div class="empty-icon"><i class="fas fa-comments"></i></div>
              <p>No feedback shared yet.</p>
              <small>Be the first to share your experience!</small>
            </div>
          `;
          return;
        }
      
        data.feedbacks.forEach(feedback => {
          const entry = document.createElement('div');
          entry.className = 'feedback-entry';
          
          const stars = '★'.repeat(feedback.rating) + '☆'.repeat(5 - feedback.rating);
          
          const date = new Date(feedback.created_at);
          const formattedDate = date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
          });
          
          let photoHtml = '';
          if (feedback.photo_url) {
            photoHtml = `
              <div class="feedback-photo">
                <img src="${feedback.photo_url}" 
                     onclick="openPhotoModal('${feedback.photo_url}')" 
                     alt="Feedback photo" />
              </div>
            `;
          }
          
          const userInitial = feedback.name.charAt(0).toUpperCase();
          const badgeClass = feedback.user_type === 'Authenticated' ? 'badge-authenticated' : 'badge-guest';
          const badgeIcon = feedback.user_type === 'Authenticated' ? 'fas fa-user-check' : 'fas fa-user';
          
          entry.innerHTML = `
            <div class="feedback-meta">
              <div class="user-info">
                <div class="user-avatar">${userInitial}</div>
                <div class="user-details">
                  <h4>${feedback.name}</h4>
                  <span class="user-badge ${badgeClass}">
                    <i class="${badgeIcon}"></i>
                    ${feedback.user_type}
                  </span>
                </div>
              </div>
              <div class="rating-display">
                <div class="stars">${stars}</div>
                <div class="rating-text">${feedback.rating}/5 stars</div>
              </div>
            </div>
            
            <div class="comment-box">
              "${feedback.comment}"
            </div>
            
            ${photoHtml}
            
            <div class="feedback-footer">
              <div class="timestamp">
                <i class="fas fa-clock"></i>
                ${formattedDate}
              </div>
              <div class="verified-badge">
                <i class="fas fa-database"></i>
                Verified
              </div>
            </div>
          `;
          
          container.appendChild(entry);
        });
      })
      .catch(error => {
        console.error('Error loading feedback:', error);
        container.innerHTML = `
          <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <p>Failed to load feedback.</p>
            <small>Please try refreshing the page.</small>
          </div>
        `;
      });
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
      // Load feedback on page load
      loadFeedbacks();
      
      // Add refresh button functionality
      const refreshBtn = document.getElementById('refreshFeedbacks');
      if (refreshBtn) {
        refreshBtn.addEventListener('click', loadFeedbacks);
      }
      
      // Handle feedback form submission
      const feedbackForm = document.getElementById('feedbackForm');
      if (feedbackForm) {
        // Add rating star functionality
        const ratingStars = document.querySelectorAll('#ratingStars span');
        ratingStars.forEach((star, index) => {
          star.addEventListener('click', () => {
            selectedRating = index + 1;
            updateStars();
          });
          
          star.addEventListener('mouseenter', () => {
            highlightStars(index + 1);
          });
        });
        
        const ratingContainer = document.getElementById('ratingStars');
        if (ratingContainer) {
          ratingContainer.addEventListener('mouseleave', () => {
            updateStars();
          });
        }
        
        function highlightStars(rating) {
          ratingStars.forEach((star, index) => {
            if (index < rating) {
              star.classList.add('active');
            } else {
              star.classList.remove('active');
            }
          });
        }
        
        function updateStars() {
          ratingStars.forEach((star, index) => {
            if (index < selectedRating) {
              star.classList.add('active');
            } else {
              star.classList.remove('active');
            }
          });
        }
        
        feedbackForm.addEventListener('submit', async function(e) {
          e.preventDefault();
          
          // Prevent multiple submissions
          if (isSubmitting) {
            console.log('Form submission already in progress...');
            return;
          }
          
          const submitBtn = feedbackForm.querySelector('.submit-btn');
          const originalBtnText = submitBtn.innerHTML;
          
          isSubmitting = true;
          
          const name = document.getElementById('name').value.trim();
          const comment = document.getElementById('comment').value.trim();
          const photo = document.getElementById('photo').files[0];
          
          if (!name || !comment || selectedRating === 0) {
            showNotification('Please fill in all required fields and select a rating.', 'error');
            isSubmitting = false;
            return;
          }
          
          // Update button to loading state
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
          
          // Create form data
          const formData = new FormData();
          formData.append('name', name);
          formData.append('rating', selectedRating);
          formData.append('comment', comment);
          if (photo) {
            formData.append('photo', photo);
          }
          
          try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch('/api/feedback', {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': token
              },
              body: formData
            });
            
            if (response.ok) {
              const result = await response.json();
              
              // Reset form
              feedbackForm.reset();
              selectedRating = 0;
              updateStars();
              
              // Show success message
              showNotification('Feedback submitted successfully! Thank you for sharing your experience.', 'success');
              
              // Reload feedbacks to show the new one
              setTimeout(() => {
                loadFeedbacks();
              }, 1000);
            } else {
              throw new Error('Failed to submit feedback');
            }
          } catch (error) {
            console.error('Error submitting feedback:', error);
            showNotification('Failed to submit feedback. Please try again.', 'error');
          } finally {
            isSubmitting = false;
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
          }
        });
        
        // Notification system
        function showNotification(message, type = 'info') {
          const notification = document.createElement('div');
          const isMobile = window.innerWidth <= 768;
          
          notification.style.cssText = `
            position: fixed;
            top: ${isMobile ? '10px' : '20px'};
            right: ${isMobile ? '10px' : '20px'};
            left: ${isMobile ? '10px' : 'auto'};
            padding: ${isMobile ? '0.75rem 1rem' : '1rem 1.5rem'};
            border-radius: var(--radius-md);
            color: white;
            font-weight: 500;
            z-index: 10000;
            max-width: ${isMobile ? 'calc(100vw - 20px)' : '400px'};
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            animation: slideIn 0.3s ease;
            border: 1px solid rgba(255, 215, 0, 0.3);
            backdrop-filter: blur(10px);
            font-size: ${isMobile ? '0.9rem' : '1rem'};
          `;
          
          const colors = {
            success: 'background: linear-gradient(135deg, var(--success-color), #20c997); border-color: var(--success-color);',
            error: 'background: linear-gradient(135deg, var(--error-color), #c82333); border-color: var(--error-color);',
            info: 'background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-color: var(--primary-color);'
          };
          
          notification.style.cssText += colors[type] || colors.info;
          
          const icon = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            info: 'fas fa-info-circle'
          };
          
          notification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 0.75rem;">
              <i class="${icon[type] || icon.info}"></i>
              <span>${message}</span>
            </div>
          `;
          
          document.body.appendChild(notification);
          
          setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
              if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
              }
            }, 300);
          }, 4000);
        }
        
        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
          @keyframes slideIn {
            from {
              transform: translateX(100%);
              opacity: 0;
            }
            to {
              transform: translateX(0);
              opacity: 1;
            }
          }
          
          @keyframes slideOut {
            from {
              transform: translateX(0);
              opacity: 1;
            }
            to {
              transform: translateX(100%);
              opacity: 0;
            }
          }
        `;
        document.head.appendChild(style);
      }
      
      // Contact popup functionality
      const contactLink = document.getElementById("contactLink");
      const contactPopup = document.getElementById("contactPopup");
      const closeContact = document.getElementById("closeContact");

      if (contactLink && contactPopup && closeContact) {
        contactLink.addEventListener("click", (e) => {
          e.preventDefault();
          const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
          document.body.style.overflow = "hidden";
          document.body.style.paddingRight = `${scrollbarWidth}px`;
          contactPopup.classList.add("active");
        });

        closeContact.addEventListener("click", () => {
          contactPopup.classList.remove("active");
          document.body.style.overflow = "";
          document.body.style.paddingRight = "";
        });

        window.addEventListener("click", (e) => {
          if (e.target === contactPopup) {
            contactPopup.classList.remove("active");
            document.body.style.overflow = "";
            document.body.style.paddingRight = "";
          }
        });
      }
    });
    
    // Function to create feedback card
    function createFeedbackCard(feedback) {
      const card = document.createElement('div');
      card.className = 'feedback-entry';
      card.style.border = "2px solid #ffd700";
      card.style.borderRadius = "12px";
      card.style.padding = "20px";
      card.style.marginBottom = "20px";
      card.style.background = "linear-gradient(135deg, #fff 0%, #f8f9fa 100%)";
      card.style.boxShadow = "0 4px 15px rgba(0,0,0,0.1)";
      card.style.transition = "transform 0.2s ease";
      
      const stars = '★'.repeat(feedback.rating) + '☆'.repeat(5 - feedback.rating);
      const starColor = feedback.rating >= 4 ? '#ffd700' : feedback.rating >= 3 ? '#ffa500' : '#ff6b6b';
      
      const date = new Date(feedback.created_at);
      const formattedDate = date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
      
      let photoHtml = '';
      if (feedback.photo_url) {
        photoHtml = `
          <div style="margin-top: 15px;">
            <img src="${feedback.photo_url}" 
                 style="width: 100%; max-width: 300px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); cursor: pointer;" 
                 onclick="openPhotoModal('${feedback.photo_url}')" 
                 alt="Feedback photo" />
          </div>
        `;
      }
      
      const userTypeIcon = feedback.user_type === 'Authenticated' ? '👤' : '👥';
      const userTypeColor = feedback.user_type === 'Authenticated' ? '#007bff' : '#6c757d';
      
      card.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
          <div style="display: flex; align-items: center; gap: 10px;">
            <h4 style="margin: 0; color: #333; font-size: 1.2em; font-weight: bold;">${feedback.name}</h4>
            <span style="background: ${userTypeColor}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.7em; font-weight: bold;">
              ${userTypeIcon} ${feedback.user_type}
            </span>
          </div>
          <div style="text-align: right;">
            <div style="font-size: 1.5em; color: ${starColor}; margin-bottom: 5px;">${stars}</div>
            <small style="color: #666; font-size: 0.9em;">${feedback.rating}/5 stars</small>
          </div>
        </div>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #ffd700;">
          <p style="margin: 0; color: #555; line-height: 1.6; font-style: italic;">"${feedback.comment}"</p>
        </div>
        ${photoHtml}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
          <div style="display: flex; align-items: center; gap: 15px;">
            <small style="color: #888; font-size: 0.85em;">📅 ${formattedDate}</small>
            <small style="color: #6c757d; font-size: 0.85em;">🆔 ID: ${feedback.id}</small>
          </div>
          <div style="display: flex; align-items: center; gap: 10px;">
            <small style="color: #28a745; font-weight: bold;">✅ Just Submitted</small>
            <small style="color: #17a2b8; font-weight: bold;">📊 Saved to Database</small>
          </div>
        </div>
      `;
      
      return card;
    }
    
    // Function to show success message
    function showSuccessMessage(message) {
      const successMsg = document.createElement('div');
      successMsg.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 10px 15px;
        border-radius: 6px;
        font-size: 0.9em;
        z-index: 1000;
        animation: slideIn 0.3s ease;
      `;
      successMsg.innerHTML = message;
      document.body.appendChild(successMsg);
      
      setTimeout(() => {
        if (successMsg.parentNode) {
          successMsg.parentNode.removeChild(successMsg);
        }
      }, 3000);
    }
  </script>

  <script src="{{ asset('js/page-transitions.js') }}"></script>

  <div id="contactPopup" class="contact-popup">
    <div class="contact-card">
      <button class="close-contact" id="closeContact">&times;</button>
      <h2>Contact Us</h2>
      <p>Feel free to drop us a message</p>

      <div class="contact-row">
        <img src="{{ asset('images/facebook-icon.png') }}" alt="Facebook" class="icon" />
        <div>
          <strong>Facebook</strong><br />
          <span class="yellow">Lemon Hub Studio</span><br />
          <a href="https://www.facebook.com/lemonhubstudio" target="_blank">https://www.facebook.com/lemonhubstudio</a>
        </div>
      </div>

      <hr />

      <div class="contact-row">
        <img src="{{ asset('images/tiktok-icon.png') }}" alt="Tiktok" class="icon" />
        <div>
          <strong>Tiktok</strong><br />
          <span class="yellow">Lemon Hub Studio</span><br />
          <a href="https://www.tiktok.com/@lemon.hub.studio" target="_blank">https://www.tiktok.com/@lemon.hub.studio</a>
        </div>
      </div>

      <hr />

      <div class="contact-row">
        <img src="{{ asset('images/email-icon.png') }}" alt="Email" class="icon" />
        <div>
          <strong>Gmail</strong><br />
          <span class="yellow">Lemon Hub Studio</span><br />
          <a href="mailto:magamponr@gmail.com" class="email-link">magamponr@gmail.com</a>
        </div>
      </div>
    </div>
  </div>

<script>
    function toggleUserDropdown() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('show');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const userProfile = document.getElementById('userProfile');
        const dropdown = document.getElementById('userDropdown');
        
        if (userProfile && !userProfile.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    });

    // Prevent dropdown from closing when clicking inside
    document.getElementById('userDropdown')?.addEventListener('click', function(event) {
        event.stopPropagation();
    });

    // Handle contact link click
    document.getElementById('contactLink')?.addEventListener('click', function(e) {
        e.preventDefault();
        // Scroll to contact section or show contact modal
        const contactSection = document.querySelector('.contact-section');
        if (contactSection) {
            contactSection.scrollIntoView({ behavior: 'smooth' });
        }
        // Contact popup functionality is handled by the contact popup modal
    });
</script>

</body>
</html>
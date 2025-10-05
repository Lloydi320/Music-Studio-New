<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Book a Studio</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
  <style>
    /* Bubble Animation */
    @keyframes bubbleIn {
      0% {
        opacity: 0;
        transform: translateX(-50%) translateY(10px) scale(0.8);
      }
      100% {
        opacity: 1;
        transform: translateX(-50%) translateY(0) scale(1);
      }
    }
    
    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.8);
      backdrop-filter: blur(5px);
    }

    .modal-container {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100%;
      padding: 15px;
      overflow: hidden;
      touch-action: none;
    }

    .modal-content {
      background: white;
      border-radius: 15px;
      width: 90%;
      max-width: 1000px;
      max-height: 80vh;
      overflow-y: auto;
      overflow-x: hidden;
      display: flex;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
      scroll-behavior: smooth;
      touch-action: pan-y;
    }

    .modal-left {
      flex: 1;
      padding: 18px;
      background: #f8f9fa;
      border-radius: 20px 0 0 20px;
      overflow-y: auto;
      max-height: 80vh;
      order: 1;
    }

    .modal-center {
      flex: 1.3;
      padding: 12px;
      background: white;
      border-left: 1px solid #e9ecef;
      border-right: 1px solid #e9ecef;
      overflow-y: auto;
      max-height: 80vh;
      order: 3;
    }

    .modal-right {
      flex: 0.8;
      padding: 18px;
      background: white;
      border-radius: 0 20px 20px 0;
      display: flex;
      align-items: flex-start;
      justify-content: center;
      overflow-y: auto;
      max-height: 80vh;
      padding-top: 12px;
      order: 2;
    }

    .modal-header {
      margin-bottom: 10px;
    }

    .modal-title {
      font-size: 20px;
      font-weight: bold;
      color: #333;
      margin-bottom: 4px;
    }

    .modal-subtitle {
      color: #666;
      font-size: 13px;
      margin-bottom: 12px;
    }

    .booking-details {
      background: white;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 15px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .detail-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
      padding: 8px 0;
      border-bottom: 1px solid #eee;
    }

    .detail-label {
      font-weight: 500;
      color: #555;
    }

    .detail-value {
      color: #333;
      font-weight: 600;
    }

    .studio-image-modal {
      width: 100%;
      height: 150px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 15px;
    }

    .form-group {
      margin-bottom: 10px;
    }

    .form-label {
      display: block;
      margin-bottom: 4px;
      font-weight: 500;
      color: #333;
      font-size: 13px;
    }

    .form-input {
      width: 100%;
      padding: 8px 10px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 13px;
      transition: border-color 0.3s ease;
      box-sizing: border-box;
    }

    .form-input:focus {
      outline: none;
      border-color: #007bff;
      box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.1);
    }

    .file-input {
      width: 100%;
      padding: 8px 10px;
      border: 1px dashed #ddd;
      border-radius: 6px;
      font-size: 13px;
      background: #f8f9fa;
      cursor: pointer;
      transition: border-color 0.3s ease;
    }

    .file-input:hover {
      border-color: #007bff;
      background: #f0f8ff;
    }

    .policy-section {
      background: #f8f9fa;
      padding: 8px;
      border-radius: 6px;
      margin-bottom: 10px;
      border-left: 3px solid #007bff;
    }

    .policy-title {
      font-weight: 600;
      color: #333;
      margin-bottom: 4px;
      font-size: 13px;
    }

    .policy-text {
      font-size: 11px;
      color: #555;
      line-height: 1.3;
      margin-bottom: 3px;
    }

    .checkbox-group {
      display: flex;
      align-items: flex-start;
      margin-bottom: 10px;
      gap: 6px;
    }

    .checkbox-group input[type="checkbox"] {
      margin-top: 2px;
      transform: scale(1.1);
    }

    .checkbox-label {
      font-size: 13px;
      color: #333;
      line-height: 1.4;
    }

    .checkbox-label a {
      color: #007bff;
      text-decoration: none;
    }

    .checkbox-label a:hover {
      text-decoration: underline;
    }

    .gcash-container {
      background: linear-gradient(135deg, #0066cc, #004499);
      border-radius: 15px;
      padding: 25px;
      color: white;
      text-align: center;
      width: 100%;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
      overflow: hidden;
      box-sizing: border-box;
    }

    .gcash-logo {
      font-size: 20px;
      font-weight: bold;
      margin-bottom: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .gcash-qr {
      background: white;
      padding: 1px;
      border-radius: 8px;
      margin: 15px auto;
      display: inline-block;
      width: fit-content;
    }

    .gcash-qr img {
      width: 180px;
      height: 180px;
      display: block;
    }

    .gcash-details {
      font-size: 12px;
      margin-top: 10px;
      line-height: 1.4;
    }

    .gcash-merchant {
      font-size: 20px;
      font-weight: bold;
      margin: 12px 0;
    }

    .gcash-amount {
      font-size: 32px;
      font-weight: bold;
      margin-top: 15px;
    }

    .modal-buttons {
      display: flex;
      gap: 12px;
      margin-top: 20px;
    }

    .btn-cancel {
      flex: 1;
      padding: 10px 16px;
      background: #6c757d;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 14px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .btn-cancel:hover {
      background: #5a6268;
    }

    .btn-confirm {
      flex: 2;
      padding: 10px 16px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 14px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .btn-confirm:hover {
      background: #0056b3;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .modal-content {
        flex-direction: column;
        width: 95%;
        max-height: 90vh;
        overflow-y: auto;
      }

      .modal-left,
      .modal-center,
      .modal-right {
        border-radius: 0;
        padding: 20px;
        border: none;
        max-height: none;
        overflow-y: visible;
      }

      .modal-left {
        border-radius: 20px 20px 0 0;
      }

      .modal-center {
        border-top: 1px solid #e9ecef;
        border-bottom: 1px solid #e9ecef;
      }

      .modal-right {
        border-radius: 0 0 20px 20px;
        align-items: flex-start;
        padding-top: 10px;
      }

      .gcash-container {
        padding: 20px;
      }

      .modal-buttons {
        flex-direction: column;
      }

      .gcash-qr img {
        width: 150px;
        height: 150px;
      }

      .modal-title {
        font-size: 20px;
      }
    }

    /* Extra small devices (phones, 480px and down) */
    @media (max-width: 480px) {
      .modal-container {
        padding: 3px;
      }

      .modal-content {
        width: 99%;
        max-height: 95vh;
        border-radius: 15px;
        overflow-y: auto;
      }

      .modal-left,
      .modal-center,
      .modal-right {
        padding: 12px;
        max-height: none;
        overflow-y: visible;
      }

      .modal-left {
        border-radius: 15px 15px 0 0;
      }

      .modal-right {
        border-radius: 0 0 15px 15px;
        align-items: flex-start;
        padding-top: 8px;
      }

      .modal-title {
        font-size: 18px;
      }

      .modal-subtitle {
        font-size: 12px;
      }

      .form-label {
        font-size: 13px;
        margin-bottom: 4px;
      }

      .form-input {
        padding: 8px 10px;
        font-size: 13px;
      }

      .detail-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 2px;
        margin-bottom: 8px;
        padding: 6px 0;
      }

      .detail-label,
      .detail-value {
        font-size: 13px;
      }

      .gcash-qr {
        padding: 6px;
        margin: 8px auto;
      }

      .gcash-qr img {
        width: 140px;
        height: 140px;
      }

      .gcash-merchant {
        font-size: 16px;
        margin: 8px 0;
      }

      .gcash-amount {
        font-size: 22px;
        margin-top: 10px;
      }

      .gcash-details {
        font-size: 11px;
        margin-top: 8px;
      }

      .gcash-container {
        padding: 15px;
      }

      .btn-cancel,
      .btn-confirm {
        padding: 12px 16px;
        font-size: 13px;
      }

      .studio-image-modal {
        height: 130px;
      }

      .booking-details {
        padding: 12px;
        margin-bottom: 12px;
      }

      .warning-message {
        padding: 6px 8px;
        font-size: 11px;
        margin-bottom: 8px;
      }

      .error-message {
        padding: 6px 8px;
        font-size: 11px;
        margin-top: 4px;
      }
    }

    /* High zoom levels and very small screens */
    @media (max-width: 360px), (min-resolution: 2dppx) {
      .modal-container {
        padding: 2px;
      }

      .modal-content {
        width: 99%;
        max-height: 99vh;
        border-radius: 10px;
      }

      .modal-left,
      .modal-center,
      .modal-right {
        padding: 10px;
      }

      .gcash-qr img {
        width: 120px;
        height: 120px;
      }

      .form-input {
        padding: 6px 8px;
        font-size: 12px;
      }

      .btn-cancel,
      .btn-confirm {
        padding: 10px 12px;
        font-size: 12px;
      }
    }

    /* Fixed footer styles */
    .booking-footer {
      position: relative;
      bottom: auto;
      left: auto;
      right: auto;
      z-index: 1000;
      background: #2c3e50;
      color: #ecf0f1;
      text-align: center;
      padding: 20px 0;
      border-top: 1px solid #34495e;
      margin-top: 40px;
      width: 100%;
    }

    .footer-content {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }

    .footer-content p {
      margin: 5px 0;
      font-size: 14px;
      font-weight: 300;
    }

    .footer-content p:first-child {
      font-weight: 500;
      font-size: 15px;
    }

    .footer-content p:last-child {
      color: #bdc3c7;
      font-size: 13px;
    }

    /* Hide Sign Out button in navigation on desktop view */
    @media (min-width: 769px) {
      .nav-signout-desktop-hidden {
        display: none !important;
      }
    }
  </style>
</head>
<body class="booking-page">

  <header class="navbar">
    <div class="logo">
      <a href="/" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
        <img src="{{ asset('images/studio-logo.png') }}" alt="Lemon Hub Studio Logo">
        <span>LEMON HUB STUDIO</span>
      </a>
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
        <li><a href="#" id="feedbackLink">Feedbacks</a></li>
        <li><a href="/map">Map</a></li>
        @if(Auth::check())
        <li><a href="#" id="rescheduleBookingLink">Rescheduling</a></li>
        @endif
        @if(Auth::check() && Auth::user()->isAdmin())
        <li><a href="/admin/calendar" style="color: #ff6b35; font-weight: bold;">📅 Admin Calendar</a></li>
        @endif
        @if(Auth::check())
        <li class="nav-signout-desktop-hidden">
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
        <a href="{{ route('login') }}" class="login-btn">
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

  <div class="booking-main">
    <div class="booking-content">
      <div class="info-section fixed-info-section">
        <p class="studio-name">Lemon Hub Studio</p>
        <h2><span id="serviceType">BAND REHEARSAL</span> <span class="light-text">SELECT DATE</span></h2>
        <p class="duration">🕒 <span id="selectedDurationLabel">1 hr</span></p>
        <p class="location">📍 288H Sto.Domingo Street, 2nd Filmont Homes, Calamba, Laguna</p>
        <img src="{{ asset('images/studio.jpg') }}" alt="Studio" class="studio-image">
      </div>
      <div class="booking-right">
        <div class="calendar-section">
          <div class="calendar-header">
            <div class="date-selectors">
              <select id="monthDropdown" class="month-select"></select>
              <select id="yearDropdown" class="year-select"></select>
            </div>
          </div>
          <div class="calendar-time-layout">
            <div class="calendar-side">
              <div id="calendar" class="calendar-grid"></div>
              <p class="selected-date" id="selectedDateLabel"></p>
            </div>
            <div class="time-side">
              <!-- Duration dropdown -->
              <label for="durationSelect" class="form-label">⏰ Choose Duration</label>
              <select id="durationSelect">
                <option value="1" selected>1 hour</option>
                <option value="2">2 hours</option>
                <option value="3">3 hours</option>
                <option value="4">4 hours</option>
                <option value="5">5 hours</option>
                <option value="6">6 hours</option>
                <option value="7">7 hours</option>
                <option value="8">8 hours</option>
              </select>
              <div class="slots scrollable-time-slots"></div>
              <button class="next-btn" id="nextBtn">Next</button>
              <form id="bookingForm" action="{{ route('booking.store') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="date" id="bookingDate">
                <input type="hidden" name="time_slot" id="bookingTimeSlot">
                <input type="hidden" name="duration" id="bookingDuration">
                <input type="hidden" name="service_type" id="bookingServiceType">
                <button type="submit" class="book-btn">Confirm Booking</button>
              </form>
            </div>
          </div>
          <!-- Session Messages -->
          @if(session('success'))
            <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid #c3e6cb; position: relative; z-index: 1000;">
              {{ session('success') }}
            </div>
          @endif
          
          <div class="booking-summary" id="bookingSummary">
            <div id="bookingSummaryContent">
              <!-- Default placeholder content -->
              Select a date and time to see booking details
            </div>
            <div id="bookingConfirmationMessage" style="display:none;">
              <strong>Your booking has been confirmed!</strong>
            </div>
          </div>
          
          @if(session('error'))
            <div class="alert alert-error" style="background-color: #f8d7da; color: #721c24; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid #f5c6cb;">
              {{ session('error') }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

<!-- Studio Rental Modal -->
<div id="studioRentalModal" class="modal">
  <div class="modal-container">
    <div class="modal-content">
      <!-- Left Side - Booking Details (First) -->
      <div class="modal-left">
        <div class="modal-header">
          <h2 class="modal-title">BAND REHEARSAL</h2>
          <p class="modal-subtitle">SELECT DATE</p>
          <p class="duration">🕒 <span id="modalDurationLabel">3 hrs</span></p>
          <p class="location">📍 288H Sto.Domingo Street 2nd Filmont Homes Subdivision, Calamba, 4027 Laguna</p>
        </div>
        
        <img src="{{ asset('images/studio.jpg') }}" alt="Studio" class="studio-image-modal">
        
        <div class="booking-details">
          <div class="detail-item">
            <span class="detail-label">Date:</span>
            <span class="detail-value" id="modalSelectedDate">Thursday, August 21, 2025</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Time:</span>
            <span class="detail-value" id="modalSelectedTime">09:00 AM - 10:00 AM</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Duration:</span>
            <span class="detail-value" id="modalSelectedDuration">1 hour</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Total Price:</span>
            <span class="detail-value" id="modalTotalPrice">₱100.00</span>
          </div>
        </div>
      </div>
      
      <!-- Center - Form Section -->
      <div class="modal-center">
        <div class="modal-header">
          <h3>Enter Details</h3>
        </div>
        
        <form id="studioRentalForm" action="{{ route('booking.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="date" id="modalBookingDate">
          <input type="hidden" name="time_slot" id="modalBookingTimeSlot">
          <input type="hidden" name="duration" id="modalBookingDuration">
          <input type="hidden" name="price" id="modalBookingPrice">
          
          <div class="form-group">
            <label class="form-label" for="bandName">Band Name *</label>
            <input type="text" id="bandName" name="band_name" class="form-input" required>
          </div>
          
          <div class="form-group">
            <label class="form-label" for="email">Email *</label>
            <input type="email" id="email" name="email" class="form-input" required>
          </div>
          
          <div class="form-group">
            <label class="form-label" for="contactNumber">Contact Number *</label>
            <input type="tel" id="contactNumber" name="contact_number" class="form-input" maxlength="11" required>
          </div>
          
          <div class="form-group">
            <label class="form-label" for="referenceCode">LAST 4 DIGITS OF GCASH PAYMENT *</label>
            <input type="text" id="referenceCode" name="reference_code" class="form-input" maxlength="4" pattern="[0-9]{4}" placeholder="0000" required>
            <!-- Error message container for inline validation -->
            <div id="referenceErrorMessage" style="display: none; background-color: #fee2e2; color: #dc2626; padding: 6px 8px; margin: 3px 0 0 0; border-radius: 4px; border-left: 3px solid #dc2626; font-size: 0.8rem;">
              <span id="referenceErrorText">Reference number already exists.</span>
            </div>
          </div>
          
          <div class="form-group">
            <label class="form-label" for="uploadPicture">Upload Picture</label>
            <input type="file" id="uploadPicture" name="upload_picture" class="file-input" accept="image/*">
            <div class="alert alert-warning" style="background-color: #fff3cd; color: #856404; padding: 6px 8px; margin: 3px 0; border-radius: 4px; border: 1px solid #ffeaa7; font-size: 0.8rem;">
              ⚠️ Please upload a clear image of your GCash payment receipt. Accepted formats: JPG, PNG, GIF. Maximum file size: 5MB.
            </div>
            @error('upload_picture')
              <div class="alert alert-error" style="background-color: #f8d7da; color: #721c24; padding: 6px 8px; margin: 3px 0; border-radius: 4px; border: 1px solid #f5c6cb; font-size: 0.8rem;">
                {{ $message }}
              </div>
            @enderror
          </div>
          
          <div class="policy-section">
            <div class="policy-title">Down Payment Policy</div>
            <p class="policy-text">To secure your booking, a non-refundable down payment of 30% of the total service fee is required upon reservation.</p>
            <p class="policy-text">This ensures your preferred date and time slot is reserved exclusively for you.</p>
            <p class="policy-text">• Rebooking is allowed up to 24 hours before the session, subject to availability.</p>
          </div>
          
          <div class="checkbox-group">
            <input type="checkbox" id="agreeTerms" name="agree_terms" required>
            <label class="checkbox-label" for="agreeTerms">
              I agree to <a href="#">User Agreement</a> and <a href="#">Privacy Policy</a>
            </label>
          </div>
          
          <div class="modal-buttons">
            <button type="button" class="btn-cancel" id="cancelModal">Cancel</button>
            <button type="submit" class="btn-confirm">Confirm Booking</button>
          </div>
        </form>
      </div>
      
      <!-- Right Side - GCash Payment Section (Last) -->
      <div class="modal-right">
        <div class="gcash-container">
          <div class="gcash-logo">
            <span>💳</span> GCash
          </div>
          
          <div class="gcash-qr">
            <img src="{{ asset('images/LemonQr.png') }}" alt="GCash QR Code" id="qrCodeImage" style="cursor: pointer;" onclick="openQRModal()">
          </div>
          
          <div class="gcash-details">
            <div>Scan to pay with GCash</div>
            <div class="gcash-merchant">LEMON HUB</div>
            <div>Mobile No: 0995...217</div>
            <div>Account ID: ...60JPSU</div>
          </div>
          
          <div class="gcash-amount">₱ 100.00</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Success and error messages will be handled in the booking summary container -->

<!-- Removed the form from the bottom. It will only show in the modal after clicking Next. -->

<!-- Footer trigger area -->
<div class="footer-trigger"></div>

<!-- Footer -->
<footer class="booking-footer">
  <div class="footer-content">
    <p>&copy; 2025 Lemon Hub Studio - All Rights Reserved</p>
    <p>Professional Music Studio Services</p>
  </div>
</footer>

<!-- Success Confirmation Modal -->
<div id="successModal" class="modal" style="display: none; animation: fadeIn 0.3s ease-out;">
  <div class="modal-container" style="animation: slideInUp 0.4s ease-out;">
    <div class="modal-content" style="
      max-width: 720px;
      border-radius: 20px;
      padding: 30px;
      background: #ffffff;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      border: none;
      position: relative;
      overflow: hidden;
      display: flex;
      gap: 20px;
      align-items: flex-start;
    ">
      
      <!-- Left Section: Icon and Title -->
      <div style="
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        flex-shrink: 0;
      ">
        <!-- Success Icon -->
        <div style="
          width: 60px;
          height: 60px;
          margin-bottom: 16px;
          background: #10b981;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          animation: bounceIn 0.6s ease-out 0.2s both;
        ">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20,6 9,17 4,12"></polyline>
          </svg>
        </div>
        
        <!-- Title -->
        <h2 style="
          color: #10b981;
          margin: 0;
          font-size: 24px;
          font-weight: 600;
          letter-spacing: -0.3px;
          animation: fadeInUp 0.5s ease-out 0.3s both;
          white-space: nowrap;
        ">Booking<br>Confirmed!</h2>
      </div>
      
      <!-- Right Section: Details -->
      <div style="
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 16px;
      ">
        <!-- Success Message -->
        <div id="successMessage" style="
          background: #f0fdf4;
          color: #166534;
          padding: 20px;
          border-radius: 12px;
          border: 1px solid #bbf7d0;
          font-weight: 400;
          line-height: 1.5;
          font-size: 14px;
          animation: fadeInUp 0.5s ease-out 0.4s both;
        ">
          <!-- Success message will be populated here -->
        </div>
        
        <!-- Bottom Section -->
        <div style="
          display: flex;
          justify-content: space-between;
          align-items: flex-start;
          gap: 20px;
        ">
          <!-- Email confirmation text -->
          <p style="
            color: #6b7280;
            margin: 0;
            font-size: 14px;
            font-weight: 400;
            animation: fadeInUp 0.5s ease-out 0.5s both;
            flex: 1;
          ">You will receive an email confirmation shortly.</p>
          
          <!-- Countdown -->
          <div style="
            color: #6b7280;
            font-size: 13px;
            font-weight: 400;
            animation: fadeInUp 0.5s ease-out 0.6s both;
            text-align: right;
            flex-shrink: 0;
          ">
            Redirecting in <span id="countdown" style="color: #374151; font-weight: 500;">5</span> seconds...
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<!-- Modern CSS Animations -->
<style>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideInUp {
  from {
    transform: translateY(30px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes fadeInUp {
  from {
    transform: translateY(20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes bounceIn {
  0% {
    transform: scale(0.3);
    opacity: 0;
  }
  50% {
    transform: scale(1.05);
  }
  70% {
    transform: scale(0.9);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

/* Responsive design */
@media (max-width: 768px) {
  #successModal .modal-content {
    max-width: 90% !important;
    margin: 20px !important;
    padding: 30px 20px !important;
  }
  
  #successModal h2 {
    font-size: 24px !important;
  }
  
  #successModal .modal-container {
    padding: 20px !important;
  }
}
</style>

<script src="{{ asset('js/script.js') }}"></script>
<script src="{{ asset('js/booking.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/page-transitions.js') }}"></script>
<script>
// Reference Code Validation
document.addEventListener('DOMContentLoaded', function() {
    const referenceCodeInput = document.getElementById('referenceCode');
    const studioRentalForm = document.getElementById('studioRentalForm');
    let validationTimeout;
    let isValidating = false;
    
    if (referenceCodeInput) {
        // Create validation message element
        const validationMessage = document.createElement('div');
        validationMessage.id = 'referenceValidationMessage';
        validationMessage.style.cssText = `
            margin-top: 5px;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
            display: none;
        `;
        referenceCodeInput.parentNode.appendChild(validationMessage);
        
        // Real-time validation on input
        referenceCodeInput.addEventListener('input', function() {
            const value = this.value.trim();
            
            // Clear previous timeout
            clearTimeout(validationTimeout);
            
            // Reset validation state
            validationMessage.style.display = 'none';
            this.style.borderColor = '';
            
            // Clear validation state when user is typing
            if (value.length < 4) {
                delete this.dataset.valid;
            }
            
            // Only validate if we have 4 digits
            if (value.length === 4 && /^[0-9]{4}$/.test(value)) {
                validationTimeout = setTimeout(() => {
                    validateReferenceCode(value);
                }, 500); // Debounce for 500ms
            }
        });
        
        function validateReferenceCode(code) {
            if (isValidating) return;
            
            isValidating = true;
            validationMessage.textContent = 'Checking reference code...';
            validationMessage.style.cssText += `
                display: block;
                background-color: #f3f4f6;
                color: #6b7280;
                border: 1px solid #d1d5db;
            `;
            
            // Check if reference code exists
            fetch('/api/check-reference-code', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reference_code: code })
            })
            .then(response => response.json())
            .then(data => {
                isValidating = false;
                
                if (data.exists) {
                    // Reference code already exists
                    validationMessage.textContent = 'This reference code is already existing. Please use a different reference number from GCash 4-digits last number.';
                    validationMessage.style.cssText += `
                        display: block;
                        background-color: #fef2f2;
                        color: #dc2626;
                        border: 1px solid #fecaca;
                    `;
                    referenceCodeInput.dataset.valid = 'false';
                } else {
                    // Reference code is available
                    validationMessage.textContent = 'Reference code is available!';
                    validationMessage.style.cssText += `
                        display: block;
                        background-color: #f0fdf4;
                        color: #16a34a;
                        border: 1px solid #bbf7d0;
                    `;
                    referenceCodeInput.dataset.valid = 'true';
                }
                
                validationMessage.style.display = 'block';
            })
            .catch(error => {
                isValidating = false;
                console.error('Reference code validation error:', error);
                validationMessage.textContent = 'Error checking reference code. Please try again.';
                validationMessage.style.cssText += `
                    display: block;
                    background-color: #fef2f2;
                    color: #dc2626;
                    border: 1px solid #fecaca;
                `;
                referenceCodeInput.dataset.valid = 'unknown';
            });
        }
    }
    
    // Note: Form submission handling is now managed by booking.js
    // This prevents conflicts between inline handlers and external JS file
});

// Original script content below
function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    const profile = document.getElementById('userProfile');
    
    dropdown.classList.toggle('show');
    profile.classList.toggle('active');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const profile = document.getElementById('userProfile');
    const dropdown = document.getElementById('userDropdown');
    
    if (profile && !profile.contains(event.target)) {
        dropdown.classList.remove('show');
        profile.classList.remove('active');
    }
});

// Prevent dropdown from closing when clicking inside it
document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown) {
        dropdown.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
    
    // Handle contact link click
    document.getElementById('contactLink')?.addEventListener('click', function(e) {
        e.preventDefault();
        // Scroll to contact section or show contact modal
        const contactSection = document.querySelector('.contact-section');
        if (contactSection) {
            contactSection.scrollIntoView({ behavior: 'smooth' });
        }
        // Contact functionality handled by other contact methods
    });
});

// QR Code Modal Functions
function openQRModal() {
    document.getElementById('qrModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeQRModal() {
    document.getElementById('qrModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside the image
document.addEventListener('DOMContentLoaded', function() {
    const qrModal = document.getElementById('qrModal');
    if (qrModal) {
        qrModal.addEventListener('click', function(e) {
            if (e.target === qrModal) {
                closeQRModal();
            }
        });
    }
});
</script>

<!-- QR Code Modal -->
<div id="qrModal" class="qr-modal" style="display: none;">
    <span class="qr-close" onclick="closeQRModal()">&times;</span>
    <img src="{{ asset('images/LemonQr.png') }}" alt="GCash QR Code - Click to close" class="qr-modal-image">
</div>

<style>
.error-field {
  border: 2px solid #dc2626 !important;
  box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
  background-color: #fef2f2 !important;
}

.error-field:focus {
  border-color: #dc2626 !important;
  box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2) !important;
}

/* QR Modal Styles */
.qr-modal {
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    animation: fadeIn 0.3s ease-out;
}

.qr-close {
    position: absolute;
    top: 30px;
    right: 30px;
    font-size: 32px;
    font-weight: bold;
    color: white;
    cursor: pointer;
    transition: color 0.3s ease;
    z-index: 10001;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 50%;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.qr-close:hover {
    background: rgba(0, 0, 0, 0.7);
    color: #fff;
}

.qr-modal-image {
    width: 80vw;
    height: 80vw;
    max-width: 400px;
    max-height: 400px;
    object-fit: contain;
    border-radius: 15px;
    background: white;
    padding: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    animation: slideInUp 0.4s ease-out;
}

/* Responsive design for QR modal */
@media (max-width: 768px) {
    .qr-close {
        top: 20px;
        right: 20px;
        width: 45px;
        height: 45px;
        font-size: 28px;
    }
    
    .qr-modal-image {
        width: 90vw;
        height: 90vw;
        max-width: 350px;
        max-height: 350px;
        padding: 15px;
    }
}
</style>

</body>
</html>

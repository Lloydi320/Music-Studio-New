<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book a Studio</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
  <style>
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
      padding: 20px;
    }

    .modal-content {
      background: white;
      border-radius: 20px;
      width: 95%;
      max-width: 1200px;
      max-height: 90vh;
      overflow-y: auto;
      display: flex;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .modal-left {
      flex: 1;
      padding: 25px;
      background: #f8f9fa;
      border-radius: 20px 0 0 20px;
    }

    .modal-center {
      flex: 1.2;
      padding: 25px;
      background: white;
      border-left: 1px solid #e9ecef;
      border-right: 1px solid #e9ecef;
    }

    .modal-right {
      flex: 1;
      padding: 25px;
      background: white;
      border-radius: 0 20px 20px 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .modal-header {
      margin-bottom: 20px;
    }

    .modal-title {
      font-size: 24px;
      font-weight: bold;
      color: #333;
      margin-bottom: 5px;
    }

    .modal-subtitle {
      color: #666;
      font-size: 14px;
      margin-bottom: 15px;
    }

    .booking-details {
      background: white;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 20px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
      height: 200px;
      object-fit: cover;
      border-radius: 10px;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 16px;
    }

    .form-label {
      display: block;
      margin-bottom: 6px;
      font-weight: 500;
      color: #333;
      font-size: 14px;
    }

    .form-input {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 14px;
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
      padding: 10px 12px;
      border: 1px dashed #ddd;
      border-radius: 6px;
      font-size: 14px;
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
      padding: 12px;
      border-radius: 6px;
      margin-bottom: 16px;
      border-left: 3px solid #007bff;
    }

    .policy-title {
      font-weight: 600;
      color: #333;
      margin-bottom: 8px;
      font-size: 14px;
    }

    .policy-text {
      font-size: 12px;
      color: #555;
      line-height: 1.4;
      margin-bottom: 6px;
    }

    .checkbox-group {
      display: flex;
      align-items: flex-start;
      margin-bottom: 16px;
      gap: 8px;
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
      padding: 12px;
      border-radius: 8px;
      margin: 15px auto;
      display: inline-block;
      width: fit-content;
    }

    .gcash-qr img {
      width: 250px;
      height: 250px;
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
        max-height: 95vh;
      }

      .modal-left,
      .modal-center,
      .modal-right {
        border-radius: 0;
        padding: 20px;
        border: none;
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
      }

      .gcash-container {
        padding: 20px;
      }

      .modal-buttons {
        flex-direction: column;
      }
    }
  </style>
</head>
<body class="booking-page">

  <header class="navbar">
    <div class="logo">
      <a href="/" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
        <img src="{{ asset('images/studio-logo.png') }}" alt="Logo" />
        <span>LEMON HUB STUDIO</span>
      </a>
    </div>
    <nav>
      <ul class="nav-links">
        <li><a href="/">Home</a></li>
        <li><a href="/services">About Us & Our Services</a></li>
        <li><a href="#" id="contactLink">Contact</a></li>
        <li><a href="/feedback">Feedbacks</a></li>
      </ul>
    </nav>
    @if(Auth::check())
      <div class="user-profile">
        @php
          $user = Auth::user();
          $avatar = session('google_user_avatar') ?? null;
        @endphp
        @if($avatar)
          <img src="{{ $avatar }}" alt="Avatar">
        @endif
        <div style="display: flex; flex-direction: column; align-items: flex-end;">
          <span>{{ $user->name }}</span>
          <span style="font-size: 0.9em; color: #888;">{{ $user->email }}</span>
          <form action="/logout" method="POST" style="margin:0;">
            @csrf
            <button type="submit">Logout</button>
          </form>
        </div>
      </div>
    @else
      <a href="/auth/google" class="book-btn" style="margin-left: 30px;">Login with Google</a>
    @endif
  </header>

  <div class="booking-main">
    <div class="booking-content">
      <div class="info-section fixed-info-section">
        <p class="studio-name">Lemon Hub Studio</p>
        <h2><span id="serviceType">STUDIO RENTAL</span> <span class="light-text">SELECT DATE</span></h2>
        <p class="duration">🕒 <span id="selectedDurationLabel">1 hr</span></p>
        <p class="location">📍 288H Sto.Domingo Street, 2nd Filmont Homes, Calamba, Laguna</p>
        <img src="{{ asset('images/studio.jpg') }}" alt="Studio" class="studio-image">
      </div>
      <div class="booking-right">
        <div class="calendar-section">
          <div class="calendar-header">
            <select id="monthDropdown"></select>
          </div>
          <div class="calendar-time-layout">
            <div class="calendar-side">
              <div id="calendar" class="calendar-grid"></div>
              <p class="selected-date" id="selectedDateLabel"></p>
            </div>
            <div class="time-side">
              <!-- Service Type dropdown -->
              <label for="serviceTypeSelect" style="display:block; margin: 10px 0 5px;">Service Type:</label>
              <select id="serviceTypeSelect">
                <option value="studio_rental" selected>Studio Rental</option>
                <option value="recording_session">Recording Session</option>
                <option value="music_lesson">Music Lesson</option>
                <option value="band_practice">Band Practice</option>
                <option value="audio_production">Audio Production</option>
                <option value="instrument_rental">Instrument Rental</option>
                <option value="other">Other Services</option>
              </select>
              
              <!-- Duration dropdown -->
              <label for="durationSelect" style="display:block; margin: 10px 0 5px;">Choose Duration:</label>
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
          <div class="booking-summary" id="bookingSummary">
            <div id="bookingSummaryContent">
              <!-- Content will be populated by JavaScript -->
            </div>
            <div id="bookingConfirmationMessage" style="display:none;">
              <strong>Your booking has been confirmed!</strong>
            </div>
          </div>
          
          <!-- Success and Error Messages -->
          @if(session('success'))
            <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid #c3e6cb;">
              {{ session('success') }}
            </div>
          @endif
          
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
      <!-- Left Side - Booking Details and Form -->
      <div class="modal-left">
        <div class="modal-header">
          <h2 class="modal-title">STUDIO RENTAL</h2>
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
          <input type="hidden" name="service_type" id="modalBookingServiceType">
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
            <input type="tel" id="contactNumber" name="contact_number" class="form-input" required>
          </div>
          
          <div class="form-group">
            <label class="form-label" for="referenceCode">Reference Code (4 digits) *</label>
            <input type="text" id="referenceCode" name="reference_code" class="form-input" maxlength="4" pattern="[0-9]{4}" placeholder="0000" required>
          </div>
          
          <div class="form-group">
            <label class="form-label" for="uploadPicture">Upload Picture</label>
            <input type="file" id="uploadPicture" name="upload_picture" class="file-input" accept="image/*">
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
      
      <!-- Right Side - GCash Payment Section -->
      <div class="modal-right">
        <div class="gcash-container">
          <div class="gcash-logo">
            <span>💳</span> GCash
          </div>
          
          <div class="gcash-qr">
            <img src="{{ asset('images/LemonQr.png') }}" alt="GCash QR Code">
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

<!-- Footer -->
<footer class="booking-footer">
  <div class="footer-content">
    <p>&copy; 2025 Lemon Hub Studio - All Rights Reserved</p>
    <p>Professional Music Studio Services</p>
  </div>
</footer>

<script src="{{ asset('js/booking.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/page-transitions.js') }}"></script>
</body>
</html>

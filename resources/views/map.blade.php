<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Studio Location - Lemon Hub Studio</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
</head>

<body>

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
        <li><a href="/map" class="active">Map</a></li>
        @if(Auth::check())
        <li><a href="#" id="rescheduleBookingLink">Rescheduling</a></li>
        @endif
        @if(Auth::check() && Auth::user()->isAdmin())
        <li><a href="/admin/calendar" style="color: #ff6b35; font-weight: bold;">📅 Admin Calendar</a></li>
        @endif
        @if(!Auth::check())
        <li class="nav-login-mobile">
          <a href="{{ route('login') }}" style="color: #FFD700; padding: 15px 20px; font-size: 1.1rem; text-decoration: none; width: 100%; text-align: left; border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: block;">
            Login
          </a>
        </li>
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
      <div class="modern-user-profile" onclick="toggleUserDropdown()">
        @php
          $user = Auth::user();
          $avatar = session('google_user_avatar') ?? null;
        @endphp
        <div class="profile-trigger">
          <div class="profile-avatar">
            @if($avatar)
              <img src="{{ $avatar }}" alt="Avatar">
            @else
              <div class="avatar-placeholder">
                {{ strtoupper(substr($user->name, 0, 1)) }}
              </div>
            @endif
            @if($user->is_admin)
              <div class="admin-indicator"></div>
            @endif
          </div>
          <div class="profile-info">
            <span class="profile-name">{{ $user->name }}</span>
            <span class="profile-role">{{ $user->is_admin ? 'Admin' : 'Member' }}</span>
          </div>
          <div class="dropdown-arrow">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
        </div>
        
        <div class="user-dropdown" id="userDropdown">
          <div class="dropdown-header">
            <div class="dropdown-avatar">
              @if($avatar)
                <img src="{{ $avatar }}" alt="Avatar">
              @else
                <div class="avatar-placeholder large">
                  {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
              @endif
            </div>
            <div class="dropdown-user-info">
              <h4>{{ $user->name }}</h4>
              <p>{{ $user->email }}</p>
              <span class="user-badge">{{ $user->is_admin ? 'Administrator' : 'Member' }}</span>
            </div>
          </div>
          
          <div class="dropdown-menu">
            <form method="POST" action="{{ route('logout') }}" class="dropdown-item logout-form">
              @csrf
              <button type="submit" class="logout-button">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                  <path d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M16 17L21 12L16 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Logout</span>
              </button>
            </form>
          </div>
        </div>
      </div>
    @endif
  </header>

  <main class="main-content">
    <section class="map-section">
      <div class="container">
        <div class="map-header">
          <h1>Studio Location</h1>
          <p>Find us at Lemon Hub Studio - Your premier band rehearsal destination</p>
        </div>
        
        <div class="map-container">
          <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3867.808833802197!2d121.11519437589996!3d14.205968986614963!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd632ccd9c12f3%3A0x91160f31cf487d39!2sLemon%20Hub%20Studio%20(Band%20Rehearsal%20Studio)!5e0!3m2!1sen!2sph!4v1756837526394!5m2!1sen!2sph" 
            width="100%" 
            height="450" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
        
        <div class="directions-button-container">
          <a href="https://www.google.com/maps/dir/?api=1&destination=Lemon%20Hub%20Studio%20(Band%20Rehearsal%20Studio)&destination_place_id=ChIJs8KczcYzvcARORYRz_FHhJE" target="_blank" class="directions-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
              <path d="M12 2L13.09 8.26L22 9L13.09 9.74L12 16L10.91 9.74L2 9L10.91 8.26L12 2Z" fill="currentColor"/>
            </svg>
            Get Directions
          </a>
        </div>
        
        <div class="location-info">
          <div class="info-card">
            <h3>📍 Address</h3>
            <p>Lemon Hub Studio (Band Rehearsal Studio)</p>
          </div>
          
          <div class="info-card">
            <h3>🕒 Operating Hours</h3>
            <p>Contact us for current operating hours and availability</p>
          </div>
          
          <div class="info-card">
            <h3>📞 Contact</h3>
            <p>Get in touch to book your rehearsal session</p>
          </div>
        </div>
      </div>
    </section>
  </main>

  <style>
    .map-section {
      padding: 120px 0 60px 0;
      background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
      min-height: calc(100vh - 80px);
    }
    
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }
    
    .map-header {
      text-align: center;
      margin-bottom: 40px;
      color: white;
    }
    
    .map-header h1 {
      font-size: 2.5rem;
      margin-bottom: 10px;
      background: linear-gradient(45deg, #FFD700, #FFA500);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .map-header p {
      font-size: 1.1rem;
      opacity: 0.9;
    }
    
    .map-container {
      background: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      margin-bottom: 40px;
    }
    
    .map-container iframe {
      border-radius: 8px;
      width: 100%;
      height: 450px;
    }
    
    .location-info {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
      margin-top: 40px;
    }
    
    .info-card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border-radius: 12px;
      padding: 25px;
      text-align: center;
      border: 1px solid rgba(255, 215, 0, 0.2);
    }
    
    .info-card h3 {
      color: #FFD700;
      margin-bottom: 15px;
      font-size: 1.2rem;
    }
    
    .info-card p {
      color: white;
      opacity: 0.9;
      line-height: 1.6;
    }
    
    .directions-button-container {
      text-align: center;
      margin: 30px 0;
    }
    
    .directions-btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: linear-gradient(45deg, #FFD700, #FFA500);
      color: #1a1a1a;
      text-decoration: none;
      padding: 15px 30px;
      border-radius: 50px;
      font-weight: 600;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
      border: 2px solid transparent;
    }
    
    .directions-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(255, 215, 0, 0.4);
      background: linear-gradient(45deg, #FFA500, #FFD700);
      border-color: rgba(255, 255, 255, 0.2);
    }
    
    .directions-btn svg {
      transition: transform 0.3s ease;
      pointer-events: none;
    }
    
    .directions-btn:hover svg {
      transform: rotate(45deg);
    }
    
    @media (max-width: 768px) {
      .map-header h1 {
        font-size: 2rem;
      }
      
      .map-container {
        padding: 15px;
      }
      
      .map-container iframe {
        height: 300px;
      }
      
      .location-info {
        grid-template-columns: 1fr;
      }
      
      .directions-btn {
        padding: 12px 25px;
        font-size: 1rem;
      }
    }

    /* Hide Sign Out button in navigation on desktop view */
    @media (min-width: 769px) {
      .nav-signout-desktop-hidden {
        display: none !important;
      }
    }
  </style>

  <!-- Contact Modal -->
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

  <!-- Feedback Modal -->
  <div id="feedbackPopup" class="feedback-popup">
    <div class="feedback-modal-card">
      <div class="feedback-modal-header">
        <h2>Feedback</h2>
        <button class="close-feedback" id="closeFeedback">&times;</button>
      </div>
      <div class="feedback-modal-content">
        <div class="feedback-list">
          <div id="feedbackEntries">
            <p class="placeholder">No feedback shared yet.</p>
          </div>
        </div>
        <div class="feedback-form">
        <form id="feedbackForm">
          <div class="form-content">
            <label for="name">Your Name</label>
            <input type="text" id="name" required />

            <label for="rating">Rating</label>
            <div class="rating-stars">
              <span data-value="1">★</span>
              <span data-value="2">★</span>
              <span data-value="3">★</span>
              <span data-value="4">★</span>
              <span data-value="5">★</span>
            </div>

            <label for="comment">Comment</label>
            <textarea id="comment" rows="5" required></textarea>

            <label for="photo">Upload a Photo (optional)</label>
            <input type="file" id="photo" accept="image/*" />
          </div>
          
          <button type="submit" class="submit-btn">Submit Feedback</button>
        </form>
      </div>
      </div>
    </div>
  </div>

  <!-- Modern Rescheduling Modal (copied from home.blade.php) -->
  <div id="reschedulePopup" class="reschedule-popup">
    <div class="reschedule-modal">
      <div class="reschedule-modal-header">
        <h2>✨ Rescheduling</h2>
        <button class="close-reschedule" id="closeReschedule" aria-label="Close modal">&times;</button>
      </div>
      <div class="reschedule-modal-content">
        <form id="rescheduleForm" class="reschedule-form-grid">
          <!-- First Row: Reference Number -->
          <div class="form-row full-width">
            <div class="form-group">
              <label for="referenceNumber">🔢 Reference Number</label>
              <input type="text" id="referenceNumber" name="referenceNumber" required placeholder="Enter your booking reference number" aria-describedby="ref-help">
              <small id="ref-help" class="form-help">Enter your booking reference number to verify your booking</small>
              <div id="reference-validation" class="validation-message"></div>
            </div>
          </div>
          
          <!-- Studio Rental Fields (initially hidden) -->
          <div id="studioRentalFields" class="booking-fields" style="display: none;">
            <!-- Second Row: Date and Duration -->
            <div class="form-row">
              <div class="form-group">
                <label for="newDate">📅 New Date</label>
                <div class="date-input-wrapper">
                  <input type="date" id="newDate" name="newDate" aria-describedby="date-help" class="date-picker-input" min="{{ date('Y-m-d') }}">
                  <div class="date-picker-icon">📅</div>
                </div>
                <small id="date-help" class="form-help">Click to open calendar and select your preferred date</small>
              </div>
              
              <div class="form-group">
                <label for="duration">⏱️ Duration</label>
                <select id="duration" name="duration" aria-describedby="duration-help" disabled>
                  <option value="1">1 hour</option>
                  <option value="2">2 hours</option>
                  <option value="3">3 hours</option>
                  <option value="4">4 hours</option>
                  <option value="5">5 hours</option>
                  <option value="6">6 hours</option>
                  <option value="7">7 hours</option>
                  <option value="8">8 hours</option>
                </select>
                <small id="duration-help" class="form-help">Duration is fixed based on your original booking</small>
              </div>
            </div>
            
            <!-- Third Row: Time Slot (Full Width) -->
            <div class="form-row full-width">
              <div class="form-group">
                <label for="newTime">⏰ New Time Slot</label>
                <select id="newTime" name="newTime" aria-describedby="time-help">
                  <option value="">Select a time slot</option>
                  <option value="08:00 AM - 09:00 AM">08:00 AM - 09:00 AM</option>
                  <option value="08:30 AM - 09:30 AM">08:30 AM - 09:30 AM</option>
                  <option value="09:00 AM - 10:00 AM">09:00 AM - 10:00 AM</option>
                  <option value="09:30 AM - 10:30 AM">09:30 AM - 10:30 AM</option>
                  <option value="10:00 AM - 11:00 AM">10:00 AM - 11:00 AM</option>
                  <option value="10:30 AM - 11:30 AM">10:30 AM - 11:30 AM</option>
                  <option value="11:00 AM - 12:00 PM">11:00 AM - 12:00 PM</option>
                  <option value="11:30 AM - 12:30 PM">11:30 AM - 12:30 PM</option>
                  <option value="12:00 PM - 01:00 PM">12:00 PM - 01:00 PM</option>
                  <option value="12:30 PM - 01:30 PM">12:30 PM - 01:30 PM</option>
                  <option value="01:00 PM - 02:00 PM">01:00 PM - 02:00 PM</option>
                  <option value="01:30 PM - 02:30 PM">01:30 PM - 02:30 PM</option>
                  <option value="02:00 PM - 03:00 PM">02:00 PM - 03:00 PM</option>
                  <option value="02:30 PM - 03:30 PM">02:30 PM - 03:30 PM</option>
                  <option value="03:00 PM - 04:00 PM">03:00 PM - 04:00 PM</option>
                  <option value="03:30 PM - 04:30 PM">03:30 PM - 04:30 PM</option>
                  <option value="04:00 PM - 05:00 PM">04:00 PM - 05:00 PM</option>
                  <option value="04:30 PM - 05:30 PM">04:30 PM - 05:30 PM</option>
                  <option value="05:00 PM - 06:00 PM">05:00 PM - 06:00 PM</option>
                  <option value="05:30 PM - 06:30 PM">05:30 PM - 06:30 PM</option>
                  <option value="06:00 PM - 07:00 PM">06:00 PM - 07:00 PM</option>
                  <option value="06:30 PM - 07:30 PM">06:30 PM - 07:30 PM</option>
                  <option value="07:00 PM - 08:00 PM">07:00 PM - 08:00 PM</option>
                </select>
                <small id="time-help" class="form-help">Available time slots will update based on duration</small>
              </div>
            </div>
          </div>
          
          <!-- Instrument Rental Fields (initially hidden) -->
          <div id="instrumentRentalFields" class="booking-fields" style="display: none;">
            <div class="form-row">
              <div class="form-group">
                <label for="startDate">📅 Start Date</label>
                <div class="date-input-wrapper">
                  <input type="date" id="startDate" name="startDate" aria-describedby="start-date-help" class="date-picker-input" min="{{ date('Y-m-d') }}">
                  <div class="date-picker-icon">📅</div>
                </div>
                <small id="start-date-help" class="form-help">Select the new start date for your rental</small>
              </div>
              
              <div class="form-group">
                <label for="endDate">📅 End Date</label>
                <div class="date-input-wrapper">
                  <input type="date" id="endDate" name="endDate" aria-describedby="end-date-help" class="date-picker-input" min="{{ date('Y-m-d') }}">
                  <div class="date-picker-icon">📅</div>
                </div>
                <small id="end-date-help" class="form-help">Select the new end date for your rental</small>
              </div>
            </div>
          </div>
          
          <div class="form-actions" id="formActions" style="display: none;">
            <button type="button" class="cancel-btn" id="cancelReschedule">
              <span>Cancel</span>
            </button>
            <button type="submit" class="submit-btn">
              <span>✨ Submit Reschedule</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Reschedule Success Modal (copied from home.blade.php) -->
  <div id="rescheduleSuccessModal" class="modal" style="display: none; animation: fadeIn 0.3s ease-out;">
    <div class="modal-container" style="animation: slideInUp 0.4s ease-out;">
      <div class="modal-content" style="
        max-width: 560px;
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
          ">Reschedule<br>Request Sent!</h2>
        </div>
        
        <!-- Right Section: Details -->
        <div style="
          flex: 1;
          display: flex;
          flex-direction: column;
          gap: 16px;
        ">
          <!-- Success Message -->
          <div id="rescheduleSuccessMessage" style="
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
             <!-- Admin review text -->
             <p style="
               color: #6b7280;
               margin: 0;
               font-size: 14px;
               font-weight: 400;
               animation: fadeInUp 0.5s ease-out 0.5s both;
               flex: 1;
             ">Admin will review and approve your request.</p>
             
             <!-- Countdown -->
             <div style="
               color: #6b7280;
               font-size: 13px;
               font-weight: 400;
               animation: fadeInUp 0.5s ease-out 0.6s both;
               text-align: right;
               flex-shrink: 0;
             ">
               Redirecting in <span id="rescheduleCountdown" style="color: #374151; font-weight: 500;">5</span> seconds...
             </div>
           </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Reschedule Error Modal (copied from home.blade.php) -->
  <div id="rescheduleErrorModal" class="modal" style="display: none; animation: fadeIn 0.3s ease-out;">
    <div class="modal-container" style="animation: slideInUp 0.4s ease-out;">
      <div class="modal-content" style="
        max-width: 560px;
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
          <!-- Error Icon -->
          <div style="
            width: 60px;
            height: 60px;
            margin-bottom: 16px;
            background: #ef4444;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: bounceIn 0.6s ease-out 0.2s both;
          ">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="15" y1="9" x2="9" y2="15"></line>
              <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
          </div>
          
          <!-- Title -->
          <h2 style="
            color: #ef4444;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            letter-spacing: -0.3px;
            animation: fadeInUp 0.5s ease-out 0.3s both;
            white-space: nowrap;
          ">Request<br>Failed!</h2>
        </div>
        
        <!-- Right Section: Details -->
        <div style="
          flex: 1;
          display: flex;
          flex-direction: column;
          gap: 16px;
        ">
          <!-- Error Message -->
          <div id="rescheduleErrorMessage" style="
            background: #fef2f2;
            color: #991b1b;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #fecaca;
            font-weight: 400;
            line-height: 1.5;
            font-size: 14px;
            animation: fadeInUp 0.5s ease-out 0.4s both;
          ">
            <!-- Error message will be populated here -->
          </div>
          
          <!-- Bottom Section -->
           <div style="
             display: flex;
             justify-content: space-between;
             align-items: center;
             gap: 20px;
           ">
             <!-- Retry text -->
             <p style="
               color: #6b7280;
               margin: 0;
               font-size: 14px;
               font-weight: 400;
               animation: fadeInUp 0.5s ease-out 0.5s both;
               flex: 1;
             ">Please check your details and try again.</p>
             
             <!-- Close Button -->
             <button onclick="closeRescheduleErrorModal()" style="
               background: #ef4444;
               color: white;
               border: none;
               padding: 8px 16px;
               border-radius: 8px;
               font-size: 14px;
               font-weight: 500;
               cursor: pointer;
               animation: fadeInUp 0.5s ease-out 0.6s both;
               transition: background-color 0.2s;
             " onmouseover="this.style.backgroundColor='#dc2626'" onmouseout="this.style.backgroundColor='#ef4444'">
               Close
             </button>
           </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Rescheduling Styles (copied from home.blade.php) -->
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

  .modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
  }

  .modal-container {
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
  }

  /* Reschedule Modal Styles */
  .reschedule-popup {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    animation: fadeIn 0.3s ease;
  }

  .reschedule-modal {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
    animation: slideInUp 0.4s ease;
  }

  .reschedule-modal-header {
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    padding: 20px;
    border-radius: 20px 20px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e5e5e5;
  }

  .reschedule-modal-header h2 {
    margin: 0;
    color: #333;
    font-size: 1.4rem;
    font-weight: 600;
  }

  .close-reschedule {
    background: rgba(0, 0, 0, 0.1);
    border: none;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 20px;
    color: #333;
    transition: all 0.3s ease;
  }

  .close-reschedule:hover {
    background: rgba(0, 0, 0, 0.2);
    transform: scale(1.1);
  }

  .reschedule-modal-content {
    padding: 25px;
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-group label {
    display: block;
    margin-bottom: 8px;
    color: #333;
    font-weight: 600;
    font-size: 0.95rem;
  }

  .form-group input,
  .form-group select {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e1e5e9;
    border-radius: 10px;
    font-size: 1rem;
    color: #333;
    background: #ffffff;
    transition: all 0.3s ease;
    box-sizing: border-box;
  }

  .form-group input:focus,
  .form-group select:focus {
    outline: none;
    border-color: #ffd700;
    box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
  }

  .date-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
  }

  .date-picker-icon {
    position: absolute;
    right: 12px;
    color: #666;
    font-size: 1.1rem;
    pointer-events: none;
    z-index: 1;
  }

  .form-help {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 0.85rem;
    font-style: italic;
  }

  .form-actions {
    display: flex;
    gap: 12px;
    margin-top: 25px;
    justify-content: flex-end;
  }

  .cancel-btn {
    background: #6c757d !important;
    color: white !important;
    border: none !important;
    padding: 12px 20px !important;
    border-radius: 8px !important;
    cursor: pointer !important;
    font-size: 0.95rem !important;
    font-weight: 500 !important;
    transition: all 0.3s ease !important;
    flex: 1 !important;
    min-width: 140px !important;
    margin: 0 !important;
  }

  .cancel-btn:hover {
    background: #5a6268;
    transform: translateY(-1px);
  }

  .submit-btn {
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%) !important;
    color: #333 !important;
    border: none !important;
    padding: 12px 20px !important;
    border-radius: 8px !important;
    cursor: pointer !important;
    font-size: 0.95rem !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
    flex: 1 !important;
    min-width: 140px !important;
    margin: 0 !important;
  }

  .submit-btn:hover {
    background: linear-gradient(135deg, #ffed4e 0%, #ffd700 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
  }
  </style>

  <script src="{{ asset('js/script.js') }}"></script>
  <script src="{{ asset('js/feedback.js') }}"></script>

  <!-- Rescheduling Logic (copied from home.blade.php) -->
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    // Reschedule Popup functionality
    const rescheduleBookingLink = document.getElementById('rescheduleBookingLink');
    const reschedulePopup = document.getElementById('reschedulePopup');
    const closeRescheduleBtn = document.getElementById('closeReschedule');
    const cancelRescheduleBtn = document.getElementById('cancelReschedule');
    const rescheduleForm = document.getElementById('rescheduleForm');

    if (rescheduleBookingLink && reschedulePopup) {
        rescheduleBookingLink.addEventListener('click', function(e) {
            e.preventDefault();
            reschedulePopup.style.display = 'flex';
        });
    }

    if (closeRescheduleBtn && reschedulePopup) {
        closeRescheduleBtn.addEventListener('click', function() {
            reschedulePopup.style.display = 'none';
        });
    }

    if (cancelRescheduleBtn && reschedulePopup) {
        cancelRescheduleBtn.addEventListener('click', function() {
            reschedulePopup.style.display = 'none';
        });
    }

    // Close popup when clicking outside
    if (reschedulePopup) {
        reschedulePopup.addEventListener('click', function(e) {
            if (e.target === reschedulePopup) {
                reschedulePopup.style.display = 'none';
            }
        });
    }

    // Time helpers and improved generator with booking overlap filtering
    function formatTime12h(dateObj) {
        return dateObj.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    }
    function parseTimeStringToDate(timeStr, baseDateISO) {
        const cleaned = timeStr.replace(/\s/g, '');
        const match = cleaned.match(/^(\d{1,2}):(\d{2})(AM|PM)$/i);
        if (!match) return null;
        const [, hh, mm, ap] = match;
        let hours = parseInt(hh, 10);
        const minutes = parseInt(mm, 10);
        if (ap.toUpperCase() === 'PM' && hours !== 12) hours += 12;
        if (ap.toUpperCase() === 'AM' && hours === 12) hours = 0;
        const base = baseDateISO ? new Date(baseDateISO + 'T00:00:00') : new Date();
        base.setHours(hours, minutes, 0, 0);
        return base;
    }
    function parseTimeRange(rangeStr, baseDateISO) {
        const parts = rangeStr.split('-');
        if (parts.length < 2) return [null, null];
        const startStr = parts[0].trim();
        const endStr = parts[1].trim();
        const start = parseTimeStringToDate(startStr, baseDateISO);
        const end = parseTimeStringToDate(endStr, baseDateISO);
        return [start, end];
    }
    function generateRescheduleTimeSlots(durationHours, baseDateISO = null, bookings = []) {
        const timeSelect = document.getElementById('newTime');
        if (!timeSelect) return;
        
        // Clear existing options except the first one
        timeSelect.innerHTML = '<option value="">Select a time slot</option>';
        
        const openingHour = 8;
        const closingHour = 20; // 8 PM
        const durationMinutes = durationHours * 60;
        
        const baseDate = baseDateISO ? new Date(baseDateISO + 'T00:00:00') : new Date();
        baseDate.setHours(0, 0, 0, 0);
        
        // Define exact closing time for the selected date
        const closingTime = new Date(baseDate);
        closingTime.setHours(closingHour, 0, 0, 0);
        
        let currentHour = openingHour;
        let currentMinute = 0;
        
        while (currentHour < closingHour) {
            const startTime = new Date(baseDate);
            startTime.setHours(currentHour, currentMinute, 0, 0);
            
            const endTime = new Date(startTime.getTime() + durationMinutes * 60000);
            
            // Check if end time doesn't exceed closing time
            if (endTime <= closingTime) {
                // Only consider pending or confirmed bookings as blockers
                const blockingBookings = (bookings || []).filter(b => {
                    const status = (b.status || '').toLowerCase();
                    return status === 'pending' || status === 'confirmed';
                });
                const overlaps = blockingBookings.some(b => {
                    const [bStart, bEnd] = parseTimeRange(b.time_slot, baseDateISO);
                    if (!bStart || !bEnd) return false;
                    return startTime < bEnd && endTime > bStart;
                });
                if (!overlaps) {
                    const timeSlot = `${formatTime12h(startTime)} - ${formatTime12h(endTime)}`;
                    const option = document.createElement('option');
                    option.value = timeSlot;
                    option.textContent = timeSlot;
                    timeSelect.appendChild(option);
                }
            }
            
            // Increment by 30 minutes
            currentMinute += 30;
            if (currentMinute >= 60) {
                currentMinute = 0;
                currentHour++;
            }
        }
    }
    
    // Reference validation
    let validationTimeout;
    let isReferenceValid = false;
    
    const referenceInput = document.getElementById('referenceNumber');
    const validationMessage = document.getElementById('reference-validation');
    const newDateInput = document.getElementById('newDate');
    const durationSelect = document.getElementById('duration');
    const newTimeSelect = document.getElementById('newTime');
    
    // Initialize time slots with fixed 1-hour duration and set up filtering
    let lastReschedDateISO = null;
    let lastReschedBookings = [];
    let rescheduleInstrumentBookedDates = [];
    generateRescheduleTimeSlots(1, lastReschedDateISO, lastReschedBookings);

    // Fetch instrument rental booked dates to block entire days
    (async function fetchInstrumentRescheduleDates() {
        try {
            const response = await fetch('/api/instrument-rental/booked-dates');
            const data = await response.json();
            rescheduleInstrumentBookedDates = data.booked_dates || [];
        } catch (error) {
            console.error('Error fetching instrument rental booked dates:', error);
            rescheduleInstrumentBookedDates = [];
        }
    })();
    
    // Update slots when date changes: fetch bookings and filter overlaps
    if (newDateInput) {
        newDateInput.addEventListener('change', function() {
            const dateVal = this.value;
            lastReschedDateISO = dateVal;
            if (!dateVal) {
                const d = parseInt(durationSelect?.value || '1', 10);
                generateRescheduleTimeSlots(d);
                return;
            }
            // If date is fully blocked by instrument rental, show message and reset
            if (rescheduleInstrumentBookedDates.includes(dateVal)) {
                alert('Date unavailable: instrument rental is booked on this day. Please choose a different date.');
                this.value = '';
                const d = parseInt(durationSelect?.value || '1', 10);
                generateRescheduleTimeSlots(d);
                return;
            }
            fetch(`/api/bookings?date=${dateVal}`)
                .then(res => res.json())
                .then(bookings => {
                    lastReschedBookings = bookings || [];
                    const d = parseInt(durationSelect?.value || '1', 10);
                    generateRescheduleTimeSlots(d, lastReschedDateISO, lastReschedBookings);
                })
                .catch(() => {
                    lastReschedBookings = [];
                    const d = parseInt(durationSelect?.value || '1', 10);
                    generateRescheduleTimeSlots(d, lastReschedDateISO, lastReschedBookings);
                });
        });
    }
    
    // Update slots when duration changes
    if (durationSelect) {
        durationSelect.addEventListener('change', function() {
            const d = parseInt(this.value || '1', 10);
            generateRescheduleTimeSlots(d, lastReschedDateISO, lastReschedBookings);
        });
    }
    
    // Function to show appropriate fields based on booking type
    function showBookingFields(bookingType) {
        const studioFields = document.getElementById('studioRentalFields');
        const instrumentFields = document.getElementById('instrumentRentalFields');
        const formActions = document.getElementById('formActions');
        
        // Hide all fields first
        if (studioFields) studioFields.style.display = 'none';
        if (instrumentFields) instrumentFields.style.display = 'none';
        if (formActions) formActions.style.display = 'none';
        
        // Show appropriate fields based on booking type
        if (bookingType === 'studio_rental') {
            if (studioFields) studioFields.style.display = 'block';
            if (formActions) formActions.style.display = 'flex';
            // Set required attributes for studio rental fields
            if (newDateInput) newDateInput.required = true;
            if (newTimeSelect) newTimeSelect.required = true;
            // Remove required from instrument rental fields
            const startDateInput = document.getElementById('startDate');
            const endDateInput = document.getElementById('endDate');
            if (startDateInput) startDateInput.required = false;
            if (endDateInput) endDateInput.required = false;
        } else if (bookingType === 'instrument_rental') {
            if (instrumentFields) instrumentFields.style.display = 'block';
            if (formActions) formActions.style.display = 'flex';
            // Set required attributes for instrument rental fields
            const startDateInput = document.getElementById('startDate');
            const endDateInput = document.getElementById('endDate');
            if (startDateInput) startDateInput.required = true;
            if (endDateInput) endDateInput.required = true;
            // Remove required from studio rental fields
            if (newDateInput) newDateInput.required = false;
            if (newTimeSelect) newTimeSelect.required = false;
        }
    }
    
    // Function to hide all booking fields (initial state)
    function hideAllBookingFields() {
        const studioFields = document.getElementById('studioRentalFields');
        const instrumentFields = document.getElementById('instrumentRentalFields');
        const formActions = document.getElementById('formActions');
        
        if (studioFields) studioFields.style.display = 'none';
        if (instrumentFields) instrumentFields.style.display = 'none';
        if (formActions) formActions.style.display = 'none';
    }
    
    // Initially hide all fields except reference number
    hideAllBookingFields();
    
    if (referenceInput && validationMessage) {
        referenceInput.addEventListener('input', function() {
            const reference = this.value.trim();
            
            // Clear previous timeout
            clearTimeout(validationTimeout);
            
            // Reset validation state
            isReferenceValid = false;
            this.classList.remove('valid', 'invalid');
            validationMessage.className = 'validation-message';
            
            // Hide all booking fields when reference is empty or being validated
            hideAllBookingFields();
            
            if (reference.length === 0) {
                return;
            }
            
            // Show loading state
            validationMessage.className = 'validation-message loading';
            validationMessage.textContent = 'Validating reference...';
            
            // Debounce validation
            validationTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(`/api/validate-reference/${encodeURIComponent(reference)}`);
                    const result = await response.json();
                    
                    if (result.valid) {
                        isReferenceValid = true;
                        this.classList.add('valid');
                        validationMessage.className = 'validation-message success';
                        validationMessage.textContent = `✓ Valid booking found for ${result.booking.band_name}`;
                        
                        // Show appropriate fields based on booking type
                        const bookingType = result.booking.service_type || result.booking.type;
                        if (bookingType === 'Band Rehearsal' || bookingType === 'studio_rental') {
                            showBookingFields('studio_rental');
                            
                            // Pre-populate duration field with original booking duration
                            if (result.booking.duration && durationSelect) {
                                durationSelect.value = result.booking.duration;
                                // Regenerate time slots based on the actual duration
                                generateRescheduleTimeSlots(result.booking.duration);
                            }
                        } else if (bookingType === 'Instrument Rental' || bookingType === 'instrument_rental') {
                            showBookingFields('instrument_rental');
                            const endDateEl = document.getElementById('endDate');
                            if (endDateEl) {
                                let fixedEnd = null;
                                if (result.booking) {
                                    fixedEnd = result.booking.end_date || result.booking.return_date || result.booking.rental_end_date || null;
                                    if (!fixedEnd && result.booking.start_date) {
                                        fixedEnd = result.booking.start_date;
                                    }
                                }
                                if (fixedEnd) {
                                    endDateEl.value = fixedEnd;
                                }
                                endDateEl.setAttribute('readonly', 'true');
                                endDateEl.setAttribute('disabled', 'true');
                                endDateEl.setAttribute('placeholder', '1 day');
                                endDateEl.classList.add('fixed-date');
                                const helpEl = document.getElementById('end-date-help');
                                if (helpEl) {
                                    helpEl.textContent = 'Fixed date: 1 day';
                                }
                                const startDateInputSync = document.getElementById('startDate');
                                if (startDateInputSync) {
                                    startDateInputSync.addEventListener('change', function() {
                                        endDateEl.value = this.value;
                                    });
                                }
                            }
                        } else {
                            // Default to studio rental if type is unclear
                            showBookingFields('studio_rental');
                            
                            // Pre-populate duration field with original booking duration
                            if (result.booking.duration && durationSelect) {
                                durationSelect.value = result.booking.duration;
                                // Regenerate time slots based on the actual duration
                                generateRescheduleTimeSlots(result.booking.duration);
                            }
                        }
                    } else {
                        isReferenceValid = false;
                        this.classList.add('invalid');
                        validationMessage.className = 'validation-message error';
                        validationMessage.textContent = result.message || 'Reference number not found';
                        // Keep fields hidden when reference is invalid
                        hideAllBookingFields();
                    }
                } catch (error) {
                    console.error('Validation error:', error);
                    isReferenceValid = false;
                    this.classList.add('invalid');
                    validationMessage.className = 'validation-message error';
                    validationMessage.textContent = 'Error validating reference. Please try again.';
                    // Keep fields hidden on error
                    hideAllBookingFields();
                }
            }, 500);
        });
    }

    // Handle form submission
    if (rescheduleForm) {
        rescheduleForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Get form data
            const referenceNumber = document.getElementById('referenceNumber').value;
            
            // Check which type of booking fields are visible
            const studioFields = document.getElementById('studioRentalFields');
            const instrumentFields = document.getElementById('instrumentRentalFields');
            
            let formData = {
                reference_number: referenceNumber
            };
            
            // Validate based on visible fields
            if (studioFields && studioFields.style.display !== 'none') {
                // Studio rental validation and data
                const newDate = document.getElementById('newDate').value;
                const newTime = document.getElementById('newTime').value;
                const duration = document.getElementById('duration').value;
                
                if (!referenceNumber || !newDate || !newTime || !duration) {
                    alert('Please fill in all fields.');
                    return;
                }
                
                formData.new_date = newDate;
                formData.new_time_slot = newTime;
                formData.duration = parseInt(duration);
                formData.booking_type = 'studio_rental';
                
            } else if (instrumentFields && instrumentFields.style.display !== 'none') {
                // Instrument rental validation and data
                const startDate = document.getElementById('startDate').value;
                const endDateEl = document.getElementById('endDate');
                let endDate;
                
                // If end date is fixed/disabled, mirror start date
                if (endDateEl && endDateEl.disabled) {
                    endDate = startDate;
                } else {
                    endDate = endDateEl ? endDateEl.value : '';
                }
                
                if (!referenceNumber || !startDate) {
                    alert('Please fill in all fields.');
                    return;
                }
                
                // Validate that end date is after start date unless fixed/disabled
                if (!endDateEl || !endDateEl.disabled) {
                    if (!endDate) {
                        alert('Please fill in all fields.');
                        return;
                    }
                    if (new Date(endDate) <= new Date(startDate)) {
                        alert('End date must be after start date.');
                        return;
                    }
                }
                
                formData.start_date = startDate;
                formData.end_date = endDate;
                formData.booking_type = 'instrument_rental';
                
            } else {
                alert('Please verify your reference number first.');
                return;
            }
            
            if (!isReferenceValid) {
                alert('Please enter a valid reference number.');
                return;
            }
            
            // Show loading state
            const submitBtn = rescheduleForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Submitting...';
            submitBtn.disabled = true;
            
            try {
                
                // Submit to API (using a generic endpoint since we're sending band name and reference in the body)
                const response = await fetch('/api/bookings/reschedule', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                
                if (response.ok && result.success) {
                    // Show success modal
                    showRescheduleSuccessModal(result.message);
                    
                    // Reset form and close modal
                    rescheduleForm.reset();
                    reschedulePopup.style.display = 'none';
                    
                    // Regenerate time slots for default duration
                    generateRescheduleTimeSlots(1);
                } else {
                    // Show detailed error message from API (handles Laravel 422 validation format)
                    let message = result.error || result.message || 'Failed to submit reschedule request. Please try again.';
                    if (result && result.errors) {
                        const firstError = Object.values(result.errors).flat()[0];
                        if (firstError) message = firstError;
                    }
                    showRescheduleErrorModal(message);
                }
                
            } catch (error) {
                console.error('Error submitting reschedule request:', error);
                showRescheduleErrorModal('Network error. Please check your connection and try again.');
            } finally {
                // Restore button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });
    }
  });
  </script>

  <script>
  // Function to show reschedule success modal with countdown and auto-refresh
  function showRescheduleSuccessModal(message) {
      const modal = document.getElementById('rescheduleSuccessModal');
      const messageDiv = document.getElementById('rescheduleSuccessMessage');
      const countdownSpan = document.getElementById('rescheduleCountdown');
      
      if (modal && messageDiv && countdownSpan) {
          messageDiv.innerHTML = message;
          modal.style.display = 'flex';
          
          let countdown = 5;
          countdownSpan.textContent = countdown;
          
          const countdownInterval = setInterval(() => {
              countdown--;
              countdownSpan.textContent = countdown;
              
              if (countdown <= 0) {
                  clearInterval(countdownInterval);
                  window.location.reload();
              }
          }, 1000);
      }
  }

  // Function to close reschedule success modal
  function closeRescheduleSuccessModal() {
      const modal = document.getElementById('rescheduleSuccessModal');
      if (modal) {
          modal.style.display = 'none';
      }
  }

  // Close modal when clicking outside
  document.addEventListener('click', function(e) {
      const modal = document.getElementById('rescheduleSuccessModal');
      if (e.target === modal) {
          closeRescheduleSuccessModal();
      }
  });

  // Close modal with Escape key
  document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
          closeRescheduleSuccessModal();
      }
  });

  // Error Modal Functions
  function showRescheduleErrorModal(message) {
      const modal = document.getElementById('rescheduleErrorModal');
      const messageElement = document.getElementById('rescheduleErrorMessage');
      
      if (modal && messageElement) {
          messageElement.innerHTML = message;
          modal.style.display = 'flex';
      }
  }

  function closeRescheduleErrorModal() {
      const modal = document.getElementById('rescheduleErrorModal');
      if (modal) {
          modal.style.display = 'none';
      }
  }

  // Close error modal when clicking outside
  document.addEventListener('click', function(e) {
      const errorModal = document.getElementById('rescheduleErrorModal');
      if (e.target === errorModal) {
          closeRescheduleErrorModal();
      }
  });

  // Close error modal with Escape key
  document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
          closeRescheduleErrorModal();
      }
  });
  </script>
</body>
</html>
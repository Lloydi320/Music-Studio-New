<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
  <title> Lemon Hub Studio</title>
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
        <li><a href="/services" class="active">About Us & Our Services</a></li>
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
            
            <form action="/logout" method="POST" class="logout-form">
              @csrf
              <button type="submit" class="dropdown-item logout-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                  <path d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M16 17L21 12L16 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Sign Out</span>
              </button>
            </form>
          </div>
        </div>
        </div>
    @else
      <a href="/auth/google" class="book-btn" style="margin-left: 30px;">Login</a>
    @endif
  </header>

 
  <section class="about-modern">
    <div class="about-text">
      <h1>Why Choose Lemon Hub Studio?</h1>
      <ul>
        <li>🎸 Qualified and passionate music instructors</li>
        <li>🎤 Friendly and creative learning environment</li>
        <li>🎹 Flexible booking and guided lessons</li>
        <li>🥁 Unlock your full potential as a performing artist</li>
      </ul>
    </div>

    <div class="about-img">
      <img src="{{ asset('images/studio.jpg') }}" alt="Studio" class="static-about-img" />
    </div>
  </section>

 
  <section id="our-services" class="services-showcase">

    <h2>Our Services</h2>

    <div class="service-grid">
     <a href="/booking" class="service-box">
        <img src="{{ asset('images/studio.jpg') }}" alt="Studio Rental" />
        <h3>Band Rehearsal</h3>
        <p>Book our acoustically treated studios for jamming, rehearsals, or recording. Fully equipped and flexible.</p>
        <small class="service-hint">Click to Book</small>
      </a>

      <a href="/solo-rehearsal" class="service-box">
        <img src="{{ asset('images/SoloRehearsal.jpg') }}" alt="Solo Rehearsal" />
        <h3>Solo Rehearsal</h3>
        <p>Perfect for individual practice sessions. Book our acoustically treated studios for solo rehearsals and personal music development.</p>
        <small class="service-hint">Click to Book</small>
      </a>

      <a href="/instrument-rental" class="service-box">
        <img src="{{ asset('images/instruments.png') }}" alt="Instruments Rental" />
        <h3>Instruments Rental</h3>
        <p>Need a guitar, amp, or mic? Rent affordable gear for your session without the hassle.</p>
        <small class="service-hint">Click to Rent</small>
      </a>

      <a href="/music-lessons" class="service-box">
        <img src="{{ asset('images/lessons.jpg') }}" alt="Music Lessons" />
        <h3>Music Lessons</h3>
        <p>Private or group lessons in vocals, guitar, keyboard, and drums. Ideal for all ages and skill levels.</p>
        <small class="service-hint">Click to see more details</small>
      </a>

    </div>
  </section>

  <div id="bottom-of-services"></div>
  
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
@include('partials.feedback-modal')

<!-- Modern Rescheduling Modal -->
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
                <input type="date" id="newDate" name="newDate" aria-describedby="date-help" class="date-picker-input">
                <div class="date-picker-icon">📅</div>
              </div>
              <small id="date-help" class="form-help">Click to open calendar and select your preferred date</small>
            </div>
            
            <div class="form-group">
              <label for="duration">⏱️ Duration</label>
              <select id="duration" name="duration" aria-describedby="duration-help" disabled>
                <option value="1" selected>1 hour (Fixed)</option>
              </select>
              <small id="duration-help" class="form-help">Duration is fixed at 1 hour for rescheduling</small>
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
                <input type="date" id="startDate" name="startDate" aria-describedby="start-date-help" class="date-picker-input">
                <div class="date-picker-icon">📅</div>
              </div>
              <small id="start-date-help" class="form-help">Select the new start date for your rental</small>
            </div>
            
            <div class="form-group">
              <label for="endDate">📅 End Date</label>
              <div class="date-input-wrapper">
                <input type="date" id="endDate" name="endDate" aria-describedby="end-date-help" class="date-picker-input">
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

<!-- Reschedule Success Modal -->
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

<!-- Reschedule Error Modal -->
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

  <!-- Footer -->
  <footer class="services-footer">
    <div class="footer-content">
      <p>&copy; 2025 Lemon Hub Studio - All Rights Reserved</p>
      <p>Professional Music Studio Services</p>
    </div>
  </footer>

      <script src="{{ asset('js/script.js') }}"></script>
      <script src="{{ asset('js/page-transitions.js') }}"></script>
  <script>
  // Check for hash on page load and scroll to it
  document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash === '#bottom-of-services') {
      // Check if user is authenticated
      var isAuthenticated = @json(Auth::check());
      if (!isAuthenticated) {
        // Redirect to login if not authenticated
        window.location.href = '/auth/google';
        return;
      }
      
      setTimeout(function() {
        const element = document.getElementById('bottom-of-services');
        if (element) {
          element.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
          });
        }
      }, 500);
    }
  });
</script>

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
        // Contact functionality handled by other contact methods
    });

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

    // Function to generate time slots based on duration
    function generateRescheduleTimeSlots(durationHours) {
        const timeSelect = document.getElementById('newTime');
        if (!timeSelect) return;
        
        // Clear existing options except the first one
        timeSelect.innerHTML = '<option value="">Select a time slot</option>';
        
        const openingHour = 8;
        const closingHour = 20; // 8 PM
        const durationMinutes = durationHours * 60;
        
        let currentHour = openingHour;
        let currentMinute = 0;
        
        while (currentHour < closingHour) {
            const startTime = new Date();
            startTime.setHours(currentHour, currentMinute, 0, 0);
            
            const endTime = new Date(startTime.getTime() + durationMinutes * 60000);
            
            // Check if end time doesn't exceed closing hour
            if (endTime.getHours() <= closingHour) {
                const startTimeStr = startTime.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                }).replace(/\s/g, '');
                const endTimeStr = endTime.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                }).replace(/\s/g, '');
                
                const timeSlot = `${startTimeStr} - ${endTimeStr}`;
                const option = document.createElement('option');
                option.value = timeSlot;
                option.textContent = timeSlot;
                timeSelect.appendChild(option);
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
    
    // Initialize time slots with fixed 1-hour duration
    generateRescheduleTimeSlots(1);
    
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
                        if (bookingType === 'Studio Rental' || bookingType === 'studio_rental') {
                            showBookingFields('studio_rental');
                        } else if (bookingType === 'Instrument Rental' || bookingType === 'instrument_rental') {
                            showBookingFields('instrument_rental');
                        } else {
                            // Default to studio rental if type is unclear
                            showBookingFields('studio_rental');
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
                const endDate = document.getElementById('endDate').value;
                
                if (!referenceNumber || !startDate || !endDate) {
                    alert('Please fill in all fields.');
                    return;
                }
                
                // Validate that end date is after start date
                if (new Date(endDate) <= new Date(startDate)) {
                    alert('End date must be after start date.');
                    return;
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
                    // Show error message with better UI
                    showRescheduleErrorModal(result.error || 'Failed to submit reschedule request. Please try again.');
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

  // Function to show reschedule error modal
  function showRescheduleErrorModal(message) {
      const modal = document.getElementById('rescheduleErrorModal');
      const messageDiv = document.getElementById('rescheduleErrorMessage');
      
      if (modal && messageDiv) {
          messageDiv.innerHTML = message;
          modal.style.display = 'flex';
      }
  }

  // Function to close reschedule error modal
  function closeRescheduleErrorModal() {
      const modal = document.getElementById('rescheduleErrorModal');
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
</script>

<script>
  // Rescheduling date validation variables
  let rescheduleBookedDates = [];
  let rescheduleInstrumentBookedDates = [];

  // Fetch booked dates for studio/band bookings
  async function fetchRescheduleBookedDates() {
    try {
      const response = await fetch('/api/booked-dates');
      const data = await response.json();
      rescheduleBookedDates = data.booked_dates || [];
      console.log('Fetched booked dates for rescheduling:', rescheduleBookedDates);
    } catch (error) {
      console.error('Error fetching booked dates for rescheduling:', error);
    }
  }

  // Fetch booked dates for instrument rentals
  async function fetchRescheduleInstrumentBookedDates() {
    try {
      const response = await fetch('/api/instrument-rental/booked-dates');
      const data = await response.json();
      rescheduleInstrumentBookedDates = data.booked_dates || [];
      console.log('Fetched instrument booked dates for rescheduling:', rescheduleInstrumentBookedDates);
    } catch (error) {
      console.error('Error fetching instrument booked dates for rescheduling:', error);
    }
  }

  // Validate date selection for rescheduling
  function validateRescheduleDateSelection(event) {
    const input = event.target;
    const selectedDate = input.value;
    const isInstrumentRental = input.id === 'startDate' || input.id === 'endDate';
    
    // Remove any existing conflict alerts
    const existingAlert = input.parentNode.querySelector('.date-conflict-alert');
    if (existingAlert) {
      existingAlert.remove();
    }
    
    // Check if selected date is in the past
    if (selectedDate) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      const selected = new Date(selectedDate + 'T00:00:00');
      
      if (selected < today) {
        // Prevent past date selection
        input.value = '';
        input.classList.add('date-unavailable');
        
        const formattedDate = selected.toLocaleDateString('en-US', { 
          year: 'numeric', 
          month: 'long', 
          day: 'numeric' 
        });
        
        // Create and show alert for past date
        const alertDiv = document.createElement('div');
        alertDiv.className = 'date-conflict-alert';
        alertDiv.style.cssText = `
          background: linear-gradient(135deg, #ff6b6b, #ee5a52);
          color: white;
          padding: 12px 16px;
          border-radius: 8px;
          margin-top: 8px;
          font-size: 14px;
          box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
          border-left: 4px solid #ff4757;
          animation: slideIn 0.3s ease-out;
        `;
        alertDiv.innerHTML = `
          <strong>⚠️ Invalid Date:</strong> ${formattedDate} is in the past. Please select today or a future date.
        `;
        
        input.parentNode.appendChild(alertDiv);
        
        // Remove alert after 5 seconds
        setTimeout(() => {
          if (alertDiv.parentNode) {
            alertDiv.remove();
          }
        }, 5000);
        
        return false;
      }
    }
    
    // Check for date conflicts based on booking type
    const bookedDates = isInstrumentRental ? rescheduleInstrumentBookedDates : rescheduleBookedDates;
    
    if (selectedDate && bookedDates.includes(selectedDate)) {
      // Prevent the date from being selected
      input.value = '';
      
      // Add visual styling to indicate unavailable date
      input.classList.add('date-unavailable');
      
      // Show user-friendly message
      const dateObj = new Date(selectedDate + 'T00:00:00');
      const formattedDate = dateObj.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
      });
      
      const conflictType = isInstrumentRental ? 'instrument rental or studio booking' : 'studio/band or solo rehearsal';
      
      // Create and show alert
      const alertDiv = document.createElement('div');
      alertDiv.className = 'date-conflict-alert';
      alertDiv.style.cssText = `
        background: linear-gradient(135deg, #ff6b6b, #ee5a52);
        color: white;
        padding: 12px 16px;
        border-radius: 8px;
        margin-top: 8px;
        font-size: 14px;
        box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
        border-left: 4px solid #ff4757;
        animation: slideIn 0.3s ease-out;
      `;
      alertDiv.innerHTML = `
        <strong>🚫 Date Unavailable:</strong> ${formattedDate} is already booked for ${conflictType}. Please choose a different date.
      `;
      
      input.parentNode.appendChild(alertDiv);
      
      // Remove alert after 5 seconds
      setTimeout(() => {
        if (alertDiv.parentNode) {
          alertDiv.remove();
        }
      }, 5000);
      
      return false;
    } else {
      // Remove unavailable styling if date is valid
      input.classList.remove('date-unavailable');
    }
    
    // Additional validation for instrument rental date range conflicts
    if (isInstrumentRental) {
      const startDate = document.getElementById('startDate').value;
      const endDate = document.getElementById('endDate').value;
      
      if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const conflictDates = [];
        
        // Check if any date in the range is booked
        for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
          const dateStr = d.toISOString().split('T')[0];
          if (rescheduleInstrumentBookedDates.includes(dateStr)) {
            conflictDates.push(dateStr);
          }
        }
        
        if (conflictDates.length > 0) {
          const conflictDateStrings = conflictDates.map(date => {
            const dateObj = new Date(date + 'T00:00:00');
            return dateObj.toLocaleDateString('en-US', { 
              year: 'numeric', 
              month: 'long', 
              day: 'numeric' 
            });
          });
          
          // Create and show range conflict alert
          const alertDiv = document.createElement('div');
          alertDiv.className = 'date-conflict-alert';
          alertDiv.style.cssText = `
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            margin-top: 8px;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
            border-left: 4px solid #ff4757;
            animation: slideIn 0.3s ease-out;
          `;
          alertDiv.innerHTML = `
            <strong>⚠️ Date Range Conflict:</strong> The following dates in your rental period are already booked: ${conflictDateStrings.join(', ')}. Please choose different dates.
          `;
          
          input.parentNode.appendChild(alertDiv);
          
          // Clear the problematic date and add styling
          input.value = '';
          input.classList.add('date-unavailable');
          
          // Remove alert after 7 seconds
          setTimeout(() => {
            if (alertDiv.parentNode) {
              alertDiv.remove();
            }
          }, 7000);
          
          return false;
        }
      }
    }
    
    return true;
  }

  // Initialize rescheduling date validation when DOM is loaded
  document.addEventListener('DOMContentLoaded', function() {
    // Fetch booked dates for validation
    fetchRescheduleBookedDates();
    fetchRescheduleInstrumentBookedDates();
    
    // Add event listeners to date inputs
    const newDateInput = document.getElementById('newDate');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    
    if (newDateInput) {
      newDateInput.addEventListener('change', validateRescheduleDateSelection);
      newDateInput.addEventListener('input', validateRescheduleDateSelection);
    }
    
    if (startDateInput) {
      startDateInput.addEventListener('change', validateRescheduleDateSelection);
      startDateInput.addEventListener('input', validateRescheduleDateSelection);
    }
    
    if (endDateInput) {
      endDateInput.addEventListener('change', validateRescheduleDateSelection);
      endDateInput.addEventListener('input', validateRescheduleDateSelection);
    }
  });
</script>

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

/* Hide Sign Out button in navigation on desktop view */
@media (min-width: 769px) {
  .nav-signout-desktop-hidden {
    display: none !important;
  }
}
</style>

<script src="{{ asset('js/feedback.js') }}"></script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Lemon Hub Studio</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
  <link rel="preload" href="{{ asset('images/studio-bg.jpg') }}" as="image">

</head>

<body class="home-page">


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
        <li><a href="/" class="active">Home</a></li>
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
        @if(!Auth::check())
        <li class="nav-login-mobile">
          <a href="/login" style="color: #FFD700; padding: 15px 20px; font-size: 1.1rem; text-decoration: none; width: 100%; text-align: left; border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: block;">
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
            @if($user->is_admin)
              <a href="/admin/dashboard" class="dropdown-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                  <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Admin Dashboard</span>
              </a>
              <a href="/admin/calendar" class="dropdown-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                  <path d="M19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M16 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M8 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Calendar</span>
              </a>
            @endif
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
    @endif
  </header>

  
  <section class="hero">
    <div class="hero-overlay" id="mainOverlay">
      <div class="hero-content">
        <h1>BOOK YOUR STUDIO SESSION TODAY!</h1>
        <p>Bringing your music to life, one session at a time.</p>
        @if(Auth::check())
          <button id="openServicesPopup" class="book-btn">Book Now!</button>
          @if(Auth::user()->isAdmin())
            <a href="/admin/dashboard" class="book-btn" style="background: #e74c3c; margin-left: 10px;">Admin Panel</a>
          @endif
        @else
          <a href="/login" class="book-btn">Login to Book Now!</a>
        @endif
      </div>
      
      <!-- Home Carousel: teachers-style visuals, no overlay details -->
      <style>
        /* Home teachers-style carousel */
        .home-carousel-container { position: relative; width: 100%; max-width: 1100px; margin: 20px auto; border-radius: 14px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,.35); }
        .home-carousel-wrapper { overflow: hidden; }
        .home-carousel-track { display: flex; transition: transform 0.5s ease; }
        .home-carousel-slide { flex: 0 0 100%; }
        .home-carousel-card { position: relative; height: 60vh; min-height: 380px; background: #000; }
        .home-carousel-image, .home-carousel-image img { width: 100%; height: 100%; }
        .home-carousel-image img { object-fit: cover; display: block; }
        .home-carousel-card::before { content: ""; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,0.35) 0%, rgba(0,0,0,0.35) 100%); pointer-events: none; }
        .home-carousel-btn { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255,215,0,0.9); color: #000; border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,.25); display: flex; align-items: center; justify-content: center; }
        .home-carousel-btn-prev { left: 14px; }
        .home-carousel-btn-next { right: 14px; }
        .home-carousel-btn:hover { background: #FFD700; }
        .home-carousel-dots { position: absolute; bottom: 14px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; }
        .home-dot { width: 10px; height: 10px; border-radius: 50%; border: 2px solid #FFD700; background: transparent; cursor: pointer; opacity: 0.7; }
        .home-dot.active { background: #FFD700; opacity: 1; }
        @media (max-width: 1350px) {
          .home-carousel-card { height: 50vh; min-height: 320px; }
        }
        @media (max-width: 800px) {
          .home-carousel-card { height: 35vh; min-height: 240px; }
          .home-carousel-btn { width: 34px; height: 34px; }
        }
      </style>
      <div class="image-display" id="carouselContainer">
        <div class="home-carousel-container">
          <div class="home-carousel-wrapper">
            <div class="home-carousel-track" id="homeCarouselTrack">
              <div class="home-carousel-slide">
                <div class="home-carousel-card">
                  <div class="home-carousel-image">
                    <img src="{{ asset('images/studio.jpg') }}" alt="Lemon Hub Studio" loading="lazy">
                  </div>
                </div>
              </div>
              <div class="home-carousel-slide">
                <div class="home-carousel-card">
                  <div class="home-carousel-image">
                    <img src="{{ asset('images/Band.jpg') }}" alt="Band Rehearsal" loading="lazy">
                  </div>
                </div>
              </div>
              <div class="home-carousel-slide">
                <div class="home-carousel-card">
                  <div class="home-carousel-image">
                    <img src="{{ asset('images/SoloRehearsal.jpg') }}" alt="Solo Rehearsal" loading="lazy">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <button class="home-carousel-btn home-carousel-btn-prev" id="homePrevBtn" aria-label="Previous slide">&#9664;</button>
          <button class="home-carousel-btn home-carousel-btn-next" id="homeNextBtn" aria-label="Next slide">&#9654;</button>
          <div class="home-carousel-dots" id="homeCarouselDots">
            <button class="home-dot active" data-slide="0" aria-label="Go to slide 1"></button>
            <button class="home-dot" data-slide="1" aria-label="Go to slide 2"></button>
            <button class="home-dot" data-slide="2" aria-label="Go to slide 3"></button>
          </div>
        </div>
      </div>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          const track = document.getElementById('homeCarouselTrack');
          const prevBtn = document.getElementById('homePrevBtn');
          const nextBtn = document.getElementById('homeNextBtn');
          const dotsContainer = document.getElementById('homeCarouselDots');
          const container = document.querySelector('#carouselContainer .home-carousel-container');
          if (!track || !container) { return; }
          const slides = Array.from(track.querySelectorAll('.home-carousel-slide'));
          const dots = Array.from(dotsContainer ? dotsContainer.querySelectorAll('.home-dot') : []);
          let current = 0;
          const total = slides.length;
          function update() {
            track.style.transform = 'translateX(' + (-current * 100) + '%)';
            dots.forEach((d, i) => d.classList.toggle('active', i === current));
          }
          function next() { current = (current + 1) % total; update(); }
          function prev() { current = (current - 1 + total) % total; update(); }
          if (prevBtn) prevBtn.addEventListener('click', prev);
          if (nextBtn) nextBtn.addEventListener('click', next);
          dots.forEach((d, i) => d.addEventListener('click', () => { current = i; update(); }));
          let startX = null;
          container.addEventListener('touchstart', e => { startX = e.touches[0].clientX; });
          container.addEventListener('touchend', e => {
            if (startX === null) return;
            const endX = e.changedTouches[0].clientX;
            const dx = startX - endX;
            startX = null;
            if (Math.abs(dx) > 50) { dx > 0 ? next() : prev(); }
          });
          let auto = setInterval(next, 6000);
          container.addEventListener('mouseenter', () => clearInterval(auto));
          container.addEventListener('mouseleave', () => { auto = setInterval(next, 6000); });
          update();
        });
      </script>
      
      <!-- Floating Action Button for Calendar -->
      <button class="calendar-fab" id="calendarFab" title="Toggle Calendar">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
          <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2"/>
          <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2"/>
          <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2"/>
        </svg>
      </button>
      
      <div class="calendar-container hidden" id="calendarContainer">
        <div id="calendar-header">
          <button id="prevMonth">&#9664;</button>
          <span id="monthYear"></span>
          <button id="nextMonth">&#9654;</button>
        </div>
        <div class="calendar-grid" id="calendarGrid"></div>
        <!-- Fixed Booking Info header (left column, top row) -->
        <div class="booking-header" id="bookingHeader">Booking Info</div>
        <div class="time-slots" id="timeSlots"></div>
      </div>
    </div>
  </section>

 
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
          <div style="text-align: center; padding: 40px; color: #666;">
            <div style="font-size: 3em; margin-bottom: 15px; opacity: 0.5;">📝</div>
            <h3 style="margin: 0 0 10px 0; color: #333;">No feedback yet</h3>
            <p style="margin: 0; font-size: 0.9em;">Be the first to share your experience!</p>
          </div>
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

<!-- Services Popup Modal -->
<div id="servicesPopup" class="services-popup">
  <div class="services-modal">
    <div class="services-modal-header">
      <h2>Choose Your Service</h2>
      <button class="close-services" id="closeServices">&times;</button>
    </div>
    <div class="services-modal-content">
      <div class="service-grid-popup">
        <a href="/booking" class="service-box-popup">
          <img src="{{ asset('images/studio.jpg') }}" alt="Band Rehearsal" />
          <h3>Band Rehearsal</h3>
          <p>Book our acoustically treated studios for jamming, rehearsals, or recording. Fully equipped and flexible.</p>
          <small class="service-hint">Click to Book</small>
        </a>

        <a href="/solo-rehearsal" class="service-box-popup">
          <img src="{{ asset('images/SoloRehearsal.jpg') }}" alt="Solo Rehearsal" />
          <h3>Solo Rehearsal</h3>
          <p>Perfect for individual practice sessions. Book our acoustically treated studios for solo rehearsals and personal music development.</p>
          <small class="service-hint">Click to Book</small>
        </a>

        <a href="/instrument-rental" class="service-box-popup">
          <img src="{{ asset('images/instruments.png') }}" alt="Instruments Rental" />
          <h3>Instruments Rental</h3>
          <p>Need a guitar, amp, or mic? Rent affordable gear for your session without the hassle.</p>
          <small class="service-hint">Click to Rent</small>
        </a>

        <a href="/music-lessons" class="service-box-popup">
          <img src="{{ asset('images/lessons.jpg') }}" alt="Music Lessons" />
          <h3>Music Lessons</h3>
          <p>Private or group lessons in vocals, guitar, keyboard, and drums. Ideal for all ages and skill levels.</p>
          <small class="service-hint">Click to see more details</small>
        </a>
      </div>
    </div>
  </div>
</div>

  

      <script src="{{ asset('js/script.js') }}"></script>
      <script src="{{ asset('js/page-transitions.js') }}"></script>
  
  <script>
    // Preload background image for faster loading
    document.addEventListener('DOMContentLoaded', function() {
      const hero = document.querySelector('.hero');
      const bgImage = new Image();
      
      bgImage.onload = function() {
        hero.classList.add('loaded');
      };
      
      bgImage.src = '/images/studio-bg.jpg';
    });
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
      
      // Fetch actual feedback from database
      fetch('/api/feedbacks')
        .then(response => response.json())
        .then(data => {
          console.log('✅ Feedback loaded successfully:', data);
          
          if (!data.feedbacks || data.feedbacks.length === 0) {
            container.innerHTML = `
              <div style="text-align: center; padding: 40px; color: #666;">
                <div style="font-size: 3em; margin-bottom: 15px; opacity: 0.5;">📝</div>
                <h3 style="margin: 0 0 10px 0; color: #333;">No feedback yet</h3>
                <p style="margin: 0; font-size: 0.9em;">Be the first to share your experience!</p>
              </div>
            `;
            return;
          }
          
          container.innerHTML = '';
          data.feedbacks.forEach(feedback => {
            const card = createFeedbackCard(feedback);
            container.appendChild(card);
          });
        })
        .catch(error => {
          console.error('❌ Error loading feedback:', error);
          // Show the same "No feedback yet" message even on error
          container.innerHTML = `
            <div style="text-align: center; padding: 40px; color: #666;">
              <div style="font-size: 3em; margin-bottom: 15px; opacity: 0.5;">📝</div>
              <h3 style="margin: 0 0 10px 0; color: #333;">No feedback yet</h3>
              <p style="margin: 0; font-size: 0.9em;">Be the first to share your experience!</p>
            </div>
          `;
        });
    }
    
    // Function to create feedback card
    function createFeedbackCard(feedback) {
      const card = document.createElement('div');
      card.className = 'feedback-entry';
      card.style.cssText = `
        border: 2px solid #ffd700;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
        animation: slideIn 0.3s ease;
      `;
      
      // Add hover effect
      card.addEventListener('mouseenter', () => {
        card.style.transform = 'translateY(-2px)';
        card.style.boxShadow = '0 6px 20px rgba(0,0,0,0.15)';
      });
      
      card.addEventListener('mouseleave', () => {
        card.style.transform = 'translateY(0)';
        card.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
      });
      
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
          <div style="margin-top: 10px;">
            <img src="${feedback.photo_url}" 
                 style="width: 100%; max-width: 200px; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); cursor: pointer;" 
                 onclick="openPhotoModal('${feedback.photo_url}')" 
                 alt="Feedback photo" />
          </div>
        `;
      }
      
      const userTypeIcon = feedback.user_type === 'Authenticated' ? '👤' : '👥';
      const userTypeColor = feedback.user_type === 'Authenticated' ? '#007bff' : '#6c757d';
      
      card.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
          <div style="display: flex; align-items: center; gap: 8px;">
            <h4 style="margin: 0; color: #333; font-size: 1.1em; font-weight: bold;">${feedback.name}</h4>
            <span style="background: ${userTypeColor}; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.6em; font-weight: bold;">
              ${userTypeIcon} ${feedback.user_type}
            </span>
          </div>
          <div style="text-align: right;">
            <div style="font-size: 1.3em; color: ${starColor}; margin-bottom: 3px;">${stars}</div>
            <small style="color: #666; font-size: 0.8em;">${feedback.rating}/5 stars</small>
          </div>
        </div>
        <div style="background: #f8f9fa; padding: 12px; border-radius: 6px; margin: 8px 0; border-left: 3px solid #ffd700;">
          <p style="margin: 0; color: #555; line-height: 1.5; font-style: italic; font-size: 0.9em;">"${feedback.comment}"</p>
        </div>
        ${photoHtml}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
          <div style="display: flex; align-items: center; gap: 10px;">
            <small style="color: #888; font-size: 0.75em;">📅 ${formattedDate}</small>
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
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.8em;
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
    
    // Load feedback when page loads
    document.addEventListener('DOMContentLoaded', function() {
      console.log('🚀 Home page loaded, feedback system ready...');
      
      // Handle feedback form submission
      const feedbackForm = document.getElementById('feedbackForm');
      if (feedbackForm) {
        // Add rating star functionality
        const ratingStars = document.querySelectorAll('.rating-stars span');
        console.log('⭐ Rating stars found:', ratingStars.length);
        
        ratingStars.forEach((star, index) => {
          star.addEventListener('click', () => {
            selectedRating = index + 1;
            console.log('⭐ Rating selected:', selectedRating);
            updateStars();
          });
        });
        
        function updateStars() {
          console.log('🎨 Updating stars display for rating:', selectedRating);
          ratingStars.forEach((star, index) => {
            if (index < selectedRating) {
              star.style.color = '#ffd700';
            } else {
              star.style.color = '#ccc';
            }
          });
        }
        
        feedbackForm.addEventListener('submit', async function(e) {
          e.preventDefault();
          console.log('🚀 Form submission started...');
          
          const name = document.getElementById('name').value.trim();
          const comment = document.getElementById('comment').value.trim();
          const photo = document.getElementById('photo').files[0];
          
          console.log('📝 Form data:', { name, comment, selectedRating, hasPhoto: !!photo });
          
          if (!name || !comment || selectedRating === 0) {
            console.log('❌ Validation failed:', { name: !!name, comment: !!comment, selectedRating });
            alert('Please fill in all required fields and select a rating.');
            return;
          }
          
          console.log('✅ Validation passed, preparing submission...');
          
          // Create form data
          const formData = new FormData();
          formData.append('name', name);
          formData.append('rating', selectedRating);
          formData.append('comment', comment);
          if (photo) {
            formData.append('photo', photo);
          }
          
          console.log('📦 FormData prepared, making API request...');
          
          try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            console.log('🔐 CSRF token found:', token ? 'Yes' : 'No');
            
            const response = await fetch('/api/feedback', {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': token
              },
              body: formData
            });
            
            console.log('📡 API response status:', response.status, response.statusText);
            
            if (response.ok) {
              const result = await response.json();
              console.log('✅ Success response:', result);
              
              // Create feedback card for immediate display
              const feedbackCard = createFeedbackCard({
                id: result.feedback.id,
                name: name,
                rating: selectedRating,
                comment: comment,
                photo_url: photo ? URL.createObjectURL(photo) : null,
                user_type: 'Guest',
                created_at: new Date().toISOString()
              });
              
              // Add to display immediately
              const container = document.getElementById('feedbackEntries');
              // Remove the "No feedback yet" message if it exists
              const noFeedbackMessage = container.querySelector('div[style*="text-align: center"]');
              if (noFeedbackMessage) {
                noFeedbackMessage.remove();
              }
              
              // Insert at the top
              container.insertBefore(feedbackCard, container.firstChild);
              
              // Reset form
              feedbackForm.reset();
              selectedRating = 0;
              updateStars();
              
              // Show success message
              showSuccessMessage('✅ Feedback submitted successfully!');
              
            } else {
              const errorData = await response.json();
              console.log('❌ Error response:', errorData);
              alert('Error submitting feedback: ' + (errorData.message || 'Unknown error'));
            }
          } catch (error) {
            console.error('💥 Exception during submission:', error);
            alert('Error submitting feedback. Please try again.');
          }
        });
      }
      
      // Load feedback when popup opens
      const feedbackLink = document.getElementById('feedbackLink');
      const feedbackPopup = document.getElementById('feedbackPopup');
      const closeFeedback = document.getElementById('closeFeedback');
      
      if (feedbackLink && feedbackPopup && closeFeedback) {
        feedbackLink.addEventListener('click', (e) => {
          e.preventDefault();
          console.log('📊 Feedback popup opened, loading database content...');
          
          const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
          document.body.style.overflow = 'hidden';
          document.body.style.paddingRight = `${scrollbarWidth}px`;
          feedbackPopup.classList.add('active');
          
          // Load feedback from database when popup opens
          setTimeout(() => {
            loadFeedbacks();
          }, 100);
        });
        
        closeFeedback.addEventListener('click', () => {
          feedbackPopup.classList.remove('active');
          document.body.style.overflow = '';
          document.body.style.paddingRight = '';
        });
        
        feedbackPopup.addEventListener('click', (e) => {
          if (e.target === feedbackPopup) {
            feedbackPopup.classList.remove('active');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
          }
        });
      }
    });
  </script>

  <!-- Elfsight AI Chatbot | Untitled AI Chatbot -->
  <script src="https://static.elfsight.com/platform/platform.js" async></script>
  <div class="elfsight-app-96cc4395-da06-450f-9191-5bc6e30fa5f7" data-elfsight-app-lazy id="draggable-chatbot"></div>

  <!-- Draggable Chatbot Circle Styles -->
  <style>
    #draggable-chatbot {
      position: fixed !important;
      z-index: 9999 !important;
      cursor: move;
      transition: all 0.2s ease;
    }

    /* Make the chatbot circle draggable when minimized/closed */
    #draggable-chatbot .eapps-widget-toolbar,
    #draggable-chatbot [class*="toolbar"],
    #draggable-chatbot [class*="trigger"],
    #draggable-chatbot [class*="button"] {
      cursor: move !important;
    }

    /* Hover effect for the draggable circle */
    #draggable-chatbot:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    /* Ensure the chatbot stays within viewport */
    #draggable-chatbot {
      max-width: 100vw;
      max-height: 100vh;
    }

    /* Hide Sign Out button in navigation on desktop view */
    @media (min-width: 769px) {
      .nav-signout-desktop-hidden {
        display: none !important;
      }
    }
  </style>
  
  <!-- Reset Chatbot Conversation on Page Refresh -->
  <script>
    // Comprehensive chatbot reset with multiple approaches
    window.addEventListener('load', function() {
      // Method 1: Clear all storage data
      try {
        // Clear localStorage
        Object.keys(localStorage).forEach(key => {
          if (key.toLowerCase().includes('elfsight') || 
              key.toLowerCase().includes('chatbot') || 
              key.toLowerCase().includes('eapps') ||
              key.toLowerCase().includes('widget')) {
            localStorage.removeItem(key);
          }
        });
        
        // Clear sessionStorage
        Object.keys(sessionStorage).forEach(key => {
          if (key.toLowerCase().includes('elfsight') || 
              key.toLowerCase().includes('chatbot') || 
              key.toLowerCase().includes('eapps') ||
              key.toLowerCase().includes('widget')) {
            sessionStorage.removeItem(key);
          }
        });
        
        // Clear cookies
        document.cookie.split(';').forEach(function(c) {
          var eqPos = c.indexOf('=');
          var name = eqPos > -1 ? c.substr(0, eqPos).trim() : c.trim();
          if (name.toLowerCase().includes('elfsight') || 
              name.toLowerCase().includes('chatbot') ||
              name.toLowerCase().includes('eapps') ||
              name.toLowerCase().includes('widget')) {
            document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=' + window.location.hostname;
            document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/';
          }
        });
      } catch (e) {
        console.log('Storage clearing completed with some limitations');
      }
      
      // Method 2: Force complete widget reset
      setTimeout(function() {
        try {
          const chatbotElement = document.querySelector('.elfsight-app-96cc4395-da06-450f-9191-5bc6e30fa5f7');
          if (chatbotElement) {
            // Store parent for reinsertion
            const parent = chatbotElement.parentNode;
            const nextSibling = chatbotElement.nextSibling;
            
            // Completely remove the widget
            chatbotElement.remove();
            
            // Wait a moment then recreate
            setTimeout(function() {
              const newChatbot = document.createElement('div');
              newChatbot.className = 'elfsight-app-96cc4395-da06-450f-9191-5bc6e30fa5f7';
              newChatbot.setAttribute('data-elfsight-app-lazy', '');
              newChatbot.setAttribute('id', 'draggable-chatbot');
              
              // Insert back in original position
              if (nextSibling) {
                parent.insertBefore(newChatbot, nextSibling);
              } else {
                parent.appendChild(newChatbot);
              }
              
              // Force platform reinitialization
              setTimeout(function() {
                if (window.eapps) {
                  if (window.eapps.reinit) window.eapps.reinit();
                  if (window.eapps.refresh) window.eapps.refresh();
                  if (window.eapps.reload) window.eapps.reload();
                }
                if (window.ElfsightPlatform) {
                  if (window.ElfsightPlatform.reinit) window.ElfsightPlatform.reinit();
                  if (window.ElfsightPlatform.refresh) window.ElfsightPlatform.refresh();
                }
                console.log('Chatbot conversation reset - fresh start guaranteed');
              }, 500);
            }, 200);
          }
        } catch (e) {
          console.log('Widget reset completed with fallback method');
        }
      }, 800);
    });
  </script>

  <!-- Draggable Chatbot Circle Functionality -->
  <script>
    let isDragging = false;
    let currentX;
    let currentY;
    let initialX;
    let initialY;
    let xOffset = 0;
    let yOffset = 0;
    let chatbotElement;

    // Wait for chatbot to load and initialize dragging
    function initializeDraggableChatbot() {
      chatbotElement = document.getElementById('draggable-chatbot');
      
      if (!chatbotElement) {
        setTimeout(initializeDraggableChatbot, 1000);
        return;
      }

      // Add drag functionality to the chatbot circle
      chatbotElement.addEventListener('mousedown', dragStart);
      chatbotElement.addEventListener('touchstart', dragStart, { passive: false });
      
      document.addEventListener('mousemove', drag);
      document.addEventListener('touchmove', drag, { passive: false });
      
      document.addEventListener('mouseup', dragEnd);
      document.addEventListener('touchend', dragEnd);

      console.log('Draggable chatbot circle initialized!');
    }

    function dragStart(e) {
      // Only allow dragging when clicking on the chatbot circle (not when chat is open)
      const chatWindow = chatbotElement.querySelector('[class*="chat"], [class*="window"], [class*="content"]');
      if (chatWindow && chatWindow.offsetHeight > 100) {
        // Chat is open, don't drag
        return;
      }

      if (e.type === 'touchstart') {
        initialX = e.touches[0].clientX - xOffset;
        initialY = e.touches[0].clientY - yOffset;
      } else {
        initialX = e.clientX - xOffset;
        initialY = e.clientY - yOffset;
      }

      isDragging = true;
      chatbotElement.style.cursor = 'grabbing';
      e.preventDefault();
    }

    function drag(e) {
      if (isDragging) {
        e.preventDefault();
        
        if (e.type === 'touchmove') {
          currentX = e.touches[0].clientX - initialX;
          currentY = e.touches[0].clientY - initialY;
        } else {
          currentX = e.clientX - initialX;
          currentY = e.clientY - initialY;
        }

        xOffset = currentX;
        yOffset = currentY;

        // Keep chatbot within viewport bounds
        const rect = chatbotElement.getBoundingClientRect();
        const maxX = window.innerWidth - rect.width;
        const maxY = window.innerHeight - rect.height;
        
        currentX = Math.max(0, Math.min(currentX, maxX));
        currentY = Math.max(0, Math.min(currentY, maxY));

        setTranslate(currentX, currentY, chatbotElement);
      }
    }

    function dragEnd(e) {
      if (isDragging) {
        initialX = currentX;
        initialY = currentY;
        isDragging = false;
        chatbotElement.style.cursor = 'move';
        
        // Snap to edges if close (within 30px)
        const rect = chatbotElement.getBoundingClientRect();
        const snapDistance = 30;
        
        if (rect.left < snapDistance) {
          currentX = 0;
          xOffset = 0;
        }
        if (rect.right > window.innerWidth - snapDistance) {
          currentX = window.innerWidth - rect.width;
          xOffset = currentX;
        }
        if (rect.top < snapDistance) {
          currentY = 0;
          yOffset = 0;
        }
        if (rect.bottom > window.innerHeight - snapDistance) {
          currentY = window.innerHeight - rect.height;
          yOffset = currentY;
        }
        
        setTranslate(currentX, currentY, chatbotElement);
      }
    }

    function setTranslate(xPos, yPos, el) {
      el.style.transform = `translate3d(${xPos}px, ${yPos}px, 0)`;
    }

    // Handle window resize
    window.addEventListener('resize', function() {
      if (chatbotElement) {
        const rect = chatbotElement.getBoundingClientRect();
        
        // Ensure chatbot stays within new viewport
        if (rect.right > window.innerWidth) {
          currentX = window.innerWidth - rect.width;
          xOffset = currentX;
        }
        if (rect.bottom > window.innerHeight) {
          currentY = window.innerHeight - rect.height;
          yOffset = currentY;
        }
        
        setTranslate(currentX, currentY, chatbotElement);
      }
    });

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initializeDraggableChatbot);
    } else {
      initializeDraggableChatbot();
    }

    // Also try to initialize after a delay to ensure Elfsight widget is loaded
    setTimeout(initializeDraggableChatbot, 2000);
  </script>

<script>
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
        // Show contact popup
        const contactPopup = document.getElementById('contactPopup');
        if (contactPopup) {
            contactPopup.style.display = 'flex';
        }
    });
    
    // Handle contact popup close button
    const closeContact = document.getElementById('closeContact');
    const contactPopup = document.getElementById('contactPopup');
    
    if (closeContact && contactPopup) {
        closeContact.addEventListener('click', function() {
            contactPopup.style.display = 'none';
        });
        
        // Close popup when clicking outside
        contactPopup.addEventListener('click', function(e) {
            if (e.target === contactPopup) {
                contactPopup.style.display = 'none';
            }
        });
    }

    // Services Popup functionality
    const openServicesBtn = document.getElementById('openServicesPopup');
    const servicesPopup = document.getElementById('servicesPopup');
    const closeServicesBtn = document.getElementById('closeServices');

    if (openServicesBtn && servicesPopup && closeServicesBtn) {
        openServicesBtn.addEventListener('click', function() {
            servicesPopup.style.display = 'flex';
        });

        closeServicesBtn.addEventListener('click', function() {
            servicesPopup.style.display = 'none';
        });

        // Close popup when clicking outside
        servicesPopup.addEventListener('click', function(e) {
            if (e.target === servicesPopup) {
                servicesPopup.style.display = 'none';
            }
        });
    }

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
                    // Show error message
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
});
</script>

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

// Mobile menu functionality is handled by script.js
</script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
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
    @else
      <div class="auth-buttons">
        <a href="{{ route('login') }}" class="login-btn">Login</a>
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

  <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>
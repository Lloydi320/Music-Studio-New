<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title> Lemon Hub Studio</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
</head>

<body>

 
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
        <li><a href="/services" class="active">About Us & Our Services</a></li>
        <li><a href="#" id="contactLink">Contact</a></li>
        <li><a href="#" id="feedbackLink">Feedbacks</a></li>
        @if(Auth::check())
        <li><a href="#" id="rescheduleBookingLink">Rescheduling</a></li>
        @endif
        @if(Auth::check() && Auth::user()->isAdmin())
        <li><a href="/admin/calendar" style="color: #ff6b35; font-weight: bold;">📅 Admin Calendar</a></li>
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
            <a href="/booking" class="dropdown-item">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span>Book Session</span>
            </a>

            <a href="/services" class="dropdown-item">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M12 2L3.09 8.26L4 21L12 17L20 21L20.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span>About Us & Services</span>
            </a>
            <a href="#" id="contactLink" class="dropdown-item">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M22 6L12 13L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span>Contact</span>
            </a>
            <a href="/feedback" class="dropdown-item">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span>Feedback</span>
            </a>
            
            <div class="dropdown-divider"></div>
            
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
      <a href="/login" class="book-btn" style="margin-left: 30px;">Login</a>
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
        <h3>Studio Rental</h3>
        <p>Book our acoustically treated studios for jamming, rehearsals, or recording. Fully equipped and flexible.</p>
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
      var isAuthenticated = {{ Auth::check() ? 'true' : 'false' }};
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
</script>

</body>
</html>

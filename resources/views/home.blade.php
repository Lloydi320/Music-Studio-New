<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lemon Hub Studio</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/booking.css') }}">

</head>

<body>


  <header class="navbar" style="display: flex; justify-content: space-between; align-items: center;">
    <div class="logo">
      <img src="{{ asset('images/studio-logo.png') }}" alt="Lemon Hub Studio Logo">
      <span>LEMON HUB STUDIO</span>
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
      <div class="user-profile" style="display: flex; align-items: center; gap: 10px; margin-left: 30px;">
        @php
          $user = Auth::user();
          $avatar = session('google_user_avatar') ?? null;
        @endphp
        @if($avatar)
          <img src="{{ $avatar }}" alt="Avatar" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
        @endif
        <div style="display: flex; flex-direction: column; align-items: flex-end;">
          <span style="font-weight: bold;">{{ $user->name }}</span>
          <span style="font-size: 0.9em; color: #888;">{{ $user->email }}</span>
          <form action="/logout" method="POST" style="margin:0;">
            @csrf
            <button type="submit" style="background:none; border:none; color:#e67e22; cursor:pointer; padding:0; font-size:0.95em;">Logout</button>
          </form>
        </div>
      </div>
    @else
      <a href="/auth/google" class="book-btn" style="margin-left: 30px;">Login with Google</a>
    @endif
  </header>

  
  <section class="hero">
    <div class="hero-overlay" id="mainOverlay">
      <div class="hero-content">
        <h1>BOOK YOUR STUDIO SESSION TODAY!</h1>
        <p>Bringing your music to life, one session at a time.</p>
        <a href="{{ Auth::check() ? '/booking' : '/auth/google' }}" class="book-btn" id="openBookingModal">Book Now!</a>
      </div>
      <div class="calendar-container">
        <div id="calendar-header">
          <button id="prevMonth">&#9664;</button>
          <span id="monthYear">April 2025</span>
          <button id="nextMonth">&#9654;</button>
        </div>
        <div class="calendar-grid" id="calendarGrid"></div>
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

 
  <footer class="footer-logo">
    <img src="{{ asset('images/studio-logo.png') }}" alt="Studio Logo" />
  </footer>

  <script src="{{ asset('js/script.js') }}"></script>
<script>
  // Modal open/close logic
  document.getElementById('openBookingModal').onclick = function() {
    document.getElementById('bookingModal').style.display = 'flex';
  };
  document.getElementById('closeBookingModal').onclick = function() {
    document.getElementById('bookingModal').style.display = 'none';
  };
  // Optional: close modal when clicking outside content
  document.getElementById('bookingModal').onclick = function(e) {
    if (e.target === this) this.style.display = 'none';
  };
</script>
</body>
</html>

@auth
    <p>Welcome, {{ Auth::user()->name }}!</p>
@endauth

@guest
    <a href="{{ url('/login/google') }}">Login with Google</a>
@endguest

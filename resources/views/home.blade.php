<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lemon Hub Studio</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

</head>

<body>


  <header class="navbar">
    <div class="logo">
      <img src="{{ asset('images/studio-logo.png') }}" alt="Lemon Hub Studio Logo">
      <span>LEMON HUB STUDIO</span>
    </div>
    <nav>
      <ul class="nav-links">
        <li><a href="/">Home</a></li>
        <li><a href="/services">About Us & Our Services</a></li>
        <li><a href="#" id="contactLink">Contact</a></li>
        <li><a href="#" id="feedbackLink">Feedbacks</a></li>
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

  
  <section class="hero">
    <div class="hero-overlay" id="mainOverlay">
      <div class="hero-content">
        <h1>BOOK YOUR STUDIO SESSION TODAY!</h1>
        <p>Bringing your music to life, one session at a time.</p>
        @if(Auth::check())
          <a href="/services#bottom-of-services" class="book-btn">Book Now!</a>
        @else
          <a href="/auth/google" class="book-btn">Login to Book Now!</a>
        @endif
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

          <button type="submit" class="submit-btn">Submit Feedback</button>
        </form>
      </div>
    </div>
  </div>
</div>

 
  <footer class="footer-logo">
    <img src="{{ asset('images/studio-logo.png') }}" alt="Studio Logo" />
  </footer>

  <script src="{{ asset('js/script.js') }}"></script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title> Lemon Hub Studio</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
</head>

<body>

 
  <header class="navbar">
    <div class="logo">
      <img src="{{ asset('images/studio-logo.png') }}" alt="Logo" />
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

  <a href="/booking" class="service-box">
        <img src="{{ asset('images/lessons.jpg') }}" alt="Music Lessons" />
        <h3>Music Lessons</h3>
        <p>Private or group lessons in vocals, guitar, keyboard, and drums. Ideal for all ages and skill levels.</p>
        <small class="service-hint">Click to Book</small>
      </a>

      <a href="/booking" class="service-box">
        <img src="{{ asset('images/instruments.png') }}" alt="Instruments Rental" />
        <h3>Instruments Rental</h3>
        <p>Need a guitar, amp, or mic? Rent affordable gear for your session without the hassle.</p>
        <small class="service-hint">Click to Rent</small>
      </a>
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


 
  <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>

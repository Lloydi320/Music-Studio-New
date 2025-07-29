<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Music Lessons - Lemon Hub Studio</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
  <style>
    .lessons-hero {
      background: linear-gradient(135deg, #ffd700 0%, #dbb411 100%);
      padding: 40px 20px;
      text-align: center;
      color: #111;
      margin-bottom: 30px;
    }

    .lessons-hero h1 {
      font-size: 2.8em;
      margin: 0 0 15px 0;
      font-weight: bold;
    }

    .lessons-hero p {
      font-size: 1.1em;
      margin: 0;
      opacity: 0.9;
    }

    .lessons-container {
      max-width: 100%;
      margin: 0 auto;
      padding: 0 15px;
    }

    .why-section {
      background: #f8f9fa;
      padding: 30px;
      border-radius: 10px;
      margin-bottom: 30px;
      text-align: center;
    }

    .why-section h2 {
      color: #111;
      font-size: 1.8em;
      margin-bottom: 20px;
      border-bottom: 2px solid #ffd700;
      padding-bottom: 10px;
      display: inline-block;
    }

    .why-quote {
      font-style: italic;
      font-size: 1em;
      color: #555;
      margin-bottom: 25px;
      line-height: 1.5;
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
    }

    .benefits-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin-top: 25px;
    }

    .benefit-item {
      background: white;
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      text-align: center;
      transition: transform 0.3s ease;
    }

    .benefit-item:hover {
      transform: translateY(-3px);
    }

    .benefit-icon {
      font-size: 2em;
      margin-bottom: 10px;
      display: block;
    }

    .benefit-title {
      font-weight: bold;
      color: #111;
      margin-bottom: 8px;
      font-size: 0.95em;
    }

    .benefit-desc {
      color: #666;
      font-size: 0.85em;
      line-height: 1.3;
    }

    .instruments-section {
      margin-bottom: 30px;
    }

    .instruments-section h2 {
      text-align: center;
      color: #111;
      font-size: 1.8em;
      margin-bottom: 25px;
      border-bottom: 2px solid #ffd700;
      padding-bottom: 10px;
      display: block;
    }

    .instruments-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }

    .instrument-card {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      border: 2px solid transparent;
    }

    .instrument-card:hover {
      transform: translateY(-5px);
      border-color: #ffd700;
      box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3);
    }

    .instrument-header {
      background: linear-gradient(135deg, #ffd700 0%, #dbb411 100%);
      color: #111;
      padding: 20px;
      text-align: center;
    }

    .instrument-icon {
      font-size: 2.5em;
      margin-bottom: 10px;
      display: block;
    }

    .instrument-name {
      font-size: 1.3em;
      font-weight: bold;
      margin-bottom: 5px;
    }

    .instrument-subtitle {
      font-size: 0.9em;
      opacity: 0.9;
    }

    .instrument-content {
      padding: 20px;
    }

    .instrument-description {
      color: #555;
      line-height: 1.5;
      margin-bottom: 15px;
      font-size: 0.95em;
    }

    .learning-points {
      background: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
      margin-top: 15px;
    }

    .learning-points h4 {
      color: #111;
      margin-bottom: 10px;
      font-size: 1em;
      border-bottom: 2px solid #ffd700;
      padding-bottom: 3px;
    }

    .points-list {
      list-style: none;
      padding: 0;
    }

    .points-list li {
      padding: 5px 0;
      color: #555;
      position: relative;
      padding-left: 20px;
      font-size: 0.85em;
    }

    .points-list li:before {
      content: "🎵";
      position: absolute;
      left: 0;
      top: 5px;
      font-size: 0.8em;
    }

    .why-choose-section {
      background: linear-gradient(135deg, #fff8dc 0%, #fffacd 100%);
      padding: 30px;
      border-radius: 10px;
      margin-bottom: 30px;
      border: 2px solid #ffd700;
    }

    .why-choose-section h2 {
      text-align: center;
      color: #111;
      font-size: 1.8em;
      margin-bottom: 20px;
    }

    .reasons-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
    }

    .reason-item {
      background: white;
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .reason-title {
      font-weight: bold;
      color: #111;
      margin-bottom: 8px;
      font-size: 1em;
    }

    .reason-desc {
      color: #666;
      line-height: 1.4;
      font-size: 0.9em;
    }

    .cta-section {
      text-align: center;
      padding: 30px 20px;
      background: linear-gradient(135deg, #ffd700 0%, #dbb411 100%);
      border-radius: 10px;
      margin-bottom: 30px;
    }

    .cta-section h2 {
      color: #111;
      font-size: 1.8em;
      margin-bottom: 15px;
    }

    .cta-section p {
      color: #111;
      font-size: 1em;
      margin-bottom: 20px;
      opacity: 0.9;
    }

    .cta-btn {
      background: #111;
      color: #ffd700;
      border: none;
      padding: 12px 30px;
      font-size: 1em;
      font-weight: bold;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }

    .cta-btn:hover {
      background: #000;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    /* Responsive design */
    @media (max-width: 768px) {
      .lessons-hero h1 {
        font-size: 2.2em;
      }

      .lessons-hero p {
        font-size: 1em;
      }

      .why-section, .why-choose-section {
        padding: 20px 15px;
      }

      .instruments-grid {
        grid-template-columns: 1fr;
        gap: 15px;
      }

      .benefits-grid, .reasons-grid {
        grid-template-columns: 1fr;
        gap: 10px;
      }

      .lessons-container {
        padding: 0 10px;
      }
    }

    /* Ensure proper spacing */
    .booking-main {
      min-height: calc(100vh - 200px);
      padding-bottom: 30px;
    }

    .booking-footer {
      margin-top: 30px;
      padding-top: 20px;
      position: relative !important;
      bottom: auto !important;
    }

    /* Override the fixed footer and hidden overflow */
    body.booking-page, html {
      overflow: auto !important;
    }

    .booking-footer {
      position: relative !important;
      bottom: auto !important;
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
    <div class="lessons-hero">
      <h1> Music Lessons</h1>
      <p>Discover your musical potential with our expert instructors</p>
    </div>

    <div class="why-section">
      <h2> Why Music Education Matters?</h2>
      <div class="why-quote">
        "Through the power of music, students feel motivated to learn; they become more confident speakers; they develop analytical thinking skills; and most significantly, they discover hidden passions that give them a lifelong advantage."
      </div>
      
      <div class="benefits-grid">
        <div class="benefit-item">
          <span class="benefit-icon">🧠</span>
          <div class="benefit-title">Enhanced Cognitive Abilities</div>
          <div class="benefit-desc">Improves memory, attention, and problem-solving skills</div>
        </div>
        <div class="benefit-item">
          <span class="benefit-icon">🤝</span>
          <div class="benefit-title">Better Communication</div>
          <div class="benefit-desc">Develops social skills and teamwork abilities</div>
        </div>
        <div class="benefit-item">
          <span class="benefit-icon">🎨</span>
          <div class="benefit-title">Creativity & Expression</div>
          <div class="benefit-desc">Unleashes artistic potential and self-expression</div>
        </div>
        <div class="benefit-item">
          <span class="benefit-icon">⏰</span>
          <div class="benefit-title">Discipline & Focus</div>
          <div class="benefit-desc">Builds time management and concentration skills</div>
        </div>
        <div class="benefit-item">
          <span class="benefit-icon">😌</span>
          <div class="benefit-title">Stress Relief</div>
          <div class="benefit-desc">Provides emotional well-being and relaxation</div>
        </div>
        <div class="benefit-item">
          <span class="benefit-icon">🎯</span>
          <div class="benefit-title">Goal Achievement</div>
          <div class="benefit-desc">Teaches perseverance and accomplishment</div>
        </div>
      </div>
    </div>

    <div class="instruments-section">
      <h2> Available Lessons</h2>
      <div class="instruments-grid">
        <div class="instrument-card">
          <div class="instrument-header">
            <span class="instrument-icon">🥁</span>
            <div class="instrument-name">Drums</div>
            <div class="instrument-subtitle">Master the Rhythm</div>
          </div>
          <div class="instrument-content">
            <div class="instrument-description">
              Master the rhythm and develop your timing with our comprehensive drum lessons. Learn various styles from rock to jazz and everything in between.
            </div>
            <div class="learning-points">
              <h4>What You'll Learn:</h4>
              <ul class="points-list">
                <li>Basic drumming techniques and proper grip</li>
                <li>Reading drum notation and sheet music</li>
                <li>Different drumming styles (Rock, Jazz, Latin)</li>
                <li>Rhythm and timing exercises</li>
                <li>Playing with a band and ensemble work</li>
                <li>Drum maintenance and tuning</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="instrument-card">
          <div class="instrument-header">
            <span class="instrument-icon">🎸</span>
            <div class="instrument-name">Guitar</div>
            <div class="instrument-subtitle">Strum Your Way</div>
          </div>
          <div class="instrument-content">
            <div class="instrument-description">
              From acoustic to electric, learn to play your favorite songs and develop your own unique style. Perfect for beginners and advanced players.
            </div>
            <div class="learning-points">
              <h4>What You'll Learn:</h4>
              <ul class="points-list">
                <li>Basic chords and strumming patterns</li>
                <li>Fingerpicking and advanced techniques</li>
                <li>Music theory fundamentals</li>
                <li>Song learning and arrangement</li>
                <li>Solo playing and improvisation</li>
                <li>Guitar maintenance and care</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="instrument-card">
          <div class="instrument-header">
            <span class="instrument-icon">🎹</span>
            <div class="instrument-name">Keyboard/Piano</div>
            <div class="instrument-subtitle">Play the Keys</div>
          </div>
          <div class="instrument-content">
            <div class="instrument-description">
              Develop your musical foundation with piano lessons that cover classical and contemporary styles. Build a strong musical base.
            </div>
            <div class="learning-points">
              <h4>What You'll Learn:</h4>
              <ul class="points-list">
                <li>Proper hand positioning and posture</li>
                <li>Reading sheet music and notation</li>
                <li>Scales, arpeggios, and exercises</li>
                <li>Classical and modern pieces</li>
                <li>Music theory and composition</li>
                <li>Performance techniques</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="instrument-card">
          <div class="instrument-header">
            <span class="instrument-icon">🎤</span>
            <div class="instrument-name">Voice/Singing</div>
            <div class="instrument-subtitle">Find Your Voice</div>
          </div>
          <div class="instrument-content">
            <div class="instrument-description">
              Discover and develop your unique voice with professional vocal training and technique. Build confidence and performance skills.
            </div>
            <div class="learning-points">
              <h4>What You'll Learn:</h4>
              <ul class="points-list">
                <li>Proper breathing techniques</li>
                <li>Vocal warm-ups and exercises</li>
                <li>Pitch and tone control</li>
                <li>Performance confidence</li>
                <li>Different singing styles</li>
                <li>Vocal health and care</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="why-choose-section">
      <h2>⭐ Why Choose Lemon Hub Studio?</h2>
      <div class="reasons-grid">
        <div class="reason-item">
          <div class="reason-title">Qualified Instructors</div>
          <div class="reason-desc">We have a great team of teachers qualified in their field with years of teaching experience.</div>
        </div>
        <div class="reason-item">
          <div class="reason-title">Band Experience</div>
          <div class="reason-desc">Our goal is to unlock and discover the musical ability of a young musician to be able to play in a band.</div>
        </div>
        <div class="reason-item">
          <div class="reason-title">Flexible Scheduling</div>
          <div class="reason-desc">We'll help you to find learning time that works for you and your busy lifestyle.</div>
        </div>
        <div class="reason-item">
          <div class="reason-title">Friendly Approach</div>
          <div class="reason-desc">We take a friendly approach to make you feel comfortable and confident in your learning journey.</div>
        </div>
      </div>
    </div>

    <div class="cta-section">
      <h2>🎵 Ready to Start Your Musical Journey?</h2>
      <p>Contact us to learn more about our lesson programs, pricing, and available time slots. Our instructors are ready to help you discover your musical potential!</p>
              <a href="https://www.facebook.com/lemonhubstudio" target="_blank" class="cta-btn">Contact Us for More Details</a>
    </div>
  </div>

  <!-- Add spacing before footer -->
  <div style="height: 50px; margin-top: 30px;"></div>

  <footer class="booking-footer">
    <div class="footer-content">
      <p>&copy; 2025 Lemon Hub Studio - All Rights Reserved</p>
      <p>Professional Music Studio Services</p>
    </div>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Contact popup functionality
      const contactLink = document.getElementById('contactLink');
      const contactPopup = document.getElementById('contactPopup');
      const closeContact = document.getElementById('closeContact');

      if (contactLink && contactPopup) {
        contactLink.addEventListener('click', function(e) {
          e.preventDefault();
          contactPopup.style.display = 'flex';
        });

        if (closeContact) {
          closeContact.addEventListener('click', function() {
            contactPopup.style.display = 'none';
          });
        }

        // Close popup when clicking outside
        contactPopup.addEventListener('click', function(e) {
          if (e.target === contactPopup) {
            contactPopup.style.display = 'none';
          }
        });
      }
    });
  </script>
</body>
</html> 
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

      .booking-main {
        padding-top: 60px;
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

    /* Hide Sign Out button in navigation on desktop view */
    @media (min-width: 769px) {
      .nav-signout-desktop-hidden {
        display: none !important;
      }
    }

    /* Carousel Styles */
    .carousel-section {
      margin: 30px 0;
      padding: 0 20px;
    }

    .carousel-container {
      position: relative;
      max-width: 800px;
      margin: 0 auto;
      overflow: hidden;
      border-radius: 15px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .carousel-wrapper {
      overflow: hidden;
      border-radius: 15px;
    }

    .carousel-track {
      display: flex;
      transition: transform 0.5s ease-in-out;
      will-change: transform;
    }

    .carousel-slide {
      min-width: 100%;
      flex-shrink: 0;
    }

    .carousel-card {
      position: relative;
      background: white;
      overflow: hidden;
      height: 400px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .carousel-card:hover {
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
    }

    .carousel-card::before {
      content: '👁️ Click to view details';
      position: absolute;
      top: 15px;
      right: 15px;
      background: rgba(255, 215, 0, 0.9);
      color: #333;
      padding: 8px 12px;
      border-radius: 20px;
      font-size: 0.8em;
      font-weight: 600;
      opacity: 0;
      transform: translateY(-10px);
      transition: all 0.3s ease;
      z-index: 10;
      backdrop-filter: blur(5px);
    }

    .carousel-card:hover::before {
      opacity: 1;
      transform: translateY(0);
    }

    .carousel-image {
      width: 100%;
      height: 100%;
      position: relative;
    }

    .carousel-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .carousel-content {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      padding: 30px;
      background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
      color: white;
      transform: translateY(0);
      transition: transform 0.3s ease;
    }

    .carousel-card:hover .carousel-content {
      transform: translateY(-10px);
    }

    .carousel-card:hover .carousel-image img {
      transform: scale(1.05);
    }

    .carousel-title {
      font-size: 1.8em;
      font-weight: bold;
      color: white;
      margin-bottom: 10px;
      line-height: 1.2;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    .carousel-description {
      font-size: 1em;
      color: rgba(255, 255, 255, 0.9);
      line-height: 1.5;
      margin: 0 0 10px 0;
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .click-to-view {
      font-size: 0.9em;
      color: #FFD700;
      font-weight: 600;
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
      margin-top: 8px;
      opacity: 0.8;
      transition: opacity 0.3s ease;
    }

    .carousel-card:hover .click-to-view {
      opacity: 1;
    }

    /* Navigation Buttons */
    .carousel-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(255, 215, 0, 0.9);
      border: none;
      width: 50px;
      height: 50px;
      border-radius: 50%;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #111;
      font-size: 18px;
      transition: all 0.3s ease;
      z-index: 10;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .carousel-btn:hover {
      background: #ffd700;
      transform: translateY(-50%) scale(1.1);
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    }

    .carousel-btn-prev {
      left: 20px;
    }

    .carousel-btn-next {
      right: 20px;
    }

    /* Dots Indicator */
    .carousel-dots {
      display: flex;
      justify-content: center;
      gap: 10px;
      padding: 20px;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
    }

    .carousel-dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      border: none;
      background: #ddd;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .carousel-dot.active {
      background: #ffd700;
      transform: scale(1.2);
    }

    .carousel-dot:hover {
      background: #ffd700;
      transform: scale(1.1);
    }

    /* Responsive Carousel */
    @media (max-width: 768px) {
      .carousel-container {
        max-width: 100%;
      }

      .carousel-card {
        height: 300px;
      }

      .carousel-content {
        padding: 20px;
      }

      .carousel-title {
        font-size: 1.4em;
        margin-bottom: 8px;
      }

      .carousel-description {
        font-size: 0.9em;
        -webkit-line-clamp: 2;
      }

      .carousel-btn {
        width: 40px;
        height: 40px;
        font-size: 16px;
      }

      .carousel-btn-prev {
        left: 10px;
      }

      .carousel-btn-next {
        right: 10px;
      }

      .carousel-section {
        padding: 0 10px;
      }
    }

    @media (max-width: 480px) {
      .carousel-content {
        padding: 20px 15px;
      }

      .carousel-title {
        font-size: 1.3em;
      }

      .carousel-description {
        font-size: 0.95em;
      }
    }
  </style>
</head>
<body class="booking-page">

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

  <div class="booking-main">
    <div class="lessons-hero">
      <h1>🎵 Music Lessons</h1>
      <p>Discover your musical potential with our expert instructors</p>
    </div>

    <!-- Carousel Section -->
    @if($carouselItems->count() > 0)
    <div class="carousel-section">
      <div style="text-align: center; margin-bottom: 30px;">
        <h2 style="font-size: 2.5em; font-weight: bold; color: #333; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">🎼 Meet Our Expert Teachers</h2>
        <p style="font-size: 1.2em; color: #666; max-width: 600px; margin: 0 auto; line-height: 1.6;">Discover the talented instructors who will guide your musical journey with passion and expertise.</p>
      </div>
      <div class="carousel-container">
        <div class="carousel-wrapper">
          <div class="carousel-track" id="carouselTrack">
            @foreach($carouselItems as $item)
            <div class="carousel-slide">
              <div class="carousel-card" onclick="openTeacherModal({{ json_encode($item->title) }}, {{ json_encode($item->description) }}, {{ json_encode($item->expertise ?? 'No expertise information available') }}, {{ json_encode(asset('images/carousel/' . $item->image_path)) }})">
                <div class="carousel-image">
                  <img src="{{ asset('images/carousel/' . $item->image_path) }}" alt="{{ $item->title }}" loading="lazy">
                </div>
                <div class="carousel-content">
                  <h3 class="carousel-title">{{ $item->title }}</h3>
                  <p class="carousel-description">{{ $item->expertise ?? 'Music Instructor' }}</p>
                  <div class="click-to-view">Click to view details</div>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
        
        <!-- Navigation buttons -->
        <button class="carousel-btn carousel-btn-prev" id="prevBtn">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
        <button class="carousel-btn carousel-btn-next" id="nextBtn">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
        
        <!-- Dots indicator -->
        <div class="carousel-dots" id="carouselDots">
          @foreach($carouselItems as $index => $item)
          <button class="carousel-dot {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}"></button>
          @endforeach
        </div>
      </div>
    </div>
    @endif

    <div class="why-section">
      <h2>🎯 Why Music Education Matters?</h2>
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
          <span class="benefit-icon">🗣️</span>
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
      <h2>🎼 Available Lessons</h2>
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

 

  <script src="{{ asset('js/script.js') }}"></script>
  <script src="{{ asset('js/page-transitions.js') }}"></script>

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

      // Carousel functionality
      const carouselTrack = document.getElementById('carouselTrack');
      const prevBtn = document.getElementById('prevBtn');
      const nextBtn = document.getElementById('nextBtn');
      const carouselDots = document.getElementById('carouselDots');
      
      if (carouselTrack && prevBtn && nextBtn && carouselDots) {
        const slides = carouselTrack.querySelectorAll('.carousel-slide');
        const dots = carouselDots.querySelectorAll('.carousel-dot');
        let currentSlide = 0;
        const totalSlides = slides.length;

        // Function to update carousel position
        function updateCarousel() {
          const translateX = -currentSlide * 100;
          carouselTrack.style.transform = `translateX(${translateX}%)`;
          
          // Update dots
          dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
          });
        }

        // Next button functionality
        nextBtn.addEventListener('click', function() {
          currentSlide = (currentSlide + 1) % totalSlides;
          updateCarousel();
        });

        // Previous button functionality
        prevBtn.addEventListener('click', function() {
          currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
          updateCarousel();
        });

        // Dot navigation
        dots.forEach((dot, index) => {
          dot.addEventListener('click', function() {
            currentSlide = index;
            updateCarousel();
          });
        });

        // Auto-play functionality (optional)
        let autoPlayInterval;
        
        function startAutoPlay() {
          autoPlayInterval = setInterval(() => {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateCarousel();
          }, 5000); // Change slide every 5 seconds
        }

        function stopAutoPlay() {
          clearInterval(autoPlayInterval);
        }

        // Start auto-play
        if (totalSlides > 1) {
          startAutoPlay();

          // Pause auto-play on hover
          const carouselContainer = document.querySelector('.carousel-container');
          if (carouselContainer) {
            carouselContainer.addEventListener('mouseenter', stopAutoPlay);
            carouselContainer.addEventListener('mouseleave', startAutoPlay);
          }

          // Pause auto-play when user interacts with controls
          [prevBtn, nextBtn, ...dots].forEach(element => {
            element.addEventListener('click', () => {
              stopAutoPlay();
              setTimeout(startAutoPlay, 3000); // Resume after 3 seconds
            });
          });
        }

        // Touch/swipe support for mobile
        let startX = 0;
        let isDragging = false;

        carouselTrack.addEventListener('touchstart', function(e) {
          startX = e.touches[0].clientX;
          isDragging = true;
          stopAutoPlay();
        });

        carouselTrack.addEventListener('touchmove', function(e) {
          if (!isDragging) return;
          e.preventDefault();
        });

        carouselTrack.addEventListener('touchend', function(e) {
          if (!isDragging) return;
          
          const endX = e.changedTouches[0].clientX;
          const diffX = startX - endX;
          
          // Minimum swipe distance
          if (Math.abs(diffX) > 50) {
            if (diffX > 0) {
              // Swipe left - next slide
              currentSlide = (currentSlide + 1) % totalSlides;
            } else {
              // Swipe right - previous slide
              currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            }
            updateCarousel();
          }
          
          isDragging = false;
          setTimeout(startAutoPlay, 3000);
        });

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
          if (e.key === 'ArrowLeft') {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateCarousel();
            stopAutoPlay();
            setTimeout(startAutoPlay, 3000);
          } else if (e.key === 'ArrowRight') {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateCarousel();
            stopAutoPlay();
            setTimeout(startAutoPlay, 3000);
          }
        });
      }
    });

    // Teacher Modal Functions
    function openTeacherModal(name, description, expertise, imagePath) {
      const modal = document.getElementById('teacherModal');
      const modalName = document.getElementById('modalTeacherName');
      const modalDescription = document.getElementById('modalTeacherDescription');
      const modalExpertise = document.getElementById('modalTeacherExpertise');
      const modalImage = document.getElementById('modalTeacherImage');

      modalName.textContent = name;
      modalDescription.textContent = description;
      modalExpertise.textContent = expertise;
      modalImage.src = imagePath;
      modalImage.alt = name;

      modal.style.display = 'block';
      document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function closeTeacherModal() {
      const modal = document.getElementById('teacherModal');
      modal.style.display = 'none';
      document.body.style.overflow = 'auto'; // Restore scrolling
    }

    // Modal event listeners
    document.addEventListener('DOMContentLoaded', function() {
      const modal = document.getElementById('teacherModal');
      const closeBtn = document.querySelector('.teacher-modal-close');

      // Close modal when clicking the X button
      if (closeBtn) {
        closeBtn.addEventListener('click', closeTeacherModal);
      }

      // Close modal when clicking outside of it
      if (modal) {
        modal.addEventListener('click', function(e) {
          if (e.target === modal) {
            closeTeacherModal();
          }
        });
      }

      // Close modal with Escape key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          closeTeacherModal();
        }
      });
    });
  </script>

  <!-- Teacher Details Modal -->
  <div id="teacherModal" class="teacher-modal">
    <div class="teacher-modal-content">
      <div class="teacher-modal-header">
        <h2 id="modalTeacherName" class="teacher-modal-title"></h2>
        <span class="teacher-modal-close">&times;</span>
      </div>
      <div class="teacher-modal-body">
        <div class="teacher-modal-image">
          <img id="modalTeacherImage" src="" alt="Teacher Photo">
        </div>
        <div class="teacher-modal-info">
          <div class="teacher-modal-section">
            <h3>About</h3>
            <p id="modalTeacherDescription"></p>
          </div>
          <div class="teacher-modal-section">
            <h3>Expertise</h3>
            <p id="modalTeacherExpertise"></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .teacher-modal {
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

    .teacher-modal-content {
      background-color: white;
      margin: 5% auto;
      padding: 0;
      border-radius: 15px;
      width: 90%;
      max-width: 600px;
      max-height: 80vh;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      animation: modalSlideIn 0.3s ease-out;
    }

    @keyframes modalSlideIn {
      from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .teacher-modal-header {
      background: linear-gradient(135deg, #FFD700, #FFA500);
      padding: 20px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .teacher-modal-title {
      margin: 0;
      color: #333;
      font-size: 1.8em;
      font-weight: bold;
    }

    .teacher-modal-close {
      color: #333;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
      transition: color 0.3s ease;
    }

    .teacher-modal-close:hover {
      color: #666;
    }

    .teacher-modal-body {
      padding: 30px;
      max-height: 60vh;
      overflow-y: auto;
    }

    .teacher-modal-image {
      text-align: center;
      margin-bottom: 25px;
    }

    .teacher-modal-image img {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #FFD700;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    .teacher-modal-section {
      margin-bottom: 25px;
    }

    .teacher-modal-section h3 {
      color: #333;
      font-size: 1.3em;
      margin-bottom: 10px;
      padding-bottom: 8px;
      border-bottom: 2px solid #FFD700;
      display: inline-block;
    }

    .teacher-modal-section p {
      color: #666;
      line-height: 1.6;
      font-size: 1em;
      margin: 0;
    }

    @media (max-width: 768px) {
      .teacher-modal-content {
        width: 95%;
        margin: 10% auto;
      }

      .teacher-modal-header {
        padding: 15px 20px;
      }

      .teacher-modal-title {
        font-size: 1.5em;
      }

      .teacher-modal-body {
        padding: 20px;
      }

      .teacher-modal-image img {
        width: 120px;
        height: 120px;
      }
    }
  </style>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book a Studio</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
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
    <div class="info-section fixed-info-section">
      <p class="studio-name">Lemon Hub Studio</p>
      <h2><span id="serviceType">STUDIO RENTAL</span> <span class="light-text">SELECT DATE</span></h2>
      <p class="duration">🕒 <span id="selectedDurationLabel">3 hrs</span></p>
      <p class="location">📍 288H Sto.Domingo Street, 2nd Filmont Homes, Calamba, Laguna</p>
      <img src="{{ asset('images/studio.jpg') }}" alt="Studio" class="studio-image">
      <div class="summary-wrapper">
        <form id="bookingForm" action="{{ route('booking.store') }}" method="POST" style="display:none;">
          @csrf
          <input type="hidden" name="date" id="bookingDate">
          <input type="hidden" name="time_slot" id="bookingTimeSlot">
          <input type="hidden" name="duration" id="bookingDuration">
          <div>
            <strong>Date:</strong> <span id="confirmDate"></span><br>
            <strong>Time Slot:</strong> <span id="confirmTimeSlot"></span><br>
            <strong>Duration:</strong> <span id="confirmDuration"></span>
          </div>
          <button type="submit" class="book-btn">Confirm Booking</button>
        </form>
      </div>
    </div>
    <div class="booking-right">
      <div class="calendar-section">
        <div class="calendar-header">
          <select id="monthDropdown"></select>
        </div>
        <div id="calendar" class="calendar-grid"></div>
        <p class="selected-date" id="selectedDateLabel"></p>
        <!-- Duration dropdown -->
        <label for="durationSelect" style="display:block; margin: 10px 0 5px;">Choose Duration:</label>
        <select id="durationSelect">
          <option value="1">1 hour</option>
          <option value="2">2 hours</option>
          <option value="3" selected>3 hours</option>
          <option value="4">4 hours</option>
          <option value="5">5 hours</option>
          <option value="6">6 hours</option>
          <option value="7">7 hours</option>
          <option value="8">8 hours</option>
        </select>
        <div class="slots scrollable-time-slots"></div>
        <button class="next-btn" id="nextBtn">Next</button>
      </div>
    </div>
  </div>

@if(session('success'))
  <div class="alert alert-success" style="color: green; text-align: center; margin-bottom: 10px;">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger" style="color: red; text-align: center; margin-bottom: 10px;">{{ session('error') }}</div>
@endif

<!-- Removed the form from the bottom. It will only show in the modal after clicking Next. -->

<script src="{{ asset('js/booking.js') }}"></script>
</body>
</html>

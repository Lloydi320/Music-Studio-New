<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book a Studio</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
</head>
<body>

<div class="booking-container">
  
  <div class="info-section">
    <p class="studio-name">Lemon Hub Studio</p>
    <h2><span id="serviceType">STUDIO RENTAL</span> <span class="light-text">SELECT DATE</span></h2>
    <p class="duration">🕒 <span id="selectedDurationLabel">3 hrs</span></p>
    <p class="location">📍 288H Sto.Domingo Street, 2nd Filmont Homes, Calamba, Laguna</p>
    <img src="{{ asset('images/studio.jpg') }}" alt="Studio" class="studio-image">
    <button class="cancel-btn" onclick="window.location.href='/services'">Cancel</button>
  </div>

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

@if(session('success'))
  <div class="alert alert-success" style="color: green; text-align: center; margin-bottom: 10px;">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger" style="color: red; text-align: center; margin-bottom: 10px;">{{ session('error') }}</div>
@endif

<form id="bookingForm" action="{{ route('booking.store') }}" method="POST" style="display:none; flex-direction: column; align-items: center; margin-top: 20px;">
  @csrf
  <input type="hidden" name="date" id="bookingDate">
  <input type="hidden" name="time_slot" id="bookingTimeSlot">
  <input type="hidden" name="duration" id="bookingDuration">
  <div style="margin-bottom: 10px;">
    <strong>Date:</strong> <span id="confirmDate"></span><br>
    <strong>Time Slot:</strong> <span id="confirmTimeSlot"></span><br>
    <strong>Duration:</strong> <span id="confirmDuration"></span>
  </div>
  <button type="submit" class="book-btn">Confirm Booking</button>
</form>

<script src="{{ asset('js/booking.js') }}"></script>
</body>
</html>

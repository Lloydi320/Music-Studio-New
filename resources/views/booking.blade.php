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

<section class="hero">
  <div class="hero-overlay" style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="booking-container">
      <div class="info-section">
        <p class="studio-name">Lemon Hub Studio</p>
        <h2><span id="serviceType">STUDIO RENTAL</span> <span class="light-text">SELECT DATE</span></h2>
        <p class="duration">🕒 3 hrs</p>
        <p class="location">📍 288H Sto.Domingo Street, 2nd Filmont Homes, Calamba, Laguna</p>
        <img src="{{ asset('images/studio.jpg') }}" alt="Studio" class="studio-image">
        <button class="cancel-btn" onclick="window.location.href='/services'">Cancel</button>
      </div>
      <div class="calendar-section">
        <div class="calendar-header">
          <select id="monthDropdown"></select>
        </div>
        <div id="calendar" class="calendar-grid"></div>
        <div class="booking-info-box" style="background: #fff8e1; border-radius: 10px; padding: 15px; margin-top: 10px; min-height: 60px;"></div>
        <div id="timeSlots" class="time-slots hidden">
          <p class="selected-date" id="selectedDateLabel"></p>
          <div class="slots">
            <button>8:00 - 11:00 AM</button>
            <button>11:00 - 1:00 PM</button>
            <button>1:00 - 4:00 PM</button>
            <button>4:00 - 7:00 PM</button>
          </div>
          <button class="next-btn">Next</button>
        </div>
      </div>
    </div>
  </div>
</section>

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
  <div style="margin-bottom: 10px;">
    <strong>Date:</strong> <span id="confirmDate"></span><br>
    <strong>Time Slot:</strong> <span id="confirmTimeSlot"></span>
  </div>
  <button type="submit" class="book-btn">Confirm Booking</button>
</form>

<script src="{{ asset('js/booking.js') }}"></script>
<script>
// Show booking form when a slot is selected
let selectedDate = '';
let selectedTimeSlot = '';

// Example: You should update this logic to match your actual calendar/time slot selection logic
const slotButtons = document.querySelectorAll('.slots button');
slotButtons.forEach(btn => {
  btn.addEventListener('click', function() {
    selectedTimeSlot = this.textContent;
    // Assume selectedDate is set by your calendar logic
    selectedDate = document.getElementById('selectedDateLabel').textContent;
    if(selectedDate && selectedTimeSlot) {
      document.getElementById('bookingDate').value = selectedDate;
      document.getElementById('bookingTimeSlot').value = selectedTimeSlot;
      document.getElementById('confirmDate').textContent = selectedDate;
      document.getElementById('confirmTimeSlot').textContent = selectedTimeSlot;
      document.getElementById('bookingForm').style.display = 'flex';
    }
  });
});
</script>
</body>
</html>

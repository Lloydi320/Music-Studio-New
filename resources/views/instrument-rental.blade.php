<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Band Equipment Rental - Lemon Hub Studio</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
  <style>
         .rental-container {
       max-width: 1200px;
       margin: 0 auto;
       padding: 20px;
       background: #fff;
       border-radius: 10px;
       box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
     }

         .rental-header {
       text-align: center;
       margin-bottom: 30px;
       padding: 20px;
       background: linear-gradient(135deg, #ffd700 0%, #dbb411 100%);
       color: #111;
       border-radius: 10px;
       box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
     }

         .rental-header h1 {
       margin: 0;
       font-size: 2.5em;
       font-weight: bold;
     }

         .rental-header p {
       margin: 10px 0 0 0;
       font-size: 1.1em;
       opacity: 0.9;
     }

         .rental-form {
       display: grid;
       grid-template-columns: 1fr 1fr;
       gap: 30px;
       margin-bottom: 30px;
     }

         .form-section {
       background: #f8f9fa;
       padding: 25px;
       border-radius: 8px;
       border: 1px solid #e9ecef;
     }

         .form-section h3 {
       margin: 0 0 20px 0;
       color: #111;
       font-size: 1.3em;
       border-bottom: 2px solid #ffd700;
       padding-bottom: 10px;
     }

         .form-group {
       margin-bottom: 20px;
     }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #555;
    }

    .form-group select,
    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 12px;
      border: 2px solid #ddd;
      border-radius: 6px;
      font-size: 16px;
      transition: border-color 0.3s ease;
    }

    .form-group select:focus,
    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

         .price-summary {
       background: linear-gradient(135deg, #ffd700 0%, #dbb411 100%);
       color: #111;
       padding: 25px;
       border-radius: 10px;
       margin-bottom: 30px;
       box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
     }

         .price-summary h3 {
       margin: 0 0 15px 0;
       font-size: 1.4em;
     }

         .price-details {
       display: grid;
       grid-template-columns: 1fr 1fr;
       gap: 15px;
       margin-bottom: 20px;
     }

    .price-item {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

         .package-option {
       margin: 15px 0;
       padding: 15px;
       background: rgba(255, 255, 255, 0.3);
       border-radius: 8px;
       border: 2px solid rgba(255, 255, 255, 0.5);
       box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
     }

     .package-option label {
       display: flex;
       align-items: center;
       gap: 10px;
       cursor: pointer;
       color: #111;
       font-weight: 500;
     }

     .package-option input[type="checkbox"] {
       transform: scale(1.2);
     }

     .total-price {
       font-size: 1.5em;
       font-weight: bold;
       text-align: center;
       padding: 15px;
       background: rgba(255, 255, 255, 0.3);
       border-radius: 8px;
       margin-top: 15px;
       color: #111;
       box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
     }

         .submit-section {
       text-align: center;
       padding: 20px;
       background: #f8f9fa;
       border-radius: 8px;
     }

         .submit-btn {
       background: linear-gradient(135deg, #ffd700 0%, #dbb411 100%);
       color: #111;
       border: none;
       padding: 15px 40px;
       font-size: 18px;
       font-weight: bold;
       border-radius: 8px;
       cursor: pointer;
       transition: all 0.3s ease;
       box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
     }

         .submit-btn:hover {
       transform: translateY(-2px);
       box-shadow: 0 6px 20px rgba(255, 215, 0, 0.5);
       background: linear-gradient(135deg, #dbb411 0%, #ffd700 100%);
     }

     .rental-terms {
       background: linear-gradient(135deg, #fff8dc 0%, #fffacd 100%);
       border: 2px solid #ffd700;
       border-radius: 8px;
       padding: 20px;
       margin: 20px 0;
       box-shadow: 0 2px 10px rgba(255, 215, 0, 0.2);
     }

     .rental-terms h3 {
       color: #111;
       margin: 0 0 15px 0;
       font-size: 1.3em;
     }

     .terms-list {
       display: flex;
       flex-direction: column;
       gap: 10px;
     }

     .term-item {
       background: white;
       padding: 12px;
       border-radius: 6px;
       border-left: 4px solid #ffd700;
       font-size: 0.9em;
       line-height: 1.4;
       box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
     }

     .term-item strong {
       color: #dbb411;
     }
     
     /* Custom input styles to match the yellow theme */
     input[type="text"], input[type="email"], input[type="number"], input[type="date"], select, textarea {
       width: 100%;
       padding: 12px;
       border: 2px solid #e9ecef;
       border-radius: 6px;
       font-size: 16px;
       transition: all 0.3s ease;
       background: white;
     }
     
     input[type="text"]:focus, input[type="email"]:focus, input[type="number"]:focus, input[type="date"]:focus, select:focus, textarea:focus {
       outline: none;
       border-color: #ffd700;
       box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
     }
     
     select {
       background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23ffd700' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
       background-repeat: no-repeat;
       background-position: right 12px center;
       background-size: 16px;
       padding-right: 40px;
     }

    .alert {
      padding: 15px;
      margin: 20px 0;
      border-radius: 6px;
      font-weight: 500;
    }

    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .alert-error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .instrument-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 15px;
      margin-top: 15px;
    }

    .instrument-card {
      background: white;
      border: 2px solid #e9ecef;
      border-radius: 8px;
      padding: 15px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .instrument-card:hover {
      border-color: #ffd700;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
    }

    .instrument-card.selected {
      border-color: #ffd700;
      background: linear-gradient(135deg, #ffd700 0%, #dbb411 100%);
      color: #111;
    }

    .instrument-name {
      font-weight: bold;
      margin-bottom: 5px;
    }

    .instrument-price {
      font-size: 0.9em;
      opacity: 0.8;
    }

         @media (max-width: 768px) {
       .rental-form {
         grid-template-columns: 1fr;
       }
       
       .price-details {
         grid-template-columns: 1fr;
       }
     }

     /* Ensure proper spacing */
     .booking-main {
       min-height: calc(100vh - 200px);
       padding-bottom: 50px;
     }

     .rental-container {
       margin-bottom: 30px;
     }

     .booking-footer {
       margin-top: 50px;
       padding-top: 30px;
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
    <div class="rental-container">
      <div class="rental-header">
        <h1>🎸 Band Equipment Rental</h1>
        <p>Rent professional-grade band equipment for events, gigs, and performances</p>
      </div>

      @if(session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      @endif
      
      @if(session('error'))
        <div class="alert alert-error">
          {{ session('error') }}
        </div>
      @endif

      <form id="rentalForm" action="{{ route('instrument-rental.store') }}" method="POST">
        @csrf
        <div class="rental-form">
          <div class="form-section">
            <h3>📋 Instrument Selection</h3>
            
            <div class="form-group">
              <label for="instrument_type">Instrument Type:</label>
              <select id="instrument_type" name="instrument_type" required>
                <option value="">Select Instrument Type</option>
                @foreach($instrumentTypes as $key => $type)
                  <option value="{{ $key }}" data-rate="{{ $dailyRates[$key] ?? 10 }}">{{ $type }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="instrument_name">Specific Instrument:</label>
              <select id="instrument_name" name="instrument_name" required disabled>
                <option value="">Select Instrument Type First</option>
              </select>
            </div>

            <div class="form-group">
              <label for="daily_rate">Daily Rate:</label>
              <input type="text" id="daily_rate" value="₱0.00" readonly>
            </div>
          </div>

          <div class="form-section">
            <h3>📅 Rental Period</h3>
            
            <div class="form-group">
              <label for="rental_start_date">Start Date:</label>
              <input type="date" id="rental_start_date" name="rental_start_date" required min="{{ date('Y-m-d') }}">
            </div>

            <div class="form-group">
              <label for="rental_end_date">End Date:</label>
              <input type="date" id="rental_end_date" name="rental_end_date" required>
            </div>

            <div class="form-group">
              <label for="rental_duration">Duration:</label>
              <input type="text" id="rental_duration" value="0 days" readonly>
            </div>

            <div class="form-group">
              <label for="pickup_location">Pickup Location:</label>
              <select name="pickup_location" required>
                <option value="Studio">Studio (288H Sto.Domingo Street, Calamba)</option>
                <option value="Delivery">Delivery (Additional fee may apply)</option>
              </select>
            </div>

            <div class="form-group">
              <label for="return_location">Return Location:</label>
              <select name="return_location" required>
                <option value="Studio">Studio (288H Sto.Domingo Street, Calamba)</option>
                <option value="Pickup">Pickup Service (Additional fee may apply)</option>
              </select>
            </div>

            <div class="form-group">
              <label for="transportation">Transportation Service:</label>
              <select name="transportation" id="transportation_select">
                <option value="none">No Transportation (Self Pickup)</option>
                <option value="delivery">Delivery & Pickup (₱500-₱600 within Laguna)</option>
              </select>
            </div>

            <div class="form-group">
              <label for="venue_type">Venue Type:</label>
              <select name="venue_type" required>
                <option value="indoor">Indoor Venue (Required for safety)</option>
                <option value="outdoor" disabled>Outdoor Venue (Not allowed)</option>
              </select>
            </div>

            <div class="form-group">
              <label for="event_duration_hours">Event Duration (Hours):</label>
              <input type="number" name="event_duration_hours" min="1" max="12" value="7" required>
              <small>Maximum 7 hours included. ₱200 per exceeding hour.</small>
            </div>

            <div class="form-group">
              <label>
                <input type="checkbox" name="documentation_consent" value="1" checked>
                I consent to photos/videos being taken for studio documentation
              </label>
            </div>
          </div>
        </div>

        <div class="price-summary">
          <h3>💰 Price Summary</h3>
                     <div class="price-details">
             <div class="price-item">
               <span>Daily Rate:</span>
               <span id="summary_daily_rate">₱0.00</span>
             </div>
             <div class="price-item">
               <span>Duration:</span>
               <span id="summary_duration">0 days</span>
             </div>
             <div class="price-item">
               <span>Subtotal:</span>
               <span id="summary_subtotal">₱0.00</span>
             </div>
             <div class="price-item">
               <span>Transportation Fee:</span>
               <span id="transportation_fee">₱0.00</span>
             </div>
             <div class="price-item">
               <span>Reservation Fee & Security Deposit:</span>
               <span>₱300.00</span>
             </div>
           </div>
          <div class="package-option">
            <label>
              <input type="checkbox" id="full_package" name="full_package">
              <strong>Full Package Rental (₱4,500/day)</strong> - Includes all equipment
            </label>
          </div>
          <div class="total-price">
            Total: <span id="summary_total">₱0.00</span>
          </div>
        </div>

        <div class="form-section">
          <h3>📝 Additional Notes</h3>
          <div class="form-group">
            <label for="notes">Special Requirements or Notes:</label>
            <textarea name="notes" id="notes" rows="4" placeholder="Any special requirements, setup needs, or additional information..."></textarea>
          </div>
        </div>

        <div class="rental-terms">
          <h3>⚠️ Rental Terms & Conditions</h3>
          <div class="terms-list">
            <div class="term-item">
              <strong>₱300 Reservation Fee & Security Deposit</strong> - Refundable after return of equipment
            </div>
            <div class="term-item">
              <strong>Payment Policy</strong> - Must be paid in full before pickup
            </div>
            <div class="term-item">
              <strong>ID Requirement</strong> - Leave 1 government ID upon receiving the rented instrument/s
            </div>
            <div class="term-item">
              <strong>Venue Restriction</strong> - For safety and preservation, rentals are strictly for indoor venues use only
            </div>
            <div class="term-item">
              <strong>Event Duration</strong> - For single event only maximum of 7 Hours (₱200 charge per exceeding hours)
            </div>
            <div class="term-item">
              <strong>Transportation</strong> - Additional ₱1,000 charge for beyond Laguna area event (Transportation fee not included)
            </div>
            <div class="term-item">
              <strong>Cancellation Policy</strong> - Cancellations will not be eligible for a refund
            </div>
            <div class="term-item">
              <strong>Documentation</strong> - We document with photos/videos for posting. Message us if you prefer not to be shown
            </div>
          </div>
        </div>

        <div class="submit-section">
          <button type="submit" class="submit-btn">🎵 Confirm Instrument Rental</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add spacing before footer -->
  <div style="height: 100px; margin-top: 50px;"></div>

  <footer class="booking-footer">
    <div class="footer-content">
      <p>&copy; 2025 Lemon Hub Studio - All Rights Reserved</p>
      <p>Professional Music Studio Services</p>
    </div>
  </footer>

  <script>
    // Instrument rental form functionality
    document.addEventListener('DOMContentLoaded', function() {
      const instrumentTypeSelect = document.getElementById('instrument_type');
      const instrumentNameSelect = document.getElementById('instrument_name');
      const dailyRateInput = document.getElementById('daily_rate');
      const startDateInput = document.getElementById('rental_start_date');
      const endDateInput = document.getElementById('rental_end_date');
      const durationInput = document.getElementById('rental_duration');
      const form = document.getElementById('rentalForm');

      // Available instruments data
      const availableInstruments = @json($availableInstruments);
      const dailyRates = @json($dailyRates);

      // Update instruments when type changes
      instrumentTypeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        const dailyRate = dailyRates[selectedType] || 10;
        
        // Update daily rate display
        dailyRateInput.value = `₱${dailyRate.toFixed(2)}`;
        
        // Update instrument options
        instrumentNameSelect.innerHTML = '<option value="">Select Specific Instrument</option>';
        instrumentNameSelect.disabled = !selectedType;
        
        if (selectedType && availableInstruments[selectedType]) {
          Object.entries(availableInstruments[selectedType]).forEach(([key, name]) => {
            const option = document.createElement('option');
            option.value = key;
            option.textContent = name;
            instrumentNameSelect.appendChild(option);
          });
        }
        
        updatePriceSummary();
      });

      // Calculate duration when dates change
      function calculateDuration() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        
        if (startDate && endDate && endDate >= startDate) {
          const diffTime = Math.abs(endDate - startDate);
          const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // Include both start and end dates
          durationInput.value = `${diffDays} day${diffDays !== 1 ? 's' : ''}`;
        } else {
          durationInput.value = '0 days';
        }
        
        updatePriceSummary();
      }

             startDateInput.addEventListener('change', calculateDuration);
       endDateInput.addEventListener('change', calculateDuration);

       // Add event listeners for full package and transportation
       document.getElementById('full_package').addEventListener('change', updatePriceSummary);
       document.getElementById('transportation_select').addEventListener('change', updatePriceSummary);

             // Update price summary
       function updatePriceSummary() {
         const selectedType = instrumentTypeSelect.value;
         const fullPackage = document.getElementById('full_package').checked;
         const transportation = document.getElementById('transportation_select').value;
         
         let dailyRate = 0;
         let duration = parseInt(durationInput.value) || 0;
         
         if (fullPackage) {
           dailyRate = 4500; // Full package rate
         } else {
           dailyRate = dailyRates[selectedType] || 0;
         }
         
         const subtotal = dailyRate * duration;
         const transportationFee = transportation === 'delivery' ? 550 : 0; // Average of ₱500-₱600
         const reservationFee = 300; // Reservation fee & security deposit
         const total = subtotal + transportationFee + reservationFee;
         
         document.getElementById('summary_daily_rate').textContent = `₱${dailyRate.toFixed(2)}`;
         document.getElementById('summary_duration').textContent = `${duration} day${duration !== 1 ? 's' : ''}`;
         document.getElementById('summary_subtotal').textContent = `₱${subtotal.toFixed(2)}`;
         document.getElementById('transportation_fee').textContent = `₱${transportationFee.toFixed(2)}`;
         document.getElementById('summary_total').textContent = `₱${total.toFixed(2)}`;
       }

      // Form validation
      form.addEventListener('submit', function(e) {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (startDate < today) {
          e.preventDefault();
          alert('Start date cannot be in the past.');
          return;
        }
        
        if (endDate <= startDate) {
          e.preventDefault();
          alert('End date must be after start date.');
          return;
        }
        
        if (!instrumentTypeSelect.value || !instrumentNameSelect.value) {
          e.preventDefault();
          alert('Please select both instrument type and specific instrument.');
          return;
        }
      });

      // Set minimum end date based on start date
      startDateInput.addEventListener('change', function() {
        const startDate = new Date(this.value);
        const minEndDate = new Date(startDate);
        minEndDate.setDate(minEndDate.getDate() + 1);
        endDateInput.min = minEndDate.toISOString().split('T')[0];
      });
    });
  </script>
</body>
</html> 
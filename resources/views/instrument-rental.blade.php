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
       max-width: 1400px;
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
       grid-template-columns: 1fr 1fr 1fr;
       gap: 25px;
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
     
     .rental-terms-compact {
       margin-top: 20px;
       padding: 15px;
       background: linear-gradient(135deg, #fff8dc 0%, #fffacd 100%);
       border-radius: 8px;
       border: 2px solid #ffd700;
       box-shadow: 0 2px 10px rgba(255, 215, 0, 0.2);
     }
     
     .rental-terms-compact h4 {
       color: #111;
       margin: 0 0 10px 0;
       font-size: 1.1em;
     }
     
     .terms-compact {
       display: flex;
       flex-direction: column;
       gap: 8px;
     }
     
     .term-compact {
       font-size: 0.85em;
       color: #555;
       line-height: 1.3;
     }
     
     .form-section .price-details {
       margin-bottom: 20px;
     }
     
     .form-section .price-item {
       display: flex;
       justify-content: space-between;
       padding: 8px 0;
       border-bottom: 1px solid rgba(255, 255, 255, 0.3);
       font-size: 0.9em;
     }
     
     .form-section .price-item:last-child {
       border-bottom: none;
       font-weight: bold;
       font-size: 1em;
     }
     
     .form-section .package-option {
       margin: 15px 0;
       padding: 12px;
       background: rgba(255, 255, 255, 0.4);
       border-radius: 6px;
       border: 1px solid rgba(255, 255, 255, 0.6);
     }
     
     .form-section .total-price {
       font-size: 1.3em;
       padding: 12px;
       margin-top: 15px;
       background: rgba(255, 255, 255, 0.5);
       border: 2px solid rgba(255, 215, 0, 0.5);
     }
     
     .payment-note {
       margin-top: 15px;
       padding: 12px;
       background: rgba(255, 255, 255, 0.4);
       border-radius: 6px;
       border: 1px solid rgba(255, 215, 0, 0.3);
       font-size: 0.9em;
     }
     
     .payment-note ul {
       margin: 8px 0 0 0;
       padding-left: 20px;
     }
     
            .payment-note li {
         margin-bottom: 4px;
         color: #555;
       }
       
       /* Modal Styles */
       .modal {
         display: none;
         position: fixed;
         z-index: 9999;
         left: 0;
         top: 0;
         width: 100vw;
         height: 100vh;
         background-color: rgba(0, 0, 0, 0.6);
         backdrop-filter: blur(5px);
       }
       
       .modal-content {
         background-color: white;
         margin: 1% auto;
         padding: 0;
         border-radius: 10px;
         width: 95%;
         max-width: 700px;
         max-height: 85vh;
         overflow-y: auto;
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
       }
       
       .modal-header {
         background: linear-gradient(135deg, #ffd700 0%, #dbb411 100%);
         color: #111;
         padding: 15px 20px;
         border-radius: 10px 10px 0 0;
         display: flex;
         justify-content: space-between;
         align-items: center;
       }
       
       .modal-header h2 {
         margin: 0;
         font-size: 1.3em;
       }
       
       .close-modal {
         color: #111;
         font-size: 28px;
         font-weight: bold;
         cursor: pointer;
         line-height: 1;
       }
       
       .close-modal:hover {
         color: #333;
       }
       
       .modal-body {
         padding: 12px 18px;
       }
       
       .summary-section {
         margin-bottom: 15px;
       }
       
       .summary-section h3 {
         color: #111;
         margin: 0 0 8px 0;
         font-size: 1em;
         border-bottom: 2px solid #ffd700;
         padding-bottom: 2px;
       }
       
       .summary-item {
         display: flex;
         justify-content: space-between;
         padding: 4px 0;
         border-bottom: 1px solid #eee;
         font-size: 0.9em;
       }
       
       .summary-item.total {
         font-weight: bold;
         font-size: 1.05em;
         color: #dbb411;
         border-bottom: 2px solid #ffd700;
       }
       
       .payment-reminder {
         background: linear-gradient(135deg, #fff8dc 0%, #fffacd 100%);
         border: 2px solid #ffd700;
         border-radius: 8px;
         padding: 10px;
         margin-top: 12px;
       }
       
       .payment-reminder strong {
         color: #111;
       }
       
       .payment-reminder p {
         margin: 6px 0 0 0;
         color: #555;
         font-size: 0.85em;
       }
       
       .modal-footer {
         padding: 12px 18px;
         border-top: 1px solid #eee;
         display: flex;
         gap: 15px;
         justify-content: flex-end;
       }
       
       .btn-cancel, .btn-confirm {
         padding: 10px 20px;
         border: none;
         border-radius: 6px;
         font-size: 14px;
         font-weight: bold;
         cursor: pointer;
         transition: all 0.3s ease;
       }
       
       .btn-cancel {
         background: #f8f9fa;
         color: #666;
         border: 2px solid #ddd;
       }
       
       .btn-cancel:hover {
         background: #e9ecef;
         border-color: #adb5bd;
       }
       
       .btn-confirm {
         background: linear-gradient(135deg, #ffd700 0%, #dbb411 100%);
         color: #111;
         border: 2px solid #ffd700;
         box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
       }
       
       .btn-confirm:hover {
         transform: translateY(-2px);
         box-shadow: 0 6px 20px rgba(255, 215, 0, 0.5);
       }
       
       /* Responsive modal for smaller screens */
       @media (max-height: 700px) {
         .modal-content {
           margin: 0.5% auto;
           max-height: 98vh;
         }
         
         .modal-header {
           padding: 10px 15px;
         }
         
         .modal-header h2 {
           font-size: 1.2em;
         }
         
         .modal-body {
           padding: 8px 12px;
         }
         
         .summary-section {
           margin-bottom: 10px;
         }
         
         .summary-section h3 {
           font-size: 0.95em;
           margin: 0 0 6px 0;
         }
         
         .summary-item {
           padding: 3px 0;
           font-size: 0.85em;
         }
         
         .payment-reminder {
           padding: 6px;
           margin-top: 8px;
         }
         
         .payment-reminder p {
           font-size: 0.75em;
         }
         
         .modal-footer {
           padding: 8px 12px;
         }
         
         .btn-cancel, .btn-confirm {
           padding: 6px 14px;
           font-size: 12px;
         }
       }
     
     /* Responsive design */
     @media (max-width: 1200px) {
       .rental-form {
         grid-template-columns: 1fr 1fr;
         gap: 20px;
       }
       
       .rental-form .form-section:last-child {
         grid-column: 1 / -1;
         max-width: 600px;
         margin: 0 auto;
       }
     }
     
     @media (max-width: 768px) {
       .rental-form {
         grid-template-columns: 1fr;
         gap: 20px;
       }
       
       .rental-container {
         padding: 15px;
       }
       
       .rental-header h1 {
         font-size: 2em;
       }
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
              <label for="notes">Special Requirements or Notes:</label>
              <textarea name="notes" id="notes" rows="3" placeholder="Any special requirements, setup needs, or additional information..."></textarea>
            </div>
          </div>

          <div class="form-section">
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
            
            <div class="payment-note">
              <strong>💳 Payment Terms:</strong>
              <ul>
                <li>Security deposit (₱300) must be paid first to confirm booking</li>
                <li>Full payment will be collected upon pickup/delivery</li>
                <li>Cash or digital payment accepted</li>
              </ul>
            </div>

            <div class="rental-terms-compact">
              <h4>⚠️ Key Terms</h4>
              <div class="terms-compact">
                <div class="term-compact">• ₱300 Reservation Fee (Refundable Upon Event Completion)</div>
                <div class="term-compact">• Indoor venues only</div>
                <div class="term-compact">• Max 7 hours included</div>
                <div class="term-compact">• ID required for pickup</div>
                <div class="term-compact">• Full payment before pickup</div>
              </div>
            </div>
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
          <button type="button" class="submit-btn" id="showSummaryBtn">🎵 Confirm Instrument Rental</button>
        </div>
      </form>
      
      <!-- Rental Summary Modal -->
      <div id="rentalSummaryModal" class="modal">
        <div class="modal-content">
          <div class="modal-header">
            <h2>📋 Rental Booking Summary</h2>
            <span class="close-modal">&times;</span>
          </div>
          <div class="modal-body">
            <div class="summary-section">
              <h3>🎸 Instrument Details</h3>
              <div class="summary-item">
                <span>Instrument Type:</span>
                <span id="modal_instrument_type">-</span>
              </div>
              <div class="summary-item">
                <span>Specific Instrument:</span>
                <span id="modal_instrument_name">-</span>
              </div>
              <div class="summary-item">
                <span>Daily Rate:</span>
                <span id="modal_daily_rate">-</span>
              </div>
            </div>
            
            <div class="summary-section">
              <h3>📅 Rental Period</h3>
              <div class="summary-item">
                <span>Start Date:</span>
                <span id="modal_start_date">-</span>
              </div>
              <div class="summary-item">
                <span>End Date:</span>
                <span id="modal_end_date">-</span>
              </div>
              <div class="summary-item">
                <span>Duration:</span>
                <span id="modal_duration">-</span>
              </div>
              <div class="summary-item">
                <span>Event Duration:</span>
                <span id="modal_event_duration">-</span>
              </div>
            </div>
            
            <div class="summary-section">
              <h3>📍 Location & Services</h3>
              <div class="summary-item">
                <span>Pickup Location:</span>
                <span id="modal_pickup">-</span>
              </div>
              <div class="summary-item">
                <span>Return Location:</span>
                <span id="modal_return">-</span>
              </div>
              <div class="summary-item">
                <span>Transportation:</span>
                <span id="modal_transportation">-</span>
              </div>
            </div>
            
            <div class="summary-section">
              <h3>💰 Payment Summary</h3>
              <div class="summary-item">
                <span>Subtotal:</span>
                <span id="modal_subtotal">-</span>
              </div>
              <div class="summary-item">
                <span>Transportation Fee:</span>
                <span id="modal_transportation_fee">-</span>
              </div>
              <div class="summary-item">
                <span>Extra Hour Charges:</span>
                <span id="modal_extra_hours">-</span>
              </div>
              <div class="summary-item total">
                <span>Total Amount:</span>
                <span id="modal_total">-</span>
              </div>
              <div class="summary-item">
                <span>Security Deposit (Paid First):</span>
                <span>₱300.00</span>
              </div>
            </div>
            
            <div class="payment-reminder">
              <strong>💳 Payment Reminder:</strong>
              <p>Security deposit (₱300) must be paid first to confirm this booking. Full payment will be collected upon pickup/delivery. Security deposit is refundable upon event completion.</p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn-cancel" id="cancelBooking">❌ Cancel</button>
            <button type="button" class="btn-confirm" id="confirmBooking">✅ Confirm Booking</button>
          </div>
        </div>
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
       
       // Add event listener for event duration hours
       document.querySelector('input[name="event_duration_hours"]').addEventListener('input', updatePriceSummary);

             // Update price summary
       function updatePriceSummary() {
         const selectedType = instrumentTypeSelect.value;
         const fullPackage = document.getElementById('full_package').checked;
         const transportation = document.getElementById('transportation_select').value;
         const eventDurationHours = parseInt(document.querySelector('input[name="event_duration_hours"]').value) || 7;
         
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
         
         // Calculate extra hour charges (₱200 per exceeding hour after 7 hours)
         const maxIncludedHours = 7;
         const extraHours = Math.max(0, eventDurationHours - maxIncludedHours);
         const extraHourCharges = extraHours * 200;
         
         const total = subtotal + transportationFee + reservationFee + extraHourCharges;
         
         document.getElementById('summary_daily_rate').textContent = `₱${dailyRate.toFixed(2)}`;
         document.getElementById('summary_duration').textContent = `${duration} day${duration !== 1 ? 's' : ''}`;
         document.getElementById('summary_subtotal').textContent = `₱${subtotal.toFixed(2)}`;
         document.getElementById('transportation_fee').textContent = `₱${transportationFee.toFixed(2)}`;
         document.getElementById('summary_total').textContent = `₱${total.toFixed(2)}`;
         
         // Update the event duration note to show extra charges if applicable
         const eventDurationInput = document.querySelector('input[name="event_duration_hours"]');
         const noteElement = eventDurationInput.parentNode.querySelector('small');
         if (extraHours > 0) {
           noteElement.textContent = `Maximum 7 hours included. ₱200 per exceeding hour. (Extra ${extraHours} hour(s): ₱${extraHourCharges.toFixed(2)})`;
           noteElement.style.color = '#e67e22';
           noteElement.style.fontWeight = 'bold';
         } else {
           noteElement.textContent = 'Maximum 7 hours included. ₱200 per exceeding hour.';
           noteElement.style.color = '';
           noteElement.style.fontWeight = '';
         }
       }

      // Modal functionality
      const modal = document.getElementById('rentalSummaryModal');
      const showSummaryBtn = document.getElementById('showSummaryBtn');
      const closeModal = document.querySelector('.close-modal');
      const cancelBooking = document.getElementById('cancelBooking');
      const confirmBooking = document.getElementById('confirmBooking');
      
      // Show modal when button is clicked
      showSummaryBtn.addEventListener('click', function() {
        // Validate form first
        if (!validateForm()) {
          return;
        }
        
        // Populate modal with current form data
        populateModal();
        
        // Show modal
        modal.style.display = 'block';
      });
      
      // Close modal functions
      function closeModalFunc() {
        modal.style.display = 'none';
      }
      
      closeModal.addEventListener('click', closeModalFunc);
      cancelBooking.addEventListener('click', closeModalFunc);
      
      // Close modal when clicking outside
      window.addEventListener('click', function(event) {
        if (event.target === modal) {
          closeModalFunc();
        }
      });
      
      // Confirm booking
      confirmBooking.addEventListener('click', function() {
        // Submit the form
        form.submit();
      });
      
      // Form validation function
      function validateForm() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (startDate < today) {
          alert('Start date cannot be in the past.');
          return false;
        }
        
        if (endDate <= startDate) {
          alert('End date must be after start date.');
          return false;
        }
        
        if (!instrumentTypeSelect.value || !instrumentNameSelect.value) {
          alert('Please select both instrument type and specific instrument.');
          return false;
        }
        
        return true;
      }
      
      // Populate modal with form data
      function populateModal() {
        const selectedType = instrumentTypeSelect.value;
        const selectedInstrument = instrumentNameSelect.value;
        const eventDurationHours = parseInt(document.querySelector('input[name="event_duration_hours"]').value) || 7;
        const transportation = document.getElementById('transportation_select').value;
        const pickupLocation = document.querySelector('select[name="pickup_location"]').value;
        const returnLocation = document.querySelector('select[name="return_location"]').value;
        
        // Get instrument names
        const instrumentTypes = @json($instrumentTypes);
        const availableInstruments = @json($availableInstruments);
        
        // Calculate prices
        const fullPackage = document.getElementById('full_package').checked;
        let dailyRate = 0;
        let duration = parseInt(durationInput.value) || 0;
        
        if (fullPackage) {
          dailyRate = 4500;
        } else {
          dailyRate = dailyRates[selectedType] || 0;
        }
        
        const subtotal = dailyRate * duration;
        const transportationFee = transportation === 'delivery' ? 550 : 0;
        const maxIncludedHours = 7;
        const extraHours = Math.max(0, eventDurationHours - maxIncludedHours);
        const extraHourCharges = extraHours * 200;
        const total = subtotal + transportationFee + 300 + extraHourCharges; // 300 is reservation fee
        
        // Populate modal fields
        document.getElementById('modal_instrument_type').textContent = instrumentTypes[selectedType] || '-';
        document.getElementById('modal_instrument_name').textContent = availableInstruments[selectedType]?.[selectedInstrument] || '-';
        document.getElementById('modal_daily_rate').textContent = `₱${dailyRate.toFixed(2)}`;
        document.getElementById('modal_start_date').textContent = startDateInput.value || '-';
        document.getElementById('modal_end_date').textContent = endDateInput.value || '-';
        document.getElementById('modal_duration').textContent = durationInput.value || '-';
        document.getElementById('modal_event_duration').textContent = `${eventDurationHours} hour(s)`;
        document.getElementById('modal_pickup').textContent = pickupLocation === 'Studio' ? 'Studio (288H Sto.Domingo Street, Calamba)' : pickupLocation;
        document.getElementById('modal_return').textContent = returnLocation === 'Studio' ? 'Studio (288H Sto.Domingo Street, Calamba)' : returnLocation;
        document.getElementById('modal_transportation').textContent = transportation === 'delivery' ? 'Delivery & Pickup (₱550)' : 'No Transportation (Self Pickup)';
        document.getElementById('modal_subtotal').textContent = `₱${subtotal.toFixed(2)}`;
        document.getElementById('modal_transportation_fee').textContent = `₱${transportationFee.toFixed(2)}`;
        document.getElementById('modal_extra_hours').textContent = extraHours > 0 ? `₱${extraHourCharges.toFixed(2)} (${extraHours} extra hour(s))` : '₱0.00';
        document.getElementById('modal_total').textContent = `₱${total.toFixed(2)}`;
      }

      // Set minimum end date based on start date
      startDateInput.addEventListener('change', function() {
        const startDate = new Date(this.value);
        const minEndDate = new Date(startDate);
        minEndDate.setDate(minEndDate.getDate() + 1);
        endDateInput.min = minEndDate.toISOString().split('T')[0];
      });
    });
  </script>
  <script src="{{ asset('js/page-transitions.js') }}"></script>
</body>
</html> 
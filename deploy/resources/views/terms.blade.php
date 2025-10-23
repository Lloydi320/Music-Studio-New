<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rental Terms & Agreement — Lemon Hub Studio</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
  <style>
    body { background:#f7f7f7; color:#111; margin:0; }
    .container { max-width:900px; margin:84px auto 40px; padding:24px; background:#fff; border-radius:16px; box-shadow:0 10px 24px rgba(0,0,0,0.08); }
    h1 { margin:0 0 8px; font-size:2rem; }
    .subtitle { color:#555; margin-bottom:20px; }
    h2 { margin-top:24px; font-size:1.25rem; color:#222; }
    ul { padding-left:20px; }
    li { margin:6px 0; }
    .note { background:#fffbe6; border:1px solid #ffe58f; padding:12px; border-radius:8px; margin-top:16px; }
  </style>
</head>
<body class="booking-page">
  @include('partials.nav')
  <main class="container">
    <h1>Rental Terms & Agreement</h1>
    <p class="subtitle">Effective date: {{ date('F j, Y') }} • Applies to instrument rentals and related services offered by Lemon Hub Studio.</p>

    <p>These terms form a binding agreement between you (the Renter) and Lemon Hub Studio (the Provider). By confirming a rental, you acknowledge that you have read, understood, and agree to these terms.</p>

    <h2>1. Booking & Eligibility</h2>
    <ul>
      <li>You must be at least 18 years old and able to present a valid government-issued ID upon pickup or delivery.</li>
      <li>Rental confirmation is subject to availability and successful payment of the reservation fee.</li>
      <li>We may refuse service for safety, security, or misuse concerns.</li>
    </ul>

    <h2>2. Fees & Payments</h2>
    <ul>
      <li><strong>Rates</strong>: Daily or package rates are shown before you confirm. Transportation and add-ons are billed separately when selected.</li>
      <li><strong>Reservation Fee</strong>: A non-refundable fee may be required to reserve equipment and schedule services.</li>
      <li><strong>Full Payment</strong>: Unless otherwise stated, full payment is required before pickup/delivery.</li>
      <li><strong>Extra Charges</strong>: Late returns, damages, loss, or missing accessories may incur additional fees.</li>
    </ul>

    <h2>3. Pickup, Delivery & Returns</h2>
    <ul>
      <li>Pickup/return locations and times must be the same as confirmed in your booking. Changes require prior approval.</li>
      <li>Delivery is available to supported areas and times only. Additional fees may apply.</li>
      <li>Late returns may incur daily charges at the prevailing daily rate.</li>
    </ul>

    <h2>4. Care, Use & Responsibility</h2>
    <ul>
      <li>You are responsible for the equipment from pickup/delivery until successful return and acceptance by our staff.</li>
      <li>Use equipment only in safe, indoor venues unless the booking explicitly includes outdoor approval.</li>
      <li>Do not modify, disassemble, or sub-rent equipment.</li>
      <li>Report any malfunction immediately; continued use may worsen damage and fees.</li>
    </ul>

    <h2>5. Damage, Loss & Security Deposit</h2>
    <ul>
      <li>You agree to pay repair or replacement costs for any damage, loss, or missing accessories beyond normal wear.</li>
      <li>If a security deposit is required, it may be held and applied against outstanding charges.</li>
      <li>Photos or receipts may be recorded for verification of condition on pickup/return.</li>
    </ul>

    <h2>6. Cancellations & Reschedules</h2>
    <ul>
      <li>Cancellations made after confirmation may forfeit the reservation fee and any time-sensitive costs.</li>
      <li>Reschedules are subject to availability and our approval. Rates and fees may change.</li>
    </ul>

    <h2>7. Liability & Indemnity</h2>
    <ul>
      <li>Lemon Hub Studio is not liable for indirect, incidental, or consequential losses (including lost gigs or profits).</li>
      <li>You agree to indemnify and hold the Provider harmless from claims arising out of your misuse or negligence.</li>
    </ul>

    <h2>8. Data & Communications</h2>
    <ul>
      <li>We may collect booking details, payment references, and contact information to deliver services and verify records.</li>
      <li>We may contact you via the details provided regarding your booking and service updates.</li>
    </ul>

    <h2>9. Governing Law & Disputes</h2>
    <ul>
      <li>These terms are governed by the laws of the Philippines.</li>
      <li>Disputes will be handled first through good-faith resolution with our support team.</li>
    </ul>

    <div class="note">
      This document is provided for operational clarity and is not legal advice. If you need tailored legal terms, consult a lawyer.
    </div>

    <p style="margin-top:20px">Questions? Contact us via the site’s contact section.</p>
  </main>
</body>
</html>
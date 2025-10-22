<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Privacy Policy — Lemon Hub Studio</title>
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
    .note { background:#e6f7ff; border:1px solid #91d5ff; padding:12px; border-radius:8px; margin-top:16px; }
  </style>
</head>
<body class="booking-page">
  @include('partials.nav')
  <main class="container">
    <h1>Privacy Policy</h1>
    <p class="subtitle">Effective date: {{ date('F j, Y') }} • How Lemon Hub Studio collects, uses, and protects your data.</p>

    <h2>1. Information We Collect</h2>
    <ul>
      <li><strong>Account & Contact Data</strong>: name, email, phone, and user account details.</li>
      <li><strong>Booking & Rental Data</strong>: chosen service, dates, equipment, notes, and payment references or receipts.</li>
      <li><strong>Technical Data</strong>: basic device/browser info and usage analytics to keep the site reliable.</li>
    </ul>

    <h2>2. How We Use Your Information</h2>
    <ul>
      <li>Process bookings, rentals, and service communications.</li>
      <li>Verify payments and maintain transaction records.</li>
      <li>Improve scheduling, conflict checks, and user experience.</li>
      <li>Comply with legal obligations and prevent misuse or fraud.</li>
    </ul>

    <h2>3. Sharing & Disclosure</h2>
    <ul>
      <li>We do not sell your personal data.</li>
      <li>We may share data with service providers (e.g., email or hosting) under confidentiality agreements.</li>
      <li>We may disclose information when required by law or to protect rights, safety, and property.</li>
    </ul>

    <h2>4. Data Security & Retention</h2>
    <ul>
      <li>We implement reasonable technical and organizational measures to secure data.</li>
      <li>We retain information only as long as necessary for bookings, rentals, records, and legal requirements.</li>
    </ul>

    <h2>5. Your Rights</h2>
    <ul>
      <li>You may request access, correction, or deletion where feasible and lawful.</li>
      <li>You can update contact details in your account or by contacting support.</li>
    </ul>

    <h2>6. Cookies & Similar Technologies</h2>
    <ul>
      <li>Essential cookies help the site function; optional cookies may improve performance and analytics.</li>
    </ul>

    <h2>7. Minors</h2>
    <ul>
      <li>Our services are intended for adults. Minors must be represented by a parent or guardian.</li>
    </ul>

    <h2>8. Changes to This Policy</h2>
    <ul>
      <li>We may update this policy to reflect improvements or legal changes. The latest version will be posted here.</li>
    </ul>

    <div class="note">
      This summary is operational guidance, not legal advice. For specialized compliance needs, consult a privacy professional.
    </div>

    <p style="margin-top:20px">Questions or requests? Contact us via the site’s contact section.</p>
  </main>
</body>
</html>
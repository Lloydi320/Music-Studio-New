<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Feedback | Lemon Hub Studio</title>
 <link rel="stylesheet" href="style.css" />
 <link rel="stylesheet" href="style.css" />

</head>
<body>

  
  <header class="navbar">
    <div class="logo">
      <img src="images/studio-logo.png" alt="Logo" />
      <span>LEMON HUB STUDIO</span>
    </div>
    <nav>
      <ul class="nav-links">
        <li><a href="index.html">Home</a></li>
        <li><a href="services.html">About Us & Our Services</a></li>
       <li><a href="#" id="contactLink">Contact</a></li>
        <li><a href="feedback.html">Feedbacks</a></li>
      </ul>
    </nav>
  </header>

 
  <main class="feedback-container">
   
    <div class="feedback-list">
      <h2>Recent Feedbacks</h2>
      <div id="feedbackEntries">
        <p class="placeholder">No feedback shared yet.</p>
      </div>
    </div>

    
    <div class="feedback-form">
      <h2>Share Your Experience</h2>
      <form id="feedbackForm">
        <label for="name">Your Name</label>
        <input type="text" id="name" required />

        <label for="rating">Rating</label>
        <div class="rating-stars">
          <span data-value="1">★</span>
          <span data-value="2">★</span>
          <span data-value="3">★</span>
          <span data-value="4">★</span>
          <span data-value="5">★</span>
        </div>

        <label for="comment">Comment</label>
        <textarea id="comment" rows="5" required></textarea>

        <label for="photo">Upload a Photo (optional)</label>
        <input type="file" id="photo" accept="image/*" />

        <button type="submit" class="submit-btn">Submit Feedback</button>
      </form>
    </div>
  </main>

  <script src="script.js"></script>
</body>
</html>

<div id="contactPopup" class="contact-popup">
  <div class="contact-card">
    <button class="close-contact" id="closeContact">&times;</button>
    <h2>Contact Us</h2>
    <p>Feel free to drop us a message</p>

    <div class="contact-row">
      <img src="images/facebook-icon.png" alt="Facebook" class="icon" />
      <div>
        <strong>Facebook</strong><br />
        <span class="yellow">Lemon Hub Studio</span><br />
        <a href="https://www.facebook.com/lemonhubstudio" target="_blank">https://www.facebook.com/lemonhubstudio</a>
      </div>
    </div>

    <hr />

    <div class="contact-row">
      <img src="images/tiktok-icon.png" alt="Tiktok" class="icon" />
      <div>
        <strong>Tiktok</strong><br />
        <span class="yellow">Lemon Hub Studio</span><br />
        <a href="https://www.tiktok.com/@lemon.hub.studio" target="_blank">https://www.tiktok.com/@lemon.hub.studio</a>
      </div>
    </div>

    <hr />

    <div class="contact-row">
      <img src="images/email-icon.png" alt="Email" class="icon" />
      <div>
        <strong>Gmail</strong><br />
        <span class="yellow">Lemon Hub Studio</span><br />
        <a href="mailto:magamponr@gmail.com" class="email-link">magamponr@gmail.com</a>
      </div>
    </div>
  </div>
</div>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Lemon Hub Studio</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="preload" href="{{ asset('images/studio-bg.jpg') }}" as="image">

</head>

<body>


  <header class="navbar">
    <div class="logo">
      <img src="{{ asset('images/studio-logo.png') }}" alt="Lemon Hub Studio Logo">
      <span>LEMON HUB STUDIO</span>
    </div>
    <nav>
      <ul class="nav-links">
        <li><a href="/" class="active">Home</a></li>
        <li><a href="/services">About Us & Our Services</a></li>
        <li><a href="#" id="contactLink">Contact</a></li>
        <li><a href="#" id="feedbackLink">Feedbacks</a></li>
        @if(Auth::check() && Auth::user()->isAdmin())
        <li><a href="/admin/calendar" style="color: #ff6b35; font-weight: bold;">📅 Admin Calendar</a></li>
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
            @if($user->is_admin)
              <a href="/admin/dashboard" class="dropdown-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                  <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Admin Dashboard</span>
              </a>
              <a href="/admin/calendar" class="dropdown-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                  <path d="M19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M16 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M8 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Calendar</span>
              </a>
            @endif
            <a href="/booking" class="dropdown-item">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span>Book Session</span>
            </a>
            <a href="/services" class="dropdown-item">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M12 2L3.09 8.26L4 21L12 17L20 21L20.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span>About Us & Services</span>
            </a>
            <a href="#" id="contactLink" class="dropdown-item">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M22 6L12 13L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span>Contact</span>
            </a>
            <a href="/feedback" class="dropdown-item">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span>Feedback</span>
            </a>
            
            <div class="dropdown-divider"></div>
            
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
    @else
      <a href="/login" class="book-btn" style="margin-left: 30px;">Login</a>
    @endif
  </header>

  
  <section class="hero">
    <div class="hero-overlay" id="mainOverlay">
      <div class="hero-content">
        <h1>BOOK YOUR STUDIO SESSION TODAY!</h1>
        <p>Bringing your music to life, one session at a time.</p>
        @if(Auth::check())
          <button id="openServicesPopup" class="book-btn">Book Now!</button>
          @if(Auth::user()->isAdmin())
            <a href="/admin/dashboard" class="book-btn" style="background: #e74c3c; margin-left: 10px;">Admin Panel</a>
          @endif
        @else
          <a href="/auth/google" class="book-btn">Login to Book Now!</a>
        @endif
      </div>
      <div class="calendar-container">
        <div id="calendar-header">
          <button id="prevMonth">&#9664;</button>
          <span id="monthYear"></span>
          <button id="nextMonth">&#9654;</button>
        </div>
        <div class="calendar-grid" id="calendarGrid"></div>
        <div class="time-slots" id="timeSlots"></div>
      </div>
    </div>
  </section>

 
  <div id="contactPopup" class="contact-popup">
  <div class="contact-card">
    <button class="close-contact" id="closeContact">&times;</button>
    <h2>Contact Us</h2>
    <p>Feel free to drop us a message</p>

    <div class="contact-row">
      <img src="{{ asset('images/facebook-icon.png') }}" alt="Facebook" class="icon" />
      <div>
        <strong>Facebook</strong><br />
        <span class="yellow">Lemon Hub Studio</span><br />
        <a href="https://www.facebook.com/lemonhubstudio" target="_blank">https://www.facebook.com/lemonhubstudio</a>
      </div>
    </div>

    <hr />

    <div class="contact-row">
      <img src="{{ asset('images/tiktok-icon.png') }}" alt="Tiktok" class="icon" />
      <div>
        <strong>Tiktok</strong><br />
        <span class="yellow">Lemon Hub Studio</span><br />
        <a href="https://www.tiktok.com/@lemon.hub.studio" target="_blank">https://www.tiktok.com/@lemon.hub.studio</a>
      </div>
    </div>

    <hr />

    <div class="contact-row">
      <img src="{{ asset('images/email-icon.png') }}" alt="Email" class="icon" />
      <div>
        <strong>Gmail</strong><br />
        <span class="yellow">Lemon Hub Studio</span><br />
        <a href="mailto:magamponr@gmail.com" class="email-link">magamponr@gmail.com</a>
      </div>
    </div>
  </div>
</div>

<!-- Feedback Modal -->
<div id="feedbackPopup" class="feedback-popup">
  <div class="feedback-modal-card">
    <div class="feedback-modal-header">
      <h2>Feedback</h2>
      <button class="close-feedback" id="closeFeedback">&times;</button>
    </div>
    <div class="feedback-modal-content">
      <div class="feedback-list">
        <div id="feedbackEntries">
          <p class="placeholder">No feedback shared yet.</p>
        </div>
      </div>
      <div class="feedback-form">
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
    </div>
  </div>
</div>

<!-- Services Popup Modal -->
<div id="servicesPopup" class="services-popup">
  <div class="services-modal">
    <div class="services-modal-header">
      <h2>Choose Your Service</h2>
      <button class="close-services" id="closeServices">&times;</button>
    </div>
    <div class="services-modal-content">
      <div class="service-grid-popup">
        <a href="/booking" class="service-box-popup">
          <img src="{{ asset('images/studio.jpg') }}" alt="Studio Rental" />
          <h3>Studio Rental</h3>
          <p>Book our acoustically treated studios for jamming, rehearsals, or recording. Fully equipped and flexible.</p>
          <small class="service-hint">Click to Book</small>
        </a>

        <a href="/instrument-rental" class="service-box-popup">
          <img src="{{ asset('images/instruments.png') }}" alt="Instruments Rental" />
          <h3>Instruments Rental</h3>
          <p>Need a guitar, amp, or mic? Rent affordable gear for your session without the hassle.</p>
          <small class="service-hint">Click to Rent</small>
        </a>

        <a href="/music-lessons" class="service-box-popup">
          <img src="{{ asset('images/lessons.jpg') }}" alt="Music Lessons" />
          <h3>Music Lessons</h3>
          <p>Private or group lessons in vocals, guitar, keyboard, and drums. Ideal for all ages and skill levels.</p>
          <small class="service-hint">Click to see more details</small>
        </a>
      </div>
    </div>
  </div>
</div>

  

      <script src="{{ asset('js/script.js') }}"></script>
      <script src="{{ asset('js/page-transitions.js') }}"></script>
  
  <script>
    // Preload background image for faster loading
    document.addEventListener('DOMContentLoaded', function() {
      const hero = document.querySelector('.hero');
      const bgImage = new Image();
      
      bgImage.onload = function() {
        hero.classList.add('loaded');
      };
      
      bgImage.src = '/images/studio-bg.jpg';
    });
    // Global variables for feedback form
    let selectedRating = 0;
    
    // Function to open photo modal
    function openPhotoModal(photoUrl) {
      const modal = document.createElement('div');
      modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10000;
        cursor: pointer;
      `;
      
      const modalImg = document.createElement('img');
      modalImg.src = photoUrl;
      modalImg.style.cssText = `
        max-width: 90%;
        max-height: 90%;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
      `;
      
      modal.appendChild(modalImg);
      document.body.appendChild(modal);
      
      modal.addEventListener('click', () => {
        document.body.removeChild(modal);
      });
    }
    
    // Function to load feedback from database
    function loadFeedbacks() {
      console.log('🔄 Loading feedback from database...');
      const container = document.getElementById('feedbackEntries');
      
      if (!container) {
        console.error('❌ Container element not found!');
        return;
      }
      
      // Show loading state
      container.innerHTML = `
        <div style="text-align: center; padding: 20px; color: #666;">
          <div style="font-size: 1.5em; margin-bottom: 10px;">⏳</div>
          <p>Loading feedback from database...</p>
        </div>
      `;
      
      console.log('📡 Fetching from /api/feedbacks...');
      const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      console.log('🔑 CSRF Token:', token ? 'Present' : 'Missing');
      
      fetch('/api/feedbacks', {
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': token
        }
      })
      .then(res => {
        console.log('📥 Response status:', res.status);
        return res.json();
      })
      .then(data => {
        console.log('📊 Received data:', data);
        container.innerHTML = '';
        if (!data.feedbacks || !data.feedbacks.length) {
          container.innerHTML = `
            <div style="text-align: center; padding: 20px; color: #666;">
              <div style="font-size: 1.5em; margin-bottom: 10px;">📝</div>
              <p>No feedback shared yet.</p>
              <small>Be the first to share your experience!</small>
            </div>
          `;
          return;
        }
        
        data.feedbacks.forEach(feedback => {
          const entry = document.createElement('div');
          entry.className = 'feedback-entry';
          entry.style.cssText = `
            border: 2px solid #ffd700;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
          `;
          
          // Add hover effect
          entry.addEventListener('mouseenter', () => {
            entry.style.transform = 'translateY(-2px)';
            entry.style.boxShadow = '0 6px 20px rgba(0,0,0,0.15)';
          });
          
          entry.addEventListener('mouseleave', () => {
            entry.style.transform = 'translateY(0)';
            entry.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
          });
          
          const stars = '★'.repeat(feedback.rating) + '☆'.repeat(5 - feedback.rating);
          const starColor = feedback.rating >= 4 ? '#ffd700' : feedback.rating >= 3 ? '#ffa500' : '#ff6b6b';
          
          const date = new Date(feedback.created_at);
          const formattedDate = date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
          });
          
          let photoHtml = '';
          if (feedback.photo_url) {
            photoHtml = `
              <div style="margin-top: 10px;">
                <img src="${feedback.photo_url}" 
                     style="width: 100%; max-width: 200px; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); cursor: pointer;" 
                     onclick="openPhotoModal('${feedback.photo_url}')" 
                     alt="Feedback photo" />
              </div>
            `;
          }
          
          const userTypeIcon = feedback.user_type === 'Authenticated' ? '👤' : '👥';
          const userTypeColor = feedback.user_type === 'Authenticated' ? '#007bff' : '#6c757d';
          
          entry.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
              <div style="display: flex; align-items: center; gap: 8px;">
                <h4 style="margin: 0; color: #333; font-size: 1.1em; font-weight: bold;">${feedback.name}</h4>
                <span style="background: ${userTypeColor}; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.6em; font-weight: bold;">
                  ${userTypeIcon} ${feedback.user_type}
                </span>
              </div>
              <div style="text-align: right;">
                <div style="font-size: 1.3em; color: ${starColor}; margin-bottom: 3px;">${stars}</div>
                <small style="color: #666; font-size: 0.8em;">${feedback.rating}/5 stars</small>
              </div>
            </div>
            <div style="background: #f8f9fa; padding: 12px; border-radius: 6px; margin: 8px 0; border-left: 3px solid #ffd700;">
              <p style="margin: 0; color: #555; line-height: 1.5; font-style: italic; font-size: 0.9em;">"${feedback.comment || feedback.content || ''}"</p>
            </div>
            ${photoHtml}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
              <div style="display: flex; align-items: center; gap: 10px;">
                <small style="color: #888; font-size: 0.75em;">📅 ${formattedDate}</small>
                <small style="color: #6c757d; font-size: 0.75em;">🆔 ID: ${feedback.id}</small>
              </div>
              <div style="display: flex; align-items: center; gap: 8px;">
                <small style="color: #28a745; font-weight: bold; font-size: 0.75em;">✅ Saved to Database</small>
                <small style="color: #17a2b8; font-weight: bold; font-size: 0.75em;">📊 From phpMyAdmin</small>
              </div>
            </div>
          `;
          container.appendChild(entry);
        });
        
        // Show success message
        const successMsg = document.createElement('div');
        successMsg.style.cssText = `
          position: fixed;
          top: 20px;
          right: 20px;
          background: #28a745;
          color: white;
          padding: 8px 12px;
          border-radius: 6px;
          font-size: 0.8em;
          z-index: 1000;
          animation: slideIn 0.3s ease;
        `;
        successMsg.innerHTML = `✅ Loaded ${data.feedbacks.length} feedback entries from database`;
        document.body.appendChild(successMsg);
        
        setTimeout(() => {
          if (successMsg.parentNode) {
            successMsg.parentNode.removeChild(successMsg);
          }
        }, 3000);
      })
      .catch(error => {
        console.error('❌ Error loading feedback:', error);
        console.error('❌ Error details:', error.message);
        const container = document.getElementById('feedbackEntries');
        container.innerHTML = `
          <div style="text-align: center; padding: 20px; color: #dc3545;">
            <div style="font-size: 1.5em; margin-bottom: 10px;">❌</div>
            <p>Error loading feedback from database</p>
            <small>Please check your connection and try again</small>
            <br><br>
            <button onclick="loadFeedbacks()" style="background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.8em;">
              🔄 Retry
            </button>
          </div>
        `;
      });
    }
    
    // Function to create feedback card
    function createFeedbackCard(feedback) {
      const card = document.createElement('div');
      card.className = 'feedback-entry';
      card.style.cssText = `
        border: 2px solid #ffd700;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
        animation: slideIn 0.3s ease;
      `;
      
      // Add hover effect
      card.addEventListener('mouseenter', () => {
        card.style.transform = 'translateY(-2px)';
        card.style.boxShadow = '0 6px 20px rgba(0,0,0,0.15)';
      });
      
      card.addEventListener('mouseleave', () => {
        card.style.transform = 'translateY(0)';
        card.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
      });
      
      const stars = '★'.repeat(feedback.rating) + '☆'.repeat(5 - feedback.rating);
      const starColor = feedback.rating >= 4 ? '#ffd700' : feedback.rating >= 3 ? '#ffa500' : '#ff6b6b';
      
      const date = new Date(feedback.created_at);
      const formattedDate = date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
      
      let photoHtml = '';
      if (feedback.photo_url) {
        photoHtml = `
          <div style="margin-top: 10px;">
            <img src="${feedback.photo_url}" 
                 style="width: 100%; max-width: 200px; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); cursor: pointer;" 
                 onclick="openPhotoModal('${feedback.photo_url}')" 
                 alt="Feedback photo" />
          </div>
        `;
      }
      
      const userTypeIcon = feedback.user_type === 'Authenticated' ? '👤' : '👥';
      const userTypeColor = feedback.user_type === 'Authenticated' ? '#007bff' : '#6c757d';
      
      card.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
          <div style="display: flex; align-items: center; gap: 8px;">
            <h4 style="margin: 0; color: #333; font-size: 1.1em; font-weight: bold;">${feedback.name}</h4>
            <span style="background: ${userTypeColor}; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.6em; font-weight: bold;">
              ${userTypeIcon} ${feedback.user_type}
            </span>
          </div>
          <div style="text-align: right;">
            <div style="font-size: 1.3em; color: ${starColor}; margin-bottom: 3px;">${stars}</div>
            <small style="color: #666; font-size: 0.8em;">${feedback.rating}/5 stars</small>
          </div>
        </div>
        <div style="background: #f8f9fa; padding: 12px; border-radius: 6px; margin: 8px 0; border-left: 3px solid #ffd700;">
          <p style="margin: 0; color: #555; line-height: 1.5; font-style: italic; font-size: 0.9em;">"${feedback.comment}"</p>
        </div>
        ${photoHtml}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
          <div style="display: flex; align-items: center; gap: 10px;">
            <small style="color: #888; font-size: 0.75em;">📅 ${formattedDate}</small>
            <small style="color: #6c757d; font-size: 0.75em;">🆔 ID: ${feedback.id}</small>
          </div>
          <div style="display: flex; align-items: center; gap: 8px;">
            <small style="color: #28a745; font-weight: bold; font-size: 0.75em;">✅ Just Submitted</small>
            <small style="color: #17a2b8; font-weight: bold; font-size: 0.75em;">📊 Saved to Database</small>
          </div>
        </div>
      `;
      
      return card;
    }
    
    // Function to show success message
    function showSuccessMessage(message) {
      const successMsg = document.createElement('div');
      successMsg.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.8em;
        z-index: 1000;
        animation: slideIn 0.3s ease;
      `;
      successMsg.innerHTML = message;
      document.body.appendChild(successMsg);
      
      setTimeout(() => {
        if (successMsg.parentNode) {
          successMsg.parentNode.removeChild(successMsg);
        }
      }, 3000);
    }
    
    // Load feedback when page loads
    document.addEventListener('DOMContentLoaded', function() {
      console.log('🚀 Home page loaded, feedback system ready...');
      
      // Handle feedback form submission
      const feedbackForm = document.getElementById('feedbackForm');
      if (feedbackForm) {
        // Add rating star functionality
        const ratingStars = document.querySelectorAll('.rating-stars span');
        ratingStars.forEach((star, index) => {
          star.addEventListener('click', () => {
            selectedRating = index + 1;
            updateStars();
          });
        });
        
        function updateStars() {
          ratingStars.forEach((star, index) => {
            if (index < selectedRating) {
              star.style.color = '#ffd700';
            } else {
              star.style.color = '#ccc';
            }
          });
        }
        
        feedbackForm.addEventListener('submit', async function(e) {
          e.preventDefault();
          
          const name = document.getElementById('name').value.trim();
          const comment = document.getElementById('comment').value.trim();
          const photo = document.getElementById('photo').files[0];
          
          if (!name || !comment || selectedRating === 0) {
            alert('Please fill in all required fields and select a rating.');
            return;
          }
          
          // Create form data
          const formData = new FormData();
          formData.append('name', name);
          formData.append('rating', selectedRating);
          formData.append('comment', comment);
          if (photo) {
            formData.append('photo', photo);
          }
          
          try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch('/api/feedback', {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': token
              },
              body: formData
            });
            
            if (response.ok) {
              const result = await response.json();
              
              // Create feedback card for immediate display
              const feedbackCard = createFeedbackCard({
                id: result.feedback.id,
                name: name,
                rating: selectedRating,
                comment: comment,
                photo_url: photo ? URL.createObjectURL(photo) : null,
                user_type: 'Guest',
                created_at: new Date().toISOString()
              });
              
              // Add to display immediately
              const container = document.getElementById('feedbackEntries');
              const placeholder = container.querySelector('.placeholder');
              if (placeholder) {
                placeholder.remove();
              }
              
              // Insert at the top
              container.insertBefore(feedbackCard, container.firstChild);
              
              // Reset form
              feedbackForm.reset();
              selectedRating = 0;
              updateStars();
              
              // Show success message
              showSuccessMessage('✅ Feedback submitted successfully!');
              
            } else {
              const errorData = await response.json();
              alert('Error submitting feedback: ' + (errorData.message || 'Unknown error'));
            }
          } catch (error) {
            console.error('Error submitting feedback:', error);
            alert('Error submitting feedback. Please try again.');
          }
        });
      }
      
      // Load feedback when popup opens
      const feedbackLink = document.getElementById('feedbackLink');
      const feedbackPopup = document.getElementById('feedbackPopup');
      const closeFeedback = document.getElementById('closeFeedback');
      
      if (feedbackLink && feedbackPopup && closeFeedback) {
        feedbackLink.addEventListener('click', (e) => {
          e.preventDefault();
          console.log('📊 Feedback popup opened, loading database content...');
          
          const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
          document.body.style.overflow = 'hidden';
          document.body.style.paddingRight = `${scrollbarWidth}px`;
          feedbackPopup.classList.add('active');
          
          // Load feedback from database when popup opens
          setTimeout(() => {
            loadFeedbacks();
          }, 100);
        });
        
        closeFeedback.addEventListener('click', () => {
          feedbackPopup.classList.remove('active');
          document.body.style.overflow = '';
          document.body.style.paddingRight = '';
        });
        
        feedbackPopup.addEventListener('click', (e) => {
          if (e.target === feedbackPopup) {
            feedbackPopup.classList.remove('active');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
          }
        });
      }
    });
  </script>

  <!-- Elfsight AI Chatbot | Untitled AI Chatbot -->
  <script src="https://static.elfsight.com/platform/platform.js" async></script>
  <div class="elfsight-app-96cc4395-da06-450f-9191-5bc6e30fa5f7" data-elfsight-app-lazy id="draggable-chatbot"></div>

  <!-- Draggable Chatbot Circle Styles -->
  <style>
    #draggable-chatbot {
      position: fixed !important;
      z-index: 9999 !important;
      cursor: move;
      transition: all 0.2s ease;
    }

    /* Make the chatbot circle draggable when minimized/closed */
    #draggable-chatbot .eapps-widget-toolbar,
    #draggable-chatbot [class*="toolbar"],
    #draggable-chatbot [class*="trigger"],
    #draggable-chatbot [class*="button"] {
      cursor: move !important;
    }

    /* Hover effect for the draggable circle */
    #draggable-chatbot:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    /* Ensure the chatbot stays within viewport */
    #draggable-chatbot {
      max-width: 100vw;
      max-height: 100vh;
    }
  </style>
  
  <!-- Reset Chatbot Conversation on Page Refresh -->
  <script>
    // Comprehensive chatbot reset with multiple approaches
    window.addEventListener('load', function() {
      // Method 1: Clear all storage data
      try {
        // Clear localStorage
        Object.keys(localStorage).forEach(key => {
          if (key.toLowerCase().includes('elfsight') || 
              key.toLowerCase().includes('chatbot') || 
              key.toLowerCase().includes('eapps') ||
              key.toLowerCase().includes('widget')) {
            localStorage.removeItem(key);
          }
        });
        
        // Clear sessionStorage
        Object.keys(sessionStorage).forEach(key => {
          if (key.toLowerCase().includes('elfsight') || 
              key.toLowerCase().includes('chatbot') || 
              key.toLowerCase().includes('eapps') ||
              key.toLowerCase().includes('widget')) {
            sessionStorage.removeItem(key);
          }
        });
        
        // Clear cookies
        document.cookie.split(';').forEach(function(c) {
          var eqPos = c.indexOf('=');
          var name = eqPos > -1 ? c.substr(0, eqPos).trim() : c.trim();
          if (name.toLowerCase().includes('elfsight') || 
              name.toLowerCase().includes('chatbot') ||
              name.toLowerCase().includes('eapps') ||
              name.toLowerCase().includes('widget')) {
            document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=' + window.location.hostname;
            document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/';
          }
        });
      } catch (e) {
        console.log('Storage clearing completed with some limitations');
      }
      
      // Method 2: Force complete widget reset
      setTimeout(function() {
        try {
          const chatbotElement = document.querySelector('.elfsight-app-96cc4395-da06-450f-9191-5bc6e30fa5f7');
          if (chatbotElement) {
            // Store parent for reinsertion
            const parent = chatbotElement.parentNode;
            const nextSibling = chatbotElement.nextSibling;
            
            // Completely remove the widget
            chatbotElement.remove();
            
            // Wait a moment then recreate
            setTimeout(function() {
              const newChatbot = document.createElement('div');
              newChatbot.className = 'elfsight-app-96cc4395-da06-450f-9191-5bc6e30fa5f7';
              newChatbot.setAttribute('data-elfsight-app-lazy', '');
              newChatbot.setAttribute('id', 'draggable-chatbot');
              
              // Insert back in original position
              if (nextSibling) {
                parent.insertBefore(newChatbot, nextSibling);
              } else {
                parent.appendChild(newChatbot);
              }
              
              // Force platform reinitialization
              setTimeout(function() {
                if (window.eapps) {
                  if (window.eapps.reinit) window.eapps.reinit();
                  if (window.eapps.refresh) window.eapps.refresh();
                  if (window.eapps.reload) window.eapps.reload();
                }
                if (window.ElfsightPlatform) {
                  if (window.ElfsightPlatform.reinit) window.ElfsightPlatform.reinit();
                  if (window.ElfsightPlatform.refresh) window.ElfsightPlatform.refresh();
                }
                console.log('Chatbot conversation reset - fresh start guaranteed');
              }, 500);
            }, 200);
          }
        } catch (e) {
          console.log('Widget reset completed with fallback method');
        }
      }, 800);
    });
  </script>

  <!-- Draggable Chatbot Circle Functionality -->
  <script>
    let isDragging = false;
    let currentX;
    let currentY;
    let initialX;
    let initialY;
    let xOffset = 0;
    let yOffset = 0;
    let chatbotElement;

    // Wait for chatbot to load and initialize dragging
    function initializeDraggableChatbot() {
      chatbotElement = document.getElementById('draggable-chatbot');
      
      if (!chatbotElement) {
        setTimeout(initializeDraggableChatbot, 1000);
        return;
      }

      // Add drag functionality to the chatbot circle
      chatbotElement.addEventListener('mousedown', dragStart);
      chatbotElement.addEventListener('touchstart', dragStart, { passive: false });
      
      document.addEventListener('mousemove', drag);
      document.addEventListener('touchmove', drag, { passive: false });
      
      document.addEventListener('mouseup', dragEnd);
      document.addEventListener('touchend', dragEnd);

      console.log('Draggable chatbot circle initialized!');
    }

    function dragStart(e) {
      // Only allow dragging when clicking on the chatbot circle (not when chat is open)
      const chatWindow = chatbotElement.querySelector('[class*="chat"], [class*="window"], [class*="content"]');
      if (chatWindow && chatWindow.offsetHeight > 100) {
        // Chat is open, don't drag
        return;
      }

      if (e.type === 'touchstart') {
        initialX = e.touches[0].clientX - xOffset;
        initialY = e.touches[0].clientY - yOffset;
      } else {
        initialX = e.clientX - xOffset;
        initialY = e.clientY - yOffset;
      }

      isDragging = true;
      chatbotElement.style.cursor = 'grabbing';
      e.preventDefault();
    }

    function drag(e) {
      if (isDragging) {
        e.preventDefault();
        
        if (e.type === 'touchmove') {
          currentX = e.touches[0].clientX - initialX;
          currentY = e.touches[0].clientY - initialY;
        } else {
          currentX = e.clientX - initialX;
          currentY = e.clientY - initialY;
        }

        xOffset = currentX;
        yOffset = currentY;

        // Keep chatbot within viewport bounds
        const rect = chatbotElement.getBoundingClientRect();
        const maxX = window.innerWidth - rect.width;
        const maxY = window.innerHeight - rect.height;
        
        currentX = Math.max(0, Math.min(currentX, maxX));
        currentY = Math.max(0, Math.min(currentY, maxY));

        setTranslate(currentX, currentY, chatbotElement);
      }
    }

    function dragEnd(e) {
      if (isDragging) {
        initialX = currentX;
        initialY = currentY;
        isDragging = false;
        chatbotElement.style.cursor = 'move';
        
        // Snap to edges if close (within 30px)
        const rect = chatbotElement.getBoundingClientRect();
        const snapDistance = 30;
        
        if (rect.left < snapDistance) {
          currentX = 0;
          xOffset = 0;
        }
        if (rect.right > window.innerWidth - snapDistance) {
          currentX = window.innerWidth - rect.width;
          xOffset = currentX;
        }
        if (rect.top < snapDistance) {
          currentY = 0;
          yOffset = 0;
        }
        if (rect.bottom > window.innerHeight - snapDistance) {
          currentY = window.innerHeight - rect.height;
          yOffset = currentY;
        }
        
        setTranslate(currentX, currentY, chatbotElement);
      }
    }

    function setTranslate(xPos, yPos, el) {
      el.style.transform = `translate3d(${xPos}px, ${yPos}px, 0)`;
    }

    // Handle window resize
    window.addEventListener('resize', function() {
      if (chatbotElement) {
        const rect = chatbotElement.getBoundingClientRect();
        
        // Ensure chatbot stays within new viewport
        if (rect.right > window.innerWidth) {
          currentX = window.innerWidth - rect.width;
          xOffset = currentX;
        }
        if (rect.bottom > window.innerHeight) {
          currentY = window.innerHeight - rect.height;
          yOffset = currentY;
        }
        
        setTranslate(currentX, currentY, chatbotElement);
      }
    });

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initializeDraggableChatbot);
    } else {
      initializeDraggableChatbot();
    }

    // Also try to initialize after a delay to ensure Elfsight widget is loaded
    setTimeout(initializeDraggableChatbot, 2000);
  </script>

<script>
function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    const profile = document.getElementById('userProfile');
    
    dropdown.classList.toggle('show');
    profile.classList.toggle('active');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const profile = document.getElementById('userProfile');
    const dropdown = document.getElementById('userDropdown');
    
    if (profile && !profile.contains(event.target)) {
        dropdown.classList.remove('show');
        profile.classList.remove('active');
    }
});

// Prevent dropdown from closing when clicking inside it
document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown) {
        dropdown.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
    
    // Handle contact link click
    document.getElementById('contactLink')?.addEventListener('click', function(e) {
        e.preventDefault();
        // Show contact popup
        const contactPopup = document.getElementById('contactPopup');
        if (contactPopup) {
            contactPopup.style.display = 'flex';
        }
    });
    
    // Handle contact popup close button
    const closeContact = document.getElementById('closeContact');
    const contactPopup = document.getElementById('contactPopup');
    
    if (closeContact && contactPopup) {
        closeContact.addEventListener('click', function() {
            contactPopup.style.display = 'none';
        });
        
        // Close popup when clicking outside
        contactPopup.addEventListener('click', function(e) {
            if (e.target === contactPopup) {
                contactPopup.style.display = 'none';
            }
        });
    }

    // Services Popup functionality
    const openServicesBtn = document.getElementById('openServicesPopup');
    const servicesPopup = document.getElementById('servicesPopup');
    const closeServicesBtn = document.getElementById('closeServices');

    if (openServicesBtn && servicesPopup && closeServicesBtn) {
        openServicesBtn.addEventListener('click', function() {
            servicesPopup.style.display = 'flex';
        });

        closeServicesBtn.addEventListener('click', function() {
            servicesPopup.style.display = 'none';
        });

        // Close popup when clicking outside
        servicesPopup.addEventListener('click', function(e) {
            if (e.target === servicesPopup) {
                servicesPopup.style.display = 'none';
            }
        });
    }
});
</script>

</body>
</html>

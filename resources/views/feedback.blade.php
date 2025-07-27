<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Feedback | Lemon Hub Studio</title>
 <link rel="stylesheet" href="{{ asset('css/style.css') }}" />


</head>
<body class="feedback-page">

  
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

 
  <main class="feedback-container">
   
    <div class="feedback-list">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Recent Feedbacks</h2>
        <div style="display: flex; gap: 10px;">
          <button id="refreshFeedbacks" style="background: #ffd700; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: bold;">
            🔄 Refresh
          </button>
          <button id="showFallback" style="background: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: bold;">
            📊 Show Database Data
          </button>
        </div>
      </div>
      <div id="feedbackEntries">
        <div style="text-align: center; padding: 40px; color: #666;">
          <div style="font-size: 2em; margin-bottom: 10px;">⏳</div>
          <p>Loading feedback from database...</p>
          <small>If this doesn't load, check browser console (F12)</small>
        </div>
      </div>
      
      <!-- Fallback: Direct PHP rendering if JavaScript fails -->
      <div id="fallbackFeedback" style="display: none;">
        <h3>Database Feedback (Fallback)</h3>
        @php
          $feedbacks = \App\Models\Feedback::latest()->take(5)->get();
        @endphp
        @if($feedbacks->count() > 0)
          @foreach($feedbacks as $feedback)
            <div style="border: 2px solid #ffd700; border-radius: 12px; padding: 20px; margin-bottom: 20px; background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);">
              <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                <h4 style="margin: 0; color: #333; font-size: 1.2em; font-weight: bold;">{{ $feedback->name }}</h4>
                <div style="text-align: right;">
                  <div style="font-size: 1.5em; color: #ffd700; margin-bottom: 5px;">{{ str_repeat('★', $feedback->rating) . str_repeat('☆', 5 - $feedback->rating) }}</div>
                  <small style="color: #666; font-size: 0.9em;">{{ $feedback->rating }}/5 stars</small>
                </div>
              </div>
              <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #ffd700;">
                <p style="margin: 0; color: #555; line-height: 1.6; font-style: italic;">"{{ $feedback->comment ?: $feedback->content }}"</p>
              </div>
              @if($feedback->photo)
                <div style="margin-top: 15px;">
                  <img src="{{ asset('storage/' . $feedback->photo) }}" 
                       style="width: 100%; max-width: 300px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" 
                       alt="Feedback photo" />
                </div>
              @endif
              <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                <small style="color: #888; font-size: 0.85em;">📅 {{ $feedback->created_at->format('M d, Y g:i A') }}</small>
                <small style="color: #28a745; font-weight: bold;">✅ From Database</small>
              </div>
            </div>
          @endforeach
        @else
          <p>No feedback found in database.</p>
        @endif
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

  <style>
    @keyframes slideIn {
      from {
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
    
    @keyframes slideOut {
      from {
        transform: translateX(0);
        opacity: 1;
      }
      to {
        transform: translateX(100%);
        opacity: 0;
      }
    }
  </style>
  <script>
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
  </script>
  <script>
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
      <div style="text-align: center; padding: 40px; color: #666;">
        <div style="font-size: 2em; margin-bottom: 10px;">⏳</div>
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
          <div style="text-align: center; padding: 40px; color: #666;">
            <div style="font-size: 2em; margin-bottom: 10px;">📝</div>
            <p>No feedback shared yet.</p>
            <small>Be the first to share your experience!</small>
          </div>
        `;
        return;
      }
    
    console.log('Loaded feedback data:', data.feedbacks);
    
    data.feedbacks.forEach(feedback => {
      const entry = document.createElement('div');
      entry.className = 'feedback-entry';
      entry.style.border = "2px solid #ffd700";
      entry.style.borderRadius = "12px";
      entry.style.padding = "20px";
      entry.style.marginBottom = "20px";
      entry.style.background = "linear-gradient(135deg, #fff 0%, #f8f9fa 100%)";
      entry.style.boxShadow = "0 4px 15px rgba(0,0,0,0.1)";
      entry.style.transition = "transform 0.2s ease";
      
      // Add hover effect
      entry.addEventListener('mouseenter', () => {
        entry.style.transform = "translateY(-2px)";
        entry.style.boxShadow = "0 6px 20px rgba(0,0,0,0.15)";
      });
      
      entry.addEventListener('mouseleave', () => {
        entry.style.transform = "translateY(0)";
        entry.style.boxShadow = "0 4px 15px rgba(0,0,0,0.1)";
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
          <div style="margin-top: 15px;">
            <img src="${feedback.photo_url}" 
                 style="width: 100%; max-width: 300px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); cursor: pointer;" 
                 onclick="openPhotoModal('${feedback.photo_url}')" 
                 alt="Feedback photo" />
          </div>
        `;
      }
      
      const userTypeIcon = feedback.user_type === 'Authenticated' ? '👤' : '👥';
      const userTypeColor = feedback.user_type === 'Authenticated' ? '#007bff' : '#6c757d';
      
      entry.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
          <div style="display: flex; align-items: center; gap: 10px;">
            <h4 style="margin: 0; color: #333; font-size: 1.2em; font-weight: bold;">${feedback.name || 'Anonymous'}</h4>
            <span style="background: ${userTypeColor}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.7em; font-weight: bold;">
              ${userTypeIcon} ${feedback.user_type}
            </span>
          </div>
          <div style="text-align: right;">
            <div style="font-size: 1.5em; color: ${starColor}; margin-bottom: 5px;">${stars}</div>
            <small style="color: #666; font-size: 0.9em;">${feedback.rating}/5 stars</small>
          </div>
        </div>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #ffd700;">
          <p style="margin: 0; color: #555; line-height: 1.6; font-style: italic;">"${feedback.comment || feedback.content || ''}"</p>
        </div>
        ${photoHtml}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
          <div style="display: flex; align-items: center; gap: 15px;">
            <small style="color: #888; font-size: 0.85em;">📅 ${formattedDate}</small>
            <small style="color: #6c757d; font-size: 0.85em;">🆔 ID: ${feedback.id}</small>
          </div>
          <div style="display: flex; align-items: center; gap: 10px;">
            <small style="color: #28a745; font-weight: bold;">✅ Saved to Database</small>
            <small style="color: #17a2b8; font-weight: bold;">📊 From phpMyAdmin</small>
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
      padding: 10px 15px;
      border-radius: 6px;
      font-size: 0.9em;
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
      <div style="text-align: center; padding: 40px; color: #dc3545;">
        <div style="font-size: 2em; margin-bottom: 10px;">❌</div>
        <p>Error loading feedback from database</p>
        <small>Please check your connection and try again</small>
        <br><br>
        <button onclick="loadFeedbacks()" style="background: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">
          🔄 Retry
        </button>
      </div>
    `;
  });
  }
  
  // Load feedback when page loads
  document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Page loaded, starting feedback load...');
    
    // Force immediate load
    setTimeout(() => {
      console.log('⏰ Forcing feedback load after timeout...');
      loadFeedbacks();
    }, 100);
    
    // Also try immediate load
    loadFeedbacks();
    
    // Add refresh button functionality
    const refreshBtn = document.getElementById('refreshFeedbacks');
    if (refreshBtn) {
      refreshBtn.addEventListener('click', function() {
        this.innerHTML = '🔄 Loading...';
        this.disabled = true;
        
        loadFeedbacks();
        
        setTimeout(() => {
          this.innerHTML = '🔄 Refresh';
          this.disabled = false;
        }, 2000);
      });
    }
    
    // Add fallback button functionality
    const fallbackBtn = document.getElementById('showFallback');
    if (fallbackBtn) {
      fallbackBtn.addEventListener('click', function() {
        const fallbackDiv = document.getElementById('fallbackFeedback');
        const entriesDiv = document.getElementById('feedbackEntries');
        
        if (fallbackDiv.style.display === 'none') {
          fallbackDiv.style.display = 'block';
          entriesDiv.style.display = 'none';
          this.innerHTML = '🔄 Back to JavaScript';
        } else {
          fallbackDiv.style.display = 'none';
          entriesDiv.style.display = 'block';
          this.innerHTML = '📊 Show Database Data';
          loadFeedbacks();
        }
      });
    }
    
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
    
    // Function to create feedback card
    function createFeedbackCard(feedback) {
      const card = document.createElement('div');
      card.className = 'feedback-entry';
      card.style.cssText = `
        border: 2px solid #ffd700;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
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
          <div style="margin-top: 15px;">
            <img src="${feedback.photo_url}" 
                 style="width: 100%; max-width: 300px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); cursor: pointer;" 
                 onclick="openPhotoModal('${feedback.photo_url}')" 
                 alt="Feedback photo" />
          </div>
        `;
      }
      
      const userTypeIcon = feedback.user_type === 'Authenticated' ? '👤' : '👥';
      const userTypeColor = feedback.user_type === 'Authenticated' ? '#007bff' : '#6c757d';
      
      card.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
          <div style="display: flex; align-items: center; gap: 10px;">
            <h4 style="margin: 0; color: #333; font-size: 1.2em; font-weight: bold;">${feedback.name}</h4>
            <span style="background: ${userTypeColor}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.7em; font-weight: bold;">
              ${userTypeIcon} ${feedback.user_type}
            </span>
          </div>
          <div style="text-align: right;">
            <div style="font-size: 1.5em; color: ${starColor}; margin-bottom: 5px;">${stars}</div>
            <small style="color: #666; font-size: 0.9em;">${feedback.rating}/5 stars</small>
          </div>
        </div>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #ffd700;">
          <p style="margin: 0; color: #555; line-height: 1.6; font-style: italic;">"${feedback.comment}"</p>
        </div>
        ${photoHtml}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
          <div style="display: flex; align-items: center; gap: 15px;">
            <small style="color: #888; font-size: 0.85em;">📅 ${formattedDate}</small>
            <small style="color: #6c757d; font-size: 0.85em;">🆔 ID: ${feedback.id}</small>
          </div>
          <div style="display: flex; align-items: center; gap: 10px;">
            <small style="color: #28a745; font-weight: bold;">✅ Just Submitted</small>
            <small style="color: #17a2b8; font-weight: bold;">📊 Saved to Database</small>
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
        padding: 10px 15px;
        border-radius: 6px;
        font-size: 0.9em;
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
  });
</script>

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

</body>
</html>
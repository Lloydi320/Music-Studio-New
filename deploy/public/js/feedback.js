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
  
  // Fetch actual feedback from database
  fetch('/api/feedbacks')
    .then(response => response.json())
    .then(data => {
      console.log('✅ Feedback loaded successfully:', data);
      
      if (!data.feedbacks || data.feedbacks.length === 0) {
        container.innerHTML = `
          <div style="text-align: center; padding: 40px; color: #666;">
            <div style="font-size: 3em; margin-bottom: 15px; opacity: 0.5;">📝</div>
            <h3 style="margin: 0 0 10px 0; color: #333;">No feedback yet</h3>
            <p style="margin: 0; font-size: 0.9em;">Be the first to share your experience!</p>
          </div>
        `;
        return;
      }
      
      container.innerHTML = '';
      data.feedbacks.forEach(feedback => {
        const card = createFeedbackCard(feedback);
        container.appendChild(card);
      });
    })
    .catch(error => {
      console.error('❌ Error loading feedback:', error);
      // Show the same "No feedback yet" message even on error
      container.innerHTML = `
        <div style="text-align: center; padding: 40px; color: #666;">
          <div style="font-size: 3em; margin-bottom: 15px; opacity: 0.5;">📝</div>
          <h3 style="margin: 0 0 10px 0; color: #333;">No feedback yet</h3>
          <p style="margin: 0; font-size: 0.9em;">Be the first to share your experience!</p>
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

// Initialize feedback system when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  console.log('🚀 Feedback system ready...');
  
  // Handle feedback form submission
  const feedbackForm = document.getElementById('feedbackForm');
  if (feedbackForm) {
    // Add rating star functionality
    const ratingStars = document.querySelectorAll('.rating-stars span');
    console.log('⭐ Rating stars found:', ratingStars.length);
    
    ratingStars.forEach((star, index) => {
      star.addEventListener('click', () => {
        selectedRating = index + 1;
        console.log('⭐ Rating selected:', selectedRating);
        updateStars();
      });
    });
    
    function updateStars() {
      console.log('🎨 Updating stars display for rating:', selectedRating);
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
      console.log('🚀 Form submission started...');
      
      const name = document.getElementById('name').value.trim();
      const comment = document.getElementById('comment').value.trim();
      const photo = document.getElementById('photo').files[0];
      
      console.log('📝 Form data:', { name, comment, selectedRating, hasPhoto: !!photo });
      
      if (!name || !comment || selectedRating === 0) {
        console.log('❌ Validation failed:', { name: !!name, comment: !!comment, selectedRating });
        alert('Please fill in all required fields and select a rating.');
        return;
      }
      
      console.log('✅ Validation passed, preparing submission...');
      
      // Create form data
      const formData = new FormData();
      formData.append('name', name);
      formData.append('rating', selectedRating);
      formData.append('comment', comment);
      if (photo) {
        formData.append('photo', photo);
      }
      
      console.log('📦 FormData prepared, making API request...');
      
      try {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log('🔐 CSRF token found:', token ? 'Yes' : 'No');
        
        const response = await fetch('/api/feedback', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token
          },
          body: formData
        });
        
        console.log('📡 API response status:', response.status, response.statusText);
        
        if (response.ok) {
          const result = await response.json();
          console.log('✅ Success response:', result);
          
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
          // Remove the "No feedback yet" message if it exists
          const noFeedbackMessage = container.querySelector('div[style*="text-align: center"]');
          if (noFeedbackMessage) {
            noFeedbackMessage.remove();
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
          console.log('❌ Error response:', errorData);
          alert('Error submitting feedback: ' + (errorData.message || 'Unknown error'));
        }
      } catch (error) {
        console.error('💥 Exception during submission:', error);
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
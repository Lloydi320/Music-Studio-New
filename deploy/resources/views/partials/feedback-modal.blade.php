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
          <div style="text-align: center; padding: 40px; color: #666;">
            <div style="font-size: 3em; margin-bottom: 15px; opacity: 0.5;">📝</div>
            <h3 style="margin: 0 0 10px 0; color: #333;">No feedback yet</h3>
            <p style="margin: 0; font-size: 0.9em;">Be the first to share your experience!</p>
          </div>
        </div>
      </div>
      <div class="feedback-form">
        <form id="feedbackForm">
          <div class="form-content">
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
          </div>
          
          <button type="submit" class="submit-btn">Submit Feedback</button>
        </form>
      </div>
    </div>
  </div>
</div>

console.log('*** THIS IS THE CORRECT JS FILE ***');

document.addEventListener("DOMContentLoaded", function () {
  console.log('Attaching calendar event listeners...');

  const calendarGrid = document.getElementById("calendarGrid");
  const timeSlots = document.getElementById("timeSlots");
  const monthYear = document.getElementById("monthYear");

  const now = new Date();
  let currentMonth = now.getMonth();
  let currentYear = now.getFullYear();

  const realMonth = now.getMonth();
  const realYear = now.getFullYear();
  const realDay = now.getDate();

  const dayNames = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];

  let bookedDates = [];

  async function fetchBookedDates(year, month) {
    try {
      const res = await fetch(`/api/booked-dates?year=${year}&month=${month}`);
      const data = await res.json();
      bookedDates = data.booked_dates || [];
    } catch (err) {
      bookedDates = [];
    }
  }

  if (calendarGrid && timeSlots && monthYear) {
    async function generateCalendar(year, month) {
      await fetchBookedDates(year, month);
      calendarGrid.innerHTML = "";

      dayNames.forEach(day => {
        const dayDiv = document.createElement("div");
        dayDiv.className = "day-name";
        dayDiv.textContent = day;
        calendarGrid.appendChild(dayDiv);
      });

      const firstDay = new Date(year, month, 1);
      const startDay = (firstDay.getDay() + 6) % 7;
      const daysInMonth = new Date(year, month + 1, 0).getDate();
      const monthString = (month + 1).toString().padStart(2, "0");

      for (let i = 0; i < startDay; i++) {
        calendarGrid.appendChild(document.createElement("div"));
      }

      for (let d = 1; d <= daysInMonth; d++) {
        const dateDiv = document.createElement("div");
        const dateKey = `${year}-${monthString}-${d.toString().padStart(2, "0")}`;
        dateDiv.textContent = d;

        if (bookedDates.includes(dateKey)) {
          dateDiv.classList.add("booked");
          dateDiv.title = "Booked";
        }

        const isPastDate =
          year < realYear ||
          (year === realYear && month < realMonth) ||
          (year === realYear && month === realMonth && d < realDay);

        if (isPastDate) {
          dateDiv.classList.add("disabled");
        } else {
          dateDiv.addEventListener("click", async () => {
            document.querySelectorAll(".calendar-grid div").forEach(el => el.classList.remove("selected"));
            dateDiv.classList.add("selected");
            console.log('Clicked dateKey:', dateKey);
            await showTimeSlots(dateKey);
          });
        }

        calendarGrid.appendChild(dateDiv);
      }

      monthYear.textContent = `${new Date(year, month).toLocaleString("default", { month: "long" })} ${year}`;
    }

    async function showTimeSlots(dateKey) {
      timeSlots.innerHTML = "";
      const heading = document.createElement("h4");
      heading.textContent = "Booking Info";
      timeSlots.appendChild(heading);

      try {
        const res = await fetch(`/api/bookings-by-date?date=${dateKey}`);
        const data = await res.json();
        console.log('API response for', dateKey, ':', data);
        if (data.bookings && data.bookings.length > 0) {
          data.bookings.forEach(booking => {
            const bookingDiv = document.createElement("div");
            bookingDiv.className = "booking-detail";
            bookingDiv.style.marginBottom = "16px";
            bookingDiv.style.padding = "10px";
            bookingDiv.style.borderBottom = "2.5px solid #333";
            bookingDiv.innerHTML = `
              <strong>Time Slot:</strong> ${booking.time_slot}<br>
              <strong>Status:</strong> ${booking.status}
            `;
            timeSlots.appendChild(bookingDiv);
          });
        } else {
          const message = document.createElement("p");
          message.textContent = "No bookings for this date.";
          timeSlots.appendChild(message);
        }
      } catch (err) {
        const message = document.createElement("p");
        message.textContent = "Failed to load booking info.";
        timeSlots.appendChild(message);
        console.error('Error fetching booking info for', dateKey, ':', err);
      }
    }

    document.getElementById("prevMonth").addEventListener("click", () => {
      if (
        currentYear > realYear ||
        (currentYear === realYear && currentMonth > realMonth)
      ) {
        if (currentMonth === 0) {
          currentMonth = 11;
          currentYear--;
        } else {
          currentMonth--;
        }
        generateCalendar(currentYear, currentMonth);
      }
    });

    document.getElementById("nextMonth").addEventListener("click", () => {
      if (currentMonth === 11) {
        currentMonth = 0;
        currentYear++;
      } else {
        currentMonth++;
      }
      generateCalendar(currentYear, currentMonth);
    });

    generateCalendar(currentYear, currentMonth);
  }
});



document.addEventListener("DOMContentLoaded", () => {
  const contactLink = document.getElementById("contactLink");
  const contactPopup = document.getElementById("contactPopup");
  const closeContact = document.getElementById("closeContact");

  if (contactLink && contactPopup && closeContact) {
    contactLink.addEventListener("click", (e) => {
      e.preventDefault();

     
      const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
      document.body.style.overflow = "hidden";
      document.body.style.paddingRight = `${scrollbarWidth}px`;

      contactPopup.classList.add("active");
    });

    closeContact.addEventListener("click", () => {
      contactPopup.classList.remove("active");
      document.body.style.overflow = "";
      document.body.style.paddingRight = "";
    });

    window.addEventListener("click", (e) => {
      if (e.target === contactPopup) {
        contactPopup.classList.remove("active");
        document.body.style.overflow = "";
        document.body.style.paddingRight = "";
      }
    });
  }
});



document.addEventListener("DOMContentLoaded", () => {
  const stars = document.querySelectorAll(".rating-stars span");
  let selectedRating = 0;

  stars.forEach(star => {
    star.addEventListener("click", () => {
      selectedRating = parseInt(star.dataset.value);
      updateStars();
    });
  });

  function updateStars() {
    stars.forEach(star => {
      star.classList.toggle("active", parseInt(star.dataset.value) <= selectedRating);
    });
  }


  const feedbackForm = document.getElementById("feedbackForm");
  const feedbackEntries = document.getElementById("feedbackEntries");

  feedbackForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const name = document.getElementById("name").value;
    const comment = document.getElementById("comment").value;
    const photo = document.getElementById("photo").files[0];

    // Validate required fields
    if (!name || !comment || selectedRating === 0) {
      alert("Please fill in all required fields and select a rating.");
      return;
    }

    // Create FormData for file upload
    const formData = new FormData();
    formData.append('name', name);
    formData.append('rating', selectedRating);
    formData.append('comment', comment);
    formData.append('content', comment); // Backend expects 'content' field
    if (photo) {
      formData.append('photo', photo);
    }

    try {
      // Send feedback to backend
      const response = await fetch('/api/feedback', {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          'Accept': 'application/json'
        }
      });

      if (response.ok) {
        const result = await response.json();
        
        // Create feedback card for display
        const feedbackCard = document.createElement("div");
        feedbackCard.style.border = "1px solid #ddd";
        feedbackCard.style.borderRadius = "8px";
        feedbackCard.style.padding = "15px";
        feedbackCard.style.marginBottom = "15px";
        feedbackCard.style.background = "#fff";

        feedbackCard.innerHTML = `
          <h4>${name}</h4>
          <p>Rating: ${'★'.repeat(selectedRating)}${'☆'.repeat(5 - selectedRating)}</p>
          <p>${comment}</p>
          <small style="color: #666;">✅ Saved to database</small>
        `;

        if (photo) {
          const img = document.createElement("img");
          img.src = URL.createObjectURL(photo);
          img.style.width = "100%";
          img.style.maxWidth = "300px";
          img.style.marginTop = "10px";
          img.style.borderRadius = "8px";
          img.style.boxShadow = "0 2px 8px rgba(0,0,0,0.1)";
          img.style.cursor = "pointer";
          
          // Add click to enlarge functionality
          img.addEventListener('click', () => {
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
            modalImg.src = img.src;
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
          });
          
          feedbackCard.appendChild(img);
        }

        // Remove placeholder
        const placeholder = feedbackEntries.querySelector(".placeholder");
        if (placeholder) placeholder.remove();

        // Add the new feedback to the display immediately
        feedbackEntries.appendChild(feedbackCard);
        feedbackForm.reset();
        selectedRating = 0;
        updateStars();
        
        // Show success message
        const successMessage = document.createElement('div');
        successMessage.style.cssText = `
          position: fixed;
          top: 20px;
          right: 20px;
          background: #28a745;
          color: white;
          padding: 15px 20px;
          border-radius: 8px;
          box-shadow: 0 4px 15px rgba(0,0,0,0.2);
          z-index: 1000;
          animation: slideIn 0.3s ease;
        `;
        successMessage.innerHTML = '✅ Feedback submitted successfully!';
        document.body.appendChild(successMessage);
        
        // Remove success message after 3 seconds
        setTimeout(() => {
          successMessage.style.animation = 'slideOut 0.3s ease';
          setTimeout(() => {
            if (successMessage.parentNode) {
              successMessage.parentNode.removeChild(successMessage);
            }
          }, 300);
        }, 3000);
      } else {
        const errorData = await response.json();
        alert("Error submitting feedback: " + (errorData.message || "Unknown error"));
      }
    } catch (error) {
      console.error("Error submitting feedback:", error);
      alert("Error submitting feedback. Please try again.");
    }
  });
});



document.addEventListener("DOMContentLoaded", () => {
  const feedbackLink = document.getElementById("feedbackLink");
  const feedbackPopup = document.getElementById("feedbackPopup");
  const closeFeedback = document.getElementById("closeFeedback");

  if (feedbackLink && feedbackPopup && closeFeedback) {
    feedbackLink.addEventListener("click", (e) => {
      e.preventDefault();
      const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
      document.body.style.overflow = "hidden";
      document.body.style.paddingRight = `${scrollbarWidth}px`;
      feedbackPopup.classList.add("active");
    });
    closeFeedback.addEventListener("click", () => {
      feedbackPopup.classList.remove("active");
      document.body.style.overflow = "";
      document.body.style.paddingRight = "";
    });
    feedbackPopup.addEventListener("click", (e) => {
      if (e.target === feedbackPopup) {
        feedbackPopup.classList.remove("active");
        document.body.style.overflow = "";
        document.body.style.paddingRight = "";
      }
    });
  }
});



console.log('script.js loaded');

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
          bookingDiv.innerHTML = `
            <strong>Time Slot:</strong> ${booking.time_slot}<br>
            <strong>Status:</strong> ${booking.status}<br>
            <strong>Reference:</strong> ${booking.reference}
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
  // Fetch and display feedback on page load
  fetchFeedback();

  const feedbackForm = document.getElementById("feedbackForm");
  const feedbackEntries = document.getElementById("feedbackEntries");
  let selectedRating = 0;

  // Rating stars logic
  const stars = document.querySelectorAll(".rating-stars span");
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

  feedbackForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const name = document.getElementById("name").value;
    const comment = document.getElementById("comment").value;
    const photo = document.getElementById("photo").files[0];

    if (!selectedRating) {
      alert("Please select a rating.");
      return;
    }

    const formData = new FormData();
    formData.append("name", name);
    formData.append("rating", selectedRating);
    formData.append("comment", comment);
    if (photo) formData.append("photo", photo);

    try {
      await fetch("/feedback", {
        method: "POST",
        body: formData
      });
      feedbackForm.reset();
      selectedRating = 0;
      updateStars();
      fetchFeedback();
    } catch (err) {
      alert("Failed to submit feedback.");
    }
  });

  async function fetchFeedback() {
    try {
      const res = await fetch("/api/feedback");
      const data = await res.json();
      console.log('Fetched feedback data:', data); // Debug output
      renderFeedback(data.feedbacks);
    } catch (err) {
      console.error('Error fetching feedback:', err); // Debug output
      feedbackEntries.innerHTML = '<p class="placeholder">Failed to load feedback.</p>';
    }
  }

  function renderFeedback(feedbacks) {
    feedbackEntries.innerHTML = "";
    if (!feedbacks.length) {
      feedbackEntries.innerHTML = '<p class="placeholder">No feedback shared yet.</p>';
      return;
    }
    feedbacks.forEach(fb => {
      const card = document.createElement("div");
      card.className = "feedback-card";
      card.innerHTML = `
        <h4>${fb.name || "Anonymous"}</h4>
        <p>Rating: ${'★'.repeat(fb.rating || 0)}${'☆'.repeat(5 - (fb.rating || 0))}</p>
        <p>${fb.comment}</p>
        ${fb.photo ? `<img src="/storage/${fb.photo}" style="max-width:200px;max-height:200px;margin-top:10px;border-radius:6px;" />` : ""}
        <small>${fb.created_at ? new Date(fb.created_at).toLocaleString() : ""}</small>
      `;
      feedbackEntries.appendChild(card);
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const stars = document.querySelectorAll('.rating-stars span');
  let selectedRating = 0;

  stars.forEach((star, index) => {
    star.addEventListener('click', () => {
      selectedRating = index + 1;
      updateStars(selectedRating);
    });

    star.addEventListener('mouseover', () => {
      updateStars(index + 1);
    });

    star.addEventListener('mouseout', () => {
      updateStars(selectedRating);
    });
  });

  function updateStars(rating) {
    stars.forEach((star, idx) => {
      if (idx < rating) {
        star.style.color = '#f7c400'; // filled color
      } else {
        star.style.color = '#ccc'; // unfilled
      }
    });
  }
});

// Place this in your frontend JS file or <script> tag
fetch('/api/my-feedback', {
  headers: {
    'Authorization': 'Bearer ' + localStorage.getItem('token') // if using token auth
  }
})
  .then(res => res.json())
  .then(data => {
    // Render feedbacks in your UI
    renderFeedbacks(data.feedbacks);
  });

document.addEventListener('DOMContentLoaded', function() {
  // Fetch and render feedbacks on page load
  fetchFeedbacks();

  // Handle feedback form submission
  document.getElementById('feedbackForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const name = document.getElementById('name').value;
    const comment = document.getElementById('comment').value;
    const rating = document.querySelector('.rating-stars .selected')?.getAttribute('data-value') || 0;
    const photoInput = document.getElementById('photo');
    const formData = new FormData();

    formData.append('name', name);
    formData.append('comment', comment);
    formData.append('rating', rating);
    formData.append('content', comment); // Assuming 'content' is the main feedback text
    if (photoInput.files[0]) {
      formData.append('photo', photoInput.files[0]);
    }

    fetch('/api/feedback', {
      method: 'POST',
      headers: {
        'Authorization': 'Bearer ' + localStorage.getItem('token'), // if using token auth
        'Accept': 'application/json'
      },
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      fetchFeedbacks(); // Refresh feedback list
      document.getElementById('feedbackForm').reset();
    });
  });

  // Handle star rating selection
  document.querySelectorAll('.rating-stars span').forEach(star => {
    star.addEventListener('click', function() {
      document.querySelectorAll('.rating-stars span').forEach(s => s.classList.remove('selected'));
      this.classList.add('selected');
    });
  });
});

function fetchFeedbacks() {
  fetch('/api/my-feedback', {
    headers: {
      'Authorization': 'Bearer ' + localStorage.getItem('token'), // if using token auth
      'Accept': 'application/json'
    }
  })
  .then(res => res.json())
  .then(data => {
    renderFeedbacks(data.feedbacks);
  });
}

document.addEventListener('DOMContentLoaded', function() {
  console.log('Fetching feedbacks...');
  fetch('/api/feedbacks', {
    headers: { 'Accept': 'application/json' }
  })
  .then(res => res.json())
  .then(data => {
    console.log('Feedbacks:', data);
  })
  .catch(err => {
    console.error('Error fetching feedbacks:', err);
  });
});

function renderFeedbacks(feedbacks) {
  const container = document.getElementById('feedbackEntries');
  container.innerHTML = '';
  if (!feedbacks || !feedbacks.length) {
    container.innerHTML = '<p class="placeholder">No feedback shared yet.</p>';
    return;
  }
  feedbacks.forEach(feedback => {
    const entry = document.createElement('div');
    entry.className = 'feedback-entry';
    entry.innerHTML = `
      <strong>${feedback.name || 'Anonymous'}</strong>
      <p>${feedback.comment || feedback.content}</p>
      <small>${new Date(feedback.created_at).toLocaleString()}</small>
    `;
    container.appendChild(entry);
  });
}


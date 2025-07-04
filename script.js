
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

if (calendarGrid && timeSlots && monthYear) {
  function generateCalendar(year, month) {
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

      const isPastDate =
        year < realYear ||
        (year === realYear && month < realMonth) ||
        (year === realYear && month === realMonth && d < realDay);

      if (isPastDate) {
        dateDiv.classList.add("disabled");
      } else {
        dateDiv.addEventListener("click", () => {
          document.querySelectorAll(".calendar-grid div").forEach(el => el.classList.remove("selected"));
          dateDiv.classList.add("selected");
          showTimeSlots(dateKey);
        });
      }

      calendarGrid.appendChild(dateDiv);
    }

    monthYear.textContent = `${new Date(year, month).toLocaleString("default", { month: "long" })} ${year}`;
  }

  function showTimeSlots(dateKey) {
    timeSlots.innerHTML = "";
    const heading = document.createElement("h4");
    heading.textContent = "Booking Info";
    timeSlots.appendChild(heading);
    const message = document.createElement("p");
    message.textContent = "No bookings for this date.";
    timeSlots.appendChild(message);
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

  feedbackForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const name = document.getElementById("name").value;
    const comment = document.getElementById("comment").value;
    const photo = document.getElementById("photo").files[0];

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
    `;

    if (photo) {
      const img = document.createElement("img");
      img.src = URL.createObjectURL(photo);
      img.style.width = "100%";
      img.style.marginTop = "10px";
      img.style.borderRadius = "6px";
      feedbackCard.appendChild(img);
    }

    // Remove placeholder
    const placeholder = feedbackEntries.querySelector(".placeholder");
    if (placeholder) placeholder.remove();

    feedbackEntries.appendChild(feedbackCard);
    feedbackForm.reset();
    selectedRating = 0;
    updateStars();
  });
});



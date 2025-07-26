document.addEventListener("DOMContentLoaded", function () {
  const calendar = document.getElementById("calendar");
  const selectedDateLabel = document.getElementById("selectedDateLabel");
  const monthDropdown = document.getElementById("monthDropdown");
  const slotsContainer = document.querySelector(".slots");
  const durationSelect = document.getElementById("durationSelect");
  const selectedDurationLabel = document.getElementById("selectedDurationLabel");
  const nextBtn = document.getElementById("nextBtn");
  const bookingForm = document.getElementById("bookingForm");

  let selectedDate = '';
  let selectedTimeSlot = '';
  let selectedDuration = 3;

  const today = new Date();
  const currentYear = today.getFullYear();
  const currentMonth = today.getMonth();

  const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
  ];

  // Populate month dropdown with current and future months
  for (let m = currentMonth; m < 12; m++) {
    const option = document.createElement("option");
    option.value = m;
    option.textContent = `${monthNames[m]} ${currentYear}`;
    monthDropdown.appendChild(option);
  }

  function renderCalendar(monthIndex) {
    calendar.innerHTML = "";
    const firstDayOfMonth = new Date(currentYear, monthIndex, 1).getDay();
    const daysInMonth = new Date(currentYear, monthIndex + 1, 0).getDate();
    const offset = firstDayOfMonth === 0 ? 6 : firstDayOfMonth - 1;

    // Add empty cells for offset
    for (let i = 0; i < offset; i++) {
      const empty = document.createElement("div");
      empty.classList.add("calendar-cell", "empty");
      calendar.appendChild(empty);
    }

    // Add day cells
    for (let day = 1; day <= daysInMonth; day++) {
      const cell = document.createElement("div");
      cell.classList.add("calendar-cell");
      cell.textContent = day;

      const date = new Date(currentYear, monthIndex, day);

      // Disable past dates
      if (date < today.setHours(0, 0, 0, 0)) {
        cell.classList.add("disabled");
      } else {
        cell.addEventListener("click", function () {
          // Remove previous selection
          document.querySelectorAll(".calendar-cell.selected").forEach(c => c.classList.remove("selected"));
          cell.classList.add("selected");

          const dateStr = `${monthNames[monthIndex]} ${day}, ${currentYear}`;
          const weekday = date.toLocaleDateString('en-US', { weekday: 'long' });

          selectedDate = dateStr;
          selectedDateLabel.textContent = `${weekday}, ${dateStr}`;

          generateTimeSlots(selectedDuration);
        });
      }

      calendar.appendChild(cell);
    }
  }

  function formatTime(date) {
    return date.toLocaleTimeString('en-US', {
      hour: '2-digit',
      minute: '2-digit',
      hour12: true
    });
  }

  function generateTimeSlots(durationHours) {
    slotsContainer.innerHTML = "";

    const openingHour = 8;
    const closingHour = 20; // 8 PM
    const durationMinutes = durationHours * 60;

    let start = new Date();
    start.setHours(openingHour, 0, 0, 0);

    const latestStart = new Date();
    latestStart.setHours(closingHour, 0, 0, 0);
    latestStart.setMinutes(latestStart.getMinutes() - durationMinutes);

    while (start <= latestStart) {
      const end = new Date(start.getTime() + durationMinutes * 60000);

      const btn = document.createElement("button");
      btn.textContent = `${formatTime(start)} - ${formatTime(end)}`;
      
      // Add click event for time slot selection
      btn.addEventListener("click", function() {
        document.querySelectorAll(".slots button").forEach(b => b.classList.remove("selected"));
        this.classList.add("selected");
        selectedTimeSlot = this.textContent;
      });
      
      slotsContainer.appendChild(btn);

      // increment by 30 mins
      start.setMinutes(start.getMinutes() + 30);
    }
  }

  // Duration change handler
  durationSelect.addEventListener("change", () => {
    selectedDuration = parseInt(durationSelect.value);
    selectedDurationLabel.textContent = `${selectedDuration} hr${selectedDuration > 1 ? 's' : ''}`;
    
    const selected = document.querySelector(".calendar-cell.selected");
    if (selected) {
      generateTimeSlots(selectedDuration);
    }
  });

  // Next button handler
  nextBtn.addEventListener("click", function() {
    if (selectedDate && selectedTimeSlot) {
      // Populate the booking form
      document.getElementById('bookingDate').value = selectedDate;
      document.getElementById('bookingTimeSlot').value = selectedTimeSlot;
      document.getElementById('bookingDuration').value = selectedDuration;
      
      document.getElementById('confirmDate').textContent = selectedDate;
      document.getElementById('confirmTimeSlot').textContent = selectedTimeSlot;
      document.getElementById('confirmDuration').textContent = `${selectedDuration} hour${selectedDuration > 1 ? 's' : ''}`;
      
      // Show the booking form
      bookingForm.style.display = 'flex';
      
      // Scroll to the form
      bookingForm.scrollIntoView({ behavior: 'smooth' });
    } else {
      alert('Please select a date and time slot first.');
    }
  });

  // Month dropdown change handler
  monthDropdown.addEventListener("change", function () {
    const selectedMonth = parseInt(this.value);
    renderCalendar(selectedMonth);
  });

  // Initialize calendar
  renderCalendar(currentMonth);
});

// Reapply calendar styles for dropdown
const reapplyCalendarStyles = () => {
  const dropdown = document.querySelector('.calendar select');
  if (dropdown) {
    dropdown.style.backgroundColor = '#f4c200';
    dropdown.style.border = '2px solid #cba700';
    dropdown.style.borderRadius = '6px';
    dropdown.style.padding = '8px 12px';
    dropdown.style.fontWeight = 'bold';
    dropdown.style.fontSize = '16px';
    dropdown.style.color = '#111';
  }
};

document.addEventListener('DOMContentLoaded', reapplyCalendarStyles);
document.addEventListener('change', function(e) {
  if (e.target.matches('.calendar select')) {
    reapplyCalendarStyles();
  }
});

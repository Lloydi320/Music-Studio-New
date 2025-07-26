document.addEventListener("DOMContentLoaded", function () {
  const calendar = document.getElementById("calendar");
  const selectedDateLabel = document.getElementById("selectedDateLabel");
  const timeSlots = document.getElementById("timeSlots");
  const monthDropdown = document.getElementById("monthDropdown");
  const slotsContainer = document.querySelector(".slots");
  const durationSelect = document.getElementById("durationSelect");
  const selectedDurationLabel = document.getElementById("selectedDurationLabel");

  const today = new Date();
  let selectedYear = today.getFullYear();
  let selectedMonth = today.getMonth();
  let selectedCell = null;
  let selectedTimeSlot = '';
  let selectedDuration = 3;

  const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
  ];
  const weekdayNames = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];

  // Add year and month navigation
  function renderMonthDropdown(year, month) {
    monthDropdown.innerHTML = "";
    for (let m = 0; m < 12; m++) {
      const option = document.createElement("option");
      option.value = m;
      option.textContent = `${monthNames[m]} ${year}`;
      if (m === month) option.selected = true;
      monthDropdown.appendChild(option);
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
    if (!slotsContainer) return;
    
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

  function renderCalendar(year, month) {
    calendar.innerHTML = "";
    // Add weekday headers
    for (let i = 0; i < 7; i++) {
      const header = document.createElement("div");
      header.classList.add("day-name");
      header.textContent = weekdayNames[i];
      calendar.appendChild(header);
    }
    // Calculate offset for first day
    const firstDayOfMonth = new Date(year, month, 1).getDay();
    // JS: 0=Sun, 1=Mon, ..., 6=Sat. We want Mon=0, Sun=6
    const offset = (firstDayOfMonth + 6) % 7;
    // Blank cells
    for (let i = 0; i < offset; i++) {
      const empty = document.createElement("div");
      empty.classList.add("empty");
      calendar.appendChild(empty);
    }
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    for (let day = 1; day <= daysInMonth; day++) {
      const cell = document.createElement("div");
      cell.classList.add("date-box");
      const span = document.createElement("span");
      span.textContent = day;
      cell.appendChild(span);
      const date = new Date(year, month, day);
      if (date < new Date(today.getFullYear(), today.getMonth(), today.getDate())) {
        cell.classList.add("disabled");
      } else {
        cell.addEventListener("click", function (e) {
          e.stopPropagation();
          if (cell.classList.contains("selected")) {
            // Single click on selected does nothing (keep selected)
          } else {
            document.querySelectorAll(".date-box.selected").forEach(c => c.classList.remove("selected"));
            cell.classList.add("selected");
            const dateStr = `${monthNames[month]} ${day}, ${year}`;
            const weekday = date.toLocaleDateString('en-US', { weekday: 'long' });
            selectedDateLabel.textContent = `${weekday}, ${dateStr}`;
            timeSlots.classList.remove("hidden");
            selectedCell = cell;

            // Generate time slots when date is selected
            generateTimeSlots(selectedDuration);

            const bookingInfoBox = document.querySelector('.booking-info-box');
            const slotButtons = document.querySelectorAll('.slots button');

            // Parse selected date to YYYY-MM-DD
            const pad = n => n.toString().padStart(2, '0');
            const dateStrISO = `${selectedYear}-${pad(selectedMonth+1)}-${pad(day)}`;

            fetch(`/api/bookings?date=${dateStrISO}`)
              .then(res => res.json())
              .then(bookings => {
                // Show booking info
                let info = '';
                if (bookings.length === 0) {
                  info = 'No bookings for this date.';
                } else {
                  info = 'Booked slots:<br>' + bookings.map(b => b.time_slot).join('<br>');
                }
                if (bookingInfoBox) bookingInfoBox.innerHTML = `<strong>Booking Info</strong><br>${info}`;
                // Disable booked slots
                slotButtons.forEach(btn => {
                  btn.disabled = bookings.some(b => b.time_slot === btn.textContent);
                  btn.style.opacity = btn.disabled ? 0.5 : 1;
                });
              });
          }
        });
        // Double-click to unselect
        cell.addEventListener("dblclick", function (e) {
          e.stopPropagation();
          if (cell.classList.contains("selected")) {
            cell.classList.remove("selected");
            selectedDateLabel.textContent = "";
            timeSlots.classList.add("hidden");
            selectedCell = null;
          }
        });
      }
      calendar.appendChild(cell);
    }
  }

  // Unselect when clicking outside the calendar
  document.addEventListener("click", function (e) {
    if (selectedCell && !calendar.contains(e.target)) {
      selectedCell.classList.remove("selected");
      selectedDateLabel.textContent = "";
      timeSlots.classList.add("hidden");
      selectedCell = null;
    }
  });

  // Duration change handler
  if (durationSelect) {
    durationSelect.addEventListener("change", () => {
      selectedDuration = parseInt(durationSelect.value);
      if (selectedDurationLabel) {
        selectedDurationLabel.textContent = `${selectedDuration} hr${selectedDuration > 1 ? 's' : ''}`;
      }
      
      const selected = document.querySelector(".date-box.selected");
      if (selected) {
        generateTimeSlots(selectedDuration);
      }
    });
  }

  // Month navigation buttons
  const calendarHeader = document.getElementById("calendar-header");
  const nextBtn = document.querySelector('.next-btn'); // Move declaration here, only once
  if (calendarHeader) {
    const prevBtn = document.getElementById("prevMonth");
    const nextMonthBtn = document.getElementById("nextMonth");
    prevBtn.addEventListener("click", function () {
      if (selectedMonth === 0) {
        selectedMonth = 11;
        selectedYear--;
      } else {
        selectedMonth--;
      }
      renderMonthDropdown(selectedYear, selectedMonth);
      renderCalendar(selectedYear, selectedMonth);
    });
  }
  if (nextBtn) {
    nextBtn.addEventListener('click', function() {
      console.log('Next button clicked!');    
      
      // Get selected date, time slot, and duration
      const selectedDate = document.getElementById('selectedDateLabel').textContent;
      const selectedSlotBtn = document.querySelector('.slots button.selected, .slots button[style*="background: #FFD700"]');
      let selectedTimeSlot = '';
      if (selectedSlotBtn) {
        selectedTimeSlot = selectedSlotBtn.textContent;
      } else {
        // fallback: get first enabled slot
        const enabledBtn = document.querySelector('.slots button:not([disabled])');
        if (enabledBtn) selectedTimeSlot = enabledBtn.textContent;
      }
      
      console.log('Selected date:', selectedDate);
      console.log('Selected time slot:', selectedTimeSlot);
      console.log('Duration select element:', durationSelect);
      
      if (!selectedDate || !selectedTimeSlot) {
        alert('Please select a date and time slot.');
        return;
      }
      // Parse date to YYYY-MM-DD
      const dateParts = selectedDate.match(/\w+, (\w+) (\d+), (\d+)/);
      if (!dateParts) return alert('Invalid date format.');
      const months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
      const month = (months.indexOf(dateParts[1]) + 1).toString().padStart(2, '0');
      const day = dateParts[2].padStart(2, '0');
      const year = dateParts[3];
      const dateISO = `${year}-${month}-${day}`;
      
      console.log('Date ISO:', dateISO);
      
      // Show booking summary in the booking form (right column)
      const bookingSummary = document.getElementById('bookingSummary');
      console.log('Booking summary element:', bookingSummary);
      console.log('Current display style:', bookingSummary ? bookingSummary.style.display : 'element not found');
      
      if (bookingSummary) {
        bookingSummary.style.display = 'block';
        bookingSummary.classList.add('show');
        console.log('Booking summary should now be visible in the form');
        console.log('New display style:', bookingSummary.style.display);
      } else {
        console.error('Booking summary element not found');
      }
      
      // Set form values
      document.getElementById('bookingDate').value = dateISO;
      document.getElementById('bookingTimeSlot').value = selectedTimeSlot;
      document.getElementById('bookingDuration').value = durationSelect.value;
      document.getElementById('confirmDate').textContent = selectedDate;
      document.getElementById('confirmTimeSlot').textContent = selectedTimeSlot;
      document.getElementById('confirmDuration').textContent = durationSelect.options[durationSelect.selectedIndex].text;
      
      // Hide Next button and show Confirm Booking button
      nextBtn.style.display = 'none';
      const bookingForm = document.getElementById('bookingForm');
      console.log('Booking form element:', bookingForm);
      if (bookingForm) {
        bookingForm.style.display = 'block';
        console.log('Booking form display set to:', bookingForm.style.display);
      }
    });
  }

  monthDropdown.addEventListener("change", function () {
    selectedMonth = parseInt(this.value);
    renderCalendar(selectedYear, selectedMonth);
  });

  // Initial render
  renderMonthDropdown(selectedYear, selectedMonth);
  renderCalendar(selectedYear, selectedMonth);
});

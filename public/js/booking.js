document.addEventListener("DOMContentLoaded", function () {
  const calendar = document.getElementById("calendar");
  const selectedDateLabel = document.getElementById("selectedDateLabel");
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
        
        // Update booking summary if it exists and user has made selections
        const bookingSummary = document.getElementById('bookingSummary');
        const confirmTimeSlot = document.getElementById('confirmTimeSlot');
        const confirmDuration = document.getElementById('confirmDuration');
        
        if (bookingSummary && confirmTimeSlot && selectedDateLabel.textContent) {
          bookingSummary.style.display = 'block';
          confirmTimeSlot.textContent = selectedTimeSlot;
          confirmDuration.textContent = durationSelect.options[durationSelect.selectedIndex].text;
        }
        
        // If booking form is visible (user clicked Next), revert back to Next button
        const bookingForm = document.getElementById('bookingForm');
        const nextBtn = document.querySelector('.next-btn');
        if (bookingForm && bookingForm.style.display !== 'none') {
          bookingForm.style.display = 'none';
          if (nextBtn) {
            nextBtn.style.display = 'block';
          }
        }
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
      cell.classList.add("calendar-cell");
      cell.textContent = day;
      const date = new Date(year, month, day);
      if (date < new Date(today.getFullYear(), today.getMonth(), today.getDate())) {
        cell.classList.add("disabled");
      } else {
        cell.addEventListener("click", function (e) {
          e.stopPropagation();
          if (cell.classList.contains("selected")) {
            // Single click on selected does nothing (keep selected)
          } else {
            document.querySelectorAll(".calendar-cell.selected").forEach(c => c.classList.remove("selected"));
            cell.classList.add("selected");
            const dateStr = `${monthNames[month]} ${day}, ${year}`;
            const weekday = date.toLocaleDateString('en-US', { weekday: 'long' });
            selectedDateLabel.textContent = `${weekday}, ${dateStr}`;
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
              
            // Update booking summary if it exists and user has made selections
            const bookingSummary = document.getElementById('bookingSummary');
            const confirmDate = document.getElementById('confirmDate');
            const confirmTimeSlot = document.getElementById('confirmTimeSlot');
            const confirmDuration = document.getElementById('confirmDuration');
            
            if (bookingSummary && confirmDate && selectedTimeSlot) {
              bookingSummary.style.display = 'block';
              confirmDate.textContent = `${weekday}, ${dateStr}`;
              confirmTimeSlot.textContent = selectedTimeSlot;
              confirmDuration.textContent = durationSelect.options[durationSelect.selectedIndex].text;
            }
              
            // If booking form is visible (user clicked Next), revert back to Next button
            const bookingForm = document.getElementById('bookingForm');
            const nextBtn = document.querySelector('.next-btn');
            if (bookingForm && bookingForm.style.display !== 'none') {
              bookingForm.style.display = 'none';
              if (nextBtn) {
                nextBtn.style.display = 'block';
              }
            }
          }
        });
        // Double-click to unselect
        cell.addEventListener("dblclick", function (e) {
          e.stopPropagation();
          if (cell.classList.contains("selected")) {
            cell.classList.remove("selected");
            selectedDateLabel.textContent = "";
            selectedCell = null;
          }
        });
      }
      calendar.appendChild(cell);
    }
  }

  // Unselect when clicking outside the calendar
  document.addEventListener("click", function (e) {
    if (selectedCell && !calendar.contains(e.target) && !e.target.closest('.slots') && !e.target.closest('.next-btn') && !e.target.closest('.book-btn')) {
      selectedCell.classList.remove("selected");
      selectedDateLabel.textContent = "";
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
      
      const selected = document.querySelector(".calendar-cell.selected");
      if (selected) {
        // Store the currently selected time slot
        const currentSelectedSlot = document.querySelector('.slots button.selected');
        const currentSelectedText = currentSelectedSlot ? currentSelectedSlot.textContent : '';
        
        generateTimeSlots(selectedDuration);
        
        // Restore the selection if the same time slot exists
        if (currentSelectedText) {
          const newSlotButtons = document.querySelectorAll('.slots button');
          newSlotButtons.forEach(btn => {
            if (btn.textContent === currentSelectedText) {
              btn.classList.add('selected');
              selectedTimeSlot = currentSelectedText;
            }
          });
        }
        
        // Update booking summary if it exists and user has made selections
        const bookingSummary = document.getElementById('bookingSummary');
        const confirmDuration = document.getElementById('confirmDuration');
        
        if (bookingSummary && confirmDuration && selectedDateLabel.textContent && selectedTimeSlot) {
          bookingSummary.style.display = 'block';
          confirmDuration.textContent = durationSelect.options[durationSelect.selectedIndex].text;
        }
        
        // If booking form is visible (user clicked Next), revert back to Next button
        const bookingForm = document.getElementById('bookingForm');
        const nextBtn = document.querySelector('.next-btn');
        if (bookingForm && bookingForm.style.display !== 'none') {
          bookingForm.style.display = 'none';
          if (nextBtn) {
            nextBtn.style.display = 'block';
          }
        }
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
      const bookingSummaryContent = document.getElementById('bookingSummaryContent');
      console.log('Booking summary element:', bookingSummary);
      
      if (bookingSummary && bookingSummaryContent) {
        // Populate the booking summary content
        bookingSummaryContent.innerHTML = `
          <strong>Date:</strong> <span id="confirmDate">${selectedDate}</span><br>
          <strong>Time Slot:</strong> <span id="confirmTimeSlot">${selectedTimeSlot}</span><br>
          <strong>Duration:</strong> <span id="confirmDuration">${durationSelect.options[durationSelect.selectedIndex].text}</span>
        `;
        
        // Remove empty class and add normal styling
        bookingSummary.classList.remove('empty');
        bookingSummary.classList.remove('confirmed');
        
        console.log('Booking summary should now be visible in the form');
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
      // BUT keep the date highlight and selected date text visible
      nextBtn.style.display = 'none';
      const bookingForm = document.getElementById('bookingForm');
      console.log('Booking form element:', bookingForm);
      if (bookingForm) {
        bookingForm.style.display = 'block';
        console.log('Booking form display set to:', bookingForm.style.display);
      }
      
      // Ensure the selected date stays highlighted and text remains visible
      // The date highlight and text should NOT be affected by clicking Next
      const selectedCell = document.querySelector('.calendar-cell.selected');
      console.log('Selected cell after Next click:', selectedCell);
      console.log('Selected cell classes:', selectedCell ? selectedCell.className : 'no cell found');
      console.log('Selected date label text:', document.getElementById('selectedDateLabel').textContent);
      
      // Force the date to stay highlighted if it exists
      if (selectedCell && !selectedCell.classList.contains('selected')) {
        selectedCell.classList.add('selected');
        console.log('Re-added selected class to date cell');
      }
      
      // Ensure the selected date text stays visible
      const selectedDateLabel = document.getElementById('selectedDateLabel');
      if (selectedDateLabel && selectedDateLabel.textContent) {
        console.log('Selected date label is visible with text:', selectedDateLabel.textContent);
        console.log('Selected date label display style:', selectedDateLabel.style.display);
        console.log('Selected date label visibility:', selectedDateLabel.style.visibility);
        
        // Force the text to stay visible
        selectedDateLabel.style.display = 'block';
        selectedDateLabel.style.visibility = 'visible';
        console.log('Forced selected date label to stay visible');
      } else {
        console.log('Selected date label is missing or empty');
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
  
  // Handle form submission
  const bookingForm = document.getElementById('bookingForm');
  if (bookingForm) {
    bookingForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Show confirmation message in booking summary
      const bookingSummary = document.getElementById('bookingSummary');
      const bookingSummaryContent = document.getElementById('bookingSummaryContent');
      const bookingConfirmationMessage = document.getElementById('bookingConfirmationMessage');
      
      if (bookingSummary && bookingSummaryContent && bookingConfirmationMessage) {
        // Hide booking summary content and show confirmation message
        bookingSummaryContent.style.display = 'none';
        bookingConfirmationMessage.style.display = 'block';
        
        // Add confirmed styling
        bookingSummary.classList.add('confirmed');
        bookingSummary.classList.remove('empty');
        
        // Hide the form buttons
        const nextBtn = document.querySelector('.next-btn');
        if (nextBtn) nextBtn.style.display = 'none';
        bookingForm.style.display = 'none';
      }
      
      // Submit the form data via AJAX to avoid page reload
      const formData = new FormData(bookingForm);
      fetch(bookingForm.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
      })
      .then(response => response.json())
      .then(data => {
        console.log('Booking submitted successfully:', data);
      })
      .catch(error => {
        console.error('Error submitting booking:', error);
      });
    });
  }
  
  // Initialize booking summary as empty
  const bookingSummary = document.getElementById('bookingSummary');
  if (bookingSummary) {
    bookingSummary.classList.add('empty');
    const bookingSummaryContent = document.getElementById('bookingSummaryContent');
    if (bookingSummaryContent) {
      bookingSummaryContent.innerHTML = 'Select a date and time to see booking details';
    }
  }
});

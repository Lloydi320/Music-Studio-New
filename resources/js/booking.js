document.addEventListener("DOMContentLoaded", function () {
  const calendar = document.getElementById("calendar");
  const selectedDateLabel = document.getElementById("selectedDateLabel");
  const monthDropdown = document.getElementById("monthDropdown");
  const yearDropdown = document.getElementById("yearDropdown");
  const slotsContainer = document.querySelector(".slots");
  const durationSelect = document.getElementById("durationSelect");
  const selectedDurationLabel = document.getElementById("selectedDurationLabel");


  const today = new Date();
  let selectedYear = today.getFullYear();
  let selectedMonth = today.getMonth();
  let selectedCell = null;
  let selectedTimeSlot = '';
  let selectedDuration = 1;

  const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
  ];
  const weekdayNames = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];

  function convertTo24H(timeStr) {
    const [time, modifier] = timeStr.trim().split(' ');
    let [hours, minutes] = time.split(':').map(Number);
    if (modifier === 'PM' && hours !== 12) hours += 12;
    if (modifier === 'AM' && hours === 12) hours = 0;
    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:00`;
  }

  function blockBookedSlots(bookings) {
    const slotButtons = document.querySelectorAll('.slots button');
    
    // First, make ALL slots available by default
    slotButtons.forEach(btn => {
      btn.disabled = false;
      btn.style.opacity = 1;
      btn.style.backgroundColor = '';
      btn.style.color = '';
      btn.style.cursor = '';
      btn.title = '';
    });
    
    // Then, only hide the specific slots that are actually booked (exact match only)
    slotButtons.forEach(btn => {
      // Extract start and end times from button (e.g., "10:00 AM - 01:00 PM")
      const btnTimeRange = btn.textContent.trim();
      // Hide the button if its time range exactly matches any booked slot's time range
      const isBooked = bookings.some(b => b.time_slot.trim() === btnTimeRange);
      if (isBooked) {
        btn.remove();
      }
    });
  }



  // Add year and month navigation
  function renderMonthDropdown(year, month) {
    // Populate month dropdown
    monthDropdown.innerHTML = "";
    for (let m = 0; m < 12; m++) {
      const option = document.createElement("option");
      option.value = m;
      option.textContent = monthNames[m];
      if (m === month) option.selected = true;
      monthDropdown.appendChild(option);
    }
  }

  function renderYearDropdown(currentYear) {
    // Populate year dropdown (current year + next 2 years)
    yearDropdown.innerHTML = "";
    for (let y = currentYear; y <= currentYear + 2; y++) {
      const option = document.createElement("option");
      option.value = y;
      option.textContent = y;
      if (y === currentYear) option.selected = true;
      yearDropdown.appendChild(option);
    }
  }

  function formatTime(date) {
    return date.toLocaleTimeString('en-US', {
      hour: '2-digit',
      minute: '2-digit',
      hour12: true
    });
  }

  function parseTimeRange(timeRange, date) {
    // timeRange: "08:00 AM - 11:00 AM"
    const [startStr, endStr] = timeRange.split('-').map(s => s.trim());
    const start = new Date(date);
    const end = new Date(date);
    // Parse times
    const [startHour, startMin, startPeriod] = startStr.match(/(\d+):(\d+) (\w+)/).slice(1);
    const [endHour, endMin, endPeriod] = endStr.match(/(\d+):(\d+) (\w+)/).slice(1);
    start.setHours((startPeriod === 'PM' && startHour !== '12' ? +startHour + 12 : +startHour), +startMin, 0, 0);
    end.setHours((endPeriod === 'PM' && endHour !== '12' ? +endHour + 12 : +endHour), +endMin, 0, 0);
    return [start, end];
  }

  function generateTimeSlots(durationHours, selectedDate = null, bookings = []) {
    if (!slotsContainer) return;
    
    slotsContainer.innerHTML = "";
    const openingHour = 8;
    const closingHour = 20; // 8 PM
    const durationMinutes = durationHours * 60;

    // Use selectedDate if provided, otherwise default to today
    let slotDate = selectedDate ? new Date(selectedDate) : new Date();
    slotDate.setHours(0, 0, 0, 0);

    let start = new Date(slotDate);
    start.setHours(openingHour, 0, 0, 0);

    const latestStart = new Date(slotDate);
    latestStart.setHours(closingHour, 0, 0, 0);
    latestStart.setMinutes(latestStart.getMinutes() - durationMinutes);

    const now = new Date();
    const isToday = slotDate.toDateString() === now.toDateString();

    let slotAvailable = false;
    while (start <= latestStart) {
      // If today, hide slots that start in the past
      if (!(isToday && start < now)) {
        const end = new Date(start.getTime() + durationMinutes * 60000);
        const slotLabel = `${formatTime(start)} - ${formatTime(end)}`;
        // Check for overlap with any booking
        const overlaps = bookings.some(b => {
          const [bookedStart, bookedEnd] = parseTimeRange(b.time_slot, slotDate);
          return start < bookedEnd && end > bookedStart;
        });
        if (!overlaps) {
          const btn = document.createElement("button");
          btn.textContent = slotLabel;
          btn.addEventListener("click", function() {
            if (!this.disabled) {
              document.querySelectorAll(".slots button").forEach(b => b.classList.remove("selected"));
              this.classList.add("selected");
              selectedTimeSlot = this.textContent;
              const bookingSummary = document.getElementById('bookingSummary');
              const confirmTimeSlot = document.getElementById('confirmTimeSlot');
              const confirmDuration = document.getElementById('confirmDuration');
              if (bookingSummary && confirmTimeSlot && selectedDateLabel.textContent) {
                bookingSummary.style.display = 'block';
                confirmTimeSlot.textContent = selectedTimeSlot;
                confirmDuration.textContent = durationSelect.options[durationSelect.selectedIndex].text;
              }
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
          slotsContainer.appendChild(btn);
          slotAvailable = true;
        }
      }
      // Increment by 30 minutes instead of 1 hour
      start.setMinutes(start.getMinutes() + 30);
    }
    if (!slotAvailable) {
      const note = document.createElement('div');
      note.style.margin = '16px 0';
      note.style.textAlign = 'center';
      note.style.color = '#888';
      note.textContent = 'No available time slots for the selected date and duration.';
      slotsContainer.appendChild(note);
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
    
    // Fetch booked dates to disable unavailable dates
    fetch('/api/booked-dates')
      .then(response => response.json())
      .then(data => {
        let bookedDates = [];
        
        // Handle the API response format
        if (data.booked_dates) {
          // Convert object to array of values
          if (typeof data.booked_dates === 'object' && !Array.isArray(data.booked_dates)) {
            bookedDates = Object.values(data.booked_dates);
          } else {
            bookedDates = data.booked_dates;
          }
        } else if (Array.isArray(data)) {
          bookedDates = data;
        } else {
          bookedDates = [];
        }
        
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        for (let day = 1; day <= daysInMonth; day++) {
          const cell = document.createElement("div");
          cell.classList.add("calendar-cell");
          cell.textContent = day;
          const date = new Date(year, month, day);
          
          // Format date as YYYY-MM-DD for comparison
          const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
          
          if (date < new Date(today.getFullYear(), today.getMonth(), today.getDate()) || (Array.isArray(bookedDates) && bookedDates.includes(dateStr))) {
          cell.classList.add('disabled');
          if (Array.isArray(bookedDates) && bookedDates.includes(dateStr)) {
              cell.title = "Studio unavailable (drum/full package rental or existing booking)";
            }
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
                const selectedDateObj = new Date(year, month, day);
                generateTimeSlots(selectedDuration, selectedDateObj);

                // Parse selected date to YYYY-MM-DD
                const pad = n => n.toString().padStart(2, '0');
                const dateStrISO = `${selectedYear}-${pad(selectedMonth+1)}-${pad(day)}`;

                // Fetch bookings and block overlapping slots
                fetch(`/api/bookings?date=${dateStrISO}`)
                  .then(res => res.json())
                  .then(bookings => {
                    // Show booking info
                    const bookingInfoBox = document.querySelector('.booking-info-box');
                    let info = '';
                    if (bookings.length === 0) {
                      info = 'No bookings for this date.';
                    } else {
                      info = 'Booked slots:<br>' + bookings.map(b => b.time_slot).join('<br>');
                    }
                    if (bookingInfoBox) bookingInfoBox.innerHTML = `<strong>Booking Info</strong><br>${info}`;
                    // Generate time slots and hide booked ones
                    generateTimeSlots(selectedDuration, selectedDateObj, bookings);
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
      })
      .catch(error => {
        console.error('Error fetching booked dates:', error);
        // Fallback: render calendar without booked date checking
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        for (let day = 1; day <= daysInMonth; day++) {
          const cell = document.createElement("div");
          cell.classList.add("calendar-cell");
          cell.textContent = day;
          const date = new Date(year, month, day);
          if (date < new Date(today.getFullYear(), today.getMonth(), today.getDate())) {
            cell.classList.add("disabled");
          } else {
            // Add click handlers for non-disabled dates
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
              }
            });
          }
          calendar.appendChild(cell);
        }
      });
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
        
        // Get the selected date to check bookings
        const selectedDateText = document.getElementById('selectedDateLabel').textContent;
        if (selectedDateText) {
          // Parse selected date to YYYY-MM-DD
          const dateParts = selectedDateText.match(/\w+, (\w+) (\d+), (\d+)/);
          if (dateParts) {
            const months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
            const month = (months.indexOf(dateParts[1]) + 1).toString().padStart(2, '0');
            const day = dateParts[2].padStart(2, '0');
            const year = dateParts[3];
            const dateStrISO = `${year}-${month}-${day}`;
            
            // Check bookings for this date and disable overlapping slots
            fetch(`/api/bookings?date=${dateStrISO}`)
              .then(res => res.json())
              .then(bookings => {
                // Block overlapping slots
                blockBookedSlots(bookings);
                
                // Restore the selection if the same time slot exists and is not disabled
                if (currentSelectedText) {
                  const newSlotButtons = document.querySelectorAll('.slots button');
                  newSlotButtons.forEach(btn => {
                    if (btn.textContent === currentSelectedText && !btn.disabled) {
                      btn.classList.add('selected');
                      selectedTimeSlot = currentSelectedText;
                    }
                  });
                }
              });
          }
        }
        
        // Update booking summary if it exists and user has made selections
        const bookingSummary = document.getElementById('bookingSummary');
        const confirmDuration = document.getElementById('confirmDuration');
        
        if (bookingSummary && confirmDuration && selectedDateLabel.textContent && selectedTimeSlot) {
          bookingSummary.classList.remove('empty');
          const bookingSummaryContent = document.getElementById('bookingSummaryContent');
          if (bookingSummaryContent) {
            bookingSummaryContent.innerHTML = `
              <strong>Date:</strong> <span id="confirmDate">${selectedDateLabel.textContent}</span><br>
              <strong>Time Slot:</strong> <span id="confirmTimeSlot">${selectedTimeSlot}</span><br>
              <strong>Duration:</strong> <span id="confirmDuration">${durationSelect.options[durationSelect.selectedIndex].text}</span>
            `;
          }
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
      renderYearDropdown(selectedYear);
      renderCalendar(selectedYear, selectedMonth);
    });
    
    if (nextMonthBtn) {
      nextMonthBtn.addEventListener("click", function () {
        if (selectedMonth === 11) {
          selectedMonth = 0;
          selectedYear++;
        } else {
          selectedMonth++;
        }
        renderMonthDropdown(selectedYear, selectedMonth);
        renderYearDropdown(selectedYear);
        renderCalendar(selectedYear, selectedMonth);
      });
    }
  }
  if (nextBtn) {
    nextBtn.addEventListener('click', function() {
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
      
      // Show the studio rental modal
      const modal = document.getElementById('studioRentalModal');
      if (modal) {
        modal.style.display = 'block';
        
        // Populate modal with booking details
        document.getElementById('modalSelectedDate').textContent = selectedDate;
        document.getElementById('modalSelectedTime').textContent = selectedTimeSlot;
        document.getElementById('modalSelectedDuration').textContent = durationSelect.options[durationSelect.selectedIndex].text;
        document.getElementById('modalDurationLabel').textContent = durationSelect.options[durationSelect.selectedIndex].text;
        
        // Calculate and display price (₱100 per hour)
        const duration = parseInt(durationSelect.value);
        const totalPrice = duration * 100;
        document.getElementById('modalTotalPrice').textContent = `₱${totalPrice}.00`;
        document.querySelector('.gcash-amount').textContent = `₱ ${totalPrice}.00`;
        
        // Set hidden form values
        document.getElementById('modalBookingDate').value = dateISO;
        document.getElementById('modalBookingTimeSlot').value = selectedTimeSlot;
        document.getElementById('modalBookingDuration').value = durationSelect.value;
        document.getElementById('modalBookingPrice').value = totalPrice;
      }
    });
  }

  // Modal close functionality
  const modal = document.getElementById('studioRentalModal');
  const cancelBtn = document.getElementById('cancelModal');
  
  if (cancelBtn) {
    cancelBtn.addEventListener('click', function() {
      modal.style.display = 'none';
    });
  }
  
  // Close modal when clicking outside of it
  if (modal) {
    modal.addEventListener('click', function(event) {
      if (event.target === modal) {
        modal.style.display = 'none';
      }
    });
  }

  monthDropdown.addEventListener("change", function () {
    selectedMonth = parseInt(this.value);
    renderCalendar(selectedYear, selectedMonth);
  });

  // Year dropdown event listener
  yearDropdown.addEventListener("change", function () {
    selectedYear = parseInt(this.value);
    renderCalendar(selectedYear, selectedMonth);
  });

  // Initial render
  renderMonthDropdown(selectedYear, selectedMonth);
  renderYearDropdown(selectedYear);
  renderCalendar(selectedYear, selectedMonth);
  

  


  // Handle form submission via AJAX to show modal
  const bookingForm = document.getElementById('bookingForm');
  const studioRentalForm = document.getElementById('studioRentalForm');
  
  function handleFormSubmission(form, e) {
    e.preventDefault();
    
    // Check reference code validation for both booking and studio rental forms
    if (form.id === 'studioRentalForm' || form.id === 'bookingForm') {
      const referenceCodeInput = document.getElementById('referenceCode');
      
      if (referenceCodeInput) {
        const validationState = referenceCodeInput.dataset.valid;
        const referenceValue = referenceCodeInput.value.trim();
        
        // Check if reference code is empty - show warning but allow submission
        if (!referenceValue || referenceValue.length !== 4) {
          showReferenceWarning('Please enter a valid 4-digit GCash reference number.', form.id);
        }
        
        // Check if validation failed (duplicate reference) - allow submission but show warning
        if (validationState === 'false') {
          // Show warning message but allow form submission to proceed
          showErrorModal('Reference number already exists.');
        }
        
        // Check if validation is still in progress or unknown - show warning but allow submission
        if (validationState === undefined || validationState === 'unknown') {
          showReferenceWarning('Please wait for reference code validation to complete or try again.', form.id);
        }
        
        // Check if validation hasn't been performed yet (no dataset.valid set) - show warning but allow submission
        if (validationState !== 'true' && validationState !== 'false') {
          showReferenceWarning('Please wait for reference code validation to complete.', form.id);
        }
      }
    }
    
    const formData = new FormData(form);
    
    // Add AJAX header
    fetch(form.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        showSuccessModal(data.message);
      } else {
        showReferenceWarning('Error: ' + data.message, form.id);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showReferenceWarning('An error occurred while processing your booking. Please try again.', form.id);
    });
  }
  
  if (bookingForm) {
    bookingForm.addEventListener('submit', function(e) {
      handleFormSubmission(bookingForm, e);
    });
  }
  
  if (studioRentalForm) {
    studioRentalForm.addEventListener('submit', function(e) {
      handleFormSubmission(studioRentalForm, e);
    });
  }
  
  // Function to show success modal with countdown and redirect to home
  function showSuccessModal(message) {
    const modal = document.getElementById('successModal');
    const messageDiv = document.getElementById('successMessage');
    const countdownSpan = document.getElementById('countdown');
    
    if (modal && messageDiv && countdownSpan) {
      messageDiv.innerHTML = message;
      modal.style.display = 'block';
      
      let countdown = 5;
      countdownSpan.textContent = countdown;
      
      const countdownInterval = setInterval(() => {
        countdown--;
        countdownSpan.textContent = countdown;
        
        if (countdown <= 0) {
          clearInterval(countdownInterval);
          window.location.href = '/';
        }
      }, 1000);
    }
  }
  
  // Function to show inline error for reference field
  function showErrorModal(message) {
    const referenceField = document.getElementById('referenceCode');
    const errorMessage = document.getElementById('referenceErrorMessage');
    const errorText = document.getElementById('referenceErrorText');
    
    if (referenceField && errorMessage && errorText) {
      // Add error styling to the field
      referenceField.classList.add('error-field');
      
      // Show error message
      errorText.textContent = message;
      errorMessage.style.display = 'block';
      
      // Auto-hide error after 5 seconds
      setTimeout(() => {
        hideReferenceError();
      }, 5000);
    }
  }
  
  // Function to hide reference field error
  function hideReferenceError() {
    const referenceField = document.getElementById('referenceCode');
    const errorMessage = document.getElementById('referenceErrorMessage');
    
    if (referenceField) {
      referenceField.classList.remove('error-field');
    }
    
    if (errorMessage) {
      errorMessage.style.display = 'none';
    }
  }
  
  // Clear error when user starts typing in reference field
  const referenceField = document.getElementById('referenceCode');
  if (referenceField) {
    referenceField.addEventListener('input', hideReferenceError);
  }
  
  // Initialize booking summary as empty
  const bookingSummary = document.getElementById('bookingSummary');
  if (bookingSummary) {
    const bookingSummaryContent = document.getElementById('bookingSummaryContent');
    if (bookingSummaryContent) {
      bookingSummaryContent.innerHTML = 'Select a date and time to see booking details';
    }
  }
  
  // Auto-refresh functionality to update UI when bookings change
  let autoRefreshInterval;
  
  function startAutoRefresh() {
    // Clear any existing interval
    if (autoRefreshInterval) {
      clearInterval(autoRefreshInterval);
    }
    
    // Refresh every 10 seconds
    autoRefreshInterval = setInterval(() => {
      // Only refresh if a date is selected
      if (selectedDate) {
        fetchBookings(selectedDate);
      }
    }, 10000); // 10 seconds
  }
  
  function stopAutoRefresh() {
    if (autoRefreshInterval) {
      clearInterval(autoRefreshInterval);
      autoRefreshInterval = null;
    }
  }
  
  // Start auto-refresh when page loads
  startAutoRefresh();
  
  // Stop auto-refresh when page is hidden (to save resources)
  document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
      stopAutoRefresh();
    } else {
      startAutoRefresh();
    }
  });
  
  // Also refresh immediately after a successful booking submission
  const originalFetchBookings = fetchBookings;
  fetchBookings = function(date) {
    originalFetchBookings.call(this, date);
    
    // After fetching, restart the auto-refresh timer
    setTimeout(() => {
      if (!autoRefreshInterval) {
        startAutoRefresh();
      }
    }, 1000);
  };
});

// Global functions for error modal (accessible from HTML onclick)

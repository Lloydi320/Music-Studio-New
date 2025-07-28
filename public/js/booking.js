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

  function convertTo24H(timeStr) {
    const [time, modifier] = timeStr.trim().split(' ');
    let [hours, minutes] = time.split(':').map(Number);
    if (modifier === 'PM' && hours !== 12) hours += 12;
    if (modifier === 'AM' && hours === 12) hours = 0;
    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:00`;
  }

  function blockBookedSlots(bookings) {
    console.log('=== blockBookedSlots called ===');
    console.log('Bookings from database:', bookings);
    console.log('Number of bookings:', bookings.length);
    
    const slotButtons = document.querySelectorAll('.slots button');
    console.log('Found slot buttons:', slotButtons.length);
    console.log('Slot button texts:', Array.from(slotButtons).map(btn => btn.textContent));
    
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
        // Hide if this slot is already booked (exact match)
        const isBooked = bookings.some(b => b.time_slot.trim() === slotLabel);
        if (!isBooked) {
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
      start.setHours(start.getHours() + 1);
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
      
      // Log the form data being sent to backend
      console.log('Form data being sent to backend:');
      console.log('- Date:', dateISO);
      console.log('- Time Slot:', selectedTimeSlot);
      console.log('- Duration:', durationSelect.value);
      console.log('- CSRF Token:', document.querySelector('input[name="_token"]').value);
      
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
      
      // Debug: Log form data
      console.log('Submitting booking form...');
      console.log('Form action:', bookingForm.action);
      console.log('Date:', formData.get('date'));
      console.log('Time slot:', formData.get('time_slot'));
      console.log('Duration:', formData.get('duration'));
      
      fetch(bookingForm.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
      })
      .then(response => {
        console.log('Response status:', response.status);
        console.log('Response redirected:', response.redirected);
        console.log('Response ok:', response.ok);
        
        if (response.redirected) {
          // If there's a redirect, follow it
          console.log('Following redirect to:', response.url);
          window.location.href = response.url;
        } else if (response.ok) {
          // If successful, refresh the booking data and show success message
          console.log('Booking submitted successfully, refreshing data...');
          
          // Refresh the booking data immediately
          if (selectedDate) {
            fetchBookings(selectedDate);
          }
          
          // Show success message
          alert('Booking created successfully! The time slot is now blocked for other users.');
        } else {
          // If there's an error, handle it
          return response.text().then(text => {
            console.error('Booking submission error:', text);
            
            // Try to extract error message from response
            let errorMessage = 'There was an error creating your booking. Please try again.';
            try {
              // Check if response contains validation errors
              if (text.includes('validation')) {
                errorMessage = 'Please check your booking details and try again.';
              } else if (text.includes('overlaps')) {
                errorMessage = 'This time slot overlaps with an existing booking. Please choose a different time.';
              } else if (text.includes('unauthenticated')) {
                errorMessage = 'Please log in to make a booking.';
              }
            } catch (e) {
              console.log('Could not parse error response');
            }
            
            alert(errorMessage);
          });
        }
      })
      .catch(error => {
        console.error('Error submitting booking:', error);
        alert('There was an error creating your booking. Please try again.');
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
  
  // Auto-refresh functionality to update UI when bookings change
  let autoRefreshInterval;
  
  function startAutoRefresh() {
    // Clear any existing interval
    if (autoRefreshInterval) {
      clearInterval(autoRefreshInterval);
    }
    
    // Refresh every 10 seconds
    autoRefreshInterval = setInterval(() => {
      console.log('Auto-refreshing booking data...');
      
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

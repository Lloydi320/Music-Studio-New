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

  // Simple function to check if two time slots overlap
  function timeSlotsOverlap(slot1, slot2) {
    const parseTime = (timeStr) => {
      const [time, period] = timeStr.split(' ');
      let [hours, minutes] = time.split(':').map(Number);
      if (period === 'PM' && hours !== 12) hours += 12;
      if (period === 'AM' && hours === 12) hours = 0;
      return hours * 60 + minutes;
    };
    
    const [start1, end1] = slot1.split(' - ');
    const [start2, end2] = slot2.split(' - ');
    
    const start1Minutes = parseTime(start1);
    const end1Minutes = parseTime(end1);
    const start2Minutes = parseTime(start2);
    const end2Minutes = parseTime(end2);
    
    return start1Minutes < end2Minutes && end1Minutes > start2Minutes;
  }

  // Generate time slots based on selected duration
  function generateTimeSlots(duration) {
    const slotsContainer = document.querySelector('.slots');
    if (!slotsContainer) return;
    
    slotsContainer.innerHTML = '';
    
    const startTime = 8; // 8 AM
    const endTime = 22; // 10 PM
    const slotDuration = duration;
    
    for (let hour = startTime; hour <= endTime - slotDuration; hour++) {
      for (let minute = 0; minute < 60; minute += 30) {
        const startHour = hour + Math.floor(minute / 60);
        const startMinute = minute % 60;
        
        const endHour = startHour + slotDuration;
        const endMinute = startMinute;
        
        if (endHour <= endTime) {
          const startTimeStr = formatTime(startHour, startMinute);
          const endTimeStr = formatTime(endHour, endMinute);
          const timeSlot = `${startTimeStr} - ${endTimeStr}`;
          
          const button = document.createElement('button');
          button.textContent = timeSlot;
          button.type = 'button';
          
          button.addEventListener('click', function() {
            // Remove selected class from all buttons
            document.querySelectorAll('.slots button').forEach(btn => {
              btn.classList.remove('selected');
            });
            
            // Add selected class to clicked button
            this.classList.add('selected');
            selectedTimeSlot = this.textContent;
            
            // Update booking summary content
            if (selectedDate && selectedTimeSlot) {
              const dateParts = selectedDate.split(' ');
              const day = dateParts[0];
              const month = dateParts[1];
              const year = dateParts[3];
              const dateISO = `${year}-${month}-${day}`;
              
              const bookingSummaryContent = document.getElementById('bookingSummaryContent');
              if (bookingSummaryContent) {
                bookingSummaryContent.innerHTML = `
                  <div><strong>Date:</strong> ${selectedDate}</div>
                  <div><strong>Time:</strong> ${selectedTimeSlot}</div>
                  <div><strong>Duration:</strong> ${durationSelect.value}</div>
                `;
              }
              
              const bookingSummary = document.getElementById('bookingSummary');
              if (bookingSummary) {
                bookingSummary.style.display = 'block';
              }
            }
            
            // Revert to "Next" button if booking form was visible
            const bookingForm = document.getElementById('bookingForm');
            if (bookingForm && bookingForm.style.display === 'block') {
              bookingForm.style.display = 'none';
              const nextBtn = document.querySelector('.next-btn');
              if (nextBtn) {
                nextBtn.style.display = 'block';
              }
            }
          });
          
          slotsContainer.appendChild(button);
        }
      }
    }
  }

  // Format time to 12-hour format
  function formatTime(hour, minute) {
    const period = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour === 0 ? 12 : hour > 12 ? hour - 12 : hour;
    return `${displayHour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')} ${period}`;
  }

  // Initialize time slots on page load
  if (durationSelect) {
    generateTimeSlots(parseInt(durationSelect.value));
  }

  // Handle duration change
  if (durationSelect) {
    durationSelect.addEventListener('change', () => {
      const currentSelectedText = selectedTimeSlot;
      generateTimeSlots(parseInt(durationSelect.value));
      
              // Check bookings for this date and disable booked slots
        if (selectedDate) {
          const dateParts = selectedDate.split(' ');
          const day = dateParts[0];
          const month = dateParts[1];
          const year = dateParts[3];
          const dateStrISO = `${year}-${month}-${day}`;
          
          fetch(`/api/bookings?date=${dateStrISO}`)
          .then(res => res.json())
          .then(bookings => {
            const slotButtons = document.querySelectorAll('.slots button');
            slotButtons.forEach(btn => {
              const btnTimeSlot = btn.textContent;
              
              // Check if this time slot overlaps with any existing booking
              const isOverlapping = bookings.some(booking => timeSlotsOverlap(btnTimeSlot, booking.time_slot));
              
              btn.disabled = isOverlapping;
              btn.style.opacity = btn.disabled ? 0.5 : 1;
            });
            
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
      
      // Revert to "Next" button if booking form was visible
      const bookingForm = document.getElementById('bookingForm');
      if (bookingForm && bookingForm.style.display === 'block') {
        bookingForm.style.display = 'none';
        const nextBtn = document.querySelector('.next-btn');
        if (nextBtn) {
          nextBtn.style.display = 'block';
        }
      }
      
      // Update booking summary content
      if (selectedDate && selectedTimeSlot) {
        const dateParts = selectedDate.split(' ');
        const day = dateParts[0];
        const month = dateParts[1];
        const year = dateParts[3];
        const dateISO = `${year}-${month}-${day}`;
        
        const bookingSummaryContent = document.getElementById('bookingSummaryContent');
        if (bookingSummaryContent) {
          bookingSummaryContent.innerHTML = `
            <div><strong>Date:</strong> ${selectedDate}</div>
            <div><strong>Time:</strong> ${selectedTimeSlot}</div>
            <div><strong>Duration:</strong> ${durationSelect.value}</div>
          `;
        }
        
        const bookingSummary = document.getElementById('bookingSummary');
        if (bookingSummary) {
          bookingSummary.style.display = 'block';
        }
      }
    });
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
                console.log('API Response - Date:', dateStrISO);
                console.log('API Response - Bookings:', bookings);
                
                const slotButtons = document.querySelectorAll('.slots button');
                slotButtons.forEach(btn => {
                  const btnTimeSlot = btn.textContent;
                  console.log('Checking time slot:', btnTimeSlot);
                  
                  // Check if this time slot overlaps with any existing booking
                  const isOverlapping = bookings.some(booking => {
                    const overlaps = timeSlotsOverlap(btnTimeSlot, booking.time_slot);
                    console.log(`Comparing ${btnTimeSlot} with ${booking.time_slot}: ${overlaps}`);
                    return overlaps;
                  });
                  
                  console.log(`Time slot ${btnTimeSlot} is overlapping: ${isOverlapping}`);
                  btn.disabled = isOverlapping;
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
            
            // Check bookings for this date and disable booked slots
                    fetch(`/api/bookings?date=${dateStrISO}`)
          .then(res => res.json())
          .then(bookings => {
            const slotButtons = document.querySelectorAll('.slots button');
            slotButtons.forEach(btn => {
              const btnTimeSlot = btn.textContent;
              
              // Check if this time slot overlaps with any existing booking
              const isOverlapping = bookings.some(booking => timeSlotsOverlap(btnTimeSlot, booking.time_slot));
              
              btn.disabled = isOverlapping;
              btn.style.opacity = btn.disabled ? 0.5 : 1;
            });
            
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
      
      // Show booking summary in the booking form (right column)
      const bookingSummary = document.getElementById('bookingSummary');
      const bookingSummaryContent = document.getElementById('bookingSummaryContent');
      
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
      if (bookingForm) {
        bookingForm.style.display = 'block';
      }
      
      // Ensure the selected date stays highlighted and text remains visible
      // The date highlight and text should NOT be affected by clicking Next
      const selectedCell = document.querySelector('.calendar-cell.selected');
      
      // Force the date to stay highlighted if it exists
      if (selectedCell && !selectedCell.classList.contains('selected')) {
        selectedCell.classList.add('selected');
      }
      
      // Ensure the selected date text stays visible
      const selectedDateLabel = document.getElementById('selectedDateLabel');
      if (selectedDateLabel && selectedDateLabel.textContent) {
        
        // Force the text to stay visible
        selectedDateLabel.style.display = 'block';
        selectedDateLabel.style.visibility = 'visible';
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

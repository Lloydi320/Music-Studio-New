// Home page calendar functionality
document.addEventListener('DOMContentLoaded', function() {
  console.log('🎯 Calendar script loaded!');
  
  // Calendar variables
  let currentYear = new Date().getFullYear();
  let currentMonth = new Date().getMonth();
  const realYear = new Date().getFullYear();
  const realMonth = new Date().getMonth();

  // Calendar elements
  const calendarGrid = document.getElementById('calendarGrid');
  const monthYear = document.getElementById('monthYear');
  const timeSlots = document.getElementById('timeSlots');
  const prevMonth = document.getElementById('prevMonth');
  const nextMonth = document.getElementById('nextMonth');

  console.log('🔍 Calendar elements found:', {
    calendarGrid: !!calendarGrid,
    monthYear: !!monthYear,
    timeSlots: !!timeSlots,
    prevMonth: !!prevMonth,
    nextMonth: !!nextMonth
  });

  if (calendarGrid && monthYear) {
    console.log('✅ Calendar elements found, generating calendar...');
    
    // Generate calendar
    async function generateCalendar(year, month) {
      console.log('📅 Generating calendar for:', year, month);
      
      const firstDay = new Date(year, month, 1);
      const lastDay = new Date(year, month + 1, 0);
      const daysInMonth = lastDay.getDate();
      const firstDayOfWeek = firstDay.getDay(); // 0 = Sunday, 1 = Monday, etc.

      calendarGrid.innerHTML = '';

      // Days of week header
      const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
      daysOfWeek.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.className = 'day-name';
        dayHeader.textContent = day;
        calendarGrid.appendChild(dayHeader);
      });

      // Fetch booked dates for this month
      let bookedDates = [];
      try {
        const response = await fetch(`/api/booked-dates`);
        const data = await response.json();
        console.log('📊 API Response:', data);
        
        // Handle the API response format
        if (data.booked_dates) {
          bookedDates = data.booked_dates;
        } else if (Array.isArray(data)) {
          bookedDates = data;
        } else {
          bookedDates = [];
        }
        
        console.log('📊 Booked dates:', bookedDates);
      } catch (error) {
        console.error('Error fetching booked dates:', error);
        bookedDates = [];
      }

      // Calculate the start date for the calendar grid (first day of the week that contains the first day of the month)
      const startDate = new Date(firstDay);
      startDate.setDate(startDate.getDate() - firstDayOfWeek);

      // Generate 42 cells (6 rows × 7 days)
      for (let i = 0; i < 42; i++) {
        const currentDate = new Date(startDate);
        currentDate.setDate(startDate.getDate() + i);
        
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        
        dayElement.textContent = currentDate.getDate();
        
        // Check if this date is in the current month
        if (currentDate.getMonth() === month) {
          dayElement.classList.add('current-month');
          
          // Check if date is today
          const today = new Date();
          if (currentDate.toDateString() === today.toDateString()) {
            dayElement.classList.add('today');
          }
          
          // Check if date has bookings - use local date formatting to avoid timezone issues
          const year = currentDate.getFullYear();
          const month = String(currentDate.getMonth() + 1).padStart(2, '0');
          const day = String(currentDate.getDate()).padStart(2, '0');
          const dateKey = `${year}-${month}-${day}`;
          console.log('Checking date:', dateKey, 'against booked dates:', bookedDates);
          
          // Add simple circle indicator under each day
          const circle = document.createElement('div');
          circle.className = 'day-circle';
          dayElement.appendChild(circle);
          
          // Check if date has bookings
          if (bookedDates.includes(dateKey)) {
            dayElement.classList.add('booked');
            dayElement.title = 'Booked';
            console.log('✅ Date is booked:', dateKey);
          }
          
          // Check if date is in the past
          if (currentDate < new Date().setHours(0, 0, 0, 0)) {
            dayElement.classList.add('past');
            dayElement.style.color = '#bbb';
            dayElement.style.backgroundColor = '#ececec';
            dayElement.style.textDecoration = 'line-through';
            dayElement.style.opacity = '0.7';
            dayElement.style.pointerEvents = 'none';
          } else {
            // Add click event for future dates
            dayElement.addEventListener('click', () => {
              showTimeSlots(dateKey);
            });
          }
        } else {
          // This date is not in the current month
          dayElement.classList.add('other-month');
          dayElement.style.color = '#ccc';
          dayElement.style.backgroundColor = 'transparent';
        }
        
        calendarGrid.appendChild(dayElement);
      }

      // Update month/year display
      const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
      ];
      monthYear.textContent = `${monthNames[month]} ${year}`;
      
      console.log('✅ Calendar generated successfully!');
    }

    // Show time slots for selected date
    async function showTimeSlots(dateKey) {
      try {
        // Clear previous time slots
        timeSlots.innerHTML = '';
        
        // Show loading
        timeSlots.innerHTML = '<p>Loading booking info...</p>';
        
        // Fetch booking details from API
        const response = await fetch(`/api/bookings-by-date?date=${dateKey}`);
        const data = await response.json();
        console.log('📋 Booking details for', dateKey, ':', data);
        
        // Clear loading message
        timeSlots.innerHTML = '';
        
        const heading = document.createElement('h4');
        heading.textContent = 'Booking Info';
        timeSlots.appendChild(heading);
        
        if (data.bookings && data.bookings.length > 0) {
          data.bookings.forEach(booking => {
            const bookingDiv = document.createElement('div');
            bookingDiv.className = 'booking-detail';
            bookingDiv.style.marginBottom = '16px';
            bookingDiv.style.padding = '10px';
            bookingDiv.style.borderBottom = '2.5px solid #333';
            bookingDiv.innerHTML = `
              <strong>Time Slot:</strong> ${booking.time_slot}<br>
              <strong>Status:</strong> ${booking.status}
            `;
            timeSlots.appendChild(bookingDiv);
          });
        } else {
          const message = document.createElement('p');
          message.textContent = 'No bookings for this date.';
          timeSlots.appendChild(message);
        }
      } catch (err) {
        console.error('Error fetching booking info for', dateKey, ':', err);
        timeSlots.innerHTML = '<p>Failed to load booking info.</p>';
      }
    }

    // Navigation buttons
    if (prevMonth) {
      prevMonth.addEventListener("click", () => {
        if (currentYear > realYear || (currentYear === realYear && currentMonth > realMonth)) {
          if (currentMonth === 0) {
            currentMonth = 11;
            currentYear--;
          } else {
            currentMonth--;
          }
          generateCalendar(currentYear, currentMonth);
        }
      });
    }

    if (nextMonth) {
      nextMonth.addEventListener("click", () => {
        if (currentMonth === 11) {
          currentMonth = 0;
          currentYear++;
        } else {
          currentMonth++;
        }
        generateCalendar(currentYear, currentMonth);
      });
    }

    // Initial calendar generation
    console.log('🚀 Starting initial calendar generation...');
    generateCalendar(currentYear, currentMonth);
  } else {
    console.error('❌ Calendar elements not found!');
  }
});

// Contact popup functionality
document.addEventListener("DOMContentLoaded", () => {
  const contactLink = document.getElementById("contactLink");
  const contactPopup = document.getElementById("contactPopup");
  const closeContact = document.getElementById("closeContact");

  if (contactLink && contactPopup && closeContact) {
    contactLink.addEventListener("click", (e) => {
      e.preventDefault();
      
      // Calculate scrollbar width to prevent layout shift
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

// Feedback popup functionality
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

    window.addEventListener("click", (e) => {
      if (e.target === feedbackPopup) {
        feedbackPopup.classList.remove("active");
        document.body.style.overflow = "";
        document.body.style.paddingRight = "";
      }
    });
  }
});
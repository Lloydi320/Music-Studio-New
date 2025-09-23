// Home page calendar functionality
document.addEventListener('DOMContentLoaded', function() {
  console.log('🎯 Calendar script loaded!');
  
  // Calendar variables
  let currentYear = new Date().getFullYear();
  let currentMonth = new Date().getMonth();
  const realYear = new Date().getFullYear();
  const realMonth = new Date().getMonth();
  let selectedDate = null; // Track selected date

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
            dayElement.title = 'Booked - Studio unavailable';
            dayElement.style.pointerEvents = 'none'; // Disable clicking
            dayElement.style.opacity = '0.6';
            dayElement.style.cursor = 'not-allowed';
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
              // Remove previous selection
              const previousSelected = document.querySelector('.calendar-day.selected');
              if (previousSelected) {
                previousSelected.classList.remove('selected');
              }
              
              // Add selection to clicked date
              dayElement.classList.add('selected');
              selectedDate = dateKey;
              
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
            
            // Create time slot element
            const timeElement = document.createElement('div');
            timeElement.className = 'booking-time';
            timeElement.textContent = booking.time_slot;
            
            // Create status element with appropriate class
            const statusElement = document.createElement('div');
            statusElement.className = `booking-status status-${booking.status.toLowerCase()}`;
            statusElement.textContent = booking.status.charAt(0).toUpperCase() + booking.status.slice(1);
            
            // Append elements to booking div
            bookingDiv.appendChild(timeElement);
            bookingDiv.appendChild(statusElement);
            timeSlots.appendChild(bookingDiv);
          });
        } else {
          const message = document.createElement('div');
          message.className = 'no-bookings';
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
    console.log('ℹ️ Calendar elements not found - skipping calendar initialization (this is normal for non-home pages)');
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

// Rescheduling popup functionality
document.addEventListener("DOMContentLoaded", () => {
  const rescheduleBookingLink = document.getElementById("rescheduleBookingLink");
  const reschedulePopup = document.getElementById("reschedulePopup");
  const closeReschedule = document.getElementById("closeReschedule");

  if (rescheduleBookingLink && reschedulePopup && closeReschedule) {
    rescheduleBookingLink.addEventListener("click", (e) => {
      e.preventDefault();
      
      const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
      document.body.style.overflow = "hidden";
      document.body.style.paddingRight = `${scrollbarWidth}px`;

      reschedulePopup.classList.add("active");
    });

    closeReschedule.addEventListener("click", () => {
      reschedulePopup.classList.remove("active");
      document.body.style.overflow = "";
      document.body.style.paddingRight = "";
    });

    window.addEventListener("click", (e) => {
      if (e.target === reschedulePopup) {
        reschedulePopup.classList.remove("active");
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

  // FAB functionality - toggle between calendar and carousel
  const calendarFab = document.querySelector('.calendar-fab');
  const calendarContainer = document.getElementById('calendarContainer');
  const carouselContainer = document.getElementById('carouselContainer');
  
  let isCalendarVisible = false; // Carousel is visible by default
  
  if (calendarFab && calendarContainer && carouselContainer) {
    console.log('✅ Calendar FAB and containers found, setting up toggle functionality...');
    
    // Set initial state - carousel visible, calendar hidden
    calendarContainer.classList.add('hidden');
    carouselContainer.classList.remove('hidden');
    
    // Update FAB title based on current state
    calendarFab.title = isCalendarVisible ? 'Show Image Carousel' : 'Show Calendar';
    
    calendarFab.addEventListener('click', function() {
      console.log('FAB clicked, current state:', isCalendarVisible ? 'calendar' : 'carousel');
      
      if (isCalendarVisible) {
        // Hide calendar, show carousel
        calendarContainer.classList.add('hidden');
        carouselContainer.classList.remove('hidden');
        calendarFab.title = 'Show Calendar';
        isCalendarVisible = false;
        console.log('Switched to carousel view');
      } else {
        // Hide carousel, show calendar
        carouselContainer.classList.add('hidden');
        calendarContainer.classList.remove('hidden');
        calendarFab.title = 'Show Image Carousel';
        isCalendarVisible = true;
        console.log('Switched to calendar view');
      }
    });
    
    console.log('🎠 Carousel is visible by default, FAB toggles to calendar');
  } else {
    console.log('❌ Calendar FAB or containers not found:', {
      calendarFab: !!calendarFab,
      calendarContainer: !!calendarContainer,
      carouselContainer: !!carouselContainer
    });
  }
  
  // Image Display Functionality
  const displayImages = document.querySelectorAll('.display-image');
  const indicators = document.querySelectorAll('.indicator');

  if (displayImages.length > 0) {
    let currentImage = 0;
    const totalImages = displayImages.length;

    function updateImageDisplay() {
      // Hide all images
      displayImages.forEach((image, index) => {
        image.classList.toggle('active', index === currentImage);
      });
      
      // Update indicators
      indicators.forEach((indicator, index) => {
        indicator.classList.toggle('active', index === currentImage);
      });
    }

    function nextImage() {
      currentImage = (currentImage + 1) % totalImages;
      updateImageDisplay();
    }

    function prevImage() {
      currentImage = (currentImage - 1 + totalImages) % totalImages;
      updateImageDisplay();
    }

    // Event listeners for indicators
    indicators.forEach((indicator, index) => {
      indicator.addEventListener('click', () => {
        currentImage = index;
        updateImageDisplay();
      });
    });

    // Touch/swipe support
    let startX = 0;
    let endX = 0;

    if (carouselContainer) {
      carouselContainer.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
      });

      carouselContainer.addEventListener('touchend', (e) => {
        endX = e.changedTouches[0].clientX;
        
        const threshold = 50; // Minimum swipe distance
        const diff = startX - endX;
        
        if (Math.abs(diff) > threshold) {
          if (diff > 0) {
            nextImage(); // Swipe left - next image
          } else {
            prevImage(); // Swipe right - previous image
          }
        }
      });
    }

    // Auto-play images (optional)
    let autoPlayInterval = setInterval(nextImage, 5000);
    
    // Pause auto-play on hover
    if (carouselContainer) {
      carouselContainer.addEventListener('mouseenter', () => {
        clearInterval(autoPlayInterval);
      });
      
      carouselContainer.addEventListener('mouseleave', () => {
        autoPlayInterval = setInterval(nextImage, 5000);
      });
    }

    // Initialize display
    updateImageDisplay();

    console.log('🖼️ Image display functionality initialized with swipe support');
  } else {
    console.log('❌ Display image elements not found');
  }
});

// Mobile Menu Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
  const mobileMenuToggle = document.getElementById('mobileMenuToggle');
  const navContainer = document.querySelector('.nav-container');

  if (mobileMenuToggle && navContainer) {
    mobileMenuToggle.addEventListener('click', function() {
      // Toggle active class on button for animation
      mobileMenuToggle.classList.toggle('active');
      
      // Toggle active class on nav container to show/hide menu
      navContainer.classList.toggle('active');
      
      console.log('Mobile menu toggled:', navContainer.classList.contains('active'));
    });
    
    // Close mobile menu when clicking on nav links
    const navLinks = document.querySelectorAll('.nav-links a');
    navLinks.forEach(link => {
      link.addEventListener('click', function() {
        mobileMenuToggle.classList.remove('active');
        navContainer.classList.remove('active');
      });
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
      if (!mobileMenuToggle.contains(event.target) && !navContainer.contains(event.target)) {
        mobileMenuToggle.classList.remove('active');
        navContainer.classList.remove('active');
      }
    });
    
    console.log('Mobile menu toggle initialized');
  } else {
    console.log('Mobile menu elements not found:', {
      toggle: !!mobileMenuToggle,
      navContainer: !!navContainer
    });
  }
});

// Floating Action Button for Calendar Toggle
document.addEventListener('DOMContentLoaded', function() {
  const calendarFab = document.querySelector('.calendar-fab');
  const calendarContainer = document.getElementById('calendarContainer');
  const carouselContainer = document.getElementById('carouselContainer');
  
  let isCalendarVisible = false; // Carousel is visible by default
  
  if (calendarFab && calendarContainer && carouselContainer) {
    console.log('✅ FAB and containers found, initializing toggle functionality...');
    
    // Set initial state - carousel visible, calendar hidden
    calendarContainer.classList.add('hidden');
    carouselContainer.classList.remove('hidden');
    
    // Update FAB title
    calendarFab.title = 'Show Calendar';
    
    calendarFab.addEventListener('click', function() {
      console.log('Calendar FAB toggle clicked');
      
      if (isCalendarVisible) {
        // Switch to carousel
        calendarContainer.classList.add('hidden');
        carouselContainer.classList.remove('hidden');
        calendarFab.title = 'Show Calendar';
        isCalendarVisible = false;
      } else {
        // Switch to calendar
        carouselContainer.classList.add('hidden');
        calendarContainer.classList.remove('hidden');
        calendarFab.title = 'Show Image Carousel';
        isCalendarVisible = true;
      }
    });
    
    console.log('🎠 Carousel visible by default, FAB toggles to calendar');
  } else {
    console.log('❌ FAB or container elements not found:', {
      calendarFab: !!calendarFab,
      calendarContainer: !!calendarContainer,
      carouselContainer: !!carouselContainer
    });
  }
});
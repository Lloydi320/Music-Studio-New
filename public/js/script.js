// Home page calendar functionality
// Silent debug logger
const debugLog = (...args) => {};

document.addEventListener('DOMContentLoaded', function() {
  debugLog('🎯 Calendar script loaded!');
  
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

  debugLog('🔍 Calendar elements found:', {
    calendarGrid: !!calendarGrid,
    monthYear: !!monthYear,
    timeSlots: !!timeSlots,
    prevMonth: !!prevMonth,
    nextMonth: !!nextMonth
  });

  if (calendarGrid && monthYear) {
    debugLog('✅ Calendar elements found, generating calendar...');
    
    // Generate calendar
    async function generateCalendar(year, month) {
      debugLog('📅 Generating calendar for:', year, month);
      
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

      // Fetch booked dates for this month (fully unavailable) and dates that have any bookings
      let bookedDates = [];
      let hasBookingDates = [];
      try {
        const response = await fetch(`/api/booked-dates`);
        const data = await response.json();
        debugLog('📊 API Response:', data);
        
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
        
        debugLog('📊 Unavailable booked dates:', bookedDates);
      } catch (error) {
        console.error('Error fetching booked dates:', error);
        bookedDates = [];
      }

      // Fetch dates that have any band/solo bookings
      try {
        const resp2 = await fetch(`/api/has-booking-dates`);
        const data2 = await resp2.json();
        if (data2.booked_dates) {
          if (typeof data2.booked_dates === 'object' && !Array.isArray(data2.booked_dates)) {
            hasBookingDates = Object.values(data2.booked_dates);
          } else {
            hasBookingDates = data2.booked_dates;
          }
        } else if (Array.isArray(data2)) {
          hasBookingDates = data2;
        } else {
          hasBookingDates = [];
        }
        debugLog('📍 Dates with any bookings:', hasBookingDates);
      } catch (error) {
        console.error('Error fetching has-booking dates:', error);
        hasBookingDates = [];
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
          debugLog('Checking date:', dateKey, 'against booked dates:', bookedDates);
          
          // Add simple circle indicator under each day
          const circle = document.createElement('div');
          circle.className = 'day-circle';
          dayElement.appendChild(circle);
          
          // Check if date is fully unavailable (instrument rentals or fully booked)
          if (bookedDates.includes(dateKey)) {
            dayElement.classList.add('booked');
            dayElement.title = 'Click to view booking details';
            // Remove the pointer-events: none to allow clicking
            dayElement.style.cursor = 'pointer';
            debugLog('✅ Date is booked:', dateKey);
          }

          // If there are bookings (band or solo) but the date isn't fully unavailable,
          // add a red dot indicator without disabling interactions
          if (!dayElement.classList.contains('booked') && hasBookingDates.includes(dateKey)) {
            dayElement.classList.add('has-booking');
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
            // Add click event for ALL future dates (including booked ones)
            dayElement.addEventListener('click', function(e) {
              e.preventDefault();
              e.stopPropagation();
              
              // Remove previous selection
              const previousSelected = document.querySelector('.calendar-day.selected');
              if (previousSelected) {
                previousSelected.classList.remove('selected');
              }
              
              // Add selection to clicked date
              dayElement.classList.add('selected');
              selectedDate = dateKey;
              
              // Set pointer events and cursor explicitly
              dayElement.style.pointerEvents = 'auto';
              dayElement.style.cursor = 'pointer';
              
              debugLog('📅 Date clicked:', dateKey);
              showTimeSlots(dateKey);
            });
            
            // Set default cursor for clickable dates
            dayElement.style.cursor = 'pointer';
            dayElement.style.pointerEvents = 'auto';
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
      
      debugLog('✅ Calendar generated successfully!');
    }

    // Show time slots for selected date
    async function showTimeSlots(dateKey) {
      try {
        // Clear previous time slots
        timeSlots.innerHTML = '';
        // Reset scroll state
        timeSlots.classList.remove('scroll');
        
        // Show loading
        timeSlots.innerHTML = '<p>Loading booking info...</p>';
        
        // Fetch booking details from API
        const response = await fetch(`/api/bookings-by-date?date=${dateKey}`);
        const data = await response.json();
        debugLog('📋 Booking details for', dateKey, ':', data);
        
        // Clear loading message
        timeSlots.innerHTML = '';

        // Reveal the Booking Info header now that a date is selected
        const bookingHeader = document.querySelector('.booking-header');
        if (bookingHeader) bookingHeader.classList.add('show');
        
        // The fixed Booking Info header is now a separate element.
        // Do not insert a scrolling heading inside the list.
        
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
            
            // Add booking type and additional info for instrument rentals
            if (booking.type === 'instrument_rental') {
              const typeElement = document.createElement('div');
              typeElement.className = 'booking-type';
              typeElement.textContent = `${booking.instrument_type} - ${booking.duration}`;
              bookingDiv.appendChild(typeElement);
            }
            
            // Append elements to booking div
            bookingDiv.appendChild(timeElement);
            bookingDiv.appendChild(statusElement);
            timeSlots.appendChild(bookingDiv);
          });

          // Toggle scroll when there are many bookings
          if (data.bookings.length >= 2) {
            timeSlots.classList.add('scroll');
          }
        } else {
          const message = document.createElement('div');
          message.className = 'no-bookings';
          message.textContent = 'No bookings for this date.';
          timeSlots.appendChild(message);
          // Ensure no scroll when there are no bookings
          timeSlots.classList.remove('scroll');
        }
      } catch (err) {
        console.error('Error fetching booking info for', dateKey, ':', err);
        timeSlots.innerHTML = '<p>Failed to load booking info.</p>';
        timeSlots.classList.remove('scroll');
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

    // Function to refresh calendar data
    window.refreshCalendar = function() {
      debugLog('🔄 Refreshing calendar...');
      generateCalendar(currentYear, currentMonth);
      
      // If a date is selected, refresh its time slots too
      if (selectedDate) {
        showTimeSlots(selectedDate);
      }
    };

    // Listen for reschedule updates from admin panel
    window.addEventListener('message', function(event) {
      // Only accept messages from same origin for security
      if (event.origin !== window.location.origin) return;
      
      if (event.data && event.data.type === 'reschedule_approved') {
        debugLog('📅 Reschedule approved, refreshing calendar...', event.data);
        
        // Show notification to user
        if (event.data.booking) {
          const message = `Booking ${event.data.booking.reference} has been rescheduled from ${event.data.booking.old_date} to ${event.data.booking.new_date}`;
          showCalendarNotification(message, 'success');
        } else if (event.data.rental) {
          const message = `Rental ${event.data.rental.reference} has been rescheduled from ${event.data.rental.old_start_date} to ${event.data.rental.new_start_date}`;
          showCalendarNotification(message, 'success');
        }
        
        // Refresh calendar after a short delay to allow backend updates to complete
        setTimeout(() => {
          refreshCalendar();
        }, 1000);
      }
    });

    // Listen for localStorage changes (cross-tab communication)
    window.addEventListener('storage', function(e) {
      if (e.key === 'reschedule_update' && e.newValue) {
        try {
          const data = JSON.parse(e.newValue);
          // Only process recent updates (within last 10 seconds)
          if (Date.now() - data.timestamp < 10000) {
            debugLog('📅 Received reschedule update from localStorage:', data);
            
            // Show notification to user
            if (data.booking) {
              const message = `Booking ${data.booking.reference} has been rescheduled from ${data.booking.old_date} to ${data.booking.new_date}`;
              showCalendarNotification(message, 'success');
            } else if (data.rental) {
              const message = `Rental ${data.rental.reference} has been rescheduled from ${data.rental.old_start_date} to ${data.rental.new_start_date}`;
              showCalendarNotification(message, 'success');
            }
            
            // Refresh calendar after a short delay to allow backend updates to complete
            setTimeout(() => {
              refreshCalendar();
            }, 1000);
          }
        } catch (error) {
          console.warn('Error parsing reschedule update from localStorage:', error);
        }
      }
    });

    // Function to show calendar notifications
    function showCalendarNotification(message, type = 'info') {
      // Create notification element if it doesn't exist
      let notification = document.getElementById('calendar-notification');
      if (!notification) {
        notification = document.createElement('div');
        notification.id = 'calendar-notification';
        notification.style.cssText = `
          position: fixed;
          top: 20px;
          right: 20px;
          padding: 15px 20px;
          border-radius: 5px;
          color: white;
          font-weight: bold;
          z-index: 10000;
          max-width: 400px;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
          transform: translateX(100%);
          transition: transform 0.3s ease;
        `;
        document.body.appendChild(notification);
      }
      
      // Set notification style based on type
      const colors = {
        success: '#28a745',
        error: '#dc3545',
        info: '#17a2b8',
        warning: '#ffc107'
      };
      notification.style.backgroundColor = colors[type] || colors.info;
      notification.textContent = message;
      
      // Show notification
      notification.style.transform = 'translateX(0)';
      
      // Hide notification after 5 seconds
      setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
      }, 5000);
    }

    // Initial calendar generation
    debugLog('🚀 Starting initial calendar generation...');
    generateCalendar(currentYear, currentMonth);
  } else {
    debugLog('ℹ️ Calendar elements not found - skipping calendar initialization (this is normal for non-home pages)');
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
// Global User Dropdown Toggle
window.toggleUserDropdown = function() {
  const dropdown = document.getElementById('userDropdown');
  const profileContainer = document.getElementById('userProfile') || document.querySelector('.modern-user-profile');
  if (!dropdown) return;
  dropdown.classList.toggle('show');
  if (profileContainer) profileContainer.classList.toggle('active');
};

// Close dropdown when clicking outside the profile container
document.addEventListener('click', function(event) {
  const profileContainer = document.getElementById('userProfile') || document.querySelector('.modern-user-profile');
  const dropdown = document.getElementById('userDropdown');
  if (dropdown && profileContainer && !profileContainer.contains(event.target)) {
    dropdown.classList.remove('show');
    profileContainer.classList.remove('active');
  }
});

// Prevent dropdown from closing when clicking inside it
document.addEventListener('DOMContentLoaded', function() {
  const dropdown = document.getElementById('userDropdown');
  if (dropdown) {
    dropdown.addEventListener('click', function(event) {
      event.stopPropagation();
    });
  }
});
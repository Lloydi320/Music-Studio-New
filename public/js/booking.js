document.addEventListener("DOMContentLoaded", function () {
  const calendar = document.getElementById("calendar");
  const selectedDateLabel = document.getElementById("selectedDateLabel");
  const timeSlots = document.getElementById("timeSlots");
  const monthDropdown = document.getElementById("monthDropdown");

  const today = new Date();
  const currentYear = today.getFullYear();
  const currentMonth = today.getMonth(); // 0 = Jan

  const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
  ];

 
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

    // Blank cells
    for (let i = 0; i < offset; i++) {
      const empty = document.createElement("div");
      empty.classList.add("calendar-cell", "empty");
      calendar.appendChild(empty);
    }


    for (let day = 1; day <= daysInMonth; day++) {
      const cell = document.createElement("div");
      cell.classList.add("calendar-cell");
      cell.textContent = day;

      const date = new Date(currentYear, monthIndex, day);

      if (date < today.setHours(0, 0, 0, 0)) {
        cell.classList.add("disabled");
      } else {
        cell.addEventListener("click", function () {
          document.querySelectorAll(".calendar-cell.selected").forEach(c => c.classList.remove("selected"));
          cell.classList.add("selected");

          const dateStr = `${monthNames[monthIndex]} ${day}, ${currentYear}`;
          const weekday = date.toLocaleDateString('en-US', { weekday: 'long' });

          selectedDateLabel.textContent = `${weekday}, ${dateStr}`;
          timeSlots.classList.remove("hidden");
        });
      }

      calendar.appendChild(cell);
    }
  }

 
  renderCalendar(currentMonth);


  monthDropdown.addEventListener("change", function () {
    const selectedMonth = parseInt(this.value);
    renderCalendar(selectedMonth);
  });
});

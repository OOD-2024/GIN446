// Calendar-related functions
function setCurrentMonth() {
  const months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];
  const now = new Date();
  const monthYear = `${months[now.getMonth()]} ${now.getFullYear()}`;
  document.querySelector(".current-month").innerText = monthYear;
}

function createCalendar() {
  const days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
  const calendarHeader = document.getElementById("calendar-header");
  const calendarBody = document.getElementById("calendar-body");

  const startDate = new Date();
  startDate.setDate(startDate.getDate() - startDate.getDay());

  calendarHeader.innerHTML = '<div class="time" style="width: 52px;"> </div>';

  days.forEach((day, index) => {
    const currentDate = new Date(startDate);
    currentDate.setDate(startDate.getDate() + index); // Increment the date for each day
    const dayNumber = currentDate.getDate(); // Get the day of the month
    calendarHeader.innerHTML += `<div class="header">${day} ${dayNumber}</div>`;
  });

  for (let hour = 0; hour < 24; hour++) {
    const timeString =
      hour < 12
        ? `${hour === 0 ? 12 : hour} AM`
        : `${hour === 12 ? 12 : hour - 12} PM`;

    calendarBody.innerHTML +=
      hour !== 0
        ? `<div class="time">${timeString}</div>`
        : `<div class="time"> </div>`;
    for (let day = 0; day < 7; day++) {
      calendarBody.innerHTML += `<div class="day-column" id="day${day}-hour${hour}"></div>`;
    }
  }
}

function setActiveDay() {
  const today = new Date().getDay();
  const activeDay = document.querySelector(
    `#calendar-header > div:nth-child(${today + 2})`
  );
  if (activeDay) activeDay.classList.add("active-day");
}

function showCurrentTime() {
  const now = new Date();
  const minutes = now.getHours() * 60 + now.getMinutes();
  const currentTimeDiv = document.createElement("div");
  currentTimeDiv.className = "current-time";
  currentTimeDiv.style.top = `${(minutes / 60) * 61}px`; // 61px to account for borders
  document
    .getElementById(`day${now.getDay()}-hour${0}`)
    .appendChild(currentTimeDiv);

  return minutes;
}

function scrollToCurrentTime(minutes) {
  const calendarContainer = document.querySelector(".calendar-container");
  const scrollPosition = (minutes / 60) * 61 - window.innerHeight / 2;
  calendarContainer.scrollTop = Math.max(0, scrollPosition);
}
// Theme-related functions
function setThemeBasedOnBrowserPreference() {
  if (
    window.matchMedia &&
    window.matchMedia("(prefers-color-scheme: dark)").matches
  ) {
    document.body.classList.add("dark-theme");
  }
}

function toggleTheme() {
  document.body.classList.toggle("dark-theme");
  const icon = document.querySelector("#theme-toggle i");
  if (icon.classList.contains("bx-sun")) {
    icon.classList.replace("bx-sun", "bx-moon");
  } else {
    icon.classList.replace("bx-moon", "bx-sun");
  }
}

const themeToggle = document.getElementById("theme-toggle");
if (themeToggle) {
  themeToggle.addEventListener("click", toggleTheme);
}

document.addEventListener("DOMContentLoaded", function () {
  setThemeBasedOnBrowserPreference();
  setCurrentMonth();
  createCalendar();
  setActiveDay();
  const currentMinutes = showCurrentTime();
  scrollToCurrentTime(currentMinutes);
});

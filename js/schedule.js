function timeToMinutes(time) {
  const [hours, minutes] = time.split(":").map(Number);
  return hours * 60 + minutes;
}
function calculateEventStyle(startTime, endTime) {
  const startMinutes = timeToMinutes(startTime);
  const endMinutes = timeToMinutes(endTime);
  const top = ((startMinutes % 60) / 60) * 61; // 61px to account for borders
  const height = ((endMinutes - startMinutes) / 60) * 61;
  return `top: ${top}px; height: ${height}px; z-index: 10;`;
}
const STATUS_COLORS = {
  Cancelled: "#808080", // Grey
  Completed: "#ff9999", // Light coral-pink
  Scheduled: "#3a4b5c", // Dark slate blue
  Available: "#4CAF50", // A green that complements the other colors
  Pending: "#FFA500", // Orange
  Rejected: "#FF0000", // Red
};
function showEventDetails(event) {
  let modal = document.getElementById("event-details-modal");
  if (!modal) {
    modal = document.createElement("div");
    modal.id = "event-details-modal";

    document.body.appendChild(modal);

    const overlay = document.createElement("div");
    overlay.id = "event-details-overlay";

    document.body.appendChild(overlay);

    overlay.addEventListener("click", () => {
      modal.style.display = "none";
      overlay.style.display = "none";
    });
  }

  let buttonHTML = "";
  switch (event.status) {
    case "Available":
      buttonHTML = `
          <div class="button-container">
            <button id="book-event" class="accept-button">Book</button>
          </div>
        `;
      break;
    case "Scheduled":
      buttonHTML = `
          <div class="button-container">
            <button id="cancel-appointment" class="reject-button">Cancel Appointment</button>
            <button id="confirm-appointment" class="accept-button">Confirm</button>
          </div>
        `;
      break;
    case "Pending":
      buttonHTML = `
          <div class="button-container">
            <button id="reject-event" class="reject-button">Reject</button>
            <button id="accept-event" class="accept-button">Accept</button>
          </div>
        `;
      break;

    default: // Status = Completed, Cancelled, Rejected
  }

  modal.innerHTML = `
        <h2 style="margin-bottom: 15px;">${event.name}</h2>
        <p><strong>Location:</strong> ${event.location}</p>
        <p><strong>Doctor:</strong> ${event.doctor}</p>
        <p><strong>Time:</strong> ${event.from_time} - ${event.to_time}</p>
        <p><strong>Status:</strong> ${event.status}</p>
        <p><strong>Date:</strong> ${event.appointment_date}</p>
        ${buttonHTML}
      `;

  const overlay = document.getElementById("event-details-overlay");
  modal.style.display = "block";
  overlay.style.display = "block";

  switch (event.status) {
    case "Available":
      const bookButton = document.getElementById("book-event");
      bookButton.addEventListener("click", () => {
        if (!document.getElementById("login_id")) {
          window.location.href =
            window.location.pathname.split("/").slice(0, -1).join("/") +
            "/login.php";

          return;
        }

        let patient_id = document.getElementById("login_id").innerText;
        fetch("includes/schedule.inc.php", {
          method: "PATCH",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            appointmentId: event.appointmentId,
            action: "book",
            patientId: patient_id,
          }),
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error("Network response was not ok");
            }
            return response.json();
          })
          .then((data) => {
            if (data.success) {
              alert("Appointment successfully booked");
              modal.style.display = "none";
              overlay.style.display = "none";
              location.reload();
            } else {
              alert(data.message || "Failed to book appointment");
            }
          })
          .catch((error) => {
            alert("An error occurred while booking the appointment");
          });
      });
      break;

    case "Scheduled":
      const cancelAppointmentButton =
        document.getElementById("cancel-appointment");
      const confirmAppointmentButton = document.getElementById(
        "confirm-appointment"
      );

      cancelAppointmentButton.addEventListener("click", () => {
        fetch("includes/schedule.inc.php", {
          method: "PATCH",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            appointmentId: event.appointmentId,
            action: "cancel",
          }),
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error("Network response was not ok");
            }
            return response.json();
          })
          .then((data) => {
            if (data.success) {
              alert("Appointment cancelled");
              modal.style.display = "none";
              overlay.style.display = "none";
              location.reload();
            } else {
              alert(data.message || "Failed to cancel appointment");
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            alert("An error occurred while cancelling the appointment");
          });
      });

      confirmAppointmentButton.addEventListener("click", () => {
        fetch("includes/schedule.inc.php", {
          method: "PATCH",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            appointmentId: event.appointmentId,
            action: "confirm",
          }),
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error("Network response was not ok");
            }
            return response.json();
          })
          .then((data) => {
            if (data.success) {
              alert("Appointment confirmed");
              modal.style.display = "none";
              overlay.style.display = "none";
              location.reload();
            } else {
              alert(data.message || "Failed to confirm appointment");
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            alert("An error occurred while confirming the appointment");
          });
      });
      break;

    case "Pending":
      const rejectButton = document.getElementById("reject-event");
      const acceptButton = document.getElementById("accept-event");

      rejectButton.addEventListener("click", () => {
        fetch("includes/schedule.inc.php", {
          method: "PATCH",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            appointmentId: event.appointmentId,
            action: "reject",
          }),
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error("Network response was not ok");
            }
            return response.json();
          })
          .then((data) => {
            if (data.success) {
              return fetch("../includes/log_rejection.inc.php", {
                method: "POST",
                headers: {
                  "Content-Type": "application/json",
                },
                body: JSON.stringify({
                  appointmentId: event.appointmentId,
                  reason: "User rejected",
                }),
              });
            } else {
              throw new Error(data.message || "Failed to reject appointment");
            }
          })
          .then((logResponse) => {
            if (logResponse.ok) {
              alert("Appointment rejected and logged");
              modal.style.display = "none";
              overlay.style.display = "none";
            } else {
              throw new Error("Failed to log rejection");
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            alert("An error occurred while rejecting the appointment");
          });
      });

      acceptButton.addEventListener("click", () => {
        fetch("includes/schedule.inc.php", {
          method: "PATCH",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            appointmentId: event.appointmentId,
            action: "accept",
          }),
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error("Network response was not ok");
            }
            return response.json();
          })
          .then((data) => {
            if (data.success) {
              alert("Appointment accepted");
              modal.style.display = "none";
              overlay.style.display = "none";
              location.reload();
            } else {
              alert(data.message || "Failed to accept appointment");
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            alert("An error occurred while accepting the appointment");
          });
      });
      break;
  }
}
function setThemeBasedOnBrowserPreference() {
  if (
    window.matchMedia &&
    window.matchMedia("(prefers-color-scheme: dark)").matches
  ) {
    document.body.classList.add("dark-theme");
  }
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

function scrollToCurrentTime() {
  const minutes = showCurrentTime();
  const calendarContainer = document.querySelector(".calendar-container");
  const scrollPosition = (minutes / 60) * 61 - window.innerHeight / 2;
  calendarContainer.scrollTop = Math.max(0, scrollPosition);
}
class WeeklyCalendar {
  constructor(events = [], options = {}) {
    this.options = {
      startDate: new Date(), // Start from the current date
      ...options,
    };

    this.calendarHeader = document.getElementById("calendar-header");
    this.calendarBody = document.getElementById("calendar-body");
    this.currentDateDisplay = document.querySelector(".current-month");

    this.currentWeekStart = new Date(this.options.startDate);
    this.currentWeekStart.setDate(
      this.currentWeekStart.getDate() - this.currentWeekStart.getDay()
    );

    this.events = events;

    this.createNavigation();
    this.initialize();
    this.displayEvents();
  }

  initialize() {
    this.calendarHeader.innerHTML = "";
    this.calendarBody.innerHTML = "";

    this.generateWeeklyCalendar();
    this.updateDateDisplay();
  }

  createNavigation() {
    const navigationContainer = document.createElement("div");
    navigationContainer.className =
      "calendar-navigation flex justify-between items-center";

    const prevButton = document.createElement("button");
    prevButton.innerHTML = '<i class="bx bxs-chevron-left"></i>';
    prevButton.addEventListener("click", () => this.navigateWeeks(-1));

    const homeButton = document.createElement("button");
    homeButton.innerHTML = '<i class="bx bx-home"></i>';
    homeButton.addEventListener("click", () => {
      this.navigateToCurrentWeek();
      showCurrentTime();
      scrollToCurrentTime();
    });

    const nextButton = document.createElement("button");
    nextButton.innerHTML = '<i class="bx bxs-chevron-right"></i>';
    nextButton.addEventListener("click", () => this.navigateWeeks(1));

    navigationContainer.appendChild(prevButton);
    navigationContainer.appendChild(homeButton);
    navigationContainer.appendChild(nextButton);

    this.currentDateDisplay.parentNode.insertBefore(
      navigationContainer,
      this.currentDateDisplay.nextSibling
    );
  }

  navigateToCurrentWeek() {
    this.currentWeekStart = new Date();
    this.currentWeekStart.setDate(
      this.currentWeekStart.getDate() - this.currentWeekStart.getDay()
    );
    this.initialize();
    this.displayEvents();
  }
  generateWeeklyCalendar() {
    const days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

    this.calendarHeader.innerHTML =
      '<div class="time" style="width: 52px;"> </div>';

    for (let day = 0; day < 7; day++) {
      const currentDate = new Date(this.currentWeekStart);
      currentDate.setDate(this.currentWeekStart.getDate() + day);
      const dayNumber = currentDate.getDate();
      this.calendarHeader.innerHTML += `<div class="header">${days[day]} ${dayNumber}</div>`;
    }

    for (let hour = 0; hour < 24; hour++) {
      const timeString = this.formatTimeString(hour);
      this.calendarBody.innerHTML +=
        hour !== 0
          ? `<div class="time">${timeString}</div>`
          : `<div class="time"> </div>`;

      for (let day = 0; day < 7; day++) {
        const id = `day${day}-hour${hour}`;
        this.calendarBody.innerHTML += `<div class="day-column" id="${id}"></div>`;
      }
    }

    if (this.options.scrollable) {
      this.enableScrolling();
    }
  }

  formatTimeString(hour) {
    return hour < 12
      ? `${hour === 0 ? 12 : hour} AM`
      : `${hour === 12 ? 12 : hour - 12} PM`;
  }

  updateDateDisplay() {
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

    const currentMonth = this.currentWeekStart.getMonth();
    const currentYear = this.currentWeekStart.getFullYear();
    this.currentDateDisplay.innerText = `${months[currentMonth]} ${currentYear}`;
  }

  navigateWeeks(direction) {
    this.currentWeekStart.setDate(
      this.currentWeekStart.getDate() + direction * 7
    );
    this.initialize();
    this.displayEvents();
  }
  displayEvents() {
    const weekStart = new Date(this.currentWeekStart);
    weekStart.setHours(0, 0, 0, 0);

    const weekEnd = new Date(weekStart);
    weekEnd.setDate(weekEnd.getDate() + 6);
    weekEnd.setHours(23, 59, 59, 999);

    const relevantEvents = [];
    for (const event of this.events) {
      const eventDate = new Date(event.appointment_date);

      if (eventDate > weekEnd) {
        break;
      }

      if (eventDate >= weekStart) {
        relevantEvents.push(event);
      }
    }

    relevantEvents.forEach((event) => this.addEvent(event));
  }
  enableScrolling() {
    const calendarContainer = document.querySelector(".calendar-container");
    calendarContainer.style.overflowX = "auto";
    calendarContainer.style.width = "100%";
  }
  addEvent(event) {
    const startDate = new Date(event.appointment_date);
    const startDay = startDate.getDay(); // 0 (Sunday) to 6 (Saturday)

    const startHour = parseInt(event.startTime.split(":")[0]);
    const cell = document.getElementById(`day${startDay}-hour${startHour}`);

    if (cell) {
      const eventDiv = document.createElement("div");
      eventDiv.className = "event";
      eventDiv.dataset.appointmentId = event.appointmentId;
      eventDiv.innerHTML = `${event.name}<br>${event.startTime} - ${event.endTime}`;

      eventDiv.style.backgroundColor =
        STATUS_COLORS[event.status] || STATUS_COLORS.Available;

      eventDiv.style.cssText += calculateEventStyle(
        event.startTime,
        event.endTime
      );

      eventDiv.addEventListener("click", () => {
        showEventDetails(event);
      });

      cell.appendChild(eventDiv);
    }
  }
}

document.addEventListener("DOMContentLoaded", function () {
  setThemeBasedOnBrowserPreference();

  const calendar = new WeeklyCalendar(eventsJson, {
    startDate: new Date(),
    scrollable: true,
  });

  scrollToCurrentTime();
});

const addEventBtn = document.getElementById("add-event-btn");
if (addEventBtn) {
  addEventBtn.addEventListener("click", showAddEventForm);
}

const cancelAddEventBtn = document.getElementById("cancel-add-event");
if (cancelAddEventBtn) {
  cancelAddEventBtn.addEventListener("click", () => {
    const form = document.getElementById("add-event-form");
    if (form) {
      form.style.display = "none";
    }
  });
}

function showAddEventForm() {
  const form = document.getElementById("add-event-form");
  if (form) {
    form.style.display = "block";
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("add-event-form");
  const cancelBtn = document.getElementById("cancel-add-event");

  if (form) {
    form.addEventListener("submit", function (event) {
      event.preventDefault();

      const formData = new FormData(form);

      fetch("includes/schedule.inc.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            alert("Appointment scheduled successfully!");
            form.reset();
            location.reload();
          } else {
            alert("Error scheduling appointment. Please try again.");
          }
        })
        .catch((error) => {});
    });
  }

  if (cancelBtn) {
    cancelBtn.addEventListener("click", function () {
      if (form) {
        form.style.display = "none";
      }
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  if (typeof events !== "undefined") {
    events.forEach(addEvent);
  }
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach((alert) => {
    setTimeout(() => {
      alert.remove();
    }, 5000);
  });
});

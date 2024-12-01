// Event-related functions
const STATUS_COLORS = {
  Cancelled: "#808080", // Grey
  Completed: "#ff9999", // Light coral-pink
  Scheduled: "#3a4b5c", // Dark slate blue
  Available: "#4CAF50", // A green that complements the other colors
  Pending: "#FFA500", // Orange
  Rejected: "#FF0000", // Red
};

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

function addEvent(event) {
  event.days.forEach((day) => {
    const startHour = parseInt(event.startTime.split(":")[0]);
    const cell = document.getElementById(`day${day}-hour${startHour}`);

    if (cell) {
      const eventDiv = document.createElement("div");
      eventDiv.className = "event";
      // Store appointmentId as a data attribute for easy access
      eventDiv.dataset.appointmentId = event.appointmentId;
      eventDiv.innerHTML = `${event.name}<br>${event.startTime} - ${event.endTime}`;

      // Apply status-based color
      eventDiv.style.backgroundColor =
        STATUS_COLORS[event.status] || STATUS_COLORS.Available;

      // Calculate and apply event style
      eventDiv.style.cssText += calculateEventStyle(
        event.startTime,
        event.endTime
      );

      // Add click event to show more details
      eventDiv.addEventListener("click", () => {
        showEventDetails(event);
      });

      cell.appendChild(eventDiv);
    }
  });
}

function showEventDetails(event) {
  // Create modal container if it doesn't exist
  let modal = document.getElementById("event-details-modal");
  if (!modal) {
    modal = document.createElement("div");
    modal.id = "event-details-modal";

    document.body.appendChild(modal);

    // Add close button and overlay
    const overlay = document.createElement("div");
    overlay.id = "event-details-overlay";

    document.body.appendChild(overlay);

    // Close modal when clicking overlay
    overlay.addEventListener("click", () => {
      modal.style.display = "none";
      overlay.style.display = "none";
    });
  }

  // Determine which buttons to show based on event status
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

  // Populate modal with event details
  modal.innerHTML = `
      <h2 style="margin-bottom: 15px;">${event.name}</h2>
      <p><strong>Location:</strong> ${event.location}</p>
      <p><strong>Doctor:</strong> ${event.doctor}</p>
      <p><strong>Time:</strong> ${event.from_time} - ${event.to_time}</p>
      <p><strong>Status:</strong> ${event.status}</p>
      ${buttonHTML}
    `;

  // Show modal and overlay
  const overlay = document.getElementById("event-details-overlay");
  modal.style.display = "block";
  overlay.style.display = "block";

  // Add event listeners for buttons based on status
  switch (event.status) {
    case "Available":
      const bookButton = document.getElementById("book-event");
      bookButton.addEventListener("click", () => {
        // Book appointment with patient ID
        fetch("../includes/schedule.inc.php", {
          method: "PATCH",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            appointmentId: event.appointmentId,
            action: "book",
            // patientId: getCurrentPatientId(), // Assume this function exists to get current patient ID
            patientId: 2,
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
            console.error("Error:", error);
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
        fetch("../includes/schedule.inc.php", {
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
        fetch("../includes/schedule.inc.php", {
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
        fetch("../includes/schedule.inc.php", {
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
              // Log rejection in database and reset to available
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
        fetch("../includes/schedule.inc.php", {
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

function addNewEvent(event) {
  events.push(event);
  addEvent(event);
}
// Add Event Form-related functions
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

const sortBySelect = document.getElementById("sort-by");
sortBySelect.addEventListener("change", () => {
  const sortBy = sortBySelect.value;
  let appointments = JSON.parse(eventsJson);
  if (sortBy === "name") {
    appointments.sort((a, b) => {
      const nameA = a.First_Name.toLowerCase() + a.Last_Name.toLowerCase();
      const nameB = b.First_Name.toLowerCase() + b.Last_Name.toLowerCase();
      if (nameA < nameB) return -1;
      if (nameA > nameB) return 1;
      return 0;
    });
  } else {
    appointments.sort((a, b) => {
      if (a.Appointment_Status < b.Appointment_Status) return -1;
      if (a.Appointment_Status > b.Appointment_Status) return 1;
      return 0;
    });
  }
  renderAppointments(appointments);
});

function renderAppointments(appointments) {
  const appointmentList = document.querySelector(".appointment-list");
  appointmentList.innerHTML = "";
  appointments.forEach((appointment) => {
    // Appointment item rendering code from previous example
  });
}

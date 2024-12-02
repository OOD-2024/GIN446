document.addEventListener("DOMContentLoaded", function () {
  const input = document.getElementById("specialty-input");
  const dropdown = document.getElementById("specialty-dropdown");
  const dropdownItems = dropdown.querySelectorAll(".dropdown-item");

  input.addEventListener("focus", function () {
    dropdown.style.display = "block";
  });

  document.addEventListener("click", function (e) {
    if (!input.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.style.display = "none";
    }
  });

  input.addEventListener("input", function () {
    const searchTerm = this.value.toLowerCase();

    dropdownItems.forEach((item) => {
      const text = item.textContent.toLowerCase();
      if (text.includes(searchTerm)) {
        item.classList.remove("hidden");
      } else {
        item.classList.add("hidden");
      }
    });
  });

  dropdownItems.forEach((item) => {
    item.addEventListener("click", function () {
      input.value = this.textContent.trimStart().trimEnd();
      dropdown.style.display = "none";
    });
  });
});

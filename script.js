// script.js
document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");
  form.addEventListener("submit", function (e) {
    const fields = form.querySelectorAll("input[required], textarea[required]");
    for (const field of fields) {
      if (!field.value.trim()) {
        alert("Please fill in all required fields.");
        e.preventDefault();
        return;
      }
    }
  });
});

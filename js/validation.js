// Form validation for all forms
document.addEventListener("DOMContentLoaded", function () {
  // Bootstrap validation
  const forms = document.querySelectorAll(".needs-validation");

  Array.from(forms).forEach(function (form) {
    form.addEventListener(
      "submit",
      function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add("was-validated");
      },
      false
    );
  });

  // Phone validation
  const phoneInputs = document.querySelectorAll('input[type="tel"]');
  phoneInputs.forEach(function (input) {
    input.addEventListener("input", function (e) {
      this.value = this.value.replace(/[^0-9]/g, "");
    });
  });

  // Price validation
  const priceInputs = document.querySelectorAll('input[name="price"]');
  priceInputs.forEach(function (input) {
    input.addEventListener("input", function (e) {
      if (this.value < 0) this.value = 0;
      if (this.value > 999.99) this.value = 999.99;
    });
  });
});

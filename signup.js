   // Get references to the password and confirm password input fields
   const passwordInput = document.getElementById("password");
   const confirmPasswordInput = document.getElementById("confirm_password");

   // Get references to the error message element and submit button
   const passwordError = document.getElementById("password-error");
   const submitButton = document.getElementById("submit");

   // Add an input event listener to the confirm password field
   confirmPasswordInput.addEventListener("input", function() {
       const password = passwordInput.value;
       const confirmPassword = confirmPasswordInput.value;

       // Compare the passwords
       if (password === confirmPassword) {
           // Passwords match, clear the error message
           passwordError.textContent = "";
           submitButton.disabled = false; // Enable the submit button
       } else {
           // Passwords don't match, display an error message
           passwordError.textContent = "Passwords do not match!";
           submitButton.disabled = true; // Disable the submit button
       }
   });

   const submit = document.getElementById('submit');
   const emailField = document.getElementById('email');
   const loaderOverlay = document.getElementById('loaderOverlay');

   submit.addEventListener('click', function() {
       const emailValue = emailField.value.trim();

       if (emailField.checkValidity()) {
           loaderOverlay.style.display = 'block'; // Show overlay

           // Simulate asynchronous task (e.g., AJAX request)
           setTimeout(function() {}, 2000); // Simulated delay of 2 seconds
       } else {
           emailField.reportValidity();
       }
   });
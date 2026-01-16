const pwShowHide = document.querySelectorAll(".eye-icon");

pwShowHide.forEach(eyeIcon => {
    eyeIcon.addEventListener("click", () => {

        const password = eyeIcon.parentElement.querySelector(".pass");

        if (password.type === "password") {
            password.type = "text";
            eyeIcon.classList.replace("fa-eye-slash", "fa-eye");
        } else {
            password.type = "password";
            eyeIcon.classList.replace("fa-eye", "fa-eye-slash");
        }
    });
});

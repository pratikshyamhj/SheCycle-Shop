

document.addEventListener('DOMContentLoaded', function() {
    var loginModal = document.getElementById("loginModal");
    var registerModal = document.getElementById("registerModal");
    var messageModal = document.getElementById("messageModal");

    var closeButtons = document.getElementsByClassName("close");
    var messageClose = document.getElementById("messageClose");

    // Open login modal
    var loginBtn = document.querySelector(".login-icon a");
    loginBtn.onclick = function() {
        loginModal.style.display = "block";
    }

    // Close modals
    for (var i = 0; i < closeButtons.length; i++) {
        closeButtons[i].onclick = function() {
            loginModal.style.display = "none";
            registerModal.style.display = "none";
            messageModal.style.display = "none";
        }
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target == loginModal) {
            loginModal.style.display = "none";
        }
        if (event.target == registerModal) {
            registerModal.style.display = "none";
        }
        if (event.target == messageModal) {
            messageModal.style.display = "none";
        }
    }

    // Get the register link
    var registerLink = document.querySelector(".form-container p a");

    // When the user clicks the register link, open the register modal and close the login modal
    registerLink.onclick = function() {
        loginModal.style.display = "none";
        registerModal.style.display = "block";
    }
});

function passwordChange() {
    var oldPassword = document.getElementById("old_password").value;
    var newPassword = document.getElementById("new_password").value;
    var retypeNewPassword = document.getElementById("retype_new_password").value;

    if (oldPassword.length < 8) {
        alert("Old password must be at least 8 characters long.");
        return false;
    }

    if (newPassword.length < 8) {
        alert("New password must be at least 8 characters long.");
        return false;
    }

    if (newPassword !== retypeNewPassword) {
        alert("New password and retype new password must match.");
        return false;
    }

    var hasUpperCase = /[A-Z]/.test(newPassword);
    var hasLowerCase = /[a-z]/.test(newPassword);
    var hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(newPassword);

    if (!hasUpperCase || !hasLowerCase || !hasSpecialChar) {
        alert("New password must contain uppercase, lowercase letters, and a special character.");
        return false;
    }

    return true;
}
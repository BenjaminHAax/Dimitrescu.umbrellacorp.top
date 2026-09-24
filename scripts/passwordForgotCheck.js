function forgotPassword() {
    var employeecode = document.getElementById("employeecode").value.trim();
    var email = document.getElementById("email").value.trim();

    if (employeecode === "") {
        alert("You must give an employee code!");
        return false;
    }

    if (email === "") {
        alert("You must give an email address!");
        return false;
    }

    return true;
}

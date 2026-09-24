// Front-end validering och AJAX-hjälp för users_add.php / users_edit.php

// Visar/döljer fältet för fritt användarnamn beroende på om
// "or other user name" är valt i dropdown-menyn.
function toggleOtherUsername() {
    var select = document.getElementById("employeecode_select");
    var otherWrapper = document.getElementById("other_username_wrapper");
    var otherInput = document.getElementById("other_username");
    var realNameInput = document.getElementById("real_name");

    if (select.value === "other") {
        otherWrapper.style.display = "block";
        realNameInput.value = "";
        otherInput.focus();
    } else {
        otherWrapper.style.display = "none";
        fetchEmployeeName(select.value);
    }
}

// Hämtar anställdas namn via AJAX och fyller i Real Name-fältet automatiskt.
function fetchEmployeeName(employeecode) {
    var realNameInput = document.getElementById("real_name");

    if (!employeecode) {
        realNameInput.value = "";
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "_f_get_employee_name.php?employeecode=" + encodeURIComponent(employeecode), true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            try {
                var data = JSON.parse(xhr.responseText);
                realNameInput.value = data.name || "";
            } catch (e) {
                realNameInput.value = "";
            }
        }
    };
    xhr.send();
}

// Validerar formuläret innan postning (add/edit av users).
// isEdit = true innebär att lösenordsfälten får lämnas tomma (behåll gammalt lösenord).
function usersFormCheck(isEdit) {
    var select = document.getElementById("employeecode_select");
    var otherInput = document.getElementById("other_username");
    var email = document.getElementById("email").value.trim();
    var password = document.getElementById("password").value;
    var retypePassword = document.getElementById("retype_password").value;

    var employeecode = (select.value === "other") ? otherInput.value.trim() : select.value;

    if (select.value === "other" && employeecode === "") {
        alert("You must enter a username!");
        return false;
    }

    if (email === "" || email.indexOf("@") === -1) {
        alert("You must enter a valid email address!");
        return false;
    }

    // Vid nyskapning krävs lösenord. Vid editering är det valfritt (tomt = oförändrat).
    var passwordProvided = (password !== "" || retypePassword !== "");

    if (!isEdit || passwordProvided) {
        if (password.length < 8) {
            alert("Password must be at least 8 characters long.");
            return false;
        }

        if (password !== retypePassword) {
            alert("Password and retype password must match.");
            return false;
        }

        var hasUpperCase = /[A-Z]/.test(password);
        var hasLowerCase = /[a-z]/.test(password);
        var hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);

        if (!hasUpperCase || !hasLowerCase || !hasSpecialChar) {
            alert("Password must contain uppercase, lowercase letters, and a special character.");
            return false;
        }
    }

    return true;
}

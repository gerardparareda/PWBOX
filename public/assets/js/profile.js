function validateEditProfile() {

    var email = document.forms["form-editProfile"]["inputNewEmail"].value;
    var password_new = document.forms["form-editProfile"]["inputNewPassword"].value;
    var password_old = document.forms["form-editProfile"]["inputOldPassword"].value;
    var errors = true;

    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    if (!re.test(String(email).toLowerCase())) {
        document.getElementById("error-newEmail").innerHTML = "Email invàlid.";
        errors = false;
    }

    var password_s = password_new.toString();
    if (password_s.length < 6 || password_s > 12) {
        document.getElementById("error-newPassword").innerHTML = "Password invàlid.";
        errors = false;
    }
    if (password_s.toLowerCase() !== password_s && password_s.toUpperCase() !== password_s) {

    } else {
        document.getElementById("error-newPassword").innerHTML = "Password must contain at least one number and one upper case letter.";
        errors = false;
    }

    password_s = password_old.toString();
    if (password_s.length < 6 || password_s > 12) {
        document.getElementById("error-oldPassword").innerHTML = "Password incorrecte.";
        errors = false;
    }
    if (password_s.toLowerCase() !== password_s && password_s.toUpperCase() !== password_s) {

    } else {
        document.getElementById("error-oldPassword").innerHTML = "Password incorrecte.";
        errors = false;
    }

    if (password_s.toLowerCase() !== password_s && password_s.toUpperCase() !== password_s){

    } else {
        document.getElementById("error-oldPassword").innerHTML = "Password incorrecte.";
        errors = false;
    }

    if (errors === false) {

    }

    return errors;

}



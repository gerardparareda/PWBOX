
var errors = true;

function validateEditProfile() {

    var email = document.forms["form-editProfile"]["inputNewEmail"].value;
    var password_new = document.forms["form-editProfile"]["inputNewPassword"].value;
    var password_old = document.forms["form-editProfile"]["inputOldPassword"].value;
    var filePath = document.forms["form-editProfile"]['inputNewProfileImage'].value;

    var password_old_s = password_old.toString();
    var password__new_s = password_new.toString();

    if (password__new_s.length > 0){
        validatePassword(password__new_s);
    }

    if (password_old_s > 0){
        validateOldPassword(password_old_s);
    }

    if (email.toString().length > 0){
        validateEmail(email);
    }

    if (filePath.toString().length > 0) {
        validateFile(filePath);
    }



    return errors;
}

function validatePassword(password_s){
    if (password_s.length < 6 || password_s > 12) {
        document.getElementById("error-newPassword").innerHTML = "Invalid password.";
        errors = false;
    }
    if (password_s.toLowerCase() !== password_s && password_s.toUpperCase() !== password_s) {

    } else {
        document.getElementById("error-newPassword").innerHTML = "Password must contain at least one number and one upper case letter.";
        errors = false;
    }
}

function validateOldPassword(password_s){
    if (password_s.length < 6 || password_s > 12) {
        document.getElementById("error-oldPassword").innerHTML = "Incorrect password.";
        errors = false;
    }
    if (password_s.toLowerCase() !== password_s && password_s.toUpperCase() !== password_s) {

    } else {
        document.getElementById("error-oldPassword").innerHTML = "Incorrect password.";
        errors = false;
    }

    if (password_s.toLowerCase() !== password_s && password_s.toUpperCase() !== password_s){

    } else {
        document.getElementById("error-oldPassword").innerHTML = "Incorrect password.";
        errors = false;
    }
}

function validateEmail(email){
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    if (!re.test(String(email).toLowerCase())) {
        document.getElementById("error-newEmail").innerHTML = "Invalid email";
        errors = false;
    }
}

function validateFile(filePath) {
    var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
    if (!allowedExtensions.exec(filePath.toLowerCase())) {
        document.getElementById("error-newProfileImage").innerHTML = "Profile image must be .jpg, .jpeg, .gif or .png";
        errors = false;
    }
}



$('#form-editProfile').on('submit', function(e) {
    e.preventDefault();

    var email = document.forms["form-editProfile"]["inputNewEmail"].value;
    var newPassword = document.forms["form-editProfile"]["inputNewPassword"].value;
    var oldPassword = document.forms["form-editProfile"]["inputOldPassword"].value;
    var newProfileImage = document.forms["form-editProfile"]['inputNewProfileImage'].files[0];

    var formData  = new FormData();

    formData.append('inputNewEmail', email);
    formData.append('inputNewPassword', newPassword);
    formData.append('inputOldPassword', oldPassword);
    formData.append('inputNewProfileImage', newProfileImage);

    //if(oldPassword !== "" && (email !== "" || newPassword !== "")){
    if(validateEditProfile() && oldPassword !== "" && (email !== "" || newPassword !== "" || newProfileImage !== "")){

        $.ajax(
            {
                url: '/profile',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType : 'json',
                success: function(data) {
                        document.getElementById('error-newEmail').innerHTML = data.errors['errorEmail'];
                        document.getElementById('error-newPassword').innerHTML = data.errors['errorNewPassword'];
                        document.getElementById('error-oldPassword').innerHTML = data.errors['errorOldPassword'];
                        document.getElementById('error-newProfileImage').innerHTML = data.errors['errorNewProfileImage'];

                        //if()
                        document.forms["form-editProfile"]["inputNewEmail"].value = "";
                        document.forms["form-editProfile"]["inputNewPassword"].value = "";
                        document.forms["form-editProfile"]["inputOldPassword"].value = "";
                        document.forms["form-editProfile"]['inputNewProfileImage'].value = "";

                        console.log("ok");
                },
                error: function(data){
                        document.getElementById('error-newEmail').innerHTML = data.errors['errorEmail'];
                        document.getElementById('error-newPassword').innerHTML = data.errors['errorNewPassword'];
                        document.getElementById('error-oldPassword').innerHTML = data.errors['errorOldPassword'];
                        document.getElementById('error-newProfileImage').innerHTML = data.errors['errorNewProfileImage'];}
            }
        );
    }
});




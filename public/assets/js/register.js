function validateForm(){

    var username = document.forms["register-form"]["inputUsername"].value;
    var email = document.forms["register-form"]["inputEmail"].value;
    var password = document.forms["register-form"]["inputPassword"].value;
    var birthDay = document.forms["register-form"]["inputBirthDay"].value;
    var birthMonth = document.forms["register-form"]["inputBirthMonth"].value;
    var birthYear = document.forms["register-form"]["inputBirthYear"].value;
    var errors = true;

    if(username.toString().length > 20 || username.toString().match("[^A-Za-z0-9]+")){
        document.getElementById("error-nom").innerHTML = "Nom d'usuari invàlid. Insereixi només majúscules, minúscules i números.";
        errors = false;
    }

    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    if(!re.test(String(email).toLowerCase())){
        document.getElementById("error-email").innerHTML = "Email invàlid.";
        errors = false;
    }

    var birth = birthMonth.toString();
    if(birth == "January" || birth == "March" || birth == "May" || birth == "July" || birth == "August" || birth == "October" || birth == "December"){
        if(birthDay.toString() > 31 || birthDay.toString() < 1){
            document.getElementById("error-email").innerHTML = "Dia ivàlid";
        }
    }

    return errors;


}
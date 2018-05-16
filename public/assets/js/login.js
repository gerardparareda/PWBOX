function validateLogin() {

    var email = document.forms["form-signin"]["inputEmail"].value;
    var password = document.forms["form-signin"]["inputPassword"].value;
    var errors = true;

    var arroba = /@/;

    //if (arroba.test(email)) {
    if (arroba.test(email)) {

        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

        if (!re.test(String(email).toLowerCase())) {
            //document.getElementById("error-email").innerHTML = "Email invàlid.";
            errors = false;
        }

    } else {

        if (email.toString().length > 20 || email.toString().match("[^A-Za-z0-9]+")) {
            document.getElementById("error-nom").innerHTML = "Nom d'usuari invàlid. Insereixi només majúscules, minúscules i números.";
            errors = false;
        }

    }


    var password_s = password.toString();
    if (password_s.length < 6 || password_s > 12) {
        //document.getElementById("error-password").innerHTML = "Password incorrecte. ";
        errors = false;
    }
    if (password_s.toLowerCase() !== password_s && password_s.toUpperCase() !== password_s) {

    } else {
        //document.getElementById("error-password").innerHTML = "Password incorrecte ";
        errors = false;
    }

    if (errors == false) {
        /*divError = document.createElement('p');
        textError = document.createTextNode('Usuari o password incorrectes ');
        element = document.getElementById('inputPassword');

        element.appendChild(divError);*/

        divError = document.createElement('p');
        divError.className = "error text-danger";
        /*divError.classList.add("error");
        divError.classList.add("text-danger");*/
        divError.innerText = "Usuari o password incorrectes";

        if (document.getElementById('errors') == null) {

            element = document.getElementById("inputPassword");
            divError.id = 'errors';
            element.parentNode.insertBefore(divError, element.nextSibling);
            console.log('hola');

        }

    }

    return errors;

}



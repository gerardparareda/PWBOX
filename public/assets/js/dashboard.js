function goToURL(url) {

    //console.log(url);
    window.location.href = '/dashboard/' + url;
    //ondblclick='location.href = "/dashboard/ {{ carpeta['urlPath'] }}";'


}

function downloadFile(url) {

    window.open(url, "_blank");
    //console.log(url);
    //window.location.href = '/dashboard/' + url;
    //ondblclick='location.href = "/dashboard/ {{ carpeta['urlPath'] }}";'

    console.log("abans de fer post!");

    $.post("/dashboard",
        {
            urlPath: url
        },
        function() {
            //alert( "success" );
        });

    /*var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {

        }
    };
    xhttp.open("POST", "/dashboard", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("urlPath=" + url);*/

    console.log("fet post!");

    //document.getElementById('my_iframe').src = url;
}

function renameFolder(idCarpeta, nomCarpeta) {

    console.log("abans de fer post!");

    var newName = prompt("Rename the folder", nomCarpeta);


    $.ajax(
        {
            url: '/renameFolder',
            type: 'POST',
            data: {
                idCarpeta: idCarpeta,
                newNameCarpeta: newName
            },
            dataType : 'json',
            success: function(data) {
                console.log(data);

                document.getElementById(data.id).innerHTML = data.newName1;

            },
            error: function(error) {
                //console.log(error);
                alert("You don't have permission to rename this item");
            }
        }
    );

    console.log("fet post!");
}



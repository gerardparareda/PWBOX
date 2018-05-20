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
                newNameCarpeta: newName,
                oldNameCarpeta: nomCarpeta
            },
            dataType : 'json',
            success: function(data) {
                console.log("fet post!");
                console.log("Data name: ", data.newName);
                console.log("Data permission: ", data.permision);
                if (data.permision){
                    //document.getElementById(data.id).innerHTML = data.newName;
                    location.reload();
                } else {
                    alert('No tens permisos');
                }
            },
            error: function (data) {
                alert('Hi ha hagut un error');
                //console.log(data);

            }
        }
    );
}



function createFolder(pathCarpetaRoot) {

    console.log("abans de fer post!");

    var newName = prompt("Folder name: ", '');

    if (pathCarpetaRoot === 'null') {
        pathCarpetaRoot = null;
    }


    $.ajax(
        {
            url: '/newItem',
            type: 'POST',
            data: {
                esCarpeta: true,
                pathCarpetaRoot: pathCarpetaRoot,
                nameCarpeta: newName
            },
            success: function(data) {
                console.log(data);

                location.reload();

            },
            error: function(error) {
                //console.log(error);
                alert("Error");
            }
        }
    );
    console.log("fet post!");
}

function createFile(pathCarpetaRoot) {

    console.log("abans de fer post!");

    var newName = prompt("Folder name: ", '');

    if (pathCarpetaRoot === 'null') {
        pathCarpetaRoot = null;
    }


    $.ajax(
        {
            url: '/newFolder',
            type: 'POST',
            data: {
                esCarpeta: false,
                pathCarpetaRoot: pathCarpetaRoot,
                nameCarpeta: newName
            },
            dataType : 'json',
            success: function(data) {

                location.reload();

            },
            error: function(error) {
                //console.log(error);
                alert("Error");
            }
        }
    );

    console.log("fet post!");
}


function removeFolder(idCarpetaEsborrar) {
    $.ajax(
        {
            url: '/removeFolder',
            type: 'POST',
            data: {
                idCarpetaAEsborrar: idCarpetaEsborrar
            },
            success: function(data) {
                document.getElementById(data.elementBorrat).remove();
            },
            error: function(error) {
                alert("Error borrant la carpeta")
            }
        }
    );
}


function shareFolder(id) {
    var userShare = prompt("Share with: ", '');

    $.ajax(
        {
            url: '/shareFolder',
            type: 'POST',
            data: {
                userShare: userShare,
                idCarpeta: id
            },
            dataType : 'json',
            success: function(data) {
                alert(data.message);
            },
            error: function(data) {
                console.log('Error inesperat"')
            }
        }
    );

    console.log("fet post!");
}

$('#droppable-modal').on("dragenter dragstart dragend dragleave dragover drag drop", function (e) {
    e.preventDefault();
    e.stopPropagation();
});


function handleDrop(evt) {

    document.getElementById('inputFiles').files = evt.dataTransfer.files;
    document.getElementById('files-form').submit();

    return false;
}

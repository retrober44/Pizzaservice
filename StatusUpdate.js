window.setInterval (requestData, 2000)

var request = new XMLHttpRequest();


function process(_bestellung) {
    var bestellung = JSON.parse(_bestellung);
    for(let i = 0; i < bestellung.length; i++){
        //console.log(bestellung);
        let radio = document.getElementById(bestellung[i][3] + bestellung[i][1] + bestellung[i][0]);
        //console.log (bestellung[i][3] + bestellung[i][1] + bestellung[i][0])
        radio.checked = true;
    }
}

function requestData() {
    request.open("GET", "kundenStatus.php");
    request.onreadystatechange = processData;
    request.send(null);
}

function processData() {
    if (request.readyState == 4) {
        if (request.status == 200) {
            if (request.responseText != null)
                process(request.responseText)
            else console.error ("Dokument ist leer");
            
        }else console.error ("Uebertragung fehlgeschlagen");
    } else;
}



function init(){
    let allInputs = document.getElementsByTagName("input");

    for(let i = 0; i < allInputs.length; i++){
        document.getElementById(allInputs[i].id).disabled = true;
    }


}
var preis = 0.00
var piz = null
var inputAdresse = document.getElementById('inputAdr')

class Pizzen {
    constructor() {
        this.pizzen = new Array();
    }

    addPizza(p, pr) {
        this.pizzen[p] = pr;
    }

    getPreis(p) {
        return this.pizzen[p];
    }
}


function iinit() {
    "use strict";

    var imgPizza = document.getElementsByTagName("img");
    for (let i = 0; i < imgPizza.length; i++) {
        imgPizza[i].addEventListener("click", pizzaClick);
    }

    inputAdresse.addEventListener("change", checkBeforeSubmit);


    piz = new Pizzen();
    var divs = document.getElementsByTagName("section")[0].
        getElementsByTagName("div");
    for (let i = 0; i < divs.length; i++) {
        var pPreis = divs[i].getElementsByTagName("p")[0];
        var pname = pPreis.id;
        var preis = pPreis.getAttribute("data-preis");
        piz.addPizza(pname, preis);
    }

}

function pizzaClick() {
    "use strict";
    let select = document.getElementsByTagName("select")[0];

    let pName = event.target.id;
    let newpName = pName.substring(3);
    
    let newOpt = select.appendChild(newOption(newpName));
    newOpt.selected = false;
    preis += parseFloat(piz.getPreis(newpName));


    let divPreis = document.getElementById("pPreis");
    divPreis.firstChild.nodeValue = preis.toFixed(2) + "€";
    checkBeforeSubmit();

}

function newOption(pName) {
    "use strict";

    let newOption = document.createElement("option");
    newOption.text = pName;
    newOption.value = pName;
    newOption.selected = true;

    return newOption;
}

function deleteAll() {
    "use strict";

    let selectedOpt = document.getElementsByTagName("select")[0]
        .getElementsByTagName("option");

    while (selectedOpt.length != 0) {
        selectedOpt[0].remove()
    }

    document.getElementById("pPreis").firstChild.nodeValue = "";
    preis = 0;
    checkBeforeSubmit();

}

function deleteFew() {
    "use strict";

    let selectedOpt = document.getElementsByTagName("select")[0].
        getElementsByTagName("option");


    for (var i = 0; i < selectedOpt.length; i++) {
        if (selectedOpt[i].selected) {
            preis -= piz.getPreis(selectedOpt[i].value)
            selectedOpt[i].remove();
            console.log("remove " + i);
            i--;
        }
    }

    if (preis >= 0) {
        document.getElementById("pPreis").firstChild.nodeValue = preis.toFixed(2) + "€";
    } else {
        document.getElementById("pPreis").firstChild.nodeValue = "";
    }
    checkBeforeSubmit();

}




function checkBeforeSubmit() {
    let text = inputAdresse.value;
    if (text.length > 1 && document.getElementsByTagName("select")[0].getElementsByTagName("option").length > 0) {
        document.getElementById("bestellButton").disabled = false;
    } else {
        document.getElementById("bestellButton").disabled = true;
    }
}

function bestellen(){
    let selectedOpt = document.getElementsByTagName("select")[0]
        .getElementsByTagName("option");

    for(let i = 0; i < selectedOpt.length; i++){
        selectedOpt[i].selected = true;
        
    }
}








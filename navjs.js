function changeNavCurrent(){
    let url = window.location + '';
    let newurl = url.split("/");
    let siteName = newurl[newurl.length - 1];
    let nav = document.getElementsByTagName("nav")[0].getElementsByTagName("a");

    for(let i = 0; i < nav.length; i++){

        let navSiteName = nav[i].href.split("/");
        if(navSiteName[navSiteName.length - 1] == siteName){
            document.getElementsByTagName("nav")[0].getElementsByTagName("a")[i].className = "current";
        }

    }

}
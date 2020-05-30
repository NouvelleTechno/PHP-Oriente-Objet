window.onload = () => {
    let boutons = document.querySelectorAll(".custom-control-input")

    for(let bouton of boutons){
        bouton.addEventListener("click", activer)
    }
}


function activer(){
    let xmlhttp = new XMLHttpRequest;

    xmlhttp.open('GET', '/admin/activeAnnonce/'+this.dataset.id)

    xmlhttp.send()
}
let photoDir = "../../~toivo.parnpuu/vr/vr-rinde/upload_normal/";
let photoId;

window.onload = function () {
    //kõik thumbnailid modalis avanema
    let allThumbs = document.querySelector(".gallery").querySelectorAll(".thumbs");
    for (let i = 0; i < allThumbs.length; i++){
        allThumbs[i].addEventListener("click", openModal);
    }
    document.querySelector("#modalclose").addEventListener("click", closeModal);
    document.querySelector("#modalimage").addEventListener("click", closeModal);
}

function openModal(e) {
    photoId = e.target.dataset.id;
    for (let i = 1; i < 6; i++){
        document.querySelector("#rate" + i).checked = false;
    }
    document.querySelector("#storeRating").addEventListener("click", storeRating);
    document.querySelector("#modalimage").src = photoDir + e.target.dataset.filename;
    document.querySelector("#modalcaption").innerHTML = e.target.alt;
    document.querySelector("#modal").showModal();
}

function closeModal() {
    document.querySelector("#modal").close();
}

function storeRating() {
    let rating = 0;
    for (let i = 1; i < 6; i++){
        if (document.querySelector("#rate" + i).checked) {
            rating = i;
        }
    }

    if (rating > 0) {
        //salvestamine ja info serverisse saatmine php scriptile, mis salvestab ja tagastab kliendile värskendatud keskmise hinde
        //AJAX tehnoloogia (Asynchroneus Javascript And XML)
        let webRequest = new XMLHttpRequest();
        //oleme valmis eduka ja kui asjad toimivad, siis jälgime, kas õnnestus
        //readyState 1- saadeti teele
        //readyState 2- saadi kätte
        //readyState 3- töödeldakse
        //readyState 4 - töötlemine lõppes
        webRequest.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                //kõik, mida teha, kui tuli vastus
                document.querySelector("#avgrating").innerHTML = this.responseText;
                document.querySelector("#storeRating").removeEventListener("click", storeRating)
            }
        }
        //paneme tööle, kui rating > 0
        //storePhotorating.php?photo=33&rating=4
        webRequest.open("GET", "storePhotorating.php?photo=" + photoId + "&rating=" + rating, true);
        webRequest.send();
    }
}
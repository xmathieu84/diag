
if (document.title ==="Détails de l'intervention") {



    let validerPrix = document.querySelector('.valid')
    let prixProp = document.querySelector('.prix')
    let message = document.querySelector('.message')
    let ouvrirModale = document.querySelector('.openModal')
    let budgetMax = document.querySelector('.budgetMax')
    let intervention = document.querySelector('.inter');
    let carte = document.querySelector('.carte');
    let map
    let idInter = intervention.value;

    fetch('/droneInter', {
        method: 'POST',
        body: idInter
    })
        .then(function (reponse) {
            return reponse.json()
        })
        .then(function (reponse) {

            map = L.map(carte, {
                center: reponse,
                zoom: 12,
                attributionControl: false,
                zoomControl: false,
                scrollWheelZoom: false,
                keyboard: false,
                doubleClickZoom: false,
                dragging: false

            });
            L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {

                maxZoom: 18,
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1,
                accessToken: 'pk.eyJ1IjoieG1hdGhpZXUxMyIsImEiOiJja2N1aHU2eTAyOGZ2MnJsZjk1bjR0ZHE0In0.-N1CSInz77-O6xViA43KQw'
            }).addTo(map);

            L.marker(reponse, {
                title: "Lieu de votre intervention"
            }).addTo(map)


        })






        prixProp.addEventListener('keyup',()=>{


            if (parseFloat(prixProp.value) > parseInt(budgetMax.value)){
                prixProp.value = budgetMax.value
            }
        })

    let dateInter = document.querySelector("#dateInter")
        validerPrix.addEventListener('click', () => {
            if (prixProp.value !== ""){
               let confirmer = confirm("Souhaiter vous valider votre proposition tarifaire ?")
                if (confirm){
                    let corps = JSON.stringify({
                        idProp: prixProp.dataset.prop,
                        prix: prixProp.value,
                        dateInter: new Date(dateInter.value).getTime()/1000

                    })
                    fetch('/validerPrix', {
                        method: 'POST',
                        body: corps
                    })
                        .then((reponse) => {
                            return reponse.json();
                        })
                        .then((reponse) => {


                            ouvrirModale.click()

                            message.innerText = reponse

                        })
                }
            }
            else{
                alert("Veuillez indiquer un prix.")
            }

        })



}
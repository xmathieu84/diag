if (document.title ==="Ajouter un abonnement Entreprise BTP et autres") {
    let valider = document.querySelector('.btn-maincolor')
    let abonnements = document.getElementsByName('abonnement')
    let abo;

    valider.addEventListener('click', () => {
        for (let i = 0; i < abonnements.length; i++) {
            if (abonnements[i].checked) {
                abo = abonnements[i].value

            }
        }
        document.location.href = "/alerteAjoutAbonnement/" + abo
    })
}



if (document.title ==="Ajouter un abonnement Pro-Btp"){
    function localise(lieu,listeTravaux){
        let carte;
        let distanceInterPremium = document.querySelector('#formControlRange')
        let distance=  document.querySelectorAll('.distance')
        let cercle;
        fetch("/localisationInscriptionProBtp",{
            method :'POST',
            body : lieu
        })
            .then((response)=>{
                return response.json()
            })
            .then((response)=>{
                fetch('/localisationPremiumProBtp',{
                    method : 'POST',
                    body : JSON.stringify(listeTravaux)
                })
                    .then((reponse)=>{
                        return reponse.json()
                    })
                    .then((reponse)=>{
                        if (carte !== undefined){
                            carte.remove()
                        }
                        let myIcon = L.icon({
                            iconUrl: "\\css\\css_site\\img\\iconePerso.png",
                            iconSize: [45, 45],
                            popupAnchor: [-3, -76],
                            shadowSize: [68, 95],
                            shadowAnchor: [22, 94]
                        });
                        carte = L.map(cartePremium, {
                            center: response,
                            zoom: 8,
                            attributionControl: false,
                            zoomControl: false,
                            scrollWheelZoom: false,
                            keyboard: false,
                            doubleClickZoom: false,
                            dragging: true

                        });
                        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {

                            maxZoom: 20,
                            id: 'mapbox/streets-v11',
                            tileSize: 512,
                            zoomOffset: -1,
                            accessToken: 'pk.eyJ1IjoieG1hdGhpZXUxMyIsImEiOiJja2N1aHU2eTAyOGZ2MnJsZjk1bjR0ZHE0In0.-N1CSInz77-O6xViA43KQw'
                        }).addTo(carte);
                        for (let i = 0; i < reponse.length; i++) {

                            L.circle([reponse[i].coordonee[0],reponse[i].coordonee[1]],reponse[i].rayon*1000,{
                                color : "#FF4200",
                                opacity : 0.5
                            }).addTo(carte);
                            L.marker([reponse[i].coordonee[0],reponse[i].coordonee[1]]).addTo(carte)

                        }
                        L.marker(response, {
                            icon : myIcon,
                            title: "Votre position"
                        }).addTo(carte)

                        distanceInterPremium.addEventListener('change',()=>{

                            distance[0].innerText = distanceInterPremium.value
                            distance[1].innerText = distanceInterPremium.value
                            if (cercle !== undefined){
                                carte.removeLayer(cercle)
                            }
                            cercle =L.circle(response,distanceInterPremium.value*1000,{
                                color : "#0067FF",
                                opacity : 0.5
                            })
                            cercle.removeFrom(carte)

                            if (distanceInterPremium.value !==""){
                                cercle.addTo(carte);

                            }
                        })
                    })
            })
    }
    let distanceInterPremium = document.querySelector('#formControlRange')
    let ville = document.getElementsByName("pro_btp[departZoneInter]")
    let abonnementProBtp = document.querySelector('#pro_btp_abonnement')
    let btnCarte = document.querySelector('.btnCarte')
    for (let i = 0; i < ville.length; i++) {
        ville[i].addEventListener('change',()=>{
            if (ville[i].value ==="non"){
                document.querySelector('.villeDepart').style.display = 'block'
                document.querySelector('#pro_btp_villeDepart').setAttribute('required','required')
            }
            else{
                document.querySelector('.villeDepart').style.display = 'none'
                document.querySelector('#pro_btp_villeDepart').value =""
                document.querySelector('#pro_btp_villeDepart').removeAttribute('required')
            }
        })
    }

    abonnementProBtp.addEventListener('change',()=>{
        let liste =[];

        if (document.querySelector('#pro_btp_departZoneInter_1').checked ===true && document.querySelector('#proBtp_villeDepart').value ===""){
            alert('Veuillez choisir une ville de départ')
            abonnementProBtp.options.selectedIndex = 0

        }
        else {
            let travaux = document.getElementsByName("pro_btp[travaux][]")
            for (let i = 0; i < travaux.length; i++) {
                if (travaux[i].checked === true){
                    liste.push(travaux[i].value)
                }
            }

            let possible = abonnementProBtp.options[abonnementProBtp.selectedIndex].dataset.possible
            let adresse;
            let cercle;
            let distance=  document.querySelectorAll('.distance')
            if (possible==="premium"){
                btnCarte.click()
                let newVille = document.querySelector('#pro_btp_villeDepart').value
                if (newVille !==""){

                    localise(newVille,liste)
                }
                else {
                    fetch("/recupereAdresse",{
                        method : 'GET'
                    })
                        .then((response)=>{
                            return response.json()
                        })
                        .then((response)=>{
                            localise(response,liste)

                        })
                }
                let demandeAbo = document.getElementsByName('demandeAbo')
                for (let i = 0; i < demandeAbo.length; i++) {
                    demandeAbo[i].addEventListener('change',()=>{
                        if (demandeAbo[i].value==='non'){
                            document.querySelector('.basculerAbo').style.display ='block'
                            document.querySelector('.distanceSouscrit').innerText = 0
                        }
                        else {
                            document.querySelector('.basculerAbo').style.display ='none'
                            document.querySelector('#pro_btp_distanceInter').value = distanceInterPremium.value
                            document.querySelector('.distanceSouscrit').innerText = distanceInterPremium.value
                            document.querySelector('#distanceFinaleInter').innerText = 30
                            document.querySelector('#pro_btp_distanceInter').max = 30
                        }
                    })
                }
                let basculer = document.getElementsByName('basculer')
                for (let i = 0; i < basculer.length; i++) {
                    basculer[i].addEventListener('change',()=>{
                        if (basculer[i].value ==='non'){
                            document.querySelector('.refus').style.display ='block'
                            abonnementProBtp.options.selectedIndex = 0

                        }
                        else {
                            document.querySelector('.refus').style.display ='none'
                            document.querySelector('#pro_btp_distanceInter').value = distanceInterPremium.value
                            document.querySelector('.distanceSouscrit').innerText = distanceInterPremium.value
                            document.querySelector('#distanceFinaleInter').innerText = 100
                            document.querySelector('#pro_btp_distanceInter').max = 100
                            abonnementProBtp.options.selectedIndex = 1
                        }
                    })
                }




            }
        if (possible==="classique"){
            document.querySelector('#pro_btp_distanceInter').max = 100
            document.querySelector('#distanceFinaleInter').innerText = 100
        }
        }

    })

    document.querySelector("#pro_btp_distanceInter").addEventListener('change',()=>{
        document.querySelector('.distanceSouscrit').innerText = document.querySelector("#pro_btp_distanceInter").value
    })

    document.querySelector('.btnAlerte').addEventListener('click',()=>{
        document.querySelector('.abonnement').innerText = abonnementProBtp.options[abonnementProBtp.selectedIndex].innerText

    })
}
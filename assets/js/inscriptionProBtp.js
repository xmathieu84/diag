if (document.title ==="Inscription Pro-Btp"){

    function changeButton(idButton,realId){
        let button = document.getElementById(idButton);
        let realFile = document.getElementById(realId);

        button.addEventListener('click', () => {
            realFile.click();
            realFile.addEventListener('change', () => {
                button.innerHTML = realFile.files[0]['name'];
            })


        })
    }
    changeButton("logo","entreprise_tp_logo")

    let logo = document.querySelector('#entreprise_tp_logo')
    let assujetis = document.getElementsByName("entreprise_tp[siretTva][assujeti]")
    for (let i = 0; i <assujetis.length ; i++) {
        assujetis[i].addEventListener('change',()=>{
            if (assujetis[i].value ==="1"){
                document.querySelector('.tva').style.display='block'
                document.querySelector('#entreprise_tp_siretTva_tva').setAttribute('required','required')
                document.querySelector('#entreprise_tp_siretTva_tva').removeAttribute('disabled')
            }
            else{
                document.querySelector('.tva').style.display='none'
                document.querySelector('#entreprise_tp_siretTva_tva').setAttribute('disabled','disabled')
                document.querySelector('#entreprise_tp_siretTva_tva').removeAttribute('required')
                document.querySelector('#entreprise_tp_siretTva_tva').value ="";
            }
        })

    }

    let siret = document.querySelector('#entreprise_tp_siretTva_siret')
    siret.addEventListener('keyup',()=>{

        if (siret.value.length === 14){

            fetch('/entreprise/siret',{
                method:'POST',
                body : siret.value
            })
                .then((response)=>{
                    return response.json()
                })
                .then((response)=>{

                    document.querySelector('#entreprise_tp_adresse_numero').value= response.adresse.numero
                    document.querySelector('#entreprise_tp_adresse_nomVoie').value= response.adresse.nomVoie
                    document.querySelector('#entreprise_tp_adresse_codePostal').value= response.adresse.codePostal
                    document.querySelector('#entreprise_tp_adresse_ville').value= response.adresse.ville
                    document.querySelector('#entreprise_tp_siretTva_tva').value = response.TVA

                })
        }
    })
    let abonnementPub = document.getElementsByName('entreprise_tp[abonnementPub]')
    for (let i = 0; i < abonnementPub.length; i++) {
        abonnementPub[i].addEventListener('change',()=>{
            if (abonnementPub[i].value==='oui'){
                document.querySelector('.btnPresentation').click()
                document.querySelector('.pub').style.display = 'block'
                document.querySelector('#entreprise_tp_proBtp_distanceInter').setAttribute('required','required')

            }
            else {
                document.querySelector('.pub').style.display = 'none'
                document.querySelector('#entreprise_tp_proBtp_distanceInter').removeAttribute('required')
                document.querySelector('#entreprise_tp_proBtp_distanceInter').setAttribute('disbaled','disbaled')
                if ( document.querySelector('#entreprise_tp_proBtp_bandeauPub')){
                    document.querySelector('#entreprise_tp_proBtp_bandeauPub').removeAttribute('required')
                }


            }
        })
    }
    let abonnementProBtp = document.querySelector("#entreprise_tp_proBtp_abonnement")
    let autreVille = document.getElementsByName('entreprise_tp[proBtp][departZoneInter]')
    let carte;
    let btnCarte = document.querySelector('.btnCarte')
    let cartePremium = document.querySelector('#cartePremium')
    let liste=[];

    let distanceInterPremium = document.querySelector('#formControlRange')
    abonnementProBtp.addEventListener('change',()=>{

        if (document.querySelector('#entreprise_tp_proBtp_departZoneInter_1').checked ===true && document.querySelector('#entreprise_tp_proBtp_villeDepart').value ===""){
            alert('Veuillez choisir une ville de départ')
            abonnementProBtp.options.selectedIndex = 0

        }
        else {
            let travaux = document.getElementsByName("entreprise_tp[proBtp][travaux][]")
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
                let newVille = document.querySelector('#entreprise_tp_proBtp_villeDepart').value
                if (newVille !==""){
                    adresse = newVille
                }
                else {
                    adresse = document.querySelector('#entreprise_tp_adresse_numero').value+" "+document.querySelector('#entreprise_tp_adresse_nomVoie').value+" "+document.querySelector('#entreprise_tp_adresse_codePostal').value+" "+document.querySelector('#entreprise_tp_adresse_ville').value
                }

                fetch("/localisationInscriptionProBtp",{
                    method :'POST',
                    body : adresse
                })
                    .then((response)=>{
                        return response.json()
                    })
                    .then((response)=>{
                        fetch('/localisationPremiumProBtp',{
                            method : 'POST',
                            body : JSON.stringify(liste)
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
        if (possible ==='classique'){
            document.querySelector('.distanceFinaleInter').innerText = "100"
            document.querySelector("#entreprise_tp_proBtp_distanceInter").max = 100
        }
        }

    })


    let choixVille = document.querySelector('.villeDepart')
    let ville = document.querySelector('#entreprise_tp_proBtp_villeDepart')
    let list = document.querySelector('#cities')
    ville.addEventListener('keyup',()=>{
        while (list.firstChild){
            list.removeChild(list.lastChild)
        }
       if (ville.value.length >=3){
           fetch("https://geo.api.gouv.fr/communes?nom="+ville.value+"&fields=nom&format=json&geometry=centre",{
               method : 'GET'
           })
               .then((reponse)=>{
                   return reponse.json()
               })
               .then((reponse)=>{

                   for (let i = 0; i < reponse.length; i++) {

                       let option = document.createElement('option')
                       option.value = reponse[i].nom
                       option.innerHTML = reponse[i].nom
                       list.appendChild(option)
                       ville.click()
                   }
               })
       }
    })
    for (let i = 0; i < autreVille.length; i++) {
        autreVille[i].addEventListener('change',(e)=>{
            if (autreVille[i].value ==="non"){
                choixVille.style.display = 'block'
                document.querySelector('#entreprise_tp_proBtp_villeDepart').setAttribute('required','required')
            }
            else {
                choixVille.style.display = 'none'
                document.querySelector('#entreprise_tp_proBtp_villeDepart').removeAttribute('required')
                document.querySelector('#entreprise_tp_proBtp_villeDepart').value =""
            }
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
                document.querySelector('#entreprise_tp_proBtp_distanceInter').value = distanceInterPremium.value
                document.querySelector('.distanceSouscrit').innerText = distanceInterPremium.value
                document.querySelector('#distanceFinaleInter').innerText = 30
                document.querySelector('#entreprise_tp_proBtp_distanceInter').max = 30
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
                document.querySelector('#entreprise_tp_proBtp_distanceInter').value = distanceInterPremium.value
                document.querySelector('.distanceSouscrit').innerText = distanceInterPremium.value
                document.querySelector('#distanceFinaleInter').innerText = 100
                document.querySelector('#entreprise_tp_proBtp_distanceInter').max = 100
                abonnementProBtp.options.selectedIndex = 1
            }
        })
    }

    document.querySelector("#entreprise_tp_proBtp_distanceInter").addEventListener('change',()=>{
        document.querySelector('.distanceSouscrit').innerText = document.querySelector("#entreprise_tp_proBtp_distanceInter").value
    })
}
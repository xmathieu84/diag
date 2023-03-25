if (document.title ==="Tarif Odi"){
    let prixs = document.querySelectorAll('.prixOdi')
    let valider = document.querySelector(".validerTarif")
    let prixPacks = document.querySelectorAll('.prixPackOdi')
    let moyen =0;
    let service=0

    let listePrix=[];
    let listeBien = [];
    let listeMission = [];
    let listePack = []
    let listeBienPack=[];
    let listePrixPack=[];
    let serviceCollecte = document.getElementsByName('serviceCollecte')
    let serviceMoyen = document.getElementsByName('serviceMoyen')
    let prelevements = document.querySelectorAll('.prelevement')
    let fichierTarif = document.querySelector('input[type=file]')
    let cgv = document.querySelector('input[type=hidden]')
    let prixExistant = false;
    let prixPackExistant = false;
    document.querySelector('.buttonFichierPrixOdi').addEventListener('click',()=>{
        document.querySelector('#fichierPrix').click()
        document.querySelector('#fichierPrix').addEventListener('change',()=>{
            document.querySelector('.buttonFichierPrixOdi').innerHTML = document.querySelector('#fichierPrix').files.length + " fichier(s) ajouté(s)"
            if (document.querySelector('#fichierPrix').files.length !==0){
                document.querySelector("#buttonFichierTarifOdiService").style.display = 'block'
            }
        })
    })
    document.querySelector('.FichierTarifOdiService').addEventListener('click',()=>{
        document.querySelector('#nonService').checked = true
    })
    document.querySelector('.TarifMoyenOdiService').addEventListener('click',()=>{
        document.querySelector('#nonMoyen').checked = true
    })
    for (let i = 0; i < serviceCollecte.length; i++) {
        serviceCollecte[i].addEventListener('change',()=>{
            if (serviceCollecte[i].value ==="Oui"){
                document.querySelector('#nonMoyen').checked = true
                document.querySelector("#saisieTaridOdi").style.display = 'none'
                document.querySelector('.btnTarif').click()

            }
            else{
                document.querySelector("#saisieTaridOdi").style.display = 'block'
            }
        })
    }
    for (let i = 0; i < serviceMoyen.length; i++) {
        serviceMoyen[i].addEventListener('change',()=>{
            if (serviceMoyen[i].value ==="Oui"){
                document.querySelector('#nonService').checked = true
                document.querySelector('.btnTarifMoyen').click()
                document.querySelector("#saisieTaridOdi").style.display = 'block'
            }
            else{
                document.querySelector("#saisieTaridOdi").style.display = 'block'

                for (const prix of prixs) {
                    if (!prixExistant){
                        prix.value = "";
                    }
                }
                for (const prixPack of prixPacks) {
                   if (!prixPackExistant){
                       prixPack.value = "";
                   }
                }
            }
        })
    }
    document.addEventListener('DOMContentLoaded',()=>{
        for (let i = 0; i < prixs.length; i++) {

            listeBien.push(prixs[i].dataset.type)
            listeMission.push(prixs[i].dataset.mission);
        }
        for (let i = 0; i < prixPacks.length; i++) {
            listePack.push(prixPacks[i].dataset.pack)
            listeBienPack.push(prixPacks[i].dataset.taille)

        }
        fetch("/entreprise/retrouverTarifMission",{
            method :'POST',
            body : JSON.stringify({
                bien : listeBien,
                mission : listeMission
            })

        })
            .then((response)=>{
                return response.json()

            })
            .then((response)=>{
                for (let i = 0; i < response.length; i++) {
                    prixs[i].value = response[i]
                }
                fetch("/entreprise/retrouverPrixPack",{
                    method : "POST",
                    body : JSON.stringify({
                        bien : listeBienPack,
                        pack : listePack
                    })
                })
                    .then((response)=>{
                        return response.json()
                    })
                    .then((response)=>{
                        for (let i = 0; i < response.length; i++) {
                            prixPacks[i].value = response[i]
                        }
                        for (let i = 0; i < response.length; i++) {
                           if (response[i] !=0){
                               prixPackExistant = true
                           }
                        }
                        let listePrelev =[];
                        let bien = [];
                        for (let i = 0; i < prelevements.length; i++) {
                            listePrelev.push(prelevements[i].dataset.prelev)
                            bien.push(prelevements[i].dataset.type)
                        }
                        fetch("/entreprise/retrouverPrixPrelevement",{
                            method : 'post',
                            body : JSON.stringify({
                                liste : listePrelev,
                                listeBien : bien
                            })

                        }).then((response)=>{
                            return response.json()
                        })
                            .then((response)=>{
                                for (let i = 0; i < response.length; i++) {
                                    prelevements[i].value = response[i]
                                }
                                for (let i = 0; i < response.length; i++) {
                                    if (response[i]!=0){
                                        prixExistant = true
                                        break;
                                    }
                                }

                            })
                    })
            })
    })


    valider.addEventListener('click',()=>{
        listePrix=[];
        listeBien = [];
        listeMission = [];
        listePack = []
        listeBienPack=[];
        listePrixPack=[];
        let listePrixPrelevement = []
        let listePrelevement=[];
        let confirmer = confirm("Souhaitez vous validez ces tarifs?")

        if (document.querySelector("#ouiMoyen").checked ===true){
            moyen=1
        }
        if (confirmer){
            for (let i = 0; i < prixs.length; i++) {
                listePrix.push(prixs[i].value)
                listeBien.push(prixs[i].dataset.type)
                listeMission.push(prixs[i].dataset.mission);
            }
            for (const prelevement of prelevements) {
                listePrixPrelevement.push(prelevement.value)
                listePrelevement.push(prelevement.dataset.prelev)
            }
            console.log(listePrixPrelevement,listePrelevement)
            fetch("/entreprise/validerTarifOdi",{
                method : "POST",
                body : JSON.stringify({
                    prixInter : listePrix,
                    bien : listeBien,
                    mission : listeMission,
                    moyen : moyen,
                    prixPrelev : listePrixPrelevement,
                    prelev : listePrelevement
                })
            })
                .then(()=>{
                    for (let i = 0; i < prixPacks.length; i++) {
                        listePack.push(prixPacks[i].dataset.pack)
                        listeBienPack.push(prixPacks[i].dataset.taille)
                        listePrixPack.push(prixPacks[i].value)
                    }
                    fetch("/entreprise/validerPrixPack",{
                        method :'POST',
                        body : JSON.stringify({
                            pack : listePack,
                            bien : listeBienPack,
                            prix : listePrixPack,
                            moyen : moyen

                        })
                    })
                        .then(()=>{
                            alert("Vos prix ont bien été enregistrés")
                            if (cgv.value ==='true'){
                                //document.location.href="/entreprise/changementValidé"

                            }
                            else{
                                //document.location.href = "/entreprise/remise"
                            }

                        })
                })
        }
    })
    function alerte(){
        alert("Les tarifs moyens ont été intégrés.")
    }
    let validerFichier = document.querySelector("#buttonFichierTarifOdiService")
    let validerMoyen = document.querySelector('#buttonTarifMoyenOdiService')
    validerFichier.addEventListener('click',()=>{
        let confirmer = confirm("Souhaitez vous envoyer ces fichiers ?")
        if (confirmer){
            let form = new FormData()
            for (let i = 0; i < fichierTarif.files.length; i++) {
                form.append('fichier'+i,fichierTarif.files[i]);
            }

            fetch('/entreprise/validerFilePrix',{
                method : 'post',
                body : form
            })
                .then((response)=>{

                    alert('Vos fichier ont bien été envoyés. Vous allez être redirigé pour continuer la procédure d\'inscription')
                    document.location.href = "/entreprise/remise"
                })
        }
    })

    validerMoyen.addEventListener('click',()=>{
        let listeMission = [];
        let listePackMoyen=[];
        let listeBien = [];
        let listePrelevement=[]
        for (let i = 0; i < prixs.length; i++) {
            listeMission.push(prixs[i].dataset.missionodi);
        }
        for (let i = 0; i < prixPacks.length ; i++) {
            listePackMoyen.push(prixPacks[i].dataset.packodi)
        }
        for (let i = 0; i < prixs.length; i++) {
            listeBien.push(prixs[i].dataset.type)
        }
        for (let i = 0; i < prelevements.length; i++) {
            listePrelevement.push(prelevements[i].dataset.prelev)
        }

        const listeFinale = Array.from(new Set(listeMission));
        const listPackFinale = Array.from(new Set(listePackMoyen))
        const listBienFinale = Array.from(new Set(listeBien))
        fetch("/entreprise/prixMoyen",{
            method : 'POST',
            body : JSON.stringify({
                pack : listPackFinale,
                mission : listeFinale,
                bien : listBienFinale,
                prelev : Array.from(new Set(listePrelevement))
            })

        })
            .then((response)=> {
                return response.json()
            })
            .then((response)=>{
                document.querySelector('.fermerModal').click()
                for (let i = 0; i < response.moyenMission.length; i++) {
                   prixs[i].value = response.moyenMission[i]
                    prixs[i].style.color = 'red'
                }
                for (let i = 0; i < response.moyenPack.length; i++) {
                    prixPacks[i].value = response.moyenPack[i]
                    prixPacks[i].style.color = 'red'
                }

                /*for (let i = 0; i < response.prixPrelev.length; i++) {
                    prelevements[i].value = response.prixPrlev[i]
                }*/
                let myTimeout;
                myTimeout = setTimeout(alerte, 1000);

            })
    })

}
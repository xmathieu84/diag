if (document.title === "Inscrition syndicat de copropriétés" ){
    // Recherche de l'abonnement coppriete
    let prixAbo = document.querySelector('.prixAbo')
    let utlisateur = document.querySelector('.utlisateur')
    fetch("/abonnementCoPro",{
        method : 'GET'
    })
        .then((response)=>{
            return response.json()
        })
        .then((response)=>{
            prixAbo.innerHTML = response.ht + " € HT ("+response.ttc+" € TTC)"
            utlisateur.innerHTML = response.utilisateur
        })

    //Retourne les information de la société à travers son numéro de SIREN

    // code promotionnel

    let codePromo = document.querySelector('#demandeur_infoCodeProm_0')

    codePromo.addEventListener('change',()=>{
        if (codePromo.checked){
            document.querySelector('.codePrm').style.display = 'block'
        }
        else {
            document.querySelector('.codePrm').style.display = 'none'
        }
    })

    let verfier = document.querySelector('.btn-sm')
    let cp = document.querySelector("#demandeur_cpAmbassadeur")
    verfier.addEventListener('click',()=>{

        let code = document.querySelector('#demandeur_codePromo')


            fetch("/verifcation/codePromo",{
                method : 'POST',
                body : JSON.stringify({profil:'Syndicat de co-propriété',code:code.value,cp:null})
            })
                .then((response)=>{
                    return response.json()
                })
                .then((response)=>{
                    document.querySelector('#btn-promo').click()
                    let zoneAbo = document.querySelector('.resultAboCoPro')
                    if (response.existe ==='promo'){
                        document.querySelector('.messagePromo').innerText = "Votre code promotionnel vous permet de bénéficier de "+response.remise +"%"+" sur l'abonnement suivant : "+response.abonnement
                        document.querySelector('.validerCodePromo').addEventListener('click',()=>{

                            while(zoneAbo.firstChild){
                                zoneAbo.removeChild(zoneAbo.lastChild)

                            }
                            let pAbonnement = document.createElement('p')
                            pAbonnement.classList.add('rappelAbo')
                            pAbonnement.innerHTML = "Coût de l'abonnement mensuel : "+response.prix+' € HT ' + '('+Math.round(response.prix*1.2)+' € TTC)'
                            let pUtilisateur = document.createElement('p')
                            pUtilisateur.innerHTML ="Utilisateur(s) : "+ response.utilisateur
                            pUtilisateur.classList.add('rappelAbo')
                            let pAvert = document.createElement('p')
                            zoneAbo.appendChild(pUtilisateur)
                            zoneAbo.appendChild(pAbonnement)
                            zoneAbo.appendChild(pAvert)
                        })
                    }
                    else if(response.existe ==='ambassadeur'){
                        if (response.possible) {

                            document.querySelector('.messagePromo').innerHTML = "<p class='h5'>Ce code vous donne accès au statut ambassadeur de DIAG-DRONE</p>" +
                                "<p class='h5'>Vous bénéficiez de l'abonnement " + response.abonnement + " au prix de" + response.prix + " € HT avec " + response.utilisateur + " utilisateur(s)</p> " +
                                "<p class='h5'>La durée de votre abonnement est de " + response.duree + " mois non reconductible</p>"
                            document.querySelector('.validerCodePromo').addEventListener('click', () => {

                                while (zoneAbo.firstChild) {
                                    zoneAbo.removeChild(zoneAbo.lastChild)

                                }
                                let pAbonnement = document.createElement('p')
                                pAbonnement.classList.add('rappelAbo')
                                pAbonnement.innerHTML = "Coût de l'abonnement : " + response.prix + ' € HT ' + '(' + Math.round(response.prix * 1.2) + ' € TTC)'
                                let pUtilisateur = document.createElement('p')
                                pUtilisateur.innerHTML = "Utilisateur(s) : " + response.utilisateur
                                pUtilisateur.classList.add('rappelAbo')
                                let pAvert = document.createElement('p')
                                pAvert.classList.add('rappelAbo')

                                zoneAbo.appendChild(pUtilisateur)
                                zoneAbo.appendChild(pAbonnement)
                                zoneAbo.appendChild(pAvert)
                            })
                        }
                        else {
                            document.querySelector('.messagePromo').innerHTML = "<p class='h5'>Tout les statuts ambassadeur pour votre département ont été definis.</p>" +
                                "<p class='h5'>DIAG DRONE vous remercie.</p>"
                            document.querySelector('.btn-secondary').addEventListener('click',()=>{
                                code.value ="";
                            })

                        }
                    }
                    else {
                        document.querySelector('.messagePromo').innerText = "Le code promotionnel renseigné n'existe pas"
                        document.querySelector('.validerCodePromo').style.display = 'none'
                        document.querySelector('.btn-secondary').addEventListener('click',()=>{
                            code.value ="";
                        })
                    }
                })

    })


}
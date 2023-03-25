if (document.title === "Choix de l'abonnement") {

    function noBack(){window.history.forward()}
    noBack();
    window.onload=noBack;
    window.onpageshow=function(evt){if(evt.persisted)noBack()}
    window.onunload=function(){void(0)}

    let mois = document.querySelector('.form-control-range')
    let a = document.createElement('a')
    let input = document.createElement('input')
    let abonnement = document.querySelectorAll('.form-check-input')
    let zoneBouton = document.querySelector('.CGU');
    let idAbonnement;
    let codePromo = document.querySelector('#codePromo')
    let verfiier = document.querySelector('.btn-sm')
    let btnModal = document.querySelector('.btnAbo')
    let btnAmbassadeur = document.querySelector('#btnAmbassadeur')
    let reponseAbo =document.querySelector('.reponseAbo')
    let selectAboBtns = document.querySelectorAll('.selectAbo');
    for (let i = 0; i < selectAboBtns.length; i++) {
        selectAboBtns[i].addEventListener('click', (event) => {
                abonnement[i].checked = true
                abonne(abonnement[i].value)
        });
    }

    

    verfiier.addEventListener('click',()=>{

        if (codePromo !==""){
            fetch("/verfication/promoOtd",{
                method : 'POST',
                body : codePromo.value
            })
                .then((response)=>{
                    return response.json()
                })
                .then((response)=>{


                    if (response.existe==='promo'){
                        btnModal.click()
                        let p1 = document.createElement('p')
                        p1.classList.add('h6')
                        p1.innerHTML = "Votre code de réduction vous permet de bénéficier de "+response.remise + "%" + "sur l'abonement suivant : "+response.nom
                        reponseAbo.appendChild(p1)
                        document.querySelector('.validerAboCodePromo').addEventListener('click',()=>{
                            document.querySelector('#prixAbo'+response.id).innerText = response.prix
                            document.querySelector('#htABo'+response.id).innerHTML = "("+response.ht+" € TTC / mois"
                            document.querySelector('#inlineRadio'+response.id).checked = true
                            abonne(response.id)
                        })
                        document.querySelector('.refuserAboCodePromo').addEventListener('click',()=>{
                            codePromo.value ="";
                        })
                    }
                    else if(response.existe==="ambassadeur"){
                        btnAmbassadeur.click()
                        if (response.possible){
                            console.log(response)
                            document.querySelector('.zoneReponse').innerHTML = "<p class='h5'>Votre code vous permet d'accéder au statut d'ambassadeur de DIAG-DRONE</p>" +
                                "<p class='h5'>Avec l'abonnement : "+response.abonnement+" vous pouvez accéder aux fonctionnalités de l'application pour une durée de "+response.duree+" mois pour un prix de "+response.prix+" € HT par mois</p>"
                            document.querySelector(".validerAmbassadeur").style.display = 'block'
                            document.querySelector('.validerAmbassadeur').addEventListener('click',()=>{
                                fetch("/valider/amabassadeur",{
                                    method : 'POST',
                                    body : response.code
                                })
                                    .then((response)=>{
                                        return response.json()
                                    })
                                    .then((response)=>{
                                        if (response['reponse'] ==='ok'){
                                            document.location.href ="/conditions générales"
                                        }
                                        else{
                                            alert("Un problème est survenu lors de la validation de votre abonnement")
                                        }
                                    })
                            })
                        }
                    else {
                        document.querySelector('.zoneReponse').innerHTML = "<p class='h5'>Malheureusement ce code ne vous permet pas d'acceder au statut d'ambassadeur</p>"
                            document.querySelector(".validerAmbassadeur").style.display = 'none'
                            document.querySelector('.refusAmbassadeur').addEventListener('click',()=>{
                                codePromo.value ="";
                            })

                        }
                    }
                    else{
                        let p1 = document.createElement('p')
                        p1.classList.add('h6')
                        btnModal.click()
                        p1.innerHTML = "Votre code de réduction  n'existe pas."
                        reponseAbo.appendChild(p1)
                        document.querySelector('.refuserAboCodePromo').addEventListener('click',()=>{
                            codePromo.value ="";
                        })
                    }
                })
        }
    })
    function abonne(idAbonnement){
        let button = document.createElement('button');
        
       /* document.querySelectorAll('.blockSelect').forEach((aboDiv) => {
            if(idAbonnement == aboDiv.getAttribute('data-aboid')) {
                aboDiv.classList.add('cs');
                aboDiv.classList.remove('ls');
            } else {
                aboDiv.classList.remove('cs');
                aboDiv.classList.add('ls');
            }
        });*/

        while (zoneBouton.firstChild) {
            zoneBouton.removeChild(zoneBouton.lastChild)
        }


        button.classList.add('btn')
        button.classList.add('btn-maincolor')
        button.setAttribute('id', 'validerAboBtn')
        button.innerText = 'Valider'

        zoneBouton.appendChild(document.createElement('br'))
        zoneBouton.appendChild(button)


        let dureeAbonnement;
        let bouton = document.querySelector('#validerAboBtn')


        bouton.addEventListener('click', () => {
            if (!mois){
                dureeAbonnement=""
            }
            else{
                dureeAbonnement = mois.value
            }
            let corps = JSON.stringify({
                duree: dureeAbonnement,
                abonnement: idAbonnement,
                code:codePromo.value

            })


            fetch('/souscrire', {
                method: 'POST',
                body: corps
            })
                .then((reponse) => {
                    return reponse.json()
                })
                .then((reponse) => {
                    console.log(reponse)
                    if (reponse['etat'] == null) {
                        document.querySelector('.message').innerHTML = "<p class='h3'>" + reponse['message'] + "</p>"
                    }
                    else  {

                        document.location.href = '/conditions générales'
                    }

                })

        })
    }

    for (let i = 0; i < abonnement.length; i++) {
        abonnement[i].addEventListener('change', () => {
            abonne(abonnement[i].value)
        })
    }













}
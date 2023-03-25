if (document.title ==='Modifier mes infos'){
    let validerMail = document.querySelector('.modifEmail')
    let validerPassword = document.querySelector('.modifPassword')
    let validetTel = document.querySelector('.modifTelephone')
    let validerLogo = document.querySelector('.validerLogo')
    let validerAdresse = document.querySelector('.validerAdresse')
    let validerBandeau = document.querySelector('.validerBandeau')
    let validerSite = document.querySelector('.modifSite')
    let validerDistance = document.querySelector('.modifDistance')
    let validerTravaux = document.querySelector('.validerTravaux')
    let travaux = document.querySelectorAll('.checkPerso')


    validerMail.addEventListener('click',()=>{
        fetch('/modifChamp/email',{
            method :'POST',
            body : document.querySelector('#mail').value
        })
            .then((response)=>{
                return response.json()
            })
            .then((response)=>{

                document.querySelector('.closeMail').click()
                document.querySelector('.email').innerText = response

            })
    })
    validerPassword.addEventListener('click',()=>{
        fetch('/modifChamp/password',{
            method :'POST',
            body : document.querySelector('#password').value
        })
            .then((response)=>{
                return response.json()
            })
            .then((response)=>{

                document.querySelector('.closePassword').click()
                document.querySelector('.password').innerText = response

            })
    })
    validetTel.addEventListener('click',()=>{
        fetch('/modifChamp/telephone',{
            method :'POST',
            body : document.querySelector('#telephone').value
        })
            .then((response)=>{
                return response.json()
            })
            .then((response)=>{

                document.querySelector('.closeTelephone').click()
                document.querySelector('.telephone').innerText = response

            })
    })
    if (validerDistance){

        validerDistance.addEventListener('click',()=>{
            fetch('/modifChamp/distanceInter',{
                method :'POST',
                body : document.querySelector('#distanceInter').value
            })
                .then((response)=>{
                    return response.json()
                })
                .then((response)=>{

                    document.querySelector('.closeDistance').click()
                    document.querySelector('.distance').innerText = response

                })
        })
    }
    if (validerSite){

        validerSite.addEventListener('click',()=>{
            fetch('/modifChamp/siteWeb',{
                method :'POST',
                body : document.querySelector('#siteWeb').value
            })
                .then((response)=>{
                    return response.json()
                })
                .then((response)=>{

                    document.querySelector('.closeSite').click()
                    document.querySelector('.modifSite').innerText = response

                })
        })
    }

    if (validerBandeau){
        validerBandeau.addEventListener('click',()=>{
            let bandeau = document.querySelector('#bandeauPub')

            let contenu = new FormData();
            contenu.append('bandeau',bandeau.files[0])
            fetch('/modifChamp/bandeau',{
                method : 'POST',
                body : contenu
            })
                .then(response=>{
                    return response.json()
                })
                .then(response=>{
                    document.querySelector('.closeBandeau').click()
                    document.querySelector('.modifBandeau').innerText = response
                })
        })
    }



    validerLogo.addEventListener('click',()=>{

        let logo = document.querySelector('#logoInsti')
        let contenu = new FormData();
        contenu.append('logo',logo.files[0])
        fetch('/modifChamp/logoInsti',{
            method : 'POST',
            body : contenu
        })
            .then(response=>{
                return response.json()
            })
            .then(response=>{
                document.querySelector('.closeLogo').click()
                document.querySelector('.modifLogo').innerText = response
            })
    })

    validerAdresse.addEventListener('click',()=>{
       let contenu = JSON.stringify({
           numero : document.querySelector('#numero').value,
           voie : document.querySelector('#rue').value,
           cp : document.querySelector('#cp').value,
           ville : document.querySelector('#ville').value
       })
        fetch('/modifChamp/adresse',{
            method : 'POST',
            body : contenu
        })
            .then(response=>{
                return response.json()
            })
            .then(response=>{
                document.querySelector('.closeAdresse').click()
                let adresseModif = document.querySelectorAll('.adresseModif')
                adresseModif[0].innerText = response.numero
                adresseModif[1].innerText = response.voie
                adresseModif[2].innerText = response.cp
                adresseModif[3].innerText = response.ville
            })

    })

    validerTravaux.addEventListener('click',()=>{
        document.location.reload();
    })
    for (let i = 0; i < travaux.length; i++) {
        travaux[i].addEventListener('change',()=>{
            if (travaux[i].checked){
                raison = "ajout";
            }
            else{
                raison = "retrait"
            }
            fetch("/modifier/ChangeTravaux",{
                method : 'POST',
                body : JSON.stringify({
                    id:travaux[i].value,
                    changement : raison
                })
            })
        })
    }
    document.addEventListener('DOMContentLoaded',()=>{
        fetch("/modifier/recupererTravaux",{
            method : 'GET'
        })
            .then((response)=>{
                return response.json()
            })
            .then((response)=>{
                for (let i = 0; i < response.length; i++) {
                    for (let j = 0; j < travaux.length; j++) {
                        if ("id"+response[i] === travaux[j].id){

                            travaux[j].checked =true
                        }
                    }
                }
            })
    })
}
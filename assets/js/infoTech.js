if (document.title === 'Phase 2 de votre inscription' || document.title === 'Modifiez vos information techniques') {

    let listInter = document.querySelectorAll('.listeInter')
    let inter = document.querySelector('.listeInter')

    let perimetre = document.querySelector('#periInter');
    let bouton = document.querySelector('.btn-prs')
    if (listInter.length ===0){
         listInter = document.querySelectorAll('.interOdi')
    }
    if (!inter){
        inter = document.querySelector(".interOdi")
    }


    function envoieInterSalarie(url, id) {
        fetch(url, {
            method: 'POST',
            body: id
        })

    }

        document.addEventListener('DOMContentLoaded', () => {
            fetch('/recupererInter', {
                method: 'POST',
                body: JSON.stringify({
                    id:inter.dataset.salarie,
                    type : inter.dataset.type
                })
            })
                .then((reponse) => {
                    return reponse.json()
                })
                .then((reponse) => {

                    for (let k = 0; k < reponse.length; k++) {
                        for (let j = 0; j < listInter.length; j++) {

                            if (reponse[k] == listInter[j].value) {
                                listInter[j].checked = true
                            }
                        }
                    }
                })
        })








    for (let i = 0; i < listInter.length; i++) {
        listInter[i].addEventListener('click', () => {

            let contenu = JSON.stringify({
                idSalarie: listInter[i].dataset.salarie,
                idInter: listInter[i].value,
                type : listInter[[i]].dataset.type
            })

            if (listInter[i].checked === true) {
                envoieInterSalarie('/ajoutTypeInterSalarie', contenu)

            }
            if (listInter[i].checked ===false) {


                envoieInterSalarie('/removeTypeInterSalarie', contenu)
            }
        })

    }

    perimetre.addEventListener('keyup', () => {
        let corps = JSON.stringify({
            distance: perimetre.value,
            idsalarie: perimetre.dataset.salarie

        })
        envoieInterSalarie('/distanceInter', corps);
    })
    let honneur = document.querySelector('#honneur')
    if (honneur){
        honneur.addEventListener('change',()=>{
            fetch('/validerHonneur',{
                method : "GET"
            })
        })
    }
    bouton.addEventListener('click', (e) => {


        if (honneur){
           if (honneur.checked){

                       fetch('/distance', {
                           method: 'POST',
                           body: perimetre.dataset.salarie
                       })
                           .then((reponse) => {
                               return reponse.json()
                           })
                           .then((reponse) => {
                               if (reponse['perimetre'] === false) {
                                   let alert = confirm("Le perimètre d'intervention n'est pas correctement renseigné.Vous ne pourrez pas être proposé pour une mission")
                                   if (alert === false) {
                                       document.location.href = '/continuer/inscription/intervention/'
                                   }
                                   else if (alert === true && reponse['aerien'] === true) {
                                       document.location.href = '/licence/' + perimetre.dataset.salarie
                                   }


                               }
                               else if (reponse['perimetre'] === true && reponse['aerien'] ===true) {
                                   if (document.title ==="Modifiez vos information techniques"){

                                       document.location.href="/entreprise/changementValidé"
                                   }else{
                                       document.location.href = '/licence/' + perimetre.dataset.salarie
                                   }

                               }
                               else if (reponse['perimetre'] ===true && reponse['aerien'] === false && reponse['odi']===false) {
                                   if (document.title ==="Modifiez vos information techniques"){

                                       document.location.href="/entreprise/changementValidé"
                                   }else{
                                       document.location.href = '/entreprise/tauxHoraire/';
                                   }
                               }
                               else if (reponse['perimetre']===true && reponse['odi']===true){

                                   document.location.href ="/entreprise/rappelInter"

                               }


                           })

                       if (document.title ==="Modifiez vos information techniques"){
                           document.location.href="/entreprise/changementValidé"
                       }else{
                           document.location.href = '/entreprise/tauxHoraire/';
                       }




           }
           else {
               alert('Veuillez cocher la déclaration')
           }
        }
        else{
            for (let i = 0; i < listInter.length; i++) {
                if (listInter[i].checked) {

                    fetch('/distance', {
                        method: 'POST',
                        body: perimetre.dataset.salarie
                    })
                        .then((reponse) => {
                            return reponse.json()
                        })
                        .then((reponse) => {
                            if (reponse['perimetre'] === false) {
                                let alert = confirm("Le perimètre d'intervention n'est pas correctement renseigné.Vous ne pourrez pas être proposé pour une mission")
                                if (alert === false) {
                                    document.location.href = '/continuer/inscription/intervention/'
                                }
                                else if (alert === true && reponse['aerien'] === true) {
                                    document.location.href = '/licence/' + perimetre.dataset.salarie
                                }


                            }
                            else if (reponse['perimetre'] === true && reponse['aerien'] ===true) {
                                if (document.title ==="Modifiez vos information techniques"){

                                    document.location.href="/entreprise/changementValidé"
                                }else{
                                    document.location.href = '/licence/' + perimetre.dataset.salarie
                                }

                            }
                            else if (reponse['perimetre'] ===true && reponse['aerien'] === false && reponse['odi']===false) {
                                if (document.title ==="Modifiez vos information techniques"){

                                    document.location.href="/entreprise/changementValidé"
                                }else{
                                    document.location.href = '/entreprise/tauxHoraire/';
                                }
                            }
                            else if (reponse['perimetre']===true && reponse['odi']===true){

                                document.location.href ="/entreprise/rappelInter"

                            }


                        })
                } else {
                    if (document.title ==="Modifiez vos information techniques"){
                        document.location.href="/entreprise/changementValidé"
                    }else{
                        document.location.href = '/entreprise/tauxHoraire/';
                    }

                }

            }
        }

    })


}
if (document.title === 'Licence') {
    let numero = document.querySelector('.numeroLicence')
    let password = document.querySelector(".passwordAT")
    let identifiantAT = document.querySelector('.identifiantAT')
    let realLicence = document.querySelector('#realLicence');
    let buttonLicence = document.querySelector('#licence')
    let buttonJustificatif = document.querySelector('#justificatif')
    let realJustificatif = document.querySelector('.justificatifAT')
    let dateValidite = document.querySelector('.validite')
    let exploitant = document.querySelector('#exploitant')
    let date = new Date();

    buttonLicence.addEventListener('click', () => {
        realLicence.click();
        realLicence.addEventListener('change', () => {
            buttonLicence.innerHTML = realLicence.files[0]['name']

        })
    })
    dateValidite.addEventListener('change',()=>{
        if (Date.parse(dateValidite.value)<date.getTime()){
            dateValidite.value = date.getFullYear()+"-"+date.getMonth()+"-"+date.getDay()
            console.log(date.getFullYear()+"-"+date.getUTCMonth()+"-"+date.getDay())

        }

    })
    buttonJustificatif.addEventListener('click', () => {
        realJustificatif.click();
        realJustificatif.addEventListener('change', () => {
            buttonJustificatif.innerHTML = realJustificatif.files[0]['name']
        })
    })
    let valider = document.querySelector('.btn-maincolor');
    let form = new FormData();
    valider.addEventListener('click', () => {
        if (dateValidite.value != null && realJustificatif.files[0]) {

            if (realLicence.files[0] && numero.value !="" && exploitant.value !="") {
                if (realLicence.files[0].type === 'application/pdf') {
                    form.append('fichier', realLicence.files[0])
                    form.append('numero', numero.value)
                    form.append('password', password.value)
                    form.append('identifiant', identifiantAT.value)
                    form.append('salarie', valider.dataset.salarie)
                    form.append('exploitant',exploitant.value)
                    form.append('validite', dateValidite.value)
                    form.append('justificatif', realJustificatif.files[0])
                    fetch('/Enregistrerlicence', {
                        method: 'POST',
                        body: form
                    })
                        .then(() => {
                            document.location.href = '/entreprise/tauxHoraire/';

                        })

                }
                else {
                    alert('La licence doit être sous un format PDF')
                }
            }
            else {
                alert('Veillez enregistrer votre licence DGAC ainsi que son numéro')
            }
        }
        else {
            alert('Veuillez renseigner votre justificatif de formation Alpha Tango ainsi que sa date de validité ')
        }
    })

}

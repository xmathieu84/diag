if (document.title==="Contraintes interventions"){

    let nbreContrainte = document.querySelectorAll('.nbreContrainte')
    let ajoutContrainte = document.querySelectorAll('.ajoutContrainte')
    let zoneAjout = document.querySelectorAll('.zoneAjout')
    let validerContrainte = document.querySelectorAll('.validerContrainte')

    let envoie=[];
    let enfant1 = "<label >Contrainte</label>\n" +
        "                                <select   class=\"form-control form-control-lg type\">\n" +
        "                                    <option value=\"\" disabled selected>Sélectionne la contrainte</option>\n" +
        "                                    <option value=\"Espace aérien\">Espace aérien</option>\n" +
        "                                    <option value=\"Aérodrome-Aéroport (AIP)\">Aérodrome-Aéroport (AIP)</option>\n" +
        "                                    <option value=\"Zone urbaine\">Zone urbaine</option>\n" +
        "                                    <option value=\"Zone densément peuplée\">Zone densément peuplée</option>\n" +
        "                                    <option value=\"Route\">Route</option>\n" +
        "                                    <option value=\"Antenne\">Antenne</option>\n" +
        "                                    <option value=\"Chemin de fer\">Chemin de fer</option>\n" +
        "                                    <option value=\"Lignes électriques\">Lignes électriques</option>\n" +
        "                                    <option value=\"Réserve naturelle\">Réserve naturelle</option>\n" +
        "                                    <option value=\"Zone militaire (VOL TAC , SETBA , SEBAH)\">Zone militaire (VOL TAC , SETBA , SEBAH)</option>\n" +
        "                                    <option value=\"Notam\">Notam</option>\n" +
        "                                    <option value=\"Construction isolée\">Construction isolée</option>\n" +
        "                                    <option value=\"Photo / Vidéo interdite\">Photo / Vidéo interdite</option>\n" +
        "                                </select>"
    let enfant2 = " <label for=\"altitude\">Altitude</label>\n" +
        "                                <input type=\"text\" class=\"form-control form-control-lg altitutde\">"
    let enfant3 =" <label for=\"specificite\">Spécificité (non obligatoire)</label>\n" +
        "            <input type=\"text\" class=\"form-control form-control-lg specificite\">";
    for (let i = 0; i < ajoutContrainte.length; i++) {
        ajoutContrainte[i].addEventListener('click',()=>{
            for (let j = 0; j < nbreContrainte[i].value; j++) {
                let div1 = document.createElement('div')
                div1.classList.add('col-4')
                div1.innerHTML = enfant1
                let div2 = document.createElement('div')
                div2.classList.add('col-4')
                div2.innerHTML = enfant2
                let div3 = document.createElement('div')
                div3.innerHTML = enfant3
                zoneAjout[i].appendChild(div1)
                zoneAjout[i].appendChild(div2)
                zoneAjout[i].appendChild(div3)
            }
        })
    }

    for (let i = 0; i < validerContrainte.length; i++) {
        validerContrainte[i].addEventListener('click',()=>{
            let type = document.querySelectorAll('.type')
            let altitude = document.querySelectorAll('.altitutde')
            let specificite = document.querySelectorAll('.specificite')
            for (let j = 0; j < altitude.length; j++) {
                envoie.push({
                        alt:altitude[j].value,
                        type : type[j].value,
                        speci: specificite[j].value
                }

                )
            }

            let confirmer = confirm('On envoie en BDD ?')
            if (confirmer){
                fetch("/administrateur/ajoutContraintInter/"+validerContrainte[i].dataset.inter,{
                    method : 'POST',
                    body : JSON.stringify({
                        content : envoie
                    })


                })
                    .then(()=>{
                        alert("Les contraintes ont été enregistrées. La page va maintenant être actualisée")
                        document.location.reload()
                    })
            }




        })
    }


}
if (document.title==="Voir contrainte"){
    let supprimer = document.querySelectorAll('.deleteContrainte')
    let zoneContr = document.querySelectorAll('.zoneContr')
    let zoneAlt = document.querySelectorAll('.zoneAlt')
    let zoneBtn = document.querySelectorAll('.zoneBtn')
    let zoneSpec = document.querySelectorAll('.zoneSpec')
    let typeM = document.querySelectorAll('.typeM')
    let altM = document.querySelectorAll('.altitutdeM')
    let specM = document.querySelectorAll('.specificiteM')
    let valider = document.querySelector('.btn-success')
    for (let i = 0; i < supprimer.length; i++) {
        supprimer[i].addEventListener('click',()=>{
            let confirmer = confirm("Souhaite tu supprimer cette contrainte de vol ?")
            if (confirmer){
                fetch("/administrateur/supprimerContrainte/"+supprimer[i].dataset.contrainte,{
                    method : 'GET'
                })
                    .then(()=>{
                        alert('Contrainte de vol supprimée')
                        document.querySelector('.zoneModif').removeChild(zoneBtn[i])
                        document.querySelector('.zoneModif').removeChild(zoneContr[i])
                        document.querySelector('.zoneModif').removeChild(zoneAlt[i])
                        document.querySelector('.zoneModif').removeChild(zoneSpec[i])
                    })


            }
        })

    }
    valider.addEventListener('click',()=>{
        let confirmer =confirm("On envoie en base de données ?")
         if (confirmer){
             let envoie=[];
             let type = document.querySelectorAll('.type')
             let altitude = document.querySelectorAll('.altitutde')
             let specificite = document.querySelectorAll('.specificite')
             for (let j = 0; j < altitude.length; j++) {
                 envoie.push({
                         alt:altitude[j].value,
                         type : type[j].value,
                        speci: specificite[j].value
                     }

                 )
                 fetch("/administrateur/ajoutContraintInter/"+altitude[j].dataset.inter,{
                     method : 'POST',
                     body : JSON.stringify({
                         content : envoie
                     })


                 })
                     .then(()=>{
                         alert("La contrainte de vol a été enregistrée. La page va maintenant être actualisée")
                         document.location.reload()
                     })
             }
         }



    })
    for (let i = 0; i < typeM.length; i++) {
        typeM[i].addEventListener('change',()=>{
            fetch('/administrateur/modifierContrainte/'+typeM[i].dataset.id,{
                method : 'POST',
                body : JSON.stringify({
                    modif : typeM[i].dataset.type,
                    valeur : typeM[i].value
                })
            })
        })
        altM[i].addEventListener('keyup',()=>{
            fetch('/administrateur/modifierContrainte/'+altM[i].dataset.id,{
                method : 'POST',
                body : JSON.stringify({
                    modif : altM[i].dataset.type,
                    valeur : altM[i].value
                })
            })
        })
        specM[i].addEventListener('keyup',()=>{
            fetch('/administrateur/modifierContrainte/'+specM[i].dataset.id,{
                method : 'POST',
                body : JSON.stringify({
                    modif : specM[i].dataset.type,
                    valeur : specM[i].value
                })
            })
        })
    }

}
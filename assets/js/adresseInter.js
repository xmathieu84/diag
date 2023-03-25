if (document.title==="Demande d'intervention (phase 2)" || "Etape 2 du diagnostic"){
    let radioAdresse = document.getElementsByName('adresse_inter[sameFact]')
    let code = document.querySelector('.codeSyndic')
    let numero = document.querySelector('#adresse_inter_numero')
    let voie = document.querySelector('#adresse_inter_nomVoie')
    let cp = document.querySelector('#adresse_inter_codePostal')
    let ville = document.querySelector('#adresse_inter_ville')
    let lien = null;
    if (code){
         lien = "/recupereAdresseInter/"+code.value
    }
    else{
        lien =  "/recupereAdresseInter"
    }
    for (let i = 0; i < radioAdresse.length; i++) {
        radioAdresse[i].addEventListener('change',()=>{
            if (radioAdresse[i].value ==='Oui'){
                fetch(lien,{
                    method:'GET'
                })
                    .then((response)=>{
                        return response.json()
                    })
                    .then((response)=>{
                         numero.value = response.numero
                        voie.value = response.nomVoie
                        cp.value = response.codePostal
                        ville.removeAttribute("readonly")
                        ville.value = response.ville
                    })
            }
            else {
                numero.value = ""
                voie.value = ""
                cp.value = ""
                ville.setAttribute('readonly','readonly')
                ville.value = ""
            }
        })
    }
}
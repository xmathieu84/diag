if (document.title =="Je recherche un drone"){
    let listeInter = document.querySelector("#listeInter")
    let typeInter = document.querySelector('#typeInter')
    let envoyer = document.querySelector('.btn-outline-maincolor');

    let adresse = document.querySelector('#adresse')

    adresse.addEventListener('keyup',()=>{
        if (adresse.value.length >=5){

            fetch("https://api-adresse.data.gouv.fr/search/?q="+adresse.value+"&autocomplete=1&limit=20",{
                method :'GET'
            })
                .then((response)=>{
                    return response.json()
                })
                .then((response)=>{
                    let datalist = document.getElementById('listAdresse')

                   while (datalist.lastChild){
                        datalist.removeChild(datalist.firstChild)
                    }
                    for (let i = 0; i < response.features.length; i++) {
                        let option = document.createElement('option')
                        option.value = response.features[i].properties.label
                        option.style.color = 'black'
                        datalist.appendChild(option)

                    }

                })
        }
    })
    listeInter.addEventListener('change',()=>{
        fetch('/selectTypeInter',{
            method:'POST',
            body:listeInter.value
        })
            .then((response)=>{
                return response.json()
            })
            .then((response)=>{
                while (typeInter.firstChild){
                    typeInter.removeChild(typeInter.lastChild)
                }
                for (let i = 0; i <response.length ; i++) {
                    let option = document.createElement('option')
                    option.value = response[i].id
                    option.innerHTML = response[i].nom
                    typeInter.appendChild(option)
                }
            })
    })

    envoyer.addEventListener('click',()=>{

        let contenu = JSON.stringify({
            adress : adresse.value,

            idListeInter : listeInter.value,
            idTypeInter : typeInter.value

        })
        fetch("/matchOtd",{
            method:'POST',
            body : contenu
        })
            .then((response)=>{
                return response.json()
            })
            .then((response)=>{
                if (response>0){
                    document.querySelector('.btn-masque').click()
                    document.querySelector(".message").innerHTML = "Diag-drone a détecté "+ response +" Opérateurs télepilotes de drone pour votre demande d'intervention.";
                }
            })
    })
}
if (document.title ==="Modifier licence"){
        function changeButton(idFakeButton,idRealButton){
            document.getElementById(idFakeButton).addEventListener('click',()=>{
                console.log(idFakeButton)
                document.getElementById(idRealButton).click()
                document.getElementById(idRealButton).addEventListener('change',()=>{
                    document.getElementById(idFakeButton).innerText = document.getElementById(idRealButton).files[0]['name']
                })
            })
        }
        changeButton("licence","realLicence")
    changeButton("justificatif","realJustificatif")

    let licence = document.querySelector('#realLicence')
    let justificatif = document.querySelector("#realJustificatif")
    let exploitant = document.querySelector("#exploitant")
    let numeroLicence = document.querySelector('#numeroLicence')
    let dateExpi = document.querySelector(".validite")
    function envoieFile(file){
            file.addEventListener('change',()=>{
                let content = new FormData()
                content.append('fichier',file.files[0])
                content.append('salarie',document.querySelector('#salarie').value)

                fetch("/saveFile/"+file.dataset.type,{
                    method:'POST',
                    body : content
                })
                    .then((response)=>{
                        return response.json()
                    })
                    .then((response)=>{
                        if (response ==="catt" ){
                            if (exploitant.value !="" && numeroLicence.value !=""){
                                document.querySelector('#dsac').value = true
                            }
                        }
                        if (response==="alpha"){
                            if (dateExpi.value !==""){
                                document.querySelector("#catt").value = true
                            }
                        }
                    })
            })
    }

    function envoieNumero(donnee){
         donnee.addEventListener('keyup',()=>{
             fetch("/saveNumberLicence/"+donnee.dataset.type,{
                 method : "POST",
                 body : JSON.stringify({
                     id : document.querySelector('#salarie').value,
                     donnee : donnee.value
                 })
             }).then((reponse)=>{
                 return reponse.json()
             })
                 .then((reponse)=>{
                     if (reponse ==="aptitutde" || reponse ==="exploitant"){
                         if (licence.files[0] && exploitant.value !=="" && numeroLicence.value !==""){
                             document.querySelector("#dsac").value = true
                         }
                     }

                 })
         })
    }
    dateExpi.addEventListener('change',()=>{
        fetch("/saveNumberLicence/"+dateExpi.dataset.type,{
            method : "POST",
            body : JSON.stringify({
                id : document.querySelector('#salarie').value,
                donnee : dateExpi.value
            })
        }).then((reponse)=>{
            return reponse.json()
        })
            .then((reponse)=>{
                if (reponse ==="validite"){
                    if (justificatif.file[0]){
                        document.querySelector("#catt").value = true
                    }
                }
            })
    })
    envoieFile(licence)
    envoieFile(justificatif)
    envoieNumero(numeroLicence)
    envoieNumero(exploitant)

    document.querySelector('.btn-maincolor').addEventListener('click',()=>{
        console.log(document.querySelector('#catt').value ==="true" && document.querySelector("#dsac").value==='true')
        if (document.querySelector('#catt').value ==="true" && document.querySelector("#dsac").value==='true'){
                document.location.href="/entreprise/changementValidé"
        }
        else{
            let confirmation = confirm("Toutes les informations ne sont pas renseignées. Souhaitez vous quitter?")
            if (confirmation){
                document.location.href="/entreprise"
            }
        }
    })
}
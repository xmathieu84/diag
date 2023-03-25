if (document.title ==="Vos assurances"){
    let compagnie = document.querySelector('#compagnie')
    let contrat = document.querySelector('#contrat')
    let attestation = document.querySelector('#attestation')
    let compagnieComplement = document.querySelector('#compagnieComplement')
    let contratComplement = document.querySelector('#contratComplement')
    let attestationComplement = document.querySelector('#attestationComplement')
    function changeButton(idRealButton,idFakeButton){
        document.getElementById(idFakeButton).addEventListener('click',()=>{
            document.getElementById(idRealButton).click()
            document.getElementById(idRealButton).addEventListener('change',()=>{
                document.getElementById(idFakeButton).innerHTML = document.getElementById(idRealButton).files[0]['name']
            })
        })
    }
    changeButton("attestation","assu")
    changeButton("rc_pro2_fichier","complement")
    changeButton("attestationComplement","assuComplement")
    function envoieAssu(infoAssurance){
        let content;
        let event;
        if (infoAssurance.dataset.type ==="attestation"){
            event = "change"
        }
        else{
            event = "keyup"
        }
        infoAssurance.addEventListener(event,()=>{
            if (infoAssurance.dataset.type ==="attestation"){
                content = new FormData();
                content.append('attestation',infoAssurance.files[0])
            }
            else{
                content = infoAssurance.value
            }
            fetch("/changeAssurancePrincipale/"+infoAssurance.dataset.type,{
                method:'POST',
                body : content
            })
        })
    }

    envoieAssu(attestation)
    envoieAssu(compagnie)
    envoieAssu(contrat)

    let form = document.querySelector('form')
    let validerForm = document.querySelector('.validerForm')

    validerForm.addEventListener('click',()=>{
        console.log(document.querySelector("#rc_pro2_fichier").files[0])
        if (document.querySelector("#rc_pro2_fichier").files[0]!==undefined && document.querySelector("#rc_pro2_compagnie").value !=="" && document.querySelector("#rc_pro2_numero").value !==""){
                form.submit()
        }
        else{
            alert("Un ou plusieurs éléments sont manquant")
        }
    })

    function envoieAssuComplement(infoAssurance){
        let content;
        let event;
        if (infoAssurance.dataset.type ==="attestation"){
            event = "change"
        }
        else{
            event = "keyup"
        }
        infoAssurance.addEventListener(event,()=>{
            if (infoAssurance.dataset.type ==="attestation"){
                content = new FormData();
                content.append('attestation',infoAssurance.files[0])
            }
            else{
                content = infoAssurance.value
            }
            fetch("/chnageAssuranceComplementaire/"+infoAssurance.dataset.type,{
                method:'POST',
                body : content
            })
        })
    }
    envoieAssuComplement(compagnieComplement)
    envoieAssuComplement(contratComplement)
    envoieAssuComplement(attestationComplement)

    document.querySelector('.btn-danger').addEventListener('click',()=>{
        let confirmer = confirm("Souhaites vous supprimer définitivement votre C Professionnelle complémentaire type aérienne-aviation ?")
        if (confirmer){
            fetch("/supprimerRcComplement/"+ document.querySelector('.btn-danger').dataset.type,{
                method :'GET'
            })
                .then(()=>{
                    document.location.reload()
                })
        }
    })
}
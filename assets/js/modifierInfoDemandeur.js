if (document.title ==='Mes informations'){
    let validerAdresse = document.querySelector('.validerAdresse')
    let validerTva = document.querySelector('.validerTva')
    let validerSiret = document.querySelector('.validerSiret')
    let numero= document.querySelector('#numero')
    let voie = document.querySelector('#voie')
    let cp = document.querySelector('#cp')
    let ville= document.querySelector('#ville')
    let validerTel = document.querySelector('.validerTelephone')
    validerAdresse.addEventListener('click',()=>{
        if (voie.value !=="" && cp.value !=="" && ville.value !==""){
            let contenu = JSON.stringify({
                num : numero.value,
                rue : voie.value,
                code : cp.value,
                city : ville.value
            })
            fetch("/modifierAdresseDemandeur",{
                method : 'POST',
                body : contenu
            })
                .then(()=>{
                    document.location.reload()
                })
        }
    })

    validerTel.addEventListener('click',()=>{
        let numero = document.querySelector('#telephone')
        if (numero.value.length === 10){
            fetch("/modifTelDemandeur",{
                method : 'POST',
                body : numero.value
            })
                .then(()=>{
                    document.location.reload()
                })
        }
    })
    if (validerTva){
        validerTva.addEventListener('click',()=>{
            let confirmer = confirm("Souhiatez vous enregister ce nouveau numéro de TVA intracommunautaire ?")
            if (confirmer){
                fetch("/modifTvaDemandeur",{
                    method : 'post',
                    body : document.querySelector("#tva").value
                })
                    .then(()=>{document.location.reload()})
            }
        })
    }
    if (validerSiret){
        validerSiret.addEventListener('click',()=>{
            let confirmer = confirm("Souhaitez vous enregister ce nouveau numéro de SIRET ?")
            if (confirmer){
                fetch("/modifSiretDemandeur",{
                    method : 'post',
                    body : document.querySelector('#siret').value
                })
                    .then(()=>{document.location.reload()})
            }
        })
    }
}
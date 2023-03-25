if (document.title ==="Changer mes coordonnées bancaires"){
    let iban = document.querySelector('#iban').value
    let bic = document.querySelector('#bic').value
    let adresse = document.querySelector('#adresse').value
    let nomBanque = document.querySelector('#nom').value
    let valider = document.querySelector('.btn-maincolor')
    valider.addEventListener('click',()=>{
        console.log(iban.length)
        let body = JSON.stringify({
            iban:iban,
            bic:bic,
            adresse:adresse,
            nom : nomBanque
        })
        if (iban.length ===27){

            fetch('/modifRouteGc',{
                body:body,
                method:'POST'
            })
                .then((response)=>{
                    return response.json()
                })
                .then((response)=>{
                    if (response ==='non valide'){
                        alert('Numéro iban inchangé')
                    }
                    else {
                        document.location.href = "/sepa";
                    }

                })
        }else{
            alert("Numéro iban invalide")
        }

    })
}
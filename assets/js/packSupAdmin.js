if (document.title ==="Pack supplémentaire grand compte"){
    let newNom = document.querySelector('.newNom')
    let newUser = document.querySelector('.newUser')
    let newPrix = document.querySelector('.newPrix')
    let profil = document.querySelector('#profil')
    let type = document.querySelector('#type')
    let valider = document.querySelector('.btn-outline-success')

    valider.addEventListener('click',()=>{
        let alerte = confirm("Est ce que tout les champs sont remplis ?")
        if (alerte){
            let content = JSON.stringify({
                type : type.value,
                profil : profil.value,
                prix : newPrix.value,
                user : newUser.value,
                nom : newNom.value
            })
            fetch("/administrateur/newPAckGc",{
                method : 'POST',
                body :content
            })
                .then(()=>{
                    alert('Le nouveau pack est enregistré.')
                    document.location.reload()
                })
        }
    })

    //modification d'un pack

    let idPack = document.querySelectorAll('.idPack')
    let nomPack = document.querySelectorAll('.nomPack')
    let prixPack = document.querySelectorAll('.prixPack')
    let userPack = document.querySelectorAll('.userPack')

    function changeInfoPack(nomChange){
        for (let i = 0; i < nomChange.length; i++) {
            nomChange[i].addEventListener('keyup',()=>{
                switch (nomChange){
                    case  nomPack : {
                        type ='nom';
                        break
                    }
                    case  prixPack : {
                        type = 'prixPack'
                        break
                    }
                    case  userPack : {
                        type = 'userPack'
                        break
                    }

                }
                let content = JSON.stringify({
                    typeChange : type,
                    id : idPack[i].dataset.id,
                    valeur : nomChange[i].value
                })
                fetch("/administrateur/modifPack",{
                    method :'POST',
                    body : content
                })
            })
        }
    }
    changeInfoPack(nomPack)
    changeInfoPack(prixPack)
    changeInfoPack(userPack)
}
if (document.title ==="Packs supplémentaires institution"){
    let newNom = document.querySelector('.newNom')
    let newUser = document.querySelector('.newUser')
    let newPrix = document.querySelector('.newPrix')
    let newProfil = document.querySelector('#newProfil')
    let newLimiteH = document.querySelector('.newlimiteH')
    let newLimiteB = document.querySelector('.newlimiteB')
    let valider = document.querySelector('.btn-success')

    newProfil.addEventListener('change',()=>{
        if (newProfil.value !=='insti'){
            newLimiteH.value = 0
            newLimiteB.value = 0
            newLimiteB.setAttribute('disabled','disabled')
            newLimiteH.setAttribute('disabled','disabled')
        }
        else {
            newLimiteB.removeAttribute('disabled')
            newLimiteH.removeAttribute('disabled')
        }

    })
    valider.addEventListener('click',()=>{
        let content = JSON.stringify({
            nom : newNom.value,
            user : newUser.value,
            prix : newPrix.value,
            profil : newProfil.value,
            limiteH : newLimiteH.value,
            limiteB : newLimiteB.value,
        })
        let alerte = confirm("Est ce que tout les champs sont correctement remplis ?")
        if (alerte){
            fetch("/administrateur/newPackInsti",{
                method:'POST',
                body : content
            })
                .then(()=>{
                    alert("Le pack est enregistré , il est disponible immédiatement pour les institutionnels")
                    document.location.reload()
                })
        }
    })

    //Modofication des Pack existants

    let idPack = document.querySelectorAll('.idpack')
    let nomPack = document.querySelectorAll('.nomPack')
    let prixPack = document.querySelectorAll('.prixPack')
    let userPack = document.querySelectorAll('.userPack')
    let limiteBPack = document.querySelectorAll('.limiteBPack')
    let limiteHPack = document.querySelectorAll('.limiteHPack')
    let type;
    function changeInfoPack(nomChange){
        for (let i = 0; i < nomChange.length; i++) {
            nomChange[i].addEventListener('keyup',()=>{
                switch (nomChange){
                    case  nomPack : {
                        type ='nom';
                        break
                    }
                    case  prixPack : {
                        type = 'prixPack'
                        break
                    }
                    case  userPack : {
                        type = 'userPack'
                        break
                    }
                    case  limiteBPack : {
                        type = 'limiteBPack'
                        break
                    }
                    case  limiteHPack : {
                        type = 'limiteHPack'
                        break
                    }

                }
                let content = JSON.stringify({
                    typeChange : type,
                    id : idPack[i].dataset.id,
                    valeur : nomChange[i].value
                })
                fetch("/administrateur/changePackInsti",{
                    method :'POST',
                    body : content
                })
            })
        }
    }
    changeInfoPack(nomPack)
    changeInfoPack(prixPack)
    changeInfoPack(userPack)
    changeInfoPack(limiteBPack)
    changeInfoPack(limiteHPack)

}
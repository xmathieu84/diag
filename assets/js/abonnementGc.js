if (document.title ==="Abonnements grand compte"){
    let nomAbonnement = document.querySelectorAll('.nomAbo')
    let prixAbonnement = document.querySelectorAll('.prixAbo')
    let idAbo = document.querySelectorAll('.aboGc')
    let type;
    function changeInfoAbonnement(nomChange){
        for (let i = 0; i < nomChange.length; i++) {
            nomChange[i].addEventListener('keyup',()=>{
                switch (nomChange){
                    case  nomAbonnement : {
                        type ='nom';
                        break
                    }
                    case  prixAbonnement : {
                        type = 'prixAbo'
                        break
                    }

                }
                let content = JSON.stringify({
                    typeChange : type,
                    id : idAbo[i].dataset.id,
                    valeur : nomChange[i].value
                })
                fetch("/administrateur/modifAbonneGc",{
                    method :'POST',
                    body : content
                })
            })
        }
    }
    changeInfoAbonnement(nomAbonnement)
    changeInfoAbonnement(prixAbonnement)

    //Création nouvel abonnement Grand compte

    let prix = document.querySelector('.newPrix')
    let nom = document.querySelector('.newAbo')
    let utilisateur = document.querySelector('#utilisateur')
    let profil = document.querySelector('.newProfil')
    let valider = document.querySelector('.btn-success')
     valider.addEventListener('click',()=>{
         let contenu = JSON.stringify({
             prixAbo : prix.value,
             nom : nom.value,
             user : utilisateur.value,
             profil : profil.value
         })
         fetch("/administrateur/creerNouvelAbonnement",{
             method : "POST",
             body : contenu
         }).then((response)=>{
             return response.json()
         })
             .then((response)=>{
                 if(response==='ok'){
                     alert("Nouvel abonnement grand compte crée. Previens un dévellopeur pour la création du profil")
                     document.location.reload();
                 }
                 else{
                     alert(response)
                 }

             })
     })
}
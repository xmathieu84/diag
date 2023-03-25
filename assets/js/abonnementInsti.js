if (document.title ==="Administrateur abonnement institution"){
   let nomAbo = document.querySelectorAll('.nomAbo')
   let prixAbo = document.querySelectorAll('.prixAbo')
   let profilAbo = document.querySelectorAll('.profilAbo')
   let limiteB = document.querySelectorAll('.limiteB')
   let limiteH = document.querySelectorAll('.limiteH')
   let userAbo = document.querySelectorAll('.userAbo')
   let idAbo = document.querySelectorAll('.idAbo')
   let dureeAbo = document.querySelectorAll(".dureeAbo")
   let type;
   function changeInfoAbonnement(nomChange){
      for (let i = 0; i < nomChange.length; i++) {
         nomChange[i].addEventListener('keyup',()=>{
            switch (nomChange){
               case  nomAbo : {
                  type ='nom';
                  break
               }
               case  prixAbo : {
                  type = 'prixAbo'
                  break
               }
               case  profilAbo : {
                  type ='profilAbo';
                  break
               }
               case  limiteB : {
                  type = 'limiteB'
                  break
               }
               case  limiteH : {
                  type ='limiteH';
                  break
               }
               case  userAbo : {
                  type = 'userAbo'
                  break
               }
               case  dureeAbo : {
                  type = 'dureeAbo'
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
   changeInfoAbonnement(nomAbo)
   changeInfoAbonnement(prixAbo)
   changeInfoAbonnement(profilAbo)
   changeInfoAbonnement(limiteB)
   changeInfoAbonnement(limiteH)
   changeInfoAbonnement(userAbo)
   changeInfoAbonnement(dureeAbo)

   //Nouvel abonnement institutionnel

   let valider = document.querySelector('.btn-success')
   let newPrix = document.querySelector('.newPrice')
   let newProfil = document.querySelector('.newProfil')
   let newlimiteH = document.querySelector('.newLimiteH')
   let newLimiteB = document.querySelector('.newLimiteB')
   let newUser = document.querySelector('.newUser')

   valider.addEventListener('click',()=>{
      let alarm = confirm("Avez vous verifié tout les éléments du nouvel abonnement ?")
      if (alarm){
         let contenu = JSON.stringify({
            prix: newPrix.value,
            profil :newProfil.value,
            limiteHaute : newlimiteH.value,
            limiteBasse : newLimiteB.value,
            user : newUser.value,

            nom : document.querySelector('.newNom').value
         })
         fetch("/administrateur/newAbonnementInsti",{
            method : 'POST',
            body : contenu
         })
             .then(()=>{
                alert("Le nouvel abonnement est enregistré . Si un nouveau profil est crée , il faut prévenir un développeur pour créer ce profil lors de l'inscription")
                document.location.reload()
             })
      }
   })
}
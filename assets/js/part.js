if (document.title === 'Inscription') {
  let btnInsti = document.querySelector('#btnInsti')
  let dem =  document.getElementById('demandeur_profil')
  let btnGc = document.querySelector('#btnGc')
  let habitant;
  let codePRm = document.querySelector('#demandeur_infoCodeProm_0')
  let zoneCodePRomo = document.querySelector('.codePrm')
  if (codePRm){
    codePRm.addEventListener('click',()=>{
      if (codePRm.checked){
        zoneCodePRomo.style.display ='block'
      }
      else {
        zoneCodePRomo.style.display ='none'
      }
    })
  }
  function retourAbonnement(nombre,route,profil){
    let body = JSON.stringify({
      hab : nombre,
      prof: profil
    })
    fetch(route,{
      body : body,
      method : 'POST'
    })
        .then(response=>{
          return response.json()
        })
        .then(response=>{

          let zoneAbo = document.querySelector('.resultAbonnement')
          document.querySelector('#prixAbo').innerHTML = response.prix+' € HT ' + '('+Math.round(response.ttc*100)/100+' € TTC)'
          document.querySelector('#utilisateur').innerHTML = response.user;
          document.querySelector('#btnAboI').click()
          while(zoneAbo.firstChild){
            zoneAbo.removeChild(zoneAbo.lastChild)
          }
          let pAbonnement = document.createElement('p')
          pAbonnement.classList.add('rappelAbo')
          pAbonnement.innerHTML = "Coût de l'abonnement : "+response.prix+' € HT ' + '('+Math.round(response.ttc*100)/100+' € TTC)'
          let pUtilisateur = document.createElement('p')
          pUtilisateur.innerHTML ="Utilisateur(s) : "+ response.user
          pUtilisateur.classList.add('rappelAbo')
          zoneAbo.appendChild(pUtilisateur)
          zoneAbo.appendChild(pAbonnement)
          zoneAbo.style.background = "#FFDD00"

        })
  }


    let type = document.querySelector('.d-inline').dataset.type

    if (type !== 'institutionnel' && type !=='Grand compte') {
      dem.addEventListener('change', function () {

        if (dem.value === '1') {
          document.querySelector('.siret').style.display = 'none'
          document.querySelector('.tva').style.display = 'none'
          document.querySelector('#demandeur_siretTva_siret').removeAttribute('required')
          document.querySelector('#demandeur_siretTva_tva').removeAttribute('required')
          document.querySelector('.nomPro').style.display = 'none'
          document.querySelector('#demandeur_nom').removeAttribute('required')


        }
        if (dem.value !== '1') {


          document.querySelector('.siret').style.display = 'block'
          document.querySelector('.tva').style.display = 'block'
          document.querySelector('.nomPro').style.display = 'block'
          document.querySelector('#demandeur_siretTva_siret').setAttribute('required','required')
          document.querySelector('#demandeur_siretTva_siret').setAttribute('required','required')
          document.querySelector('#demandeur_nom').setAttribute('required','required')
          if (dem.value === '3'){

            btnInsti.click()
          }
          if (dem.value == 2 || dem.value ==4 || dem.value == 5 || dem.value ==6 || dem.value == 7 || dem.value ==9 || dem.value == 10 || dem.value ==11 || dem.value == 12 || dem.value ==13){

          btnGc.click()
          }
        }





      })
    }
    else {
      document.querySelector('.siret').style.display = 'block'
      document.querySelector('.tva').style.display = 'block'

      if (type ==='institutionnel'){
        let route = "/abonnementInsitution"
        let insitution = document.querySelector('#demandeur_profilInsti')
        let nom = document.querySelector('#demandeur_nom')
        insitution.addEventListener('change',()=>{
          if (insitution.value ==='Commune' || insitution.value ==='Communauté de communes' ||insitution.value ==='Département'){
            document.querySelector('#population').style.display = 'block'
          }
          else{
            document.querySelector('#population').style.display = 'none'

          }
          if (insitution.value ==='Communauté de communes'){
            let listeNom = document.querySelector('#communauteCom')
            nom.addEventListener('keyup',()=>{
              if (nom.value.length >=3){
                fetch('/trouverCom',{
                  method : 'POST',
                  body : nom.value
                })
                    .then(response=>{
                      return response.json()
                    })
                    .then((response=>{
                      while (listeNom.lastChild){
                        listeNom.removeChild(listeNom.firstChild)
                      }
                      for (const responseElement of response) {
                        let option = document.createElement('option')
                        option.innerText = responseElement
                        listeNom.appendChild(option)
                      }

                    }))

              }
            })

          }
          if (insitution.value ==='Région'){
            retourAbonnement(500006,route,'insti')

          }
          else if (insitution.value ==='Communautés religieuses'||insitution.value==='Établissement public'|| insitution.value==="Société d'Économie Mixte" || insitution.value==="Société Coopérative d'Intérêt Collectif"){

            retourAbonnement(0,route,insitution.value)
          }
          else {
            document.querySelector('#demandeur_habitant').addEventListener('change',()=>{
              let habitant = document.querySelector('#demandeur_habitant').value
              retourAbonnement(habitant,route,'insti')
            })
          }
        })

      }
      if (type ==='Grand compte'){
          let gc = document.querySelector("#demandeur_profilGc")

        gc.addEventListener('change',()=>{

          if (gc.value==='Syndic de co-propriété'){

            document.location.href = '/inscription syndic de co-propriété'
          }
          if (gc.value ==="Syndicat de co-propriété"){
            document.location.href="/syndicatCorpoprietes"
          }
          if (gc.value ==="Entreprise BTP et autres"){
            document.location.href="/inscriptionProBtp/gc"
          }
        })
        let user = document.querySelector('#demandeur_utilisateur')
        let route = "/abonnementGc";
        user.addEventListener('change',()=>{
          let content = JSON.stringify({
            profil : gc.value,
            utlisateur : user.value
          })
            fetch(route,{
              method : 'POST',
              body : content
            })
                .then((response)=>{
                  return response.json()
                })
                .then((response)=>{
                  let zoneAbo = document.querySelector('.resultAbonnement')
                  document.querySelector('#prixAbo').innerHTML = response.prix*1.2+' € TTC ' + '('+response.prix+' € HT)'
                  document.querySelector('#utilisateur').innerHTML = response.user;
                  document.querySelector('#btnAboI').click()
                  while(zoneAbo.firstChild){
                    zoneAbo.removeChild(zoneAbo.lastChild)
                  }
                  let pAbonnement = document.createElement('p')
                  pAbonnement.classList.add('rappelAbo')
                  pAbonnement.innerHTML = "Coût de l'abonnement : "+response.prix*1.2+' € TTC ' + '('+response.prix+' € HT)'+ " par mois"
                  let pUtilisateur = document.createElement('p')
                  pUtilisateur.innerHTML ="Utilisateur(s) : "+ response.user
                  pUtilisateur.classList.add('rappelAbo')
                  zoneAbo.appendChild(pUtilisateur)
                  zoneAbo.appendChild(pAbonnement)
                  zoneAbo.style.background = "#FFDD00"
                })
        })
      }

    }
  let cp = document.querySelector("#demandeur_cpAmbassadeur")
  if (type==='Grand compte'){
    //Verification code promotionnel

    document.querySelector('.btn-sm').addEventListener('click',()=>{
      let utilisateur = document.querySelector('#demandeur_utilisateur')
      let profil = document.querySelector('#demandeur_profilGc')
      let code = document.querySelector('#demandeur_codePromo')
      if (profil.value !="" && utilisateur.value !="" && code.value !="" && cp.value !="" ){

        fetch("/verifcation/codePromo",{
          method : 'POST',
          body : JSON.stringify({profil:profil.value,code:code.value,cp:cp.value})
        })
            .then((response)=>{
              return response.json()
            })
            .then((response)=>{
              document.querySelector('#btn-promo').click()
              let zoneAbo = document.querySelector('.resultAbonnement')
              if (response.existe ==='promo'){
                document.querySelector('.messagePromo').innerText = "Votre code promotionnel vous permet de bénéficier de "+response.remise +"%"+" sur l'abonnement suivant : "+response.abonnement
                document.querySelector('.validerCodePromo').addEventListener('click',()=>{

                  while(zoneAbo.firstChild){
                    zoneAbo.removeChild(zoneAbo.lastChild)
                  }
                  let pAbonnement = document.createElement('p')
                  pAbonnement.classList.add('rappelAbo')
                  pAbonnement.innerHTML = "Coût de l'abonnement : "+response.prix+' € HT ' + '('+Math.round(response.prix*1.2)+' € TTC)'
                  let pUtilisateur = document.createElement('p')
                  pUtilisateur.innerHTML ="Utilisateur(s) : "+ response.utilisateur
                  pUtilisateur.classList.add('rappelAbo')
                  let pAvert = document.createElement('p')
                  pAvert.classList.add('rappelAbo')
                  pAvert.innerHTML = "Tout changement dans le code promotionnel , votre profil ou le nombre d'utlisateur peut entraîner un changement d'effet du code promotionnel voire son non fonctionnement"
                  zoneAbo.appendChild(pUtilisateur)
                  zoneAbo.appendChild(pAbonnement)
                  zoneAbo.appendChild(pAvert)
                })
              }
            else if(response.existe ==='ambassadeur'){
                  if (response.possible) {

                    document.querySelector('.messagePromo').innerHTML = "<p class='h5'>Ce code vous donne accès au statut ambassadeur de DIAG-DRONE</p>" +
                        "<p class='h5'>Vous bénéficiez de l'abonnement " + response.abonnement + " au prix de" + response.prix + " € HT avec " + response.utilisateur + " utilisateur(s)</p> " +
                        "<p class='h5'>La durée de votre abonnement est de " + response.duree + " mois non reconductible</p>"
                    document.querySelector('.validerCodePromo').addEventListener('click', () => {

                      while (zoneAbo.firstChild) {
                        zoneAbo.removeChild(zoneAbo.lastChild)

                      }
                      let pAbonnement = document.createElement('p')
                      pAbonnement.classList.add('rappelAbo')
                      pAbonnement.innerHTML = "Coût de l'abonnement : " + response.prix + ' € HT ' + '(' + Math.round(response.prix * 1.2) + ' € TTC)'
                      let pUtilisateur = document.createElement('p')
                      pUtilisateur.innerHTML = "Utilisateur(s) : " + response.utilisateur
                      pUtilisateur.classList.add('rappelAbo')
                      let pAvert = document.createElement('p')
                      pAvert.classList.add('rappelAbo')
                      pAvert.innerHTML = "Tout changement dans le code promotionnel , votre profil ou le nombre d'utlisateur peut entraîner un changement d'effet du code promotionnel voire son non fonctionnement"
                      zoneAbo.appendChild(pUtilisateur)
                      zoneAbo.appendChild(pAbonnement)
                      zoneAbo.appendChild(pAvert)
                    })
                  }
                  else {
                    document.querySelector('.messagePromo').innerHTML = "<p class='h5'>Tout les statuts ambassadeur pour votre département ont été definis.</p>" +
                        "<p class='h5'>DIAG DRONE vous remercie.</p>"
                      document.querySelector('.btn-secondary').addEventListener('click',()=>{
                        code.value ="";
                      })

                  }
              }
            else {
                document.querySelector('.messagePromo').innerText = "Le code promotionnel renseigné n'existe pas"
                document.querySelector('.validerCodePromo').style.display = 'none'
                    document.querySelector('.btn-secondary').addEventListener('click',()=>{
                      code.value ="";
                    })
              }
            })
      }
    })
  }

  if (type==='institutionnel'){
    let habitant = document.querySelector('#demandeur_habitant')
    let habitantR;
    let profil = document.querySelector('#demandeur_profilInsti')
    let verifier = document.querySelector('.btn-sm')
    let code = document.querySelector('#demandeur_codePromo')
    verifier.addEventListener('click',()=>{

      if ( profil.value !="" && code.value !="" && cp.value!=""){
          if (profil.value ==='Région' && habitant.value ===""){
            habitantR = 5000007
          }else if((profil.value !=='Commune' || profil.value !=='Communauté de communes' || profil.value !=='Département') && habitant.value ==="" ){

            habitantR = 0
          }
          else {

            habitantR = habitant.value
          }
          let content= JSON.stringify({
            habitant: habitantR,
            code : code.value,
            cp : cp.value
          })
          fetch("/verification/codePromoInsti",{
            method:'POST',
            body : content
          })
              .then((response)=>{
                return response.json()
              })
              .then((response)=>{
                document.querySelector('#btn-promo').click()
                let zoneAbo = document.querySelector('.resultAbonnement')
                if (response.existe ==='promo'){
                  document.querySelector('.messagePromo').innerText = "Votre code promotionnel vous permet de bénéficier de "+response.remise +"%"+" sur l'abonnement suivant : "+response.abonnement
                  document.querySelector('.validerCodePromo').addEventListener('click',()=>{

                    while(zoneAbo.firstChild){
                      zoneAbo.removeChild(zoneAbo.lastChild)

                    }
                    let pAbonnement = document.createElement('p')
                    pAbonnement.classList.add('rappelAbo')
                    pAbonnement.innerHTML = "Coût de l'abonnement : "+response.prix+' € HT ' + '('+Math.round(response.prix*1.2)+' € TTC)'
                    let pUtilisateur = document.createElement('p')
                    pUtilisateur.innerHTML ="Utilisateur(s) : "+ response.utilisateur
                    pUtilisateur.classList.add('rappelAbo')
                    let pAvert = document.createElement('p')
                    pAvert.classList.add('rappelAbo')
                    pAvert.innerHTML = "Tout changement dans le code promotionnel , votre profil ou le nombre d'habitant peut entraîner un changement d'effet du code promotionnel voire son non fonctionnement"
                    zoneAbo.appendChild(pUtilisateur)
                    zoneAbo.appendChild(pAbonnement)
                    zoneAbo.appendChild(pAvert)

                  })
                }
                else if(response.existe ==='ambassadeur'){
                  if (response.possible) {

                    document.querySelector('.messagePromo').innerHTML = "<p class='h5'>Ce code vous donne accès au statut ambassadeur de DIAG-DRONE</p>" +
                        "<p class='h5'>Vous bénéficiez de l'abonnement " + response.abonnement + " au prix de" + response.prix + " € HT avec " + response.utilisateur + " utilisateur(s)</p> " +
                        "<p class='h5'>La durée de votre abonnement est de " + response.duree + " mois non reconductible</p>"
                    document.querySelector('.validerCodePromo').addEventListener('click', () => {

                      while (zoneAbo.firstChild) {
                        zoneAbo.removeChild(zoneAbo.lastChild)

                      }
                      let pAbonnement = document.createElement('p')
                      pAbonnement.classList.add('rappelAbo')
                      pAbonnement.innerHTML = "Coût de l'abonnement : " + response.prix + ' € HT ' + '(' + Math.round(response.prix * 1.2) + ' € TTC)'
                      let pUtilisateur = document.createElement('p')
                      pUtilisateur.innerHTML = "Utilisateur(s) : " + response.utilisateur
                      pUtilisateur.classList.add('rappelAbo')
                      let pAvert = document.createElement('p')
                      pAvert.classList.add('rappelAbo')
                      pAvert.innerHTML = "Tout changement dans le code promotionnel , votre profil ou le nombre d'habitant peut entraîner un changement d'effet du code promotionnel voire son non fonctionnement"
                      zoneAbo.appendChild(pUtilisateur)
                      zoneAbo.appendChild(pAbonnement)
                      zoneAbo.appendChild(pAvert)
                    })
                  }
                  else {
                    document.querySelector('.messagePromo').innerHTML = "<p class='h5'>Tout les statuts ambassadeur pour votre département ont été definis.</p>" +
                        "<p class='h5'>DIAG DRONE vous remercie.</p>"
                    document.querySelector('.btn-secondary').addEventListener('click',()=>{
                      code.value ="";
                    })

                  }
                }
                else {
                  document.querySelector('.messagePromo').innerText = "Le code promotionnel renseigné n'existe pas"
                  document.querySelector('.validerCodePromo').style.display = 'none'
                  document.querySelector('.btn-secondary').addEventListener('click',()=>{
                    code.value ="";
                  })
                }
              })

      }

      else {
        alert("Veuillez compléter tout les champs")
      }
    })
  }

  let siren = document.querySelector('#demandeur_siretTva_siret')

  siren.addEventListener('keyup',()=>{
    if (siren.value.length === 14){
      fetch('/entreprise/siret',{
        method :'POST',
        body : siren.value
      })
          .then((response)=>{
            return response.json()
          })
          .then((response)=>{
            document.querySelector('#demandeur_adresse_numero').value= response.adresse.numero
            document.querySelector('#demandeur_adresse_nomVoie').value= response.adresse.nomVoie
            document.querySelector('#demandeur_adresse_codePostal').value= response.adresse.codePostal
            document.querySelector('#demandeur_cpAmbassadeur').value = response.adresse.codePostal
            document.querySelector('#demandeur_adresse_ville').value= response.adresse.ville
            document.querySelector('#demandeur_siretTva_tva').value = response.TVA
            document.querySelector('#demandeur_nom').value = response.nom
          })
    }
  })


}




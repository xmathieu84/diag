

if (document.title === 'Détails intervention') {
  document.addEventListener('DOMContentLoaded',()=>{
    let intemperies = document.getElementsByName("inter_etape3[intemperie][]")
    let choixTravaux = document.getElementsByName("inter_etape3[travaux][]")
    let inter = document.querySelector('form').dataset.inter
    fetch("/recupInfoEtape4/"+inter,{
      method : 'get'
    })
        .then((response)=>{
          return response.json()
        })
        .then((response)=>{
          if (response.budget !==null){
            document.querySelector('.budgetInter').style.display ='block'
            document.querySelector('#inter_etape3_budgetInter_montant').setAttribute('required','required')
            document.querySelector('#inter_etape3_budgetInter_montant').value = response.budget
            document.querySelector('#inter_etape3_besoinBudget_0').checked = true
          }
          if (response.photo !==null){
            document.querySelector('.nbrePhotoInter').style.display ="block"
            document.querySelector('#inter_etape3_nbrePhoto').setAttribute('required','required')
            document.querySelector("#inter_etape3_photoOnly").checked = true
          }
          if (response.video !==null){
            document.querySelector('.nbreVideoInter').style.display ="block"
            document.querySelector('#inter_etape3_nbreVideo').setAttribute('required','required')
            document.querySelector("#inter_etape3_videoOnly").checked = true
          }
          console.log(response)
          if (response.intemperie.intemperie.length>0){
            document.getElementById('intemperie').style.display = 'block';
            document.getElementById('inter_etape3_intemp_0').checked = true
            for (const intemp of response.intemperie.intemperie) {
              for (let i = 0; i < intemperies.length; i++) {
                if (intemp == intemperies[i].value){
                  intemperies[i].checked = true
                }
                else{
                    document.querySelector('#inter_etape3_autreIntemp').value = intemp
                }
              }
            }
          }
        if (response.intemperie.date[0] !==null){
          let date = response.intemperie.date[0].split("/");
          document.querySelector('#inter_etape3_dateIntemperie').value = date[2]+"-"+date[1]+"-"+date[0]
        }
        if (response.travaux.length>0){
          for (const travail of response.travaux) {
            for (let i = 0; i < choixTravaux.length; i++) {
              if (travail == choixTravaux[i].value){
                choixTravaux[i].checked =true
              }
            }
          }
        }

        })
  })
  document.querySelector('#inter_etape3_dateIntemperie').addEventListener('change',()=>{
    console.log(document.querySelector('#inter_etape3_dateIntemperie').value)
  })
  let realPhoto = document.querySelector('#inter_etape3_photos');
  let fakeButton = document.querySelector('.boutonRemplace')
  let infoFichier = document.querySelector('.infoFichier')
  fakeButton.addEventListener('click', () => {
    realPhoto.click()
    realPhoto.addEventListener('change', () => {
      if (realPhoto.files.length>5){
        alert("Nous autorisons 5 photos maximums")
        realPhoto.value=""
      }
      else{
        infoFichier.innerHTML = realPhoto.files.length + ' photos ajouté(es)'
      }


    })
  })

  var oui = document.getElementById('inter_etape3_intemp_0')
  var non = document.getElementById('inter_etape3_intemp_1')

  let element1 = document.getElementById('ajout1');
  let element2 = document.getElementById('ajout2');
  oui.addEventListener('click', function () {
    document.getElementById('intemperie').style.display = 'block';
    element1.classList.add('col-sm-2');
    element2.classList.add('col-sm-2');


  })
  non.addEventListener('click', function () {
    document.getElementById('intemperie').style.display = 'none'
    element1.classList.remove('col-sm-2');
    element2.classList.remove('col-sm-2');
  })

  let budgetDemande = document.getElementsByName("inter_etape3[besoinBudget]")
  let budget = document.querySelector('#inter_etape3_budgetInter_montant')

  for (let i = 0; i < budgetDemande.length; i++) {
    budgetDemande[i].addEventListener('change',()=>{
      if (budgetDemande[i].value ==='Oui'){
        document.querySelector('.budgetInter').style.display ='block'
        budget.setAttribute('required','required')
      }
      else{
        document.querySelector('.budgetInter').style.display ='none'
        budget.removeAttribute('required')
      }
    })
  }

  let nbrePhoto = document.querySelector("#inter_etape3_photoOnly")
  let nbreVideo = document.querySelector("#inter_etape3_videoOnly")
  let nbreVideoDemande = document.querySelector('#inter_etape3_nbreVideo')
  nbrePhoto.addEventListener('change',()=>{
    if (nbrePhoto.checked){
      document.querySelector('.nbrePhotoInter').style.display ="block"
      document.querySelector('#inter_etape3_nbrePhoto').setAttribute('required','required')
    }
    else{
      document.querySelector('.nbrePhotoInter').style.display ="none"
      document.querySelector('#inter_etape3_nbrePhoto').removeAttribute('required')
    }
  })
  nbreVideo.addEventListener('change',()=>{
    if (nbreVideo.checked){
      document.querySelector('.nbreVideoInter').style.display ="block"
      nbreVideoDemande.setAttribute('required','required')
    }
    else{
      document.querySelector('.nbreVideoInter').style.display ="none"
      nbreVideoDemande.removeAttribute('required')
    }
  })

  nbreVideoDemande.addEventListener('keyup',()=>{
    if (nbreVideoDemande.value>nbreVideoDemande.max){
      nbreVideoDemande.value = nbreVideoDemande.max
    }
  })
}



if (document.title === 'Nouvelle demande') {
  let listeinter = document.querySelectorAll('.listeinter');
  let ajout = document.querySelector('.type');
  let code = document.querySelector('.codeSyndic')


  for (let i = 0; i < listeinter.length; i++) {

      listeinter[i].addEventListener('change', () => {
        if (listeinter[i].value !=='diag') {
          while (ajout.hasChildNodes() === true) {
            ajout.removeChild(ajout.firstChild)
          }
          let corps = JSON.stringify({
            idListe: listeinter[i].value,
            idInter: listeinter[i].dataset.inter
          })

          fetch('/selectionListeinter', {
            method: 'POST',
            body: corps
          })
              .then((reponse) => {
                return reponse.json()
              })
              .then((reponse) => {

                document.querySelector('.titre2').innerHTML = "Précisez votre type d'intervention"
                reponse.liste.forEach(inter => {


                  let div = document.createElement('div')
                  let label = document.createElement('label')
                  let input = document.createElement('input')
                  input.type = 'radio';
                  input.name = 'typeInter'
                  input.id = 'typeInter_' + inter.id + reponse['raccourci'];
                  input.classList.add('typeInters')
                  input.value = inter.id
                  input.dataset.inter = reponse.idInter
                  label.classList.add('h6')
                  label.setAttribute('for', input.id)
                  //label.innerHTML = inter.nom
                  div.classList.add('col-sm-4');
                  div.classList.add('text-center');
                  div.classList.add('interType')
                  div.appendChild(input);
                  div.appendChild(label);

                  ajout.appendChild(div)

                });
                for (let j = 0; j < listeinter.length; j++) {
                  listeinter[j].dataset.inter = reponse.idInter;

                }
                let typeInters = document.querySelectorAll('.typeInters');

                for (let k = 0; k < typeInters.length; k++) {
                  typeInters[k].addEventListener('change', () => {
                    let zoneBouton = document.querySelector('.boutton')
                    while (zoneBouton.firstChild) {
                      zoneBouton.removeChild(zoneBouton.lastChild)
                    }
                    let contenu = JSON.stringify({
                      idIntervention: typeInters[k].dataset.inter,
                      idTypeInter: typeInters[k].value
                    })
                    fetch('/selectionTypeinter', {
                      method: 'POST',
                      body: contenu
                    })
                        .then((reponse) => {
                          return reponse.json()
                        })
                        .then((reponse) => {


                          let a = document.createElement('a');
                          a.classList.add('btn');
                          a.classList.add('btn-maincolor');
                          a.classList.add('text-white');
                          a.classList.add('right');

                          a.innerHTML = 'Page suivante'
                          if (code) {
                            a.href = '/etape2/' + reponse + '/' + code.value;
                          } else {
                            a.href = '/etape2/' + reponse;
                          }

                          zoneBouton.appendChild(a);
                        })
                  })

                }
              })
        }
        else {
          document.location.href="/createDiag"
        }
      })







  }
}

if (document.title ==='Demande d\'intervention (phase 1)'){

let typeBien = document.querySelector('#intervention_typeDeBien')
  typeBien.addEventListener('change',()=>{

    if (typeBien.value === 'Autre'){

      document.querySelector('.autreBien').style.display = 'block';
    }
    else {
      document.querySelector('.autreBien').style.display = 'none'
    }

  })
}




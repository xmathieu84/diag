if (document.title === 'Rapport') {
  let realPhoto = document.querySelector('#rapport_photos');
  let btnPhoto = document.querySelector('.photo');
  let info = document.querySelector('.info')
  btnPhoto.addEventListener('click', () => {
    realPhoto.click();
    realPhoto.addEventListener('change', () => {
      info.innerHTML = realPhoto.files.length + ' photos ajoutées'
    })
  })


  let ajouter = document.getElementById('boutonAjouter')
  let idRapport = ajouter.dataset.rapport;
  let video = document.getElementById('fichierVideo');
  let transfert = document.getElementById('transfertVideo')

  let form = new FormData()
  let verif = document.querySelector('#verif')

  if (verif.value === 0 && document.querySelector('#rapport_paiement_0') && document.querySelector('#rapport_paiement_1') ) {
    document.querySelector('#rapport_paiement_0').setAttribute('required', 'required')
    document.querySelector('#rapport_paiement_1').setAttribute('required', 'required')
  }


  ajouter.addEventListener('click', () => {
    video.click();
    video.addEventListener('change', () => {


      form.append('video1', video.files[0])
      form.append('video2', video.files[1])
      ajouter.innerHTML = video.files.length + ' video(s) ajoutée(s)';

      xhr = new XMLHttpRequest();

      xhr.open("POST", "/upload/" + idRapport, true);
      xhr.upload.onprogress = function (e) {
        if (e.lengthComputable) {
          var ratio = Math.floor(((e.loaded / e.total) * 100) * 100) / 100 + '%';
          transfert.innerHTML = ratio;

        }
      }
      xhr.upload.onloadstart = function (e) {



      }
      xhr.upload.onloadend = function (e) {



      }
      xhr.send(form);

    });
  })
  function designInput(bouton, input) {
    if (bouton){
      bouton.addEventListener('click', () => {
        input.click();
        input.addEventListener('change', () => {

          bouton.innerHTML = input.value.replace(/^.*\\/, "");
        })
      })
    }

  }
  let file = document.getElementById('file');
  let cerfa = document.getElementById('cerfa');
  let dTele = document.getElementById('dTele');
  let realFile = document.getElementById('rapport_rap_fichier');
  let realCerfa = document.getElementById('rapport_cerfa_inter');
  let realdTele = document.querySelector('#rapport_donnees_telemetrique');


  designInput(file, realFile);
  //designInput(cerfa, realCerfa);
  designInput(dTele, realdTele);


  let oui = document.querySelector('#rapport_paiement_0');
  let non = document.querySelector('#rapport_paiement_1');
  let date = document.getElementById('paiement')
  if (oui) {
    oui.addEventListener('click', () => {
      date.style.display = 'block';

    })
  }
  if (non) {
    non.addEventListener('click', () => {

      date.style.display = 'none';

      date.value = null;

    })
  }

  let ajouterText = document.querySelector('.btn-ajoutText')
  let ajoutResume = document.querySelector('.btnResume')
  //let supprimer = document.createElement('button')


  ajouterText.addEventListener('click', () => {
    let textarea = document.createElement('textArea')
    textarea.classList.add('text-resume')
    textarea.setAttribute('rows', 5)
    textarea.setAttribute('cols', 10)
    document.querySelector('.zone-text').appendChild(textarea);

  })
  let test = [];

  ajoutResume.addEventListener('click', () => {
    let resume = document.querySelectorAll('.text-resume')

    for (let i = 0; i < resume.length; i++) {
      test.push(resume[i].value)

    }
    document.getElementById('rapport_rap_resume').value = test
  })

let degat  =document.getElementsByName('rapport[degatInter]');
  for (let i = 0; i <degat.length ; i++) {
    degat[i].addEventListener('change',()=>{
      if (degat[i].value ==='oui'){
        document.querySelector('.materiel').style.display = 'block'
        document.querySelector('.corporel').style.display = 'block'
      }
      else {
        document.querySelector('.materiel').style.display = 'none'
        document.querySelector('.corporel').style.display = 'none'
      }
    })
  }





}
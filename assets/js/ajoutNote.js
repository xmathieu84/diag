import axios from "axios";
if (document.title =='Dossier Général'){
    let valider = document.querySelector('.valider')
    let fakeFile = document.querySelector('.btn-fichier')
    let realFile = document.querySelector('#fichier')
    let envoyer = document.querySelector('.envoyer')
    let validerDonnee = document.querySelector('.validerDonnee')
    if (valider){
        valider.addEventListener('click',()=>{
            let texteNote = document.querySelector('#texte').value
            let titreNote = document.querySelector('#titre').value

            fetch("/ajoutNoteGenerale/"+valider.dataset.id,{
                method : 'POST',
                body : JSON.stringify({
                    texte: texteNote,
                    titre:titreNote
                })

            }).then(()=>{
                document.location.reload()
            })
        })
    }

    fakeFile.addEventListener('click',()=>{
        realFile.click()
        realFile.addEventListener('change',()=>{
            fakeFile.innerHTML = realFile.files[0]['name'];
        })
    })

    envoyer.addEventListener('click',()=>{
        let form = new FormData();
        form.append('docGeneral',realFile.files[0])
        form.append('nomFichier',document.querySelector('#nom').value)
        axios.post("/ajoutDocGeneral/"+valider.dataset.id,form,{
            onUploadProgress: progressEvent => {
                document.querySelector('#modalUpload').style.display='block'
                document.querySelector('.upload').style.width = (Math.round(progressEvent.loaded/progressEvent.total))*100+'%'
                if (progressEvent.loaded === progressEvent.total){
                    document.querySelector('#modalUpload').style.display = 'none'
                }
            }
        }).then(()=>{
            document.location.reload()
        })

    })
    if (validerDonnee){
        validerDonnee.addEventListener('click',()=>{
            let content = JSON.stringify({
                information : document.querySelector('#information').value,
                presentation : document.querySelector('#presentation').value,
                intervenant : document.querySelector("#intervenant").value,
                finance : document.querySelector('#finance').value,
                juridique:document.querySelector('#juridique').value,
                complement : document.querySelector('#complement').value
            })
            fetch("/ajoutDonneeGenerale/"+validerDonnee.dataset.id,{
                method : 'POST',
                body : content
            })
                .then(()=>{
                    document.location.reload()
                })
        })
    }


    let modifier = document.querySelectorAll('.btn-primary')
    let contentDonneGen = document.querySelectorAll('.contentDonneeGen')
    let zoneValider = document.querySelectorAll('.zoneValider')
    let zoneAnnuler = document.querySelectorAll('.annuler')
    for (let i = 0; i < modifier.length; i++) {
        modifier[i].addEventListener('click',()=>{
            let button = document.createElement('button')

            let oldText = document.querySelectorAll('.donneeGen')

            button.classList.add('btn')
            button.classList.add('btn-danger')
            button.innerText = 'Annuler'
            button.dataset.number = i;

            zoneAnnuler[i].appendChild(button)
            let buttonValider = document.createElement('button')
            buttonValider.classList.add('btn')

            buttonValider.classList.add('btn-success')
            buttonValider.innerText = 'Valider les modifications'
            buttonValider.dataset.type = modifier[i].dataset.type
            buttonValider.dataset.number = i
            zoneValider[i].removeChild( modifier[i])
            zoneValider[i].appendChild(buttonValider)
            let textArea = document.createElement('textarea')

            textArea.value = oldText[i].innerText
            textArea.dataset.type = modifier[i].dataset.type
                textArea.classList.add('texteChange')
            textArea.rows = 7
            textArea.style.resize = 'none'
            contentDonneGen[i].removeChild(oldText[i])
            contentDonneGen[i].appendChild(textArea)
            let annuler = document.querySelectorAll('.btn-danger')
            for (let j = 0; j < annuler.length; j++) {
                annuler[j].addEventListener('click',()=>{
                    let numero = annuler[j].dataset.number
                    modifier[numero].classList.remove('btn-success')
                    modifier[numero].classList.add('btn-primary')
                    modifier[numero].innerText = "Modifier"
                    let p = document.createElement('p')
                    p.classList.add('donneeGen')
                    p.innerText = oldText[numero].innerText
                    contentDonneGen[numero].removeChild(textArea)
                    contentDonneGen[numero].appendChild(p)
                    zoneAnnuler[numero].removeChild(button)
                })
            }
            let validerChangement = document.querySelectorAll('.btn-success')
            let textModif = document.querySelectorAll('.texteChange')
            for (let j = 0; j < validerChangement.length; j++) {
                validerChangement[j].addEventListener('click', () => {
                    let number = validerChangement[j].dataset.number
                    let content = JSON.stringify({
                        id: document.querySelector('#tab03_pane').dataset.id,
                        texte:textModif[j].value,
                        typeChange: validerChangement[j].dataset.type
                    })
                    fetch("/modifDonneeGenerale", {
                        method: 'POST',
                        body: content
                    })
                        .then(() => {
                            zoneValider[number].removeChild(validerChangement[j])
                            let btnModifier = document.createElement('button')
                            btnModifier.classList.add('btn')
                            btnModifier.classList.add('btn-primary')
                            btnModifier.innerText = 'Modifier'
                            zoneValider[number].appendChild(btnModifier)

                            let p = document.createElement('p')
                            p.classList.add('donneeGen')
                            p.innerText = document.querySelector('.texteChange').value
                            contentDonneGen[number].removeChild(textModif[j])
                            contentDonneGen[number].appendChild(p)
                            zoneAnnuler[number].removeChild(annuler[j])
                        })
                })
            }



        })
    }

    let effacer = document.querySelectorAll('.delete')
    let zoneEffacer = document.querySelector('.zoneDelete')
    let ligneEfface = document.querySelectorAll('.lineDelete')

    for (let i = 0; i < effacer.length; i++) {
        effacer[i].addEventListener('click',()=>{

            let alerte = confirm("Souhaitez vous supprimer cette note définitivement ?")
           if (alerte){
               fetch("/effacerNote",{
                   method:'POST',
                   body : ligneEfface[i].dataset.id
               })
                   .then(()=>{
                       zoneEffacer.removeChild(ligneEfface[i])
                   })
               if (zoneEffacer.childNodes.length <=3){

                   zoneEffacer.innerHTML = " <div class=\"col-sm-12\">\n" +
                       "                                                    <p class=\"h6\">Vous n'avez pas  de notes</p>\n" +
                       "                                                </div>"
               }
           }

        })
    }
    // Suppression piéces générales

    let supprimer = document.querySelectorAll('.btnSupprimer')
    let zoneSuppression = document.querySelector('.zoneSuppression')
    let element = document.querySelectorAll('.elementDelete')

    for (let i = 0; i < supprimer.length; i++) {
        supprimer[i].addEventListener('click',()=>{
            let alerte = confirm("Souhaitez vous supprimer cette pièce définitivement ?")
            if (alerte){
                fetch("/supprimerPieceGen",{
                    method : 'POST',
                    body : supprimer[i].dataset.id
                })
                    .then(()=>{
                        zoneSuppression.removeChild(element[i]);
                    })
            }

        })
    }


}
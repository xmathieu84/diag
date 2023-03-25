if (document.title === 'Ajout pièces'){
    let inputFile = document.querySelectorAll('.file')
    let button = document.querySelectorAll('.fakeFile')
    let valider = document.querySelectorAll('.validerPiece')
    let zoneTexte = document.querySelectorAll('.textInfo')
    let nreFile = document.querySelectorAll('.nbreFile')

    function changeFile(realButton,fakeButton){
        fakeButton.addEventListener('click',()=>{

            realButton.click()
            realButton.addEventListener('change',()=>{

                fakeButton.innerHTML = realButton.files.length + ' fichier(s) ajouté(s)'
            })
        })
    }
    for (let i = 0; i < button.length; i++) {
            changeFile(inputFile[i],button[i])

            fetch('/insitution/nobreFichier/'+valider[i].dataset.id+'-'+valider[i].dataset.type,{
                method : 'GET'
            })
                .then((response)=>{
                    return response.json()
                })
                .then((response)=>{
                    if (response === 0){
                        nreFile[i].innerHTML ='(Aucun fichier ajouté)'
                    }
                    if (response === 1){
                        nreFile[i].innerHTML ='(1 fichier ajouté)'
                    }
                    else {
                        nreFile[i].innerHTML ='('+response+' fichiers ajoutés)'
                    }

                })

    }
    for (let i = 0; i < valider.length; i++) {
        valider[i].addEventListener('click',()=>{

            let type = valider[i].dataset.type
            let id = valider[i].dataset.id

            fetch('/institution/saveInfo/'+type+'/'+id,{
                method:'POST',
                body : zoneTexte[i].value
            })
                .then((response)=>{
                    return response.json()
                })
                .then((response)=>{

                    for (let j = 0; j < inputFile[i].files.length; j++) {
                        let form = new FormData();
                        form.append('file',inputFile[i].files[j])
                       fetch('/insitutionnel/saveFile/'+response,{
                           method:'POST',
                           body : form
                       })
                           .then((reponse)=>{
                               return reponse.json()
                           })
                           .then((reponse)=>{
                               document.location.reload()
                           })
                    }
                })
        })
    }
}
if (document.title ==='Précision'){
    let valider = document.querySelector('.terminer')
    let texte = document.querySelector('textarea')
    let id = document.querySelector('.id').value
    let lien
    let code = document.querySelector('.codeSyndic')
    if (code){
         lien = "/mes appels d'offre/"+code.value
    }else{
         lien= "/mes appels d'offre/"
    }

    valider.addEventListener('click',()=>{
        if (texte.value !=""){
            fetch('/institution/savePrecision/'+id,{
                method:'POST',
                body :texte.value
            })
                .then((response)=>{
                    return response.json()
                })
                .then((response)=>{
                    document.location.href =lien;
                })
        }
        else{
            document.location.href = lien;
        }

    })

}
import axios from "axios";
if (document.title ==='Ajouter des pièces'){
    function creerELement(nomElement,classElement1,classElement2,classElement3){
        let element = document.createElement(nomElement)

        element.classList.add(classElement1)
        element.classList.add(classElement2)
        element.classList.add(classElement3)
        return element;
    }
    function envoieNote(id){
        let titre = document.querySelector('#titreNote').value
        let text = document.querySelector('#texteNote').value
        if (titre.value !==""&&text.value!==""){
            fetch("/ajoutNote/"+id,{
                method : 'POST',
                body : JSON.stringify({
                    titreNote : titre,
                    texteNote : text
                })
            })
                .then(()=>{
                    document.location.reload()
                })
        }
    }
    let zoneContact = document.querySelector('.zoneContact')
    let btnContact = document.querySelector('.btn-outline-maincolor')
    let realFile = document.querySelector('#fichier')
    let fakeFile = document.querySelector('.btn-fichier')
    let envoie = document.querySelector('.btn-primary')
    fakeFile.addEventListener('click',()=>{
        realFile.click()
        realFile.addEventListener('change',()=>{
            fakeFile.innerHTML = realFile.files[0]['name'];
        })
    })
    btnContact.addEventListener('click',()=>{
        let divContact = creerELement('div','col-6','contact')
        let divRow = creerELement('div','row')
        let divNom = creerELement('div','col-12')
        let labelNom = creerELement('label')
        labelNom.innerText = 'Nom'
        let inputNom = creerELement('input','nomContact','form-control')
        divNom.appendChild(labelNom)
        divNom.appendChild(inputNom)

        let divPrenom = creerELement('div','col-12')
        let labelPrenom = creerELement('label')
        labelPrenom.innerText = 'Prénom'
        let inputPrenom = creerELement('input','prenomContact','form-control')
        divPrenom.appendChild(labelPrenom)
        divPrenom.appendChild(inputPrenom)

        let divTel = creerELement('div','col-12')
        let labelTel = creerELement('label')
        labelTel.innerText = 'téléphone'
        let inputTel = creerELement('input','telContact','form-control')
        divTel.appendChild(labelTel)
        divTel.appendChild(inputTel)

        let divEmail = creerELement('div','col-12')
        let labelEmail = creerELement('label')
        labelEmail.innerText = 'Email'
        let inputEmail = creerELement('input','emailContact','form-control')
        divEmail.appendChild(labelEmail)
        divEmail.appendChild(inputEmail)

        let divSupprimer = creerELement('div','col-12','text-center','mt-3')

        let boutonSupprimer = creerELement('button','btn','btn-danger')
        boutonSupprimer.innerText = 'Supprimer'
        divSupprimer.appendChild(boutonSupprimer)

        divRow.appendChild(divNom)
        divRow.appendChild(divPrenom)
        divRow.appendChild(divTel)
        divRow.appendChild(divEmail)
        divRow.appendChild(divSupprimer)
        divContact.appendChild(divRow)
        zoneContact.appendChild(divContact)

        let contact = document.querySelectorAll('.contact')
        let supprimer = document.querySelectorAll('.btn-danger')

        for (let i = 0; i < supprimer.length; i++) {
            supprimer[i].addEventListener('click',()=>{
                zoneContact.removeChild(contact[i]);
            })

        }





    })
    envoie.addEventListener('click',()=>{
        let libelle = document.querySelector('#libéllé').value
        let dateValidite = document.querySelector('#validite').value
        let delai = document.querySelector('#alerte').value
        fetch('/envoieDocInstitution-'+envoie.dataset.id,{
            method:'POST',
            body : JSON.stringify({
                lib:libelle,
                validite:dateValidite,
                alerte : delai
            })

                })
            .then((response)=>{
                return response.json()

            })
            .then((response)=>{
                let form = new FormData();
                console.log(realFile.files[0]);
                form.append('file',realFile.files[0])
                axios.post('/envoieFichierDoc/'+response,
                    form
                    ,{
                    onUploadProgress :progressEvent => {

                        document.querySelector('#transfert').style.display = 'block'
                        document.querySelector('.progress-bar').style.width = (Math.round(progressEvent.loaded/progressEvent.total))*100+'%'
                        if (progressEvent.loaded === progressEvent.total){
                            document.querySelector('#transfert').style.display = 'none'
                        }
                    }
                })
                    .then((reponse)=>{
                        if (document.querySelector('.contact')){
                            let nomC = document.querySelectorAll('.nomContact')
                            let prenomC = document.querySelectorAll('.prenomContact')
                            let telContact = document.querySelectorAll('.telContact')
                            let mailContact = document.querySelectorAll('.emailContact')

                            for (let i = 0; i < nomC.length; i++) {
                                fetch('/ajoutContact/'+reponse.data,{
                                    method:'POST',
                                    body : JSON.stringify({
                                        nom : nomC[i].value,
                                        prenom : prenomC[i].value,
                                        telephone : telContact[i].value,
                                        email : mailContact[i].value
                                    })
                                })


                            }
                        }else if(document.querySelector('#titreNote').value !=="" && document.querySelector('#texteNote').value !==""){
                            envoieNote(reponse.data)
                        }
                        else{
                            document.location.reload()
                        }
                    })
            })
    })
}
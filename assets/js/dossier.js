if (document.title =='Sous dossier'){
    let typeDossier = document.querySelector('#typeDossier')
    let interDrone = document.querySelectorAll('.interDrone')
    let typeInter = document.querySelector('#typeInter')
    let listeinter = document.querySelector('#listeinter')
    let diagTech = document.querySelectorAll('.diagTechnique')
    let btnGenral = document.querySelector('.dossierGeneral')
    let dtech = document.querySelector('#diagTech')
    console.log(diagTech)
    let nom;
    let valider = document.querySelector('.btn-primary');
    let idDossier = document.querySelector('.btn-maincolor').dataset.id
    typeDossier.addEventListener('change',()=>{
        if (typeDossier.value === 'Intervention drone'){
            interDrone[0].style.display ='block'
            interDrone[1].style.display ='block'

        }
        else if (typeDossier.value ==='Diagnostic technique'){

                for (let i = 0; i < diagTech.length; i++) {
                    diagTech[i].style.display ='block'
                }
            }
        else{
            interDrone[0].style.display ='none'
            interDrone[1].style.display ='none'
            for (let i = 0; i < diagTech.length; i++) {
                diagTech[i].style.display ='none'
            }
        }


    })
    listeinter.addEventListener('change',()=>{
        fetch('/selectTypeInter',{
            method : 'POST',
            body : listeinter.value
        })
            .then((response)=>{
                return response.json()
            })
            .then((response)=>{

                while (typeInter.firstChild){
                    typeInter.removeChild(typeInter.lastChild)
                }
                for (let i = 0; i < response.length; i++) {
                    let option = document.createElement('option')
                    option.innerText = response[i].nom
                    option.value =  response[i].nom
                    typeInter.appendChild(option);
                }

            })
    })
    valider.addEventListener('click',()=>{

        if (typeDossier.value === 'Intervention drone'){
            nom = typeDossier.value + '-' + listeinter.options[listeinter.selectedIndex].getAttribute('data-nom') + '-'+ typeInter.value
        }
        else if (typeDossier.value ==='Diagnostic technique'){
            nom = typeDossier.value + '-'+ dtech.value
        }
        else {
            nom = typeDossier.value
        }


        fetch('/institution/creerSousDossier/'+idDossier,{
            method : 'POST',
            body : nom
        })
            .then(()=>{
                document.location.reload();
            })
    })
    btnGenral.addEventListener('click',()=>{
        fetch('/createDossierGenarale/'+btnGenral.dataset.id,{
            method : 'GET'
        })
            .then(()=>{
                document.location.reload()
            })
    })



}
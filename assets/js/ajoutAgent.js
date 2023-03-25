if (document.title =='Ajouter un agent'|| document.title=='Ajouter un collaborateur'){


    let save = document.querySelector('.btn-primary')
    let fonctionnalite = document.querySelector('#role')
    let prenom = document.querySelector('.prenom')
    let nom = document.querySelector('.nom')
    let pass = document.querySelector('.passe')
    let civ = document.querySelector('#civilite')
    let mail = document.querySelector('.mailAgent');
    let superieur = document.querySelector('.superieur')

    fonctionnalite.addEventListener('change',()=>{
        if (fonctionnalite.value !=='RESPONSABLE'){
            fetch("/listSuperieur",{
                method:'GET'
            })
                .then((response)=>{
                    return response.json()
                })
                .then((response)=>{
                    while (superieur.firstChild){
                        superieur.removeChild(superieur.lastChild)
                    }
                    let label = document.createElement('label')
                    label.innerHTML = 'Choisissez le superieur hiérachique'
                    label.setAttribute('for','sup')
                    superieur.appendChild(label)
                    let select = document.createElement('select')
                    select.setAttribute('id','sup')
                    for (let i = 0; i < response.length; i++) {
                        let option = document.createElement('option')
                        option.value = response[i].id
                        option.innerText = response[i].nom
                        select.appendChild(option)
                    }
                    superieur.appendChild(select)

                })
        }
        else {
            while (superieur.firstChild){
                superieur.removeChild(superieur.lastChild)
            }
        }
    })

    save.addEventListener('click',()=>{
        let sup;
        if (superieur.firstChild){
            sup = document.querySelector('#sup').value
        }
        else {
            sup= null;
        }

        let contenu = JSON.stringify({
            roleG : save.dataset.role,
            role:fonctionnalite.value,
            agentPrenom:prenom.value,
            agentNom:nom.value,
            password : pass.value,
            email : mail.value,
            civilite:civ.value,
            supHiera : sup
        })
        if (prenom.value&&nom.value&&pass.value&&mail.value){
            fetch('ajout personnel',{
                method:'POST',
                body:contenu
            })
                .then(()=>{
                    document.location.reload();
                })
        }

    })
}
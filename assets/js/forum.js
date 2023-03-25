if (document.title ==='Thèmes'){
    let bouton = document.querySelector('.btn-outline-success')
    let theme = document.querySelector('#theme')

    bouton.addEventListener('click',()=>{
        fetch('/admin/ajouterTheme',{
            method:'POST',
            body:theme.value
        })
            .then(()=>{
                document.location.reload()
            })
    })

    let supprimer = document.querySelectorAll('.btn-outline-danger')

    for (let i = 0; i <supprimer.length ; i++) {
        supprimer[i].addEventListener('click',()=>{
            let id = supprimer[i].dataset.theme
            fetch('/admin/supprimerTheme/'+id,{
                method:'GET'
            })
                .then(()=>{
                    document.location.reload()
                })
        })
    }
}
if (document.title ==='Problèmes'){

    let button = document.querySelector('.btn-maincolor3')
    console.log(button)
    button.addEventListener('click',()=>{
        let cat = document.querySelector('#cat').value
        let sujet = document.querySelector("#titre").value
            fetch('/creerSujet/'+button.dataset.categorie,{
                method : 'POST',
                body:JSON.stringify({
                    message : cat,
                    titre : sujet
                })
            })
                .then(()=>{
                    document.location.reload();
                })


    })

    let supprimer = document.querySelectorAll('.delete')

    for (let i = 0; i < supprimer.length; i++) {
        supprimer[i].addEventListener('click',()=>{
            fetch('/admin/supprimerCat/'+supprimer[i].dataset.cat,{
                method:'GET',
            })
                .then(()=>{
                    document.location.reload();
                })
        })

    }


}

if (document.title ==='Liste Reponse'){
    let valider = document.querySelectorAll('.btn-success')
    let supprimer = document.querySelectorAll('.btn-warning')
    for (let i = 0; i < valider.length; i++) {
        valider[i].addEventListener('click',()=>{
            traiterMessage(i,valider)
        })
        supprimer[i].addEventListener('click',()=>{
            traiterMessage(i,supprimer)
        })
    }
    function traiterMessage(indice,bouton){
        let zone = document.querySelectorAll('.modal-body')
        fetch('/admin/validerReponse/'+bouton[indice].dataset.reponse,{
            method:'POST',
            body : bouton[indice].dataset.action
        })
            .then((response)=>{
                return response.json();
            })
            .then((response)=>{
                if (response ==='ok'){
                    while (zone[indice].firstChild){
                        zone[indice].removeChild(zone[indice].lastChild)
                    }
                    zone[indice].classList.remove('border')
                }

            })
    }
}
if(document.title =='Liste autorisations'){

    let valider = document.querySelectorAll('.btn-primary')
    let authorisation = document.querySelectorAll('.form-control')
    console.log(authorisation)
    for (let i = 0; i < valider.length; i++) {
        valider[i].addEventListener('click',()=>{
            let contenu = JSON.stringify({
                agent : valider[i].dataset.user,
                type : valider[i].dataset.type,
                role : authorisation[i].value
            })
            fetch("/institution/changerRole",{
                method : 'POST',
                body : contenu
            })
                .then(()=>{
                    document.location.reload();
                })

        })

    }
}
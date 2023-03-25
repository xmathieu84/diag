if (document.title == 'Categories') {
    let bouton = document.querySelector('.btn-maincolor3');
    let contenu = document.querySelectorAll('.form-control')
    let form = new FormData()


    bouton.addEventListener('click', () => {

        form.append('titre', contenu[0].value)
        form.append('message', contenu[1].value)
        form.append('categorie', bouton.dataset.categorie)
        fetch('/creerSujet', {
            method: 'POST',
            body: form
        })
            .then(() => {
                document.location.reload(true)

            })
    })

}
if (document.title === 'reponse du forum') {

    let bouton = document.querySelector('.btn');
    let contenu = document.querySelector('.form-control')
    let form = new FormData()


    bouton.addEventListener('click', () => {
        form.append('reponse', contenu.value)
        form.append('sujet', bouton.dataset.sujet)

        fetch('/posterReponse', {
            method: 'POST',
            body: form
        })
            .then(() => {
                document.location.reload(true);
            })
    })
}
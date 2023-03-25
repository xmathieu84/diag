if (document.title == 'Compléter mes Maps') {
    let heures = document.querySelector('.heures')
    let minutes = document.querySelector('.minutes')
    let observations = document.querySelector('.observation')
    let button = document.querySelector('.btn-maincolor')

    button.addEventListener('click', () => {
        let contenu = JSON.stringify({
            heure: heures.value,
            minute: minutes.value,
            observation: observations.value,
            idInter: observations.dataset.inter
        })
        fetch('/validerMap', {

            method: 'POST',
            body: contenu
        })
            .then(() => {
                document.location.reload()
            })
    })


}
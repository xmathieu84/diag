if (document.title == 'Paiement institutionnel') {

    let accord = document.querySelector('#accord')
    let zoneBouton = document.querySelector('.zoneBouton')
    let a = document.createElement('a')
    a.classList.add('btn')
    a.classList.add('btn-maincolor')
    a.innerHTML = 'Valider'
    a.href = '/insti/' + accord.dataset.inter
    document.addEventListener('DOMContentLoaded', () => {
        fetch('/chercherCgu', {
            method: 'POST',
            body: accord.dataset.inter
        })
            .then((reponse) => {
                return reponse.json()
            })
            .then((reponse) => {
                if (reponse == 'existe') {
                    accord.checked = true
                    zoneBouton.appendChild(a)
                }
            })

    })
    accord.addEventListener('change', () => {
        if (accord.checked) {
            fetch('/cgu', {
                method: 'POST',
                body: accord.dataset.inter
            })
                .then((reponse) => {
                    return reponse.json()
                })
                .then((reponse) => {
                    if (reponse == 'ok') {
                        zoneBouton.appendChild(a)
                    }
                })
        }
        else {
            fetch('/refus', {
                method: 'POST',
                body: accord.dataset.inter
            })
                .then((reponse) => {
                    return reponse.json()
                })
                .then((reponse) => {
                    if (reponse == 'ok') {
                        while (zoneBouton.firstChild) {
                            zoneBouton.removeChild(zoneBouton.lastChild);
                        }
                    }
                })
        }
    })
}
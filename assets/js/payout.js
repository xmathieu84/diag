if (document.title == 'Consulter mon solde') {

    let bouton = document.querySelector('.btn-maincolor')
    let montant = document.querySelector('#retraitMontant')
    let zoneMessage = document.querySelector('.message')
    bouton.addEventListener('click', () => {
        fetch('/effectuerRetrait', {
            method: 'POST',
            body: montant.value
        })
            .then((response) => {
                return response.json()
            })
            .then((response) => {
                let p = document.createElement('p')
                p.classList.add('h5')
                p.innerHTML = response
                zoneMessage.appendChild(p)
                setTimeout(() => {
                    document.location.reload()
                }, 5000);
            })
    })
}
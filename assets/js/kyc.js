if (document.title === 'Creation compte MangoPay') {
    let boutonKbis = document.querySelector('#boutonKbis')
    let boutonIdentite = document.querySelector('#boutonIdentite')
    let boutonStatut = document.querySelector('#boutonStatut')
    let realInputKbis = document.querySelector('#kbis')
    let realInputStatut = document.querySelector('#statut')
    let realInputIdentitte = document.querySelector('#identite')
    let dateNaissance = document.querySelector('#dateNaissance')
    let lieuNaissance = document.querySelector('#lieuNaissance')
    let button = document.querySelector('.btn-maincolor')
    chargeFile(boutonKbis, realInputKbis);

    if (boutonStatut){
        chargeFile(boutonStatut, realInputStatut);
    }
    chargeFile(boutonIdentite, realInputIdentitte)


    function chargeFile(nomBouton, nomRealInput) {
        nomBouton.addEventListener('click', () => {
            nomRealInput.click()
            nomRealInput.addEventListener('change', () => {
                nomBouton.innerHTML = nomRealInput.files[0].name
            })
        })
    }



    button.addEventListener('click', () => {
        let form = new FormData();

        form.append('identite', realInputIdentitte.files[0])
        if (boutonStatut){
            form.append('statut', realInputStatut.files[0])
        }

        form.append('kbis', realInputKbis.files[0])
        form.append('naissance', dateNaissance.value)
        form.append('lieu', lieuNaissance.value)
        document.querySelector('.btn-primary').click()
        fetch('/loadKyc', {
            method: 'POST',
            body: form
        })
            .then((response) => {
                return response.json()
            })
            .then((response) => {

              if (response['reponse'] === 'ok') {
                  alert('Les documents ont bien été envoyés.')
                    document.location.href = '/entreprise'

                }
                else {
                    document.querySelector('.btn-secondary').click()
                    document.querySelector('.message').innerHTML = response
                    setTimeout(() => {
                        document.location.href = '/entreprise'
                    }, 1000);
                }
            })
    })

}
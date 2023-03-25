if (document.title == 'Effectuer un paiement') {
    let prenom = document.querySelector('#prenom')
    let nom = document.querySelector('#nom');
    let valider = document.querySelector('.btn-outline-maincolor')
    let email = document.querySelector('#Email')
    let inputUser = document.querySelector('.idUser')
    let cardType = document.getElementsByName('typeCard')

    valider.addEventListener('click', () => {
        let contenu = JSON.stringify({
            'name': nom.value,
            'firstName': prenom.value,
            'mail': email.value
        })
        fetch('/createMangoUser', {
            method: 'POST',
            body: contenu

        }).then((response) => {
            return response.json()
        }).then((response) => {
            if (response) {
                inputUser.value = response.Id
                document.querySelector('.infoPaiement').style.display = 'block'
            }
        })
    })

    for (let i = 0; i < cardType.length; i++) {
        cardType[i].addEventListener('change', () => {
            if (cardType[i].checked) {
                let corps = JSON.stringify({
                    'user': inputUser.value,
                    'carte': cardType[i].value,
                    'idPaiement': inputUser.dataset.paiement
                })
                fetch('/createCardHdd', {
                    method: 'POST',
                    body: corps

                })
                    .then((reponse) => {
                        return reponse.json()
                    })
                    .then((reponse) => {
                        console.log(reponse);
                        document.querySelector("#accessKeyRef").value = reponse.AccessKey
                        document.querySelector('#data').value = reponse.PreregistrationData
                        let button = document.createElement('button')
                        button.classList.add('btn')
                        button.type = 'submit'
                        button.classList.add('btn-maincolor')
                        button.innerHTML = 'Valider'
                        document.querySelector('.zoneBouton').appendChild(button)
                    })
            }
        })

    }
}
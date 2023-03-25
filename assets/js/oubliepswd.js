if (document.title == 'Mot de passe oublié') {


    let bouton = document.querySelector('.btn-pswd');


    bouton.addEventListener('click', () => {
        let mail = document.getElementById('mail');
        fetch('/verifyMail', {
            method: 'POST',
            body: mail.value
        })
            .then((reponse) => {
                return reponse.json()
            })
            .then((reponse) => {


                if (reponse == 'trouve') {
                    document.location.href = ('/bonmail')
                }
                else {
                    document.querySelector('.message').innerHTML = "L'adresse email n'existe pas"
                }

            })
    })

}
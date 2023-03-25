if (document.title == 'Recevoir un paiement') {

    let bouton = document.querySelector('.btn-outline-maincolor')
    let message = document.querySelector('.messageReussi');
    let p = document.createElement('p')
    p.classList.add('h6')
    p.innerHTML = ("La demande de paiement est bien enregistrer. Vous allez être redirigé vers votre page d'accueil")

    bouton.addEventListener('click', () => {
        let contenu = JSON.stringify({
            montant: document.querySelector('.montant').value,
            email: document.querySelector('.email').value,
            profil: bouton.dataset.profil
        })
        fetch('/enregistrerPaiement', {
            method: 'POST',
            body: contenu,
        })
            .then((reponse) => {
                return reponse.json()
            })
            .then((reponse) => {
                alert("La demande de paiement est bien enregistrée. Vous allez être redirigé vers votre page d'accueil")
                setTimeout(() => {
                    document.location.href = reponse['home'];
                }, 2000);
            })
    })
}
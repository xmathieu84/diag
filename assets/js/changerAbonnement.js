if (document.title ==="Changer votre abonnement"){
    let mois = document.querySelector('.form-control-range')
    let button = document.createElement('button');
    let a = document.createElement('a')
    let input = document.createElement('input')
    let abonnement = document.querySelectorAll('.form-check-input')
    let zoneBouton = document.querySelector('.CGU');
    let idAbonnement;
    let restant = document.querySelector('#reste').value


    for (let i = 0; i < abonnement.length; i++) {
        abonnement[i].addEventListener('change', () => {

            while (zoneBouton.firstChild) {
                zoneBouton.removeChild(zoneBouton.lastChild)
            }

            input.type = 'checkbox'
            input.id = 'cgu'
            input.value = 1
            a.href = "/cguOTDdd"
            a.setAttribute('target', '_blank')
            a.innerText = "J'ai lu et j'accèpte les conditions générales d'utilisation"
            button.classList.add('btn')
            button.classList.add('btn-maincolor')
            button.innerText = 'Valider'
            zoneBouton.appendChild(input)
            zoneBouton.appendChild(a)
            zoneBouton.appendChild(document.createElement('br'))
            zoneBouton.appendChild(button)


            idAbonnement = abonnement[i].value
            let bouton = document.querySelector('.btn-maincolor')
            let cgu = document.querySelector('#cgu')
            let condition;
            if (cgu) {
                condition = cgu.value
            }
            else {
                condition = null;
            }
            bouton.addEventListener('click', () => {
                let corps = JSON.stringify({
                    duree: mois.value,
                    conditionUtilisation: condition
                })
                console.log(corps)
                if (!cgu.checked)
                    alert("Veuillez accepter nos condition générales d'utillisation")
                    else if (restant > mois.value){
                        alert("Sélectionnez une durée d'abonnement plus longue")
                }
                else {
                    fetch('/entreprise/validerChangementAbonnement/'+idAbonnement, {
                        method: 'POST',
                        body: corps
                    })
                        .then((reponse) => {
                            return reponse.json()
                        })
                        .then((reponse) => {
                            if (reponse==='ok'){
                                let p = document.createElement('p')
                                p.classList.add('h5')
                                p.innerText = 'Un mail concernant les instructions de validation de votre abonnement viens de vous être envoyé'
                                document.querySelector('.message').appendChild(p)
                            }

                        })
                }
            })

        })


    }
}
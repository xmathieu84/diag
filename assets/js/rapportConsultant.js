if (document.title == 'Rapport consultant') {
    let bouton = document.querySelector('.btn')
    let zonReponse = document.querySelector('.resultat')

    bouton.addEventListener('click', () => {
        let numero = document.querySelector('.form-control').value
        fetch('/trouverRapport', {
            method: 'POST',
            body: numero
        })
            .then((reponse) => {
                return reponse.json()
            })
            .then((reponse) => {
                while (zonReponse.firstChild) {
                    zonReponse.removeChild(zonReponse.lastChild)
                }
                if (reponse.trouve == 'ok') {
                    let img = document.createElement('img')
                    let span = document.createElement('span');
                    let button = document.createElement('button')
                    button.classList.add('btn')
                    button.classList.add('btn-maincolor3')
                    button.classList.add('second')
                    let br = document.createElement('br')
                    button.innerHTML = 'Ajouter'
                    img.src = '/css/css_site/img/zip.png'
                    span.innerText = reponse.archive
                    zonReponse.appendChild(img)
                    zonReponse.appendChild(span)
                    zonReponse.appendChild(br)
                    zonReponse.appendChild(button);
                    let boutonEnvoie = document.querySelector('.second')

                    boutonEnvoie.addEventListener('click', () => {
                        fetch('/lierRapport', {
                            method: 'POST',
                            body: reponse.idRapport
                        })
                            .then(() => {
                                document.location.reload()
                            })
                    })

                }
                else {
                    let p = document.createElement('p')
                    p.classList.add('h6');
                    p.innerHTML = "Le rapport demandé n'existe pas";
                    zonReponse.appendChild(p);
                }



            })


    })
}
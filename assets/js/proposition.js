if (document.title == 'Liste OTDs') {
    document.addEventListener('DOMContentLoaded', () => {
        let prop = document.querySelectorAll('.price')
        let prix = document.querySelectorAll('.horaire');
        let coutMin = document.querySelectorAll('.coutMin');


        for (let i = 0; i < prop.length; i++) {
            let contenu = JSON.stringify({
                liste: prop[i].dataset.liste,
                type: prop[i].dataset.type,
                salarie: prop[i].dataset.salarie
            })
            let contenuKm = JSON.stringify({
                salarie: prop[i].dataset.salarie,
                intervention: prop[i].dataset.inter
            })
            fetch('/propositionTaux', {
                method: 'POST',
                body: contenu
            })
                .then((reponse) => {
                    return reponse.json()
                })
                .then((reponse) => {

                    let span = document.createElement('span')
                    span.classList.add('taux');
                    span.setAttribute('class', 'h6')
                    span.innerHTML = reponse['taux'] + " €/heure";
                    prix[i].appendChild(span)
                    let span2 = document.createElement('span')
                    span2.setAttribute('class', 'h6')
                    span.classList.add('taux');
                    span2.innerHTML = "À partir de "+reponse['prixMin'] + ' €'
                    coutMin[i].appendChild(span2)
                })


        }
    })
}
if (document.title ==='Vos factures'){
    console.log('ok')
    let datum = new Date()
    let zoneFacture = document.querySelector('.zoneFacture')
    let zonedate = document.querySelector('.mois')
    let suivant = document.querySelector('.next')
    let precedent = document.querySelector('.preview')
    function facture(mois) {
        let premier = new Date(mois.getFullYear(), mois.getMonth(), 1)
        let lastDay = new Date(mois.getFullYear(), mois.getMonth() + 1, 0)


        const post = JSON.stringify({
            debut: premier.toLocaleString('fr-Fr', {
                day: 'numeric',
                month: 'numeric',
                year: 'numeric',
            }),
            fin: lastDay.toLocaleString('fr-FR', {
                day: 'numeric',
                month: 'numeric',
                year: 'numeric',
            }),
        })

        fetch('/retrouveFactureInsti', {
            method: 'POST',
            body: post

        })
            .then(function (reponse) {
                return reponse.json()
            })
            .then(function (reponse) {
                console.log(reponse)
                while (zoneFacture.firstChild) {
                    zoneFacture.removeChild(zoneFacture.lastChild)
                }
                for (let i = 0; i < reponse.facture.length; i++) {

                    let div = document.createElement('div');
                    div.classList.add('col-sm-2')
                    let a = document.createElement('a')
                    let figure = document.createElement('figure')
                    let img = document.createElement('img')
                    let figcaption = document.createElement('figcaption')
                    img.src = '/css/css_site/img/fichier.png'
                    figcaption.innerText = reponse.facture[i]
                    a.href = '/uploads/factureInsti/' + reponse.facture[i]
                    a.target = '_blank'

                    figure.appendChild(img)
                    figure.appendChild(figcaption)
                    a.appendChild(figure)
                    div.appendChild(a)
                    zoneFacture.appendChild(div)

                }
            })

    }

    facture(datum)
    let dateloc = datum.toLocaleString('fr-Fr', {
        month: 'long',
        year: 'numeric',
    })
    zonedate.innerHTML = dateloc

    suivant.addEventListener('click', () => {
        let moisSuivant = new Date(datum.setMonth(datum.getMonth() + 1))
        let dateloc = datum.toLocaleString('fr-Fr', {
            month: 'long',
            year: 'numeric',
        })
        zonedate.innerHTML = dateloc

        while (zoneFacture.firstChild) {
            zoneFacture.removeChild(zoneFacture.lastChild)
        }

        facture(moisSuivant)
    })

    precedent.addEventListener('click', () => {

        let moisPreceden = new Date(datum.setMonth(datum.getMonth() - 1))

        let dateloc = datum.toLocaleString('fr-Fr', {
            month: 'long',
            year: 'numeric',
        })
        zonedate.innerHTML = dateloc

        while (zoneFacture.firstChild) {
            zoneFacture.removeChild(zoneFacture.lastChild)
        }
        facture(moisPreceden)
    })
}
if (document.title == 'Mes factures') {
    let date = new Date();
    let zoneDevis = document.querySelector('.zoneDevis')
    let mois = document.querySelector('#moisDevis')
    let suivant = document.querySelector('#suivantDevis')
    let precedent = document.querySelector('#precedentDevis')

    function devis(mois) {
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

        fetch('/recupererDevis', {
            method: 'POST',
            body: post
        })
            .then((reponse) => {
                return reponse.json()
            })
            .then((reponse) => {
                while (zoneDevis.firstChild) {
                    zoneDevis.removeChild(zoneDevis.lastChild)
                }
                for (let i = 0; i < reponse.length; i++) {

                    let div = document.createElement('div');
                    div.classList.add('col-sm-2')
                    let img = document.createElement('img')
                    img.src = '/css/css_site/img/fichier.png'
                    let a = document.createElement('a')
                    a.href = '/uploads/devis/' + reponse[i]['nom']
                    a.target = '_blank'
                    a.innerText = reponse[i]['nom']
                    a.appendChild(img)
                    div.appendChild(a)
                    zoneDevis.appendChild(div)

                }



            })

    }
    document.addEventListener('DOMContentLoaded', () => {
        devis(date)
        let dateloc = date.toLocaleString('fr-Fr', {
            month: 'long',
            year: 'numeric',
        })
        mois.innerText = dateloc;
    })
    suivant.addEventListener('click', () => {
        let moisSuivant = new Date(date.setMonth(date.getMonth() + 1))
        let dateloc = date.toLocaleString('fr-Fr', {
            month: 'long',
            year: 'numeric',
        })
        mois.innerText = dateloc
        devis(moisSuivant)
    })
    precedent.addEventListener('click', () => {
        let moisPrecedent = new Date(date.setMonth(date.getMonth() - 1))
        let dateloc = date.toLocaleString('fr-Fr', {
            month: 'long',
            year: 'numeric',
        })
        mois.innerText = dateloc;
        devis(moisPrecedent)
    })



}
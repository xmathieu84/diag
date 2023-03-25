if (document.title == 'Mes factures') {
  let datum = new Date()
  let zoneFacture = document.querySelector('.zoneFacture')

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

    fetch('/entreprise/recuperer', {
      method: 'POST',
      body: post

    })
      .then(function (reponse) {
        return reponse.json()
      })
      .then(function (reponse) {
        console.log(zoneFacture)
        while (zoneFacture.firstChild) {
          zoneFacture.removeChild(zoneFacture.lastChild)
        }
        for (let i = 0; i < reponse.length; i++) {

          let div = document.createElement('div');
          div.classList.add('col-sm-2')
          let figure = document.createElement('figure')
          let figureC = document.createElement('figcaption')
          let img = document.createElement('img')
          img.src = '/css/css_site/img/fichier.png'
          let a = document.createElement('a')
          a.href =  reponse[i].lien + reponse[i].nom
          a.target = '_blank'
          figureC.innerText = reponse[i].nom
          figure.appendChild(img)
          figure.appendChild(figureC)
          a.appendChild(figure)
          div.appendChild(a)
          zoneFacture.appendChild(div)

        }
      })

  }
  document.addEventListener('DOMContentLoaded', () => {
    facture(datum)
  })

  let dateloc = datum.toLocaleString('fr-Fr', {
    month: 'long',
    year: 'numeric',
  })
  document.getElementById('mois').innerText = dateloc
  document.getElementById('suivant').addEventListener('click', () => {
    let suivant = new Date(datum.setMonth(datum.getMonth() + 1))
    let dateloc = datum.toLocaleString('fr-Fr', {
      month: 'long',
      year: 'numeric',
    })
    document.getElementById('mois').innerText = dateloc
    const node = document.getElementById('facture')


    facture(suivant)
  })

  document.getElementById('precedent').addEventListener('click', () => {

    let precedent = new Date(datum.setMonth(datum.getMonth() - 1))

    let dateloc = datum.toLocaleString('fr-Fr', {
      month: 'long',
      year: 'numeric',
    })
    document.getElementById('mois').innerText = dateloc
    const node = document.getElementById('facture')

    facture(precedent)
  })
}
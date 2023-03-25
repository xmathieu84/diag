if (document.title ==='Interventions réalisées'){
   let date = new Date();
   let zoneDate = document.querySelector('.mois')
    zoneDate.innerHTML = date.toLocaleString('fr-Fr', {
        month: 'long',
        year: 'numeric',
    })
    let suivant = document.querySelector('.suiv')
    let precedent = document.querySelector('.prec')
    let tbody = document.querySelector('.reponseInter')
    suivant.addEventListener('click',()=>{
        let moisSuivant = new Date(date.setMonth(date.getMonth()+1))
        zoneDate.innerHTML = moisSuivant.toLocaleString('fr-Fr', {
            month: 'long',
            year: 'numeric',
        })
        facture(moisSuivant)
    })
    precedent.addEventListener('click',()=>{
        let moisSuivant = new Date(date.setMonth(date.getMonth()-1))
        zoneDate.innerHTML = moisSuivant.toLocaleString('fr-Fr', {
            month: 'long',
            year: 'numeric',
        })
        facture(moisSuivant)
    })
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
        fetch("/findInter",{
            method:'POST',
            body : post
        }).then(response=>{
            return response.json()
        })
            .then(response=>{
                while (tbody.firstChild){
                    tbody.removeChild(tbody.lastChild)
                }
                for (const responseElement of response) {
                    let divDate = document.createElement('div')
                    divDate.classList.add('col-4')
                    divDate.classList.add('text-dark')
                    divDate.classList.add("tableAccueil")
                    divDate.innerHTML = responseElement.date
                    tbody.appendChild(divDate)
                    let divInter = document.createElement('div')
                    divInter.classList.add('col-4')
                    divInter.classList.add('text-dark')
                    divInter.classList.add("tableAccueil")
                    divInter.innerHTML = responseElement.type + '<br>' +responseElement.liste
                    tbody.appendChild(divInter)
                    let divAdresse = document.createElement('div')
                    divAdresse.classList.add('col-4')
                    divAdresse.classList.add('text-dark')
                    divAdresse.classList.add("tableAccueil")
                    divAdresse.innerHTML = responseElement.adresse.numero + ' ' +responseElement.adresse.voie + '<br>' + responseElement.adresse.cp + ' ' +responseElement.adresse.ville
                    tbody.appendChild(divAdresse)

                }
            })
    }

}
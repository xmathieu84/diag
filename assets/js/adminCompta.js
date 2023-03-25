if (document.title==="Liste Mandat de prélèvement"){
    let emailGc = document.querySelector('#emailAdminGc')
    let emailOtd = document.querySelector('#emailAdminOtd')

    function litseMandat(input,typeUser){
        input.addEventListener('keyup',()=>{
            if (input.value.length >= 5){
                let contenu = JSON.stringify({
                    email : input.value,
                    type : typeUser
                })
                fetch("/administrateur/rechercheMandat",{
                    method : 'POST',
                    body : contenu
                })
                    .then((response)=>{
                        return response.json()
                    })
                    .then((response)=>{

                        let tbody = document.querySelector('.'+typeUser)
                        while (tbody.lastChild){
                            tbody.removeChild(tbody.firstChild)
                        }
                        for (let i = 0; i < response.length; i++) {
                            let tr = document.createElement('tr')
                            let tdNom = document.createElement('td')
                            tdNom.innerHTML = response[i].preleve
                            tr.appendChild(tdNom)
                            let tdBanque = document.createElement("td")
                            for (let j = 0; j < response[i].banque.length; j++) {
                                tdBanque.innerHTML += "<div class='col-12'>"+response[i].banque[j].nom+"</div><hr class='banqueAdmin'>"
                            }
                            tr.appendChild(tdBanque)
                            let tdMandat = document.createElement('td')
                            for (let j = 0; j <response[i].mandat.length ; j++) {
                                if (response[i].mandat[j].mandat){
                                    tdMandat .innerHTML += "<div class='col-12'><a target='_blank' href='/uploads/sepa/"+response[i].mandat[j].mandat+"'><figure><img src='/css/css_site/images/pdfPetit.png'><figcaption>"+response[i].mandat[j].mandat+"</figcaption></figure></a></div>"
                                }

                            }
                            tr.appendChild(tdMandat)

                            tbody.appendChild(tr)
                        }
                    })
            }
        })
    }

    litseMandat(emailOtd,'otd')
    litseMandat(emailGc,'gc')
}

if (document.title ==="Facture prestation DD"){
    let date = new Date();

    let zoneFacture = document.querySelectorAll('.zoneFactureAdminPresta')
    let suivant = document.querySelectorAll('.suivantAdmin')
    let precedent = document.querySelectorAll('.precedentAdmin')
    let mois = document.querySelectorAll('.mois')

    for (let i = 0; i < suivant.length; i++) {
        let moisActuel = date.toLocaleString('fr-Fr', {
            month: 'long',
            year: 'numeric',
        })
        mois[i].innerHTML = moisActuel
        suivant[i].addEventListener('click',()=>{
            let nextMonth = new Date(date.setMonth(date.getMonth() + 1))
            let moisSuivant = nextMonth.toLocaleString('fr-Fr', {
                month: 'long',
                year: 'numeric',
            })
            mois[i].innerHTML = moisSuivant
            searchFact(nextMonth,suivant[i].dataset.type,i)
        })
        precedent[i].addEventListener('click',()=>{
            let previousMonth = new Date(date.setMonth(date.getMonth() - 1))
            let moisPrcedent = previousMonth.toLocaleString('fr-Fr', {
                month: 'long',
                year: 'numeric',
            })
            mois[i].innerHTML = moisPrcedent
            searchFact(previousMonth,precedent[i].dataset.type,i)

        })
    }
    function searchFact(mois,type,indice){
        let premier = new Date(mois.getFullYear(), mois.getMonth(), 1)
        let lastDay = new Date(mois.getFullYear(), mois.getMonth() + 1, 0)
        let contenu= JSON.stringify({
            type : type,
            premier :  premier.toLocaleString('fr-Fr', {
                day : "numeric",
                month: 'numeric',
                year: 'numeric',
            }),
            dernier : lastDay.toLocaleString('fr-Fr', {
                day : "numeric",
                month: 'numeric',
                year: 'numeric',
            })
        })
        fetch("/administrateur/retrouverFacturePresta",{
            method:'POST',
            body : contenu,
            headers : {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
            .then((response)=>{
                return response.json()
            })
            .then((response)=>{

                while (zoneFacture[indice].lastChild){
                    zoneFacture[indice].removeChild(zoneFacture[indice].firstChild)
                }
                for (let i = 0; i < response.length; i++) {
                    let div = document.createElement('div')
                    div.classList.add('col-3')
                    div.classList.add('text-center')
                    let a = document.createElement('a')
                    a.target = "_blank"
                    a.href = response[i].lien+response[i].nom
                    let figure = document.createElement('figure')
                    let img = document.createElement('img')
                    img.src = "/css/css_site/images/pdfmoyen.png"
                    let figcaption = document.createElement('figcaption')
                    figcaption.innerText = response[i].nom
                    figure.appendChild(img)
                    figure.appendChild(figcaption)
                    a.appendChild(figure)
                    div.appendChild(a)
                    zoneFacture[indice].appendChild(div)
                }
            })
    }

}
if (document.title ==="Retrouver doc"){
    let numero = document.querySelector('#kyc')
    let identifiant = document.querySelector('#identifiant')
    let valider = document.querySelector('.btn-primary')
    valider.addEventListener('click',()=>{
        fetch("/administrateur/findKyc",{
            method:'post',
            body : JSON.stringify({
                idKyc : numero.value,
                idUser : identifiant.value
            })
        })
    })
}
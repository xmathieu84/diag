if (document.title ==="Nos partenaires"){
    let cp = document.querySelector('#cp')
    let city = document.querySelector('#ville')
    let intervention = document.querySelector('#interventionBtp')
    cp.addEventListener('keyup',()=>{
        if (cp.value.length ===5){
            fetch("https://geo.api.gouv.fr/communes?codePostal="+cp.value+"&fields=codesPostaux&format=json&geometry=centre",{
                method : 'GET'
            })
                .then((response)=>{
                    return response.json()
                })
                .then((response)=>{

                    while (city.firstChild){
                        city.removeChild(city.lastChild)
                    }

                    for (const ville of response) {
                        let option = document.createElement('option')
                        option.innerText = ville.nom
                        option.value = ville.nom
                        city.appendChild(option)
                    }
                })
        }
    })

    intervention.addEventListener('change',()=>{
        let tbody = document.querySelector('.proBtp')
        if (city.value !==""){
            let content = JSON.stringify({
                ville : city.value,
                cp : cp.value,
                inter : intervention.value
            });
            fetch("/rechercheProBtpAccueil",{
                method : 'POST',
                body : content
            })
                .then((response)=>{
                    return response.json()
                })
                .then((response)=>{

                    if (response ==='non'){
                        document.querySelector('.btnAlerte').click()
                    }
                    else {
                        while (tbody.firstChild){
                            tbody.removeChild(tbody.lastChild)
                        }
                        for (let i = 0; i < response.length; i++) {
                            let tr = document.createElement('tr')
                            let tdLogo = document.createElement('td')
                            console.log('ok')
                            tdLogo.classList.add('text-center')
                            if (response[i].logo){
                                let img = document.createElement('img')
                                tdLogo.appendChild(img)
                            }
                            tr.appendChild(tdLogo)
                            let tdNom = document.createElement('td')
                            tdNom.classList.add('text-center')
                            tdNom.innerText = response[i].nom
                            tr.appendChild(tdNom)
                            let tdLien = document.createElement('td')
                            tdLien.classList.add('text-center')
                            let a = document.createElement('a')
                            a.href = "/contacterPro/"+response[i].boss
                            a.classList.add('btn')
                            a.classList.add('btn-maincolor')
                            a.innerText = 'Contacter'
                            tdLien.appendChild(a)
                            tr.appendChild(tdLien)
                            tbody.appendChild(tr)


                        }
                    }
                })
        }
    })



}

if (document.title ==="Contacter Pro-Btp"){
    let expediateur = document.querySelector("#expediteur")
    let pro = document.querySelector('input[type=hidden]')
    let contenu = document.querySelector('textarea')
    let titre = document.querySelector('#titre')

    let envoyer = document.querySelector('.btnEnvoie')

    envoyer.addEventListener('click',()=>{
        let content = JSON.stringify({
            expe : expediateur.value,
            proBtp :pro.value,
            message : contenu.value,
            titre : titre.value
        })
        fetch("/envoieMailProBtp",{
            method : 'POST',
            body : content
        })
            .then((response)=>{
                return response.json()
            })
            .then((response)=>{
                if (response ==='ok'){
                   document.querySelector('.fermerAlert').click()
                    document.querySelector('.btnModalMail').click()
                }
            })

    })
}

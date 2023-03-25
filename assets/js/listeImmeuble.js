if (document.title ==='Demande accès'){
    let valider = document.querySelector('.btn-maincolor')
    let ville = document.querySelector('#ville')
    let aClass = 'btn'
    let aClass2 = 'btn-success'
    let tdClass = 'color-dark'
    let tdClass2 = 'text-center'
    let tbody = document.querySelector('.corps')
    valider.addEventListener('click',()=>{
        fetch('/syndic/listeImmeuble',{
            method:'POST',
            body : ville.value
        })
            .then(response=>{
                return response.json()
            })
            .then(response=>{
                for (const responseElement of response) {
                    let tr = document.createElement('tr')
                    let tdNom  =document.createElement('td')
                    tdNom.innerText = responseElement.nom
                    tdNom.classList.add(tdClass)
                    tdNom.classList.add(tdClass2)
                    let tdAdresse = document.createElement('td')
                    tdAdresse.innerHTML = responseElement.adresse
                    tdAdresse.classList.add(tdClass)
                    tdAdresse.classList.add(tdClass2)
                    let tdLien = document.createElement('td')
                    let button = document.createElement('a')
                    button.innerText = "Demander l'accès à l'interface"
                    button.href = "/syndic/demande access/"+responseElement.identifiant
                    button.classList.add(aClass)
                    button.classList.add(aClass2)
                    tdLien.classList.add(tdClass2)
                    tdLien.appendChild(button)
                    tr.appendChild(tdNom)
                    tr.appendChild(tdAdresse)
                    tr.appendChild(tdLien)
                    tbody.appendChild(tr)
                }
            })
    })
}
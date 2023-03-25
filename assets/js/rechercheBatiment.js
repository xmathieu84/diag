if (document.title=='Recherche d\'un bâtiment'){
    let search = document.querySelector('#bat')
    let br = document.createElement('br')
    let tbody = document.querySelector('tbody')
    search.addEventListener('keyup',()=>{
        if (search.value.length>0){
            fetch('/institution/rechercheBatiment',{
                method:'POST',
                body :search.value
            })
                .then((response)=>{
                    return response.json()
                })
                .then((response)=>{
                    while (tbody.firstChild){
                        tbody.removeChild(tbody.lastChild)
                    }
                    for (let i = 0; i <response.length ; i++) {
                        const rep = response[i]

                        let tr = document.createElement('tr')
                        let tdId = document.createElement('td')
                        let tdNumInterne = document.createElement('td')
                        let tdNom = document.createElement('td')
                        let tdAdresse = document.createElement('td')
                        tdId.innerHTML = rep.batiment.id
                        tdNumInterne.innerHTML = rep.batiment.numeroInterne
                        tdNom.innerHTML = rep.batiment.nom
                        tdAdresse.innerHTML = rep.adresse.numVoie + ' '+ rep.adresse.nomVoie + ' '+ '<br>' + rep.adresse.cp + ' '+ rep.adresse.ville
                        tr.appendChild(tdId)
                        tr.appendChild(tdNumInterne)
                        tr.appendChild(tdNom)
                        tr.appendChild(tdAdresse)
                        tbody.appendChild(tr);
                    }
                })
        }
    })
}
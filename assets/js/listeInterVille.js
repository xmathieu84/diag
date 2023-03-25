if (document.title == 'Recherche par ville') {

    let bouton = document.querySelector('.btn-maincolor')
    let ville = document.querySelector('.ville')
    let zoneR = document.querySelector('.distance')
    let temps = document.querySelector("#delai")
    let tbody = document.querySelector('tbody')

    bouton.addEventListener('click', () => {
        let contenu = JSON.stringify({
            lieu: ville.value,
            distance: zoneR.value,
            delai: temps.value
        })
        fetch('/institution/listeInter/ville', {
            method: 'POST',
            body: contenu
        })
            .then((response) => {
                return response.json();
            })
            .then((response) => {
                while (tbody.firstChild) {
                    tbody.removeChild(tbody.lastChild);
                }
                for (let i = 0; i < response.length; i++) {
                    const rep = response[i]

                    let tr = document.createElement('tr')
                    tr.classList.add('color-dark')
                    let tdDate = document.createElement('td')
                    tdDate.innerHTML = rep.inter[0]
                    tr.appendChild(tdDate)
                    let tdlieu = document.createElement('td')
                    tdlieu.innerHTML = rep.inter[1]
                    tr.appendChild(tdlieu)
                    let tdinter = document.createElement('td')
                    tdinter.innerHTML = rep.inter[2]
                    tdLien = document.createElement('td')
                    if (rep.result.result == true) {
                        let a = document.createElement('a')
                        a.classList.add('btn')
                        a.classList.add('btn-maincolor')
                        a.classList.add('modalInterGc')
                        a.innerHTML = 'OTD disponible le ' + rep.result.date
                        a.style.color = 'black'
                        a.href = '/creerInterInsti/' + rep.tipi.listeInter + '/' + rep.tipi.typeInter + '/' + rep.tipi.date + '/' + rep.tipi.otd + '/' + rep.tipi.ville + '/' + rep.tipi.codeP
                        tdLien.appendChild(a)

                    } else {
                        tdLien.innerHTML = 'OTD indisponible'
                    }
                    tr.appendChild(tdinter)
                    tr.appendChild(tdLien)
                    tbody.appendChild(tr)

                }
                let validerInter = document.querySelectorAll('.modalInterGc')
                for (let i = 0; i < validerInter.length; i++) {
                    validerInter[i].addEventListener('click',(e)=>{
                        e.preventDefault()
                        console.log(validerInter[i].href)
                        document.querySelector('#modalInterInsti').style.display='block'
                        document.querySelector('#continuer').addEventListener('click',()=>{
                            document.location.href = validerInter[i].href
                        })
                        document.querySelector('#close-modal').addEventListener('click',()=>{
                            document.querySelector('#modalInterInsti').style.display='none'
                        })
                    })
                }

            })
    })
}
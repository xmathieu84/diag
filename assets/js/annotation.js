import interact from 'interactjs'
if (document.title ==='Ajouter des pièces') {

    let choix = document.querySelector('#choixFichier')
    let zoneReponse = document.querySelector('.ancienNote')
    let valider = document.querySelector('.envoyer')
    choix.addEventListener('change', () => {
        fetch('/annotation/' + choix.value, {
            method: 'GET'
        })
            .then((response) => {
                return response.json()
            })
            .then((response) => {
                for (let i = 0; i < response.length; i++) {

                    let div = document.createElement('div')
                    div.classList.add('col-12')
                    let pTitre = document.createElement('p')
                    pTitre.innerHTML = 'Note créée le ' + response[i].date + ' par <b>' + response[i].auteur + '</b>'
                    div.appendChild(pTitre)
                    let ptexte = document.createElement('p')
                    ptexte.innerText = response[i].texte
                    div.appendChild(ptexte)

                    zoneReponse.appendChild(div)
                }
            })
    })

    valider.addEventListener('click',()=>{
        let texte = document.querySelector('#newNote').value
        let titre = document.querySelector('#titre').value
        let id = document.querySelector('#choixFichier').value

        fetch('/ajoutNote/'+id,{
            method : 'POST',
            body : JSON.stringify({
                texteNote : texte,
                titreNote : titre
            })

        }).then(()=>{
            document.location.reload()
        })
    })
    //suppression d'un note d'un dcoument

    let supprimer = document.querySelectorAll('.delete')
    let zone = document.querySelector('.zoneDelete')
    let element = document.querySelectorAll('.elementDelete')
    for (let i = 0; i < supprimer.length; i++) {
        supprimer[i].addEventListener('click',()=>{
            
            let alerte = confirm("Souhaitez vous supprimer définitivement cette note ?")
            if (alerte){
                fetch("/supprimerNoteFichier",{
                    method : 'POST',
                    body : supprimer[i].dataset.id
                })
                    .then(()=>{
                        zone.removeChild(element[i])
                    })

            }
        })
    }

    //Suppression d'un document

    let supprimerDocument = document.querySelectorAll('.btnSupprimer')
    let zoneSupprimer = document.querySelector('.zoneDoc')
    let elementDelete = document.querySelectorAll('.lineSupprimer')
    let test = document.querySelectorAll('.testDrop')
    let poubelle = document.querySelector('.poubelle')


    for (let i = 0; i < supprimerDocument.length; i++) {
        supprimerDocument[i].addEventListener('click',()=>{
            let alert = confirm("Voulez vous supprimer définitivement ce document ainsi que toute les notes qui lui sont reliées ?")
            if (alert){
                fetch("/supprimerFichier",{
                    method :'POST',
                    body : supprimerDocument[i].dataset.id
                })
                    .then(()=>{
                        zoneSupprimer.removeChild(elementDelete[i]);
                    })

            }
        })
    }


}


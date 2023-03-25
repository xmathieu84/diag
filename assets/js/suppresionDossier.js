if (document.title ==='Ajout type de dossier'){
    let dossier = document.querySelectorAll('.dossier')
    let supprimer = document.querySelectorAll('.delete')
    let zone = document.querySelector('.zoneSupprimer')
    for (let i = 0; i < dossier.length; i++) {
        supprimer[i].addEventListener('click',()=>{
            let alerte = confirm("Souhaitez-vous supprimer définitivement ce dossier ainsi que tout son contenu ?")
            if (alerte){
                fetch("/suppressionDossier",{
                    method : 'POST',
                    body : dossier[i].dataset.id
                })
                    .then(()=>{
                        zone.removeChild(dossier[i])
                    })

            }

        })
    }

}
if (document.title==="Sous dossier"){
    let deleteDg = document.querySelector('.deleteDg')
    let sousDos = document.querySelectorAll('.sousDos')
    if (deleteDg){
        deleteDg.addEventListener('click',()=>{
            let alert = confirm("Voulez supprimer définitivement le dossier général ainsi que tout ses composants ?")
            if (alert){
                fetch("/deleteDossierGen",{
                    method: 'POST',
                    body :deleteDg.dataset.id
                })
                    .then(()=>{
                        document.location.reload()
                    })
            }
        })
    }

let deleteDossier = document.querySelectorAll('.deleteDossier')
    if (deleteDossier){
        for (let i = 0; i < deleteDossier.length; i++) {
            deleteDossier[i].addEventListener('click',()=>{
                let alert = confirm('Voulez vous supprimer ce dossier ainsi que tout ces composants ?')
                if (alert){
                    fetch("/deleteSousDossier",{
                        method : 'POST',
                        body : deleteDossier[i].dataset.id
                    })
                        .then(()=>{
                            document.querySelector('.zoneSupprimer').removeChild(sousDos[i]);
                        })

                }
            })
        }
    }
}
if (document.title==="Create Pack ODI"){
    let nom = document.querySelector("#nomPack")
    let typeBien = document.querySelectorAll('.typeBien')
    let missions = document.querySelectorAll('.missionAdmin')
    let valider = document.querySelector('.validerPack')

    valider.addEventListener('click',()=>{
        let confirmer = confirm("On valide le pack ?")
        let listeBienExclu = [];let listeMission=[];
        if (confirmer){
            for (const mission of missions) {
                if (mission.checked){
                    listeMission.push(mission.value)
                }
            }
            for (const bien of typeBien) {
                if (bien.checked ===false){
                    listeBienExclu.push(bien.value)
                }
            }
        }

        fetch("/administrateur/savePack",{
            method : 'post',
            body : JSON.stringify({
                nomPack:nom.value,
                bien : listeBienExclu,
                mission :listeMission
            })
        }).then(()=>{
            alert("Le pack a été enregistrés")
            document.location.reload()
        })
    })
}
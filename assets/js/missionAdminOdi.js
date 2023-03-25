if (document.title==="Modifier et ajouter des missions"){
    let missions = document.querySelectorAll(".missionAdmin")
    let oldFamilles = document.querySelectorAll(".oldFamille")
    let zoneSousFamille = document.querySelector('.zoneSousFamille')
    let validerMissions = document.querySelectorAll('.btn-success')
    let actifs =  document.querySelectorAll('.actif')
    for (const mission of missions) {
        mission.addEventListener('keyup',()=>{
            fetch("/administrateur/modifierMission/"+mission.dataset.mission,{
                method : 'post',
                body : mission.value
            })
        })
    }


    for (const validerMission of validerMissions) {
        validerMission.addEventListener('click',()=>{
            let listeTaille=[]
            let typeMission;
            let mission = document.querySelector(validerMission.dataset.mission)
            let tailles = document.querySelectorAll(validerMission.dataset.taille)
            let types = document.querySelectorAll(validerMission.dataset.type)
            for (const type of types) {
                if (type.checked){
                    typeMission = type.value
                }
            }
            for (const taille of tailles) {
                if (taille.checked===false){
                    listeTaille.push(taille.value)
                }
            }
            fetch("/administrateur/validerMission",{
                method : 'post',
                body : JSON.stringify({
                    mission : mission.value,
                    type : typeMission,
                    taille : listeTaille
                })
            })
        })
    }
    for (const actif of actifs) {
        actif.addEventListener('change',()=>{
            fetch("/administrateur/activerMission",{
                method : "post",
                body : JSON.stringify({
                    id:actif.value,
                    act : actif.dataset.actif
                })

            }).then((response)=>{
                return response.json()
            })
                .then((response)=>{
                    actif.dataset['actif']=(response)
                })
        })
    }
}
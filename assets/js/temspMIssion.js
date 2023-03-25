if (document.title=== "Durée mission"){
    let temps = document.querySelectorAll('.tempsMission')
    let valider = document.querySelector('.btn-maincolor')
    let id = document.querySelector("input[type=hidden]")
    let routeValiderMission;let routeRetrouverMission;

    if (id){
        routeValiderMission = "/entreprise/validerTemps/"+id.value
        routeRetrouverMission= "/entreprise/retrouverTempsMission/"+id.value
    }
    else{
        routeValiderMission = "/entreprise/validerTemps"
        routeRetrouverMission= "/entreprise/retrouverTempsMission"
    }
    valider.addEventListener('click',()=>{

        let confirmer = confirm("Souhaitez vous enregistrer ces durées de mission ?")
        if (confirmer){
            let listeMission=[]
            let listeBien=[]
            let tempsMission=[]
            for (const temp of temps) {
                listeMission.push(temp.dataset.mission)
                listeBien.push(temp.dataset.bien)
                tempsMission.push(temp.value)
            }
            fetch(routeValiderMission,{
                method:'post',
                body:JSON.stringify({
                    mission : listeMission,
                    bien : listeBien,
                    temps : tempsMission
                })
            }).then(()=>{
                alert("Les durées de missions on bien été enregistrées")
            })
        }
    })

    document.addEventListener("DOMContentLoaded",()=>{

        let listeMission=[]
        let listeBien=[]
        for (const temp of temps) {
            listeMission.push(temp.dataset.mission)
            listeBien.push(temp.dataset.bien)
        }
        fetch(routeRetrouverMission,{
            method:'post',
            body : JSON.stringify({
                bien:listeBien,
                mission:listeMission
            })
        }).then((response)=>{
            return response.json()
        }).then((response)=>{
            console.log(response)
            for (let i = 0; i < response.length; i++) {
                temps[i].value = response[i]
            }
        })
    })
}
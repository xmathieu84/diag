if (document.title==="Vos diagnostics immobilier"){

    let inter = document.querySelector('input[type=hidden]').value
    let missions = document.querySelectorAll('input[type=checkbox]')
    let liste=[];
    let valider = document.querySelector('.btn-maincolor')
    document.addEventListener("DOMContentLoaded",()=>{
        for (const mission of missions) {
            liste.push(mission.value)
        }
        fetch("/prixMissions/"+inter,{
            method : 'post',
            body : JSON.stringify({
                mission : liste
            })
        })
            .then((response)=>{
                return response.json()
            })
            .then((response)=>{
                console.log(response)
            })
    })

    valider.addEventListener('click',()=>{
        let missionChoisie = document.querySelectorAll('input[type=checkbox]:checked')
        let listeMission=[];
        for (const mission of missionChoisie) {
            listeMission.push(mission.value)
        }
        fetch("/ajoutMissionInter/"+inter,{
            method : "POST",
            body : JSON.stringify({
                liste : listeMission
            })
        }).then((response)=>{
            return response.json()
        }).then((response)=>{
            document.location.href = "/choixOdi/"+response
        })
    })

}
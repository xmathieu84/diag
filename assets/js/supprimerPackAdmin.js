if (document.title==="Modifier pack"){
    let supprimers = document.querySelectorAll(".deleteMission")
    let listeMission = document.querySelector('.list-group-horizontal')
    for (const supprimer of supprimers) {
        supprimer.addEventListener('click',()=>{
            let confirmer = confirm("On supprime la mission ? Cette action est définitive!")
            if (confirmer){
                document.location.href = supprimer.href
            }
        })
    }
    let missions = document.querySelectorAll('input[type=checkbox]')

    for (const mission of missions) {
        mission.addEventListener('change',()=>{
            if (mission.checked){
                fetch("/administrateur/addMissionAdmin/"+mission.dataset.pack,{
                    method : 'post',
                    body : mission.value
                })
                    .then(()=>{
                        mission.parentNode.parentNode.parentNode.removeChild(mission.parentNode.parentNode)
                        let li = document.createElement('li')
                        li.classList.add('list-group-item')
                        li.innerText = mission.dataset.nom
                        li.appendChild(document.createElement('br'))
                        let a = document.createElement("a")
                        a.classList.add("deleteMission")
                        a.innerText = "X"
                        a.href = "/administrateur/supprimerMission/"+mission.dataset.pack+"/"+mission.value
                        li.appendChild(a)
                        listeMission.appendChild(li)
                    })

            }
        })

    }
}

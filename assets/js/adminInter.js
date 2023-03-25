if (document.title ==="Admin intervention"){
    let valider= document.querySelector('.btn-lg')
    let type = document.querySelector('#type')
    let mission = document.querySelector("#mission")


    let description = document.querySelector("#description")
     valider.addEventListener('click',()=>{
         if (type.value !="" && mission.value!="" && description.value!=""){

            let contenu = JSON.stringify({
                type: type.value,
                mission : mission.value,
                description : description.value,

            })
             fetch("/administrateur/ajoutMission",{
                 body : contenu,
                 method : "POST"
             })
                 .then(()=>{

                     alert("La nouvelle mission est bien enregistrée. Avant de la mettre en ligne un developpeur dois intervenir pour la mise en forme. ")
                     document.location.reload()
                 })
         }else{
             alert("Un champs n'est pas remplis !!")
         }
     })

    let online= 0;
    let oldMission = document.querySelectorAll('.missionActif')
    let avertissement;
    for (let i = 0; i < oldMission.length; i++) {
        oldMission[i].addEventListener('change',()=>{
            if (oldMission[i].checked ===false){
                online = 0
                avertissement = confirm("La mission ne sera plus disponible auprès des demandeurs ainsi que des OTD.")
            }
            else{
                online = 1;
                avertissement = confirm("La mission va être disponible auprès des utilisateurs du site")
            }
            if (avertissement){
                fetch("/administrateur/suspendreMission/"+oldMission[i].value,{
                    body : online,
                    method : 'POST'
                })
            }

        })
    }
}
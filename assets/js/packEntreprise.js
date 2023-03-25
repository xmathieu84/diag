
if (document.title === "Pack entreprise"){
    let packs = document.querySelectorAll('.packOdi')
    let selectionner = document.querySelectorAll('button.btn-maincolor')
    let retirer = document.querySelectorAll('button.btn-danger')

    packs.forEach((pack,index)=>
        pack.addEventListener('change',()=>{
            console.log(pack)
            if (pack.checked){
                fetch("/entreprise/souscrirePack/"+pack.dataset.salarie,{
                    method : 'POST',
                    body : pack.value
                }).then(()=>{
                    selectionner[index].style.display="none"
                    retirer[index].style.display="block"

                })
            }
            else{

                fetch('/entreprise/retirerPack/'+pack.dataset.salarie,{
                    method : 'POST',
                    body : pack.value
                })
                    .then(()=>{
                        selectionner[index].style.display="block"
                        retirer[index].style.display="none"
                    })

            }
        })
    )
}

if (document.title ==="Modifier mes packs") {

    let supprimers = document.querySelectorAll(".deleteMission")
    let typeFamille = document.querySelectorAll('.typeFamille')
    let missions = document.querySelectorAll('.inputMission')
    let listeMission = document.querySelectorAll(".listeMissionPack")

    function deleteMission(supprimer,index,listeMission){
        let elementListe = document.querySelectorAll('.missionModifie')
        supprimer.addEventListener('click',()=>{
            let liste = elementListe[index].parentNode

            let confirmer = confirm("Souhaitez vous supprimer cette missions de votre pack ?")
            if (confirmer){
                fetch("/entreprise/supprimerMissionPack",{
                    method : "POST",
                    body : JSON.stringify({
                        idMission : elementListe[index].dataset.value,
                        idPack : liste.dataset.pack
                    })
                })
                    .then(()=>{
                        liste.removeChild(elementListe[index])
                        for (let i = 0; i < typeFamille.length; i++) {
                            if (typeFamille[i].dataset.type === elementListe[index].dataset.type){
                                let divTete = document.createElement('div')
                                divTete.classList.add("col-md-4","col-sm-6","col-12","mt-4", "typeMission")
                                let divRow = document.createElement('div')
                                divRow.classList.add('row')
                                let divInput = document.createElement("div")
                                divInput.classList.add('col-1')
                                let input = document.createElement('input')
                                input.classList.add("inputMission")
                                input.setAttribute('data-pack',liste.dataset.pack)
                                input.value = elementListe[index].dataset.value
                                input.type = "checkbox"
                                divInput.appendChild(input)
                                let divLabel = document.createElement('div')
                                divLabel.classList.add('col-11')
                                let label = document.createElement('label')

                                console.log(elementListe[index].innerHTML.split('<button>'))
                                let contenu = elementListe[index].innerText.split('\n');
                                label.innerText = contenu[0]
                                divLabel.appendChild(label)
                                divRow.appendChild(divInput)
                                divRow.appendChild(divLabel)
                                divTete.appendChild(divRow)
                                typeFamille[i].appendChild(divTete)

                            }
                        }
                        alert("La mission a bien été supprimée")
                        missions = document.querySelectorAll('.inputMission')
                        listeMission = document.querySelectorAll(".listeMissionPack")
                        missions.forEach((mission,i)=>{
                            addMission(mission,i,listeMission)
                        })
                    })
            }

        })
    }
    supprimers.forEach((supprimer, index) =>
      deleteMission(supprimer,index,listeMission)

    )
    function addMission(mission,i,listeMission){
        let typeMission = document.querySelectorAll('.typeMission')
        mission.addEventListener('change',()=>{
            if (mission.checked){
                fetch("/entreprise/ajouterMission",{
                    method : 'POST',
                    body : JSON.stringify({
                        idPack : mission.dataset.pack,
                        idMission : mission.value
                    })
                })
                    .then((response)=>{
                        return response.json()
                    })
                    .then((response)=>{
                        for (const lMission of listeMission) {

                            if (lMission.dataset.pack === mission.dataset.pack){
                                let li = document.createElement('li')
                                li.innerText = response.mission+" "
                                li.classList.add("missionModifie")
                                li.setAttribute('data-type',response.typeDiag)
                                li.setAttribute("data-value",response.id)
                                let button = document.createElement('button')
                                button.classList.add("deleteMission")
                                button.setAttribute("data-toggle","tooltip")
                                button.setAttribute("data-placement","top")
                                button.setAttribute("title","Supprimer")
                                button.innerText = "X"
                                li.appendChild(button)
                                lMission.appendChild(li)
                                console.log(typeMission[i])
                                typeMission[i].parentNode.removeChild(typeMission[i])
                                supprimers = document.querySelectorAll(".deleteMission")
                                listeMission = document.querySelectorAll(".listeMissionPack")
                                //Déplacer la suppression en dehors de la future fonction
                                typeMission = document.querySelectorAll('.typeMission')
                                supprimers.forEach((supprimer, index) =>
                                    deleteMission(supprimer,index,listeMission)
                                )
                            }
                        }
                    })
            }
        })
    }
    missions.forEach((mission,i)=>{
        addMission(mission,i,listeMission)
    })
}
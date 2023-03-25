if (document.title === "Inscription salarie") {

    let mission = document.querySelector('.mission')
    let odi = document.querySelector('.odi')
    let valeurOdi = document.querySelector('#ajoutersalarie_salarie')
    let missionCopie = document.querySelector('#ajoutersalarie_mission')
    let pack = document.querySelector('#ajoutersalarie_pack')
    let tafif = document.querySelector('#ajoutersalarie_tarif')


    let ajouts = document.getElementsByName("ajoutersalarie[copier]")
    for (const ajout of ajouts) {
        ajout.addEventListener('change',()=>{
            if (ajout.value==="Oui"){
                missionCopie.checked =true
                missionCopie.setAttribute('required','required')
                mission.style.display = 'block'
                odi.style.display='block'
                valeurOdi.setAttribute('required','required')
            }
            else{
                missionCopie.checked =false
                pack.checked = false
                tafif.checked = false
                mission.style.display = 'none'
                odi.style.display='none'
                valeurOdi.removeAttribute('required')
                missionCopie.removeAttribute('required')
            }
        })
    }
    missionCopie.addEventListener('change',()=>{

        if (!missionCopie.checked){
           pack.checked =false
            tafif.checked=false
        }
    })
    tafif.addEventListener('change',()=>{
        if (tafif.checked){
            missionCopie.checked =true
        }
    })
    pack.addEventListener('change',()=>{
        if (pack.checked){
            missionCopie.checked=true
        }
    })
}
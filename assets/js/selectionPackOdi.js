if (document.title ==="Choix des packs ODI"){
    let packs = document.querySelectorAll(".packOdi")


    let route;
    function retirerPack(btn,input){
        for (let i = 0; i < btn.length; i++) {
                input[i].addEventListener('change',()=>{

                    fetch("/entreprise/retirerPack",{
                        method : 'POST',
                        body : input[i].value
                    })
                        .then((response)=>{
                            return response.json()
                        })
                        .then((response)=>{

                        })

                })


        }
    }
    for (let i = 0; i < packs.length; i++) {
        packs[i].addEventListener('change',()=>{
            if (packs[i].checked){
                route = "/entreprise/souscrirePack"
            }
            else{
                route = "/entreprise/retirerPack/"
            }
                fetch(route,{
                    method : 'POST',
                    body : packs[i].value
                })
                    .then((response)=>{
                        return response.json()
                    })
                    .then((response)=>{
                        packs[i].previousElementSibling.innerText = "Retirer ce pack"
                    })

        })
    }

    document.addEventListener('DOMContentLoaded',()=>{
        fetch("/entreprise/listePackSouscrit",{
            method : 'GET'
        })
            .then((response)=>{
                return response.json()
            })
            .then((response)=>{
                for (const id of response) {
                    for (let i = 0; i < packs.length; i++) {
                        if (packs[i].value == id){
                            packs[i].checked = true
                            packs[i].previousElementSibling.innerText = "Retirer ce pack"


                        }

                    }
                }

            })
    })


}

if (document.title ==="Choix des packs ODI"){
    let missions = document.querySelectorAll('.packPersoOdi')
    let nom = document.querySelector('#nomPack')
    let biens = document.querySelectorAll('.typeBien')
    let valider = document.querySelector('.btn-primary')
    let supprimerPack = document.querySelectorAll('.supprimerPackOdi')

    valider.addEventListener('click',()=>{
        let liste = []
        let bienExlu = [];

        for (const bien of biens) {
            if (bien.checked===false){
                bienExlu.push(bien.value)
            }
        }

        for (const mission of missions) {

            if (mission.checked ===true){
                liste.push(mission.value)
            }
        }
        if (nom.value !==""&& liste.length>1){
            fetch("/entreprise/validerpack", {
                method: 'POST',
                body: JSON.stringify({
                    id: liste,
                    nom: nom.value,
                    bienExlu : bienExlu
                })
            }).then(()=>{
                document.querySelector('dialog').showModal()
                document.querySelector('.validerPackPerso').addEventListener('click',()=>{
                    document.querySelector('dialog').close()
                    for (const mission of missions) {
                        mission.checked = false
                    }
                    for (const bien of biens) {

                            bien.checked = false

                    }
                    nom.value ="";
                })

            })
        }
        else{
            if (nom.value===""){
                alert("Le nom de votre pack est obligatoire")
            }
            if (liste.length<=1){
                alert("Un pack doit être composé d'au moins 2 missions")
            }
        }

    })

    for (let i = 0; i < supprimerPack.length; i++) {
        supprimerPack[i].addEventListener('click',(e)=>{
            e.preventDefault()
            let confirmer = confirm('Souhaitez vous réelement supprimer ce pack ?')
            if (confirmer){
                document.location.href = supprimerPack[i].href
            }
        })
    }
}
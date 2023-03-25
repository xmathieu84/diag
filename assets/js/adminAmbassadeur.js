if (document.title==="Liste ambassadeur"){

    let profil = document.getElementsByName('type')
    let zoneOtd = document.querySelector('.abonnementOtd')
    let zoneInsti = document.querySelector('.profilInsti')
    let zoneGc = document.querySelector('.profilGc')
    let valider = document.querySelector('.btn-primary')
    let type;
    let profilBdd;
    let debut = document.querySelector('#debut')
    let fin = document.querySelector('#fin')
    let codePromo = document.querySelector('#codePromo')
    let departement = document.querySelector('#departement')
    let prix = document.querySelector('#prix')
    let duree = document.querySelector('#duree')
    let commentaire = document.querySelector('textarea')
    let route;
    for (let i = 0; i < profil.length; i++) {
        profil[i].addEventListener('change',()=>{
            if (profil[i].value ==='otd'){
                zoneOtd.style.display = 'block'
                zoneInsti.style.display = 'none'
                zoneGc.style.display = 'none'
                type = document.querySelector('#abpoOtd')
            }
            else if (profil[i].value ==='gc'){
                zoneOtd.style.display = 'none'
                zoneInsti.style.display = 'none'
                zoneGc.style.display = 'block'
                type = document.querySelector('#profilGc')
            }
            else{
                zoneOtd.style.display = 'none'
                zoneInsti.style.display = 'block'
                zoneGc.style.display = 'none'
                type = document.querySelector('#profilInsti')
            }
        })
        profilBdd = profil[i].value
    }
    valider.addEventListener('click',()=>{
        let alert = confirm("Tout les champs sont-ils correctement remplis ?")
        if (alert){
            if (debut.value !="" && fin.value !="" && prix.value !="" && type.value !="" && codePromo.value !="" && duree.value !=""){
                if (type.classList.contains('abonnement')) {
                    route = "/administrateur/ambassadeurOtd"
                }
                else if (type.classList.contains('institution')) {
                    route="/administrateur/ambassadeurInsti";
                }
                else{
                    route = "/administrateur/ambassadeurGc"
                }

            }
            else {
                alert('Il manque des informations pour créer le code promotionnel')
            }
            let content = JSON.stringify({
                dateDebut : debut.value,
                dateFin : fin.value,
                code : codePromo.value,
                nbreMax : departement.value,
                prix: prix.value,
                com : commentaire.value,
                profil : type.value,
                duree : duree.value
            })
            fetch(route,{
                method : 'POST',
                body : content
            })
        }
    })
}
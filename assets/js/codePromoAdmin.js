if (document.title ==='Promo abonnement'){
        let type = document.getElementsByName('type')

    let valider = document.querySelector('.btn-primary')
    let profil;
        let dateDebut = document.querySelector('#debut')
    let dateFin = document.querySelector('#fin')
    let remise = document.querySelector('#remise')
    let codePromo = document.querySelector('#codePromo')
    for (let i = 0; i < type.length; i++) {
        type[i].addEventListener('change',()=>{

            if (type[i].value ==='otd'){
                document.querySelector('.abonnementOtd').style.display = 'block'
                document.querySelector('.profilInsti').style.display = 'none'
                document.querySelector('.profilGc').style.display = 'none'
                profil = document.querySelector('#abpoOtd')


            } else if(type[i].value ==='gc'){
                document.querySelector('.abonnementOtd').style.display = 'none'
                document.querySelector('.profilInsti').style.display = 'none'
                document.querySelector('.profilGc').style.display = 'block'
                profil = document.querySelector('#profilGc')

            } else{
                document.querySelector('.abonnementOtd').style.display = 'none'
                document.querySelector('.profilInsti').style.display = 'block'
                document.querySelector('.profilGc').style.display = 'none'
                profil = document.querySelector('#profilInsti')

            }
        })
    }

    valider.addEventListener('click',()=>{
        let route;
            if (dateDebut.value !="" && dateFin.value !="" && remise.value !="" && profil.value !="" && codePromo.value !=""){
                if (profil.classList.contains('abonnement')) {
                    route = "/administrateur/promoOtd"
                }
                else if (profil.classList.contains('institution')) {
                    route="/administrateur/promoInsti";
                }
                else{
                    route = "/administrateur/promoGc"
                }

            }
            else {
                alert('Il manque des informations pour créer le code promotionnel')
            }


        let contenu = JSON.stringify({
            debut : dateDebut.value,
            fin :dateFin.value,
            type : profil.value,
            remise : remise.value,
            code : codePromo.value
        })
        fetch(route,{
            body : contenu,
            method : 'POST'
        })
            .then(()=>{
                document.location.reload();
            })
    })
}
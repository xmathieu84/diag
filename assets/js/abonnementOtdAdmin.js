if (document.title ==="Abonnement admin OTD"){
    let nom = document.querySelectorAll('.nomAboOtd')
    let prixAbo = document.querySelectorAll('.prixAboOtd')
    let prixOtdSup = document.querySelectorAll('.prixOtdSup')
    let otdMax = document.querySelectorAll('.otdMax')
    let idAbo = document.querySelectorAll('.idAbo')
    let type =null;
    function changeInfoAbonnement(nomChange){
        for (let i = 0; i < nomChange.length; i++) {
            nomChange[i].addEventListener('keyup',()=>{
                switch (nomChange){
                    case  nom : {
                        type ='nom';
                        break
                    }
                    case  prixAbo : {
                        type = 'prixAbo'
                        break
                    }
                    case  prixOtdSup : {
                        type = 'prixOtdSup'
                        break
                    }
                    case  otdMax : {
                        type = 'otdMax'
                        break
                    }
                }
                let content = JSON.stringify({
                    typeChange : type,
                    id : idAbo[i].dataset.id,
                    valeur : nomChange[i].value
                })
                fetch("/administrateur/changeElementAbonnement",{
                    method :'POST',
                    body : content
                })
            })
        }
    }
    changeInfoAbonnement(nom)
    changeInfoAbonnement(prixAbo)
    changeInfoAbonnement(prixOtdSup)
    changeInfoAbonnement(otdMax)
}
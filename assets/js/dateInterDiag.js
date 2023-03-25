import moment from "moment/moment";
if (document.title==="Diagnostics en cours"){

    let btnModals = document.querySelectorAll('.btn-maincolor')
    let heure = document.querySelectorAll('input[type=time]')
    let listeInter = document.querySelectorAll('.listeInter')
    let validerHeure = document.querySelectorAll('.validerHeure')
    let heureChoisie;
    let idInter;
    btnModals.forEach(function(btnModal,index){
        let valide = true
        btnModal.addEventListener('click',()=>{
            idInter= btnModal.dataset.inter;
            fetch("/retrouverInterDate",{
                body : idInter,
                method : 'POST'
            }).then((response)=>{
                return response.json()
            })
                .then((response)=>{
                    response.listeInter.forEach(function (reponse,key){
                        let li = document.createElement('li')
                        li.classList.add("list-group-item")
                        li.innerText = "Intervention n°"+(key+1)+" prévue de "+reponse.dateDebut+" à "+reponse.dateFin
                        listeInter[index].appendChild(li)

                    })
                    heure[index].addEventListener('change',()=>{
                        heureChoisie = Date.parse(btnModal.dataset.date+' '+heure[index].value)
                        let duree = response.duree*60*1000
                        let heureFin = heureChoisie+duree
                        let limiteDebut = Date.parse(btnModal.dataset.date+" 8:00")
                        let limiteFin = Date.parse(btnModal.dataset.date + " 19:00")
                        response.listeInter.forEach(function(dateInter,i){
                            if (heureChoisie<=limiteDebut ||  heureFin>=limiteFin
                                || (dateInter.debut*1000<heureChoisie || heureChoisie<dateInter.fin*1000  )){
                                heure[index].value=""
                                alert("Attention l'horaire ne peut pas être validé")
                            }
                        })

                    })
                })

        })
    })

    validerHeure.forEach(function (btn,i){
        btn.addEventListener('click',()=>{
            fetch("/validerHeure",{
                method:'post',
                body : JSON.stringify({
                    id : idInter,
                    temps : heureChoisie
                })
                }).then(()=>{
                alert("Votre heure de rendez vous a bien été enregistrée.")
                document.location.reload()
            })
        })
    })
}
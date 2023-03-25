if (document.title ==="Mes demandes en cours"){
    let valider = document.querySelectorAll('.btn-primary')
    let date = document.querySelectorAll('.dateInter')
    let message = document.querySelectorAll('.message')

    for (let i = 0; i < valider.length; i++) {

        date[i].addEventListener('change',()=>{

            let dateInter = Date.parse(date[i].value)
            fetch('/changerDateInter/'+valider[i].dataset.id,{
                method : 'POST',
                body : dateInter
            })
                .then((response)=>{
                    return response.json()
                })
                .then((response)=>{
                    if (response === 0){
                        message[i].innerHTML = "Il n'y a pas d'OTD disponible pour cette date. Essayez une autre date"
                    }
                    else{
                        message[i].innerHTML = 'Vous avez '+response+' OTD(s) disponible(s) pour cette date'
                    }
                })
        })
        valider[i].addEventListener('click',()=>{
            let dateInter = Date.parse(date[i].value)
            fetch('/validerDateInter/'+valider[i].dataset.id,{
                method : 'POST',
                body : dateInter
            })
                .then(()=>{
                    //document.location.reload();
                })
        })
    }
}
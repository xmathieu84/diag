if (document.title ==="Changer banque"){

    let bouton = document.querySelector('.btn-maincolor')
    let textArea = document.querySelector('textarea')

    bouton.addEventListener('click',()=>{
        let confimer = confirm("Souhaitez vous envoyer votre demande ?")
        if (confimer){
            fetch("/validerModifBanqueOtd",{
                method :'POST',
                body : textArea.value
            })
                .then((response)=>{
                    alert('Votre demande bien été enregistrée')
                })
        }
    })

}
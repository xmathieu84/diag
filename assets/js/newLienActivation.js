if (document.title==='Compte non activé'){
    let button = document.querySelector('.btn-maincolor')

    button.addEventListener('click',()=>{
        fetch("/nouveauLienActivation",{
            method:'GET'
        })
            .then(()=>{
                document.querySelector('.message').innerHTML = "Un nouveau lien d'activation vient de vous être envoyé ."
            })

    })
}
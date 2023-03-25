if (document.title==="Envoie mail Otd"){
    let paragraphe =[];
    let text= document.querySelectorAll('.paragraphe')
    let valider = document.querySelector('.btn-success')
    let titreMail = document.querySelector('#titre')
    let codePromo = document.querySelector('#codePromo')
    let ancienne = document.querySelector('#ancienne')
    let description = document.querySelector('.description')
    ancienne.addEventListener('change',()=>{
        fetch('/administrateur/recupListeMail/'+ancienne.value,{
            method : 'GET'
        })
            .then((response)=>{return response.json()})
            .then((response)=>{
                console.log(response)
                titreMail.value = response.titre
                codePromo.value = response.codePromo
                for (let i = 0; i < response.contenu.length; i++) {
                    text[i].value = response.contenu[i]
                }
            })
    })
    valider.addEventListener('click',()=>{
        for (let i = 0; i < text.length; i++) {
            if (text[i].value !==""){
                paragraphe.push(text[i].value)
            }
        }
        let confirmation = confirm("On envoie les mails ?")
        if (confirmation && description.value !=="") {
            document.querySelector('.dialogAttente').style.display ='block'

            fetch("/administrateur/mailPartOtd", {
                method: 'POST',
                body: JSON.stringify({
                    para: paragraphe,
                    titre: titreMail.value,
                    code: codePromo.value,
                    description : description.value
                })
            })
                .then(() => {
                    paragraphe = [];
                    alert("Les mails sont partis")
                    document.querySelector('.dialogAttente').style.display ='none'
                })
        }else {
            alert("Des champs ne sont pas correctement remplis")
        }
    })
}
if (document.title ==="Autre envoie"){
    let valider = document.querySelector('.btn-success')
    let cible = document.querySelector('#cible')
    let codePromo = document.querySelector('#codePromo')
    let titre = document.querySelector('#titre')
    let contenu = document.querySelectorAll('.paragraphe')
    let description = document.querySelector('.description')
    let ancienne = document.querySelector('#ancienne')
    ancienne.addEventListener('change',()=>{
        fetch("/administrateur/recupListeMail/"+ancienne.value,{
            method : 'GET'
        })
            .then((response)=>{
                return response.json()
            })
            .then((response)=>{
                titre.value = response.titre
                codePromo.value = response.codePromo
                for (let i = 0; i < response.contenu.length; i++) {
                    contenu[i].value = response.contenu[i]
                }
            })
    })
    valider.addEventListener('click',()=>{
        let paragraphe =[];

        let confirmer= confirm('Toi vouloir envoyer tout les mails ?')
        if (confirmer){
            document.querySelector('.dialogAttente').style.display ='block'
            if (cible.value !=="" && description.value !==""){
                for (let i = 0; i < contenu.length; i++) {
                    if (contenu[i].value !==""){
                        paragraphe.push(contenu[i].value)
                    }
                }
                fetch("/administrateur/envoieMailAutre",{

                    method :'POST',
                    body : JSON.stringify({
                        titre : titre.value,
                        cible : cible.value,
                        code : codePromo.value,
                        content : paragraphe,
                        description : description.value,
                        departement : document.querySelector('.departement').value

                    })
                })
                    .then(()=>{
                        document.querySelector('.dialogAttente').style.display ='none'
                        alert("Les mails ont été envoyés !!")
                    })
            }
            else {
                alert("Il faut choisir une cible pour les mails !!")
            }
        }
    })
}

if (document.title ==="Envoie mail unique ambassadeur"){
    let mail = document.querySelector('#mail')

    document.querySelector('.btn-success').addEventListener('click',()=>{
        if (mail.value !==""){
            fetch("/administrateur/envoieMailAmbassadeur",{
                method : 'POST',
                body : mail.value
            })
                .then(()=>{
                    alert("L'email a été envoyé")
                })
        } else {
            alert("il manque l'adresse mail")
        }
    })

}

if(document.title ==="Désabonnement Otd"){
    let valider = document.querySelector('.btn-maincolor')
    let mail = document.querySelector('#mail')

    valider.addEventListener('click',()=>{
        if (mail.value !==""){
            fetch("desabonnementOtd",{
                method :'POST',
                body : mail.value
            })
                .then((response)=>{
                    return response.json()
                })
                .then((response)=>{
                    document.querySelector('.btn-primary').click()
                    document.querySelector('.message').innerHTML = response
                })
        }
    })
}
if (document.title==="Envoie mail journaliste"){
    let paragraphe =[];
    let text= document.querySelectorAll('.paragraphe')
    let valider = document.querySelector('.btn-success')
    let titreMail = document.querySelector('#titre')
    let codePromo = document.querySelector('#codePromo')

    valider.addEventListener('click',()=>{
        for (let i = 0; i < text.length; i++) {
            if (text[i].value !==""){
                paragraphe.push(text[i].value)
            }
        }
        let confirmer = confirm('Faire partir les e-mails ?')
        if (confirmer){
            document.querySelector('.dialogAttente').style.display ='block'
            fetch("/administrateur/mailJournaliste",{
                method : 'POST',
                body :JSON.stringify({
                    para : paragraphe,
                    titre : titreMail.value,

                })
            })
                .then(()=>{
                    paragraphe =[];
                    document.querySelector('.dialogAttente').style.display ='none'
                    alert("Les mails sont partis")
                })
        }

    })
}
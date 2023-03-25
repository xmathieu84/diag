if (document.title==="Remises"){
    let journee = document.getElementsByName('journee')
    let demiJournee = document.getElementsByName('demijournee')
    let markePlace = document.getElementsByName('marketPlace')
    let valider = document.querySelector('.validerRemiseTemps')
    let choixJournee;let choixDemi;let choixMarket
    let choixTypes = document.getElementsByName('typeRemise')
    let zoneRemise = document.querySelector('.zoneRemise')
    let validerRemise = document.querySelector('.validerRemise')
    let validerMarket = document.querySelector('.validerMarket')
    function alerte(){
        document.location.reload()
    }
    validerMarket.addEventListener('click',()=>{
        for (let i = 0; i < markePlace.length; i++) {
            if (markePlace[i].checked){
                choixMarket = markePlace[i].value

            }
            else{
                alert("Veuillez choisir une réponse.")
            }
            break;

        }
        fetch("/entreprise/validerMarket",{
            method : 'post',
            body : choixMarket
        }).then(()=>{
            alert("Votre choix a été enregistré .")
        })
    })
    valider.addEventListener('click',() => {
            if ((document.querySelector('#ouiJournee').checked === true || document.querySelector("#nonJournee").checked === true) &&
                (document.querySelector('#ouiDemiJournee').checked === true || document.querySelector("#nonDemiJournee").checked === true)) {
                for (let i = 0; i < journee.length; i++) {
                    if (journee[i].checked) {
                        choixJournee = journee[i].value
                    }
                    if (demiJournee[i].checked){
                        choixDemi = demiJournee[i].value
                    }

                }
                    fetch("/entreprise/validerChoix",{
                        method : 'POST',
                        body : JSON.stringify({
                            journee : choixJournee,
                            demiJournee : choixDemi,

                        })
                    }).then(()=>{
                        alert("Vos choix ont été enregistrés")
                    })


            }
        else
            {
                alert("Veuillez répondre à toutes les questions")
            }
        })
    for (const choixType of choixTypes) {
        choixType.addEventListener('change',()=>{
            fetch("/entreprise/recupereType/"+choixType.value,{
                method : 'get'
            })
                .then((response)=>{return response.json()})
                .then((response)=>{
                    for (const responseElement of response) {
                        let div = document.createElement('div')
                        div.classList.add('col-sm-4')
                        div.classList.add('col-12')
                        let input = document.createElement('input')
                        input.type = 'checkbox'
                        input.classList.add("form-check-input")
                        input.value = responseElement.id
                        let label = document.createElement('label')
                        label.innerText = responseElement.type
                        label.classList.add("form-check-label")
                        div.appendChild(input)
                        div.appendChild(label)
                        zoneRemise.appendChild(div)
                    }
                    validerRemise.addEventListener('click',()=>{
                        let debut = document.querySelector('#dateDebut')
                        let fin = document.querySelector('#dateFin')
                        let missions = document.querySelectorAll('.form-check-input')
                        let montant = document.querySelector(".montantRemise")
                        let listeMission = [];
                        if (missions){
                            for (let i = 0; i < missions.length; i++) {
                                if (missions[i].checked){
                                    listeMission.push(missions[i].value)
                                }

                            }
                        }
                       fetch("/entreprise/validerRemise/"+choixType.value,{
                           method : 'post',
                           body : JSON.stringify({
                               debut : debut.value,
                               fin : fin.value,
                               mission : listeMission,
                               montant : montant.value
                           })
                       }).then((reponse)=>{return reponse.json()})
                           .then((reponse)=>{
                               alert(reponse)
                               let myTimeout;
                               myTimeout = setTimeout(alerte, 1000);
                           })
                    })
                })
        })
    }


}
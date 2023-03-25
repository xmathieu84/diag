if (document.title === 'paiement'||document.title ==="Paiement diagnostic") {



    let accord = document.querySelector('#accord')
    let zoneBouton = document.querySelector('.bouton-submit')
    let button = document.createElement('button')
    let typePaiement = document.getElementsByName('typeCard')
    let accessKeyRef=document.querySelector('#accessKeyRef')
    let data = document.querySelector('#data')

    button.type = 'submit'
    button.classList.add('btn')
    button.classList.add('btn-maincolor')
    button.innerText = 'valider'


    document.addEventListener('DOMContentLoaded', () => {
        fetch('/chercherCgu', {
            method: 'POST',
            body: accord.dataset.inter
        })
            .then((reponse) => {
                return reponse.json()
            })
            .then((reponse) => {
                if (reponse ==='existe') {
                    accord.checked = true
                    zoneBouton.appendChild(button)
                    button.addEventListener('click',(e)=>{


                        if (data.value.length !==0 && accessKeyRef.value.length !==0){

                            document.getElementById('formulaire').submit()
                        }
                        else {
                            e.preventDefault()
                            alert('Choisisssez votre type de carte (Visa , Mastercard , Maestro ou virement bancaire)')
                        }

                    })
                }
            })

    })

    accord.addEventListener('change', () => {
        if (accord.checked) {
            fetch('/cgu', {
                method: 'POST',
                body: accord.dataset.inter
            })
                .then((reponse) => {
                    return reponse.json()
                })
                .then((reponse) => {
                    if (reponse === 'ok') {
                        zoneBouton.appendChild(button)
                        button.addEventListener('click',(e)=>{


                            if (data.value.length !==0 && accessKeyRef.value.length !==0){

                                document.getElementById('formulaire').submit()
                            }
                            else {
                                e.preventDefault()
                                alert('Choisisssez votre type de carte (Visa , Mastercard , Maestro ou virement bancaire)')
                            }

                        })
                    }
                })
        }
        else {
            fetch('/refus', {
                method: 'POST',
                body: accord.dataset.inter
            })
                .then((reponse) => {
                    return reponse.json()
                })
                .then((reponse) => {
                    if (reponse === 'ok') {
                        while (zoneBouton.firstChild) {
                            zoneBouton.removeChild(zoneBouton.lastChild);
                        }
                    }
                })
        }
    })
    let form = document.querySelector('form')

    for (let i = 0; i < typePaiement.length; i++) {
        typePaiement[i].addEventListener('change', () => {
            if (typePaiement[i].checked) {
                if (typePaiement[i].value === 'virement') {
                    console.log(typePaiement[i].dataset.typeinter)
                    let contenu = JSON.stringify({
                        'idInter': typePaiement[i].dataset.inter,
                        'typeInter': typePaiement[i].dataset.type,
                        'interType':typePaiement[i].dataset.typeinter
                    })
                    fetch('/creerVirement', {
                        method: 'POST',
                        body: contenu
                    })
                        .then((response) => {
                            return response.json()
                        })
                        .then((response) => {
                            form.style.display = 'none'
                            document.querySelector('.virement').style.display = 'block'
                            document.querySelector('.titulaire').innerHTML = response.PaymentDetails.BankAccount.OwnerName
                            document.querySelector('.numero').innerHTML = response.PaymentDetails.BankAccount.Details.IBAN
                            document.querySelector('.bic').innerHTML = response.PaymentDetails.BankAccount.Details.BIC
                            document.querySelector('.adresse').innerHTML = response.PaymentDetails.BankAccount.OwnerAddress.AddressLine1 + ' ' + response.PaymentDetails.BankAccount.OwnerAddress.PostalCode + ' ' + response.PaymentDetails.BankAccount.OwnerAddress.City
                            document.querySelector('.refVirement').innerHTML = response.PaymentDetails.WireReference

                        })
                }
                else {
                    document.querySelector('.virement').style.display = 'none'
                    form.style.display = 'block'
                    fetch('/creerCarte', {
                        method: 'POST',
                        body: typePaiement[i].value
                    })
                        .then((reponse) => {

                            return reponse.json()
                        })
                        .then((reponse) => {

                            document.getElementById('formulaire').action = reponse['carte']['CardRegistrationURL']
                            document.querySelector('#accessKeyRef').value = reponse['carte']['AccessKey']
                            document.querySelector('#data').value = reponse['carte']['PreregistrationData']
                        })
                }

            }
        })


    }
}

if (document.title === 'Paiement cerfa') {


    let typePaiement = document.getElementsByName('typeCard')
    let form = document.querySelector('form')
    let zoneBouton = document.querySelector('.bouton-submit')
    let button = document.createElement('button')
    let zoneWallet = document.querySelector('.montantWallet')
    let zoneMontant = document.querySelector('.montant')
    button.classList.add('btn')
    button.classList.add('btn-maincolor')
    button.innerHTML = 'Payer'
    for (let i = 0; i < typePaiement.length; i++) {
        typePaiement[i].addEventListener('change', () => {
            if (typePaiement[i].value == 'WALLET') {
                form.style.display = 'none'
                zoneWallet.style.display = 'block'
                fetch('/recupererWallet', {
                    method: 'GET'
                })
                    .then((response) => {
                        return response.json()
                    })
                    .then((response) => {
                        zoneMontant.innerHTML = response.Balance.Amount + ' €'
                    })

            }
            else {
                form.style.display = 'block'
                zoneWallet.style.display = 'none'
                fetch('/creationCarteCerfa', {
                    method: 'POST',
                    body: typePaiement[i].value
                })
                    .then((reponse) => {
                        return reponse.json()
                    })
                    .then((reponse) => {

                        zoneBouton.appendChild(button)
                        document.querySelector('#accessKeyRef').value = reponse['AccessKey']
                        document.querySelector('#data').value = reponse['PreregistrationData']
                    })
            }




        })


    }
}
if (document.title==="Validation Paiement"){

    let agent = navigator.userAgent;
    let java = window.navigator.javaEnabled();
    let language = navigator.language;
    let hauteur = window.screen.availHeight
    let largeur = window.screen.availWidth
    let couleur = window.screen.colorDepth
    let offset = new Date().getTimezoneOffset();

    let infoPaiement =  document.querySelector('#data')
    let idInter = document.querySelector('#inter')
    let type = document.querySelector('#type')
    let uuid = document.querySelector('#uuid')

    document.addEventListener('DOMContentLoaded',()=>{
            let content = JSON.stringify({
                userAgent:agent,
                javaActif:java,
                langage:language,
                width : hauteur,
                height : largeur,
                color : couleur,
                timeZone : offset,
                data : infoPaiement.value,
                inter : idInter.value,
                typeInter : type.value,
                uuid : uuid.value
            })
        fetch('/validerPaiement',{
            method:'POST',
            body:content,

        })
            .then((response)=>{
                return response.json()
            })
            .then((response)=>{

                document.location.href = response;
            })
    })
}
if (document.title ==='Traitement paiement HDD'){
    let agent = navigator.userAgent;
    let java = window.navigator.javaEnabled();
    let language = navigator.language;
    let hauteur = window.screen.availHeight
    let largeur = window.screen.availWidth
    let couleur = window.screen.colorDepth
    let offset = new Date().getTimezoneOffset();
    let infoPaiement =  document.querySelector('#data')
    let paiement = document.querySelector('#id')
    document.addEventListener('DOMContentLoaded',()=>{
        let content = JSON.stringify({
            userAgent:agent,
            javaActif:java,
            langage:language,
            width : hauteur,
            height : largeur,
            color : couleur,
            timeZone : offset,
            data : infoPaiement.value,
            paiement : paiement.value
        })
        fetch('/terminerPaiementHdd',{
            method:'POST',
            body:content

        })
            .then((response)=>{
                return response.json()
            })
            .then((response)=>{

                document.location.href = response;
            })
    })

}
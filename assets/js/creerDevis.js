
class Client {
    constructor(nom, prenom, entreprise, adresse, telephone, email,siret) {
        this.nom = nom;
        this.prenom = prenom;
        this.entreprise = entreprise;
        this.siret = siret
        this.adresse = adresse;
        this.telephone = telephone;
        this.email = email
    }
}
class ligneTableau {
    constructor(reference, designation, quantite, prixUnitaire, tva, montantHt) {
        this.reference = reference
        this.designation = designation
        this.quantite = quantite
        this.prixUnitaire = prixUnitaire
        this.tva = tva
        this.montantHt = montantHt
    }
}

if (document.title ==='Créer un devis') {

    let devis = document.querySelectorAll('.devis')
    let clientDevis = document.querySelectorAll('.client')
    let enregistrerLigne = document.querySelector('.enregistrerLigne')
    let validerTitre = document.querySelector('.validerTitre')
    let titreDevis = document.querySelector('.titreDevis')
    let titre = document.querySelector('.titre')
    let table = document.querySelector('tbody')
    let sauverClient = document.querySelector('.sauverClient')
    let infoClient = document.querySelector('.infoClient')
    let boutonEnvoie = document.querySelector('.btn-lg')
    let totalHt = document.querySelector('.ht')
    let totalTva = document.querySelector('.tvaDevisF')
    let totalTtc = document.querySelector('.ttc')
    let conditionP = document.querySelector('#conditionP')
    let acompteDevis = document.querySelector('.acompteDevis')
    let ligneArray = [];
    let pourcentageAcompte = document.querySelector('#pourcentageAcompte')
    let pourcentageMontant = document.querySelector('#pourcentagePaiement')
    let sommeFinale = document.querySelector('input[type=hidden]')
    let listeCondition = document.querySelector('.listeCondition')
    totalHt.innerText = 0
    totalTva.innerText = 0
    totalTtc.innerText = 0

    function somme(objet) {

        let montantHt = 0;
        let montantTva = 0;
        let montantTTC = 0;
        for (let i = 0; i < objet.length; i++) {
            const element = objet[i];
            for (const key in objet[i]) {

                if (objet[i].hasOwnProperty(key)) {
                    if (key === 'montantHt') {

                        montantHt += parseFloat(element['montantHt']);
                        montantTva += (element['montantHt'] * element['tva'] / 100)
                        totalHt.innerText = montantHt
                        totalTva.innerText =  montantTva
                        totalTtc.innerText = montantTva+montantHt
                        let final = parseFloat(montantTva)+parseFloat(montantHt)
                        document.querySelector('.prix').innerHTML = " "+ final +" €"
                        sommeFinale.value = final
                        if (conditionP.value ==="acompte"){
                            document.querySelector('#montantTTCAcompte').value = pourcentageAcompte.value*final/100
                            document.querySelector('#montantTTCPaiement').value = pourcentageMontant.value*final/100
                        }



                    }



                }
            }

        }
    }

    validerTitre.addEventListener('click', () => {
        titreDevis.innerHTML = ''
        let p = document.createElement('p')
        p.classList.add('h3')
        titreDevis.appendChild(p)
        p.innerHTML = titre.value
    })
    devis[3].addEventListener('keyup',()=>{
        if (devis[2].value !==""){
            devis[5].value = Math.round((devis[3].value * devis[2].value)*100)/100
        }
        else{
            devis[5].value = 0
        }
    })
    devis[2].addEventListener('keyup',()=>{
        if (devis[3].value !==""){
            devis[5].value = Math.round((devis[3].value * devis[2].value)*100)/100
        }
        else{
            devis[5].value = 0
        }
    })
    enregistrerLigne.addEventListener('click', () => {
        let ligneDevis = new ligneTableau()
        let tr = document.createElement('tr')
        tr.classList.add("ligneDevis")
        for (let i = 0; i < devis.length; i++) {
            let td = document.createElement('td')
            td.classList.add('h6')
            td.innerText = devis[i].value;
            tr.appendChild(td)
        }

        ligneDevis.reference = devis[0].value
        ligneDevis.designation = devis[1].value
        ligneDevis.quantite = devis[2].value
        ligneDevis.prixUnitaire = devis[3].value
        ligneDevis.tva = devis[4].value
        ligneDevis.montantHt = devis[5].value
        ligneArray.push(ligneDevis)
        table.appendChild(tr)
        for (let i = 0; i < devis.length; i++) {
            devis[i].value = "";
        }


        let ligne = document.querySelectorAll(".ligneDevis")
        let devisM = document.querySelectorAll('.devisM')
        let valider = document.querySelector('.validerLigne')
        let supprimer = document.querySelector('.btnDelete')
        devisM[3].addEventListener('keyup',()=>{
            if (devisM[2].value !==""){
                devisM[5].value = Math.round((devisM[3].value * devisM[2].value)*100)/100
            }
            else{
                devisM[5].value = 0
            }
        })
        devisM[2].addEventListener('keyup',()=>{
            if (devisM[3].value !==""){
                devisM[5].value = Math.round((devisM[3].value * devisM[2].value)*100)/100
            }
            else{
                devisM[5].value = 0
            }
        })
        for (let i = 0; i < ligne.length; i++) {

            ligne[i].addEventListener('click',()=>{

                if (ligne.length===1){
                    document.querySelector(".btnDelete").style.display="none"
                }
                else{
                    document.querySelector(".btnDelete").style.display="block"
                }
                document.querySelector('.btnmodif').click()
                for (let j = 0; j < ligne[i].children.length; j++) {
                    devisM[j].value = ligne[i].children[j].innerText
                }
            valider.addEventListener('click',()=>{

                for (let j = 0; j < ligne[i].children.length; j++) {

                    ligne[i].children[j].innerText =  devisM[j].value
                }

                ligneArray[i].reference = devisM[0].value
                ligneArray[i].designation = devisM[1].value
                ligneArray[i].quantite = devisM[2].value
                ligneArray[i].prixUnitaire = devisM[3].value
                ligneArray[i].tva = devisM[4].value
                ligneArray[i].montantHt = devisM[5].value
                somme(ligneArray)
                document.querySelector('.modalModifC').click()

            })
                supprimer.addEventListener('click',()=>{

                    let index = ligneArray.indexOf(ligneArray[i])
                    ligneArray.splice(0,index)
                    try {
                        table.removeChild(ligne[i])
                    }
                    catch (e) {

                    }

                    somme(ligneArray);
                    document.querySelector('.modalModifC').click()
                })

            })
        }
        somme(ligneArray)
    })
    let client = new Client();
    sauverClient.addEventListener('click', () => {
        infoClient.innerHTML = '';
        for (let i = 0; i < clientDevis.length; i++) {
            let div = document.createElement('div')
            let span = document.createElement('span')

            span.innerHTML = clientDevis[i].value
            div.appendChild(span)
            infoClient.appendChild(div)

        }
        client.nom = clientDevis[0].value
        client.prenom = clientDevis[1].value
        client.entreprise = clientDevis[2].value
        client.siret = clientDevis[3].value
        client.adresse = clientDevis[4].value
        client.telephone = clientDevis[5].value
        client.email = clientDevis[6].value

    })

    let title;
    boutonEnvoie.addEventListener('click', () => {
        let typePaiement = [];
        if (titre.value ===""){
             title = ""
        }
        else {
             title = document.querySelector('.h3').innerHTML
        }
        if (conditionP.value ==="acompte"){
            typePaiement.push({
                condP : "acompte",
                acompte : document.querySelector('#montantTTCAcompte').value,
                comptant : document.querySelector('#montantTTCPaiement').value
            })
        }
        if (conditionP.value ==="comptant"){
            typePaiement.push({
                condP : "comptant",
                acompte : "",
                comptant : sommeFinale.value
            })
        }
        if (conditionP.value ==="30 jours"){
            typePaiement.push({
                condP : "30 jours",
                acompte : "",
                comptant : sommeFinale.value
            })
        }
        let corps = JSON.stringify({
            corpsTableau: ligneArray,
            corpsClient: client,
            ht: totalHt.innerHTML,
            TVA: totalTva.innerHTML,
            TTC: totalTtc.innerHTML,
            title: title,
            condition : typePaiement
        })
        fetch('/exportDevis', {
            method: 'POST',
            body: corps
        })
            .then((reponse) => {
                return reponse.json()
            })
            .then((reponse) => {

                window.open('/uploads/devis/' + reponse, '_blank')

            })
    })

    conditionP.addEventListener('change',()=>{
        if (conditionP.value ==="acompte"){
            acompteDevis.style.display = 'block'
        }
        else {
            acompteDevis.style.display = 'none'
        }
    })
    pourcentageAcompte.addEventListener('keyup',()=>{

        pourcentageMontant.value = 100 - pourcentageAcompte.value
        document.querySelector('#montantTTCAcompte').value = pourcentageAcompte.value*sommeFinale.value/100
        document.querySelector('#montantTTCPaiement').value = pourcentageMontant.value*sommeFinale.value/100
    })
    pourcentageMontant.addEventListener('keyup',()=>{

        pourcentageAcompte.value = 100 - pourcentageMontant.value
        document.querySelector('#montantTTCAcompte').value = pourcentageAcompte.value*sommeFinale.value/100
        document.querySelector('#montantTTCPaiement').value = pourcentageMontant.value*sommeFinale.value/100
    })

    document.querySelector('.validerConditions').addEventListener('click',()=>{
        while (listeCondition.firstChild){
            listeCondition.removeChild(listeCondition.lastChild)
        }
        if (conditionP.value ==='acompte'){

            let li1 = document.createElement('li')
            li1.innerText = "acompte : "+ document.querySelector('#montantTTCAcompte').value + "€"
            let li2 = document.createElement('li')
            li2.innerText = "Paiement comptant : "+ document.querySelector('#montantTTCPaiement').value + "€"
            listeCondition.appendChild(li1)
            listeCondition.appendChild(li2)
        }
        if (conditionP.value ==="comptant"){
            let li1 = document.createElement('li')
            li1.innerHTML = "100% soit :<span class=\"prix\">"+sommeFinale.value+" €</span><span class=\"type\"> Paiement comptant</span>"
            listeCondition.appendChild(li1)
        }
        if (conditionP.value ==="30 jours"){
            let li1 = document.createElement('li')
            li1.innerHTML = "100% soit :<span class=\"prix\">"+sommeFinale.value+" €</span><span class=\"type\"> Paiement à 30 jours</span>"
            listeCondition.appendChild(li1)
        }
    })
}


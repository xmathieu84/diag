
if (document.title == 'Création de facture') {
    class Client {
        constructor(nom, prenom, entreprise, adresse, telephone, email,siret) {
            this.nom = nom;
            this.prenom = prenom;
            this.entreprise = entreprise;
            this.adresse = adresse;
            this.telephone = telephone;
            this.email = email;
            this.siret = siret
        }
    }
    class Designation {
        constructor(designation1, designation2, designation3, designation4, designation5, designation6) {
            this.designation1 = designation1
            this.designation2 = designation2
            this.designation3 = designation3
            this.designation4 = designation4
            this.designation5 = designation5
            this.designation6 = designation6
        }
    }
    class ligneTableau {
        constructor(reference, intitule, quantite, prixUnitaire, tva, montantHt) {
            this.reference = reference
            this.intitule = intitule
            this.quantite = quantite
            this.prixUnitaire = prixUnitaire
            this.tva = tva
            this.montantHt = montantHt
        }
    }
    let facture = document.querySelectorAll('.facture')
    let clientFacture = document.querySelectorAll('.client')
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
    let caseDesignation = document.querySelectorAll('.designation')
    let ligneArray = [];
    let conditionP = document.querySelector('#conditionP')
    let acompteDevis = document.querySelector('.acompteDevis')
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
    enregistrerLigne.addEventListener('click', () => {
        let ligneFacture = new ligneTableau()
        let designation = new Designation()
        let tr = document.createElement('tr')
        tr.classList.add('ligneFacture')
        designation.designation1 = caseDesignation[0].value
        designation.designation2 = caseDesignation[1].value
        designation.designation3 = caseDesignation[2].value
        designation.designation4 = caseDesignation[3].value
        designation.designation5 = caseDesignation[4].value
        designation.designation6 = caseDesignation[5].value


        for (let i = 0; i < facture.length; i++) {
            let td = document.createElement('td')

            td.classList.add('h6')
            tr.appendChild(td)
            if (i == 1) {
                for (let k = 0; k < caseDesignation.length; k++) {

                    if (caseDesignation[i] != null) {
                        let p = document.createElement('p')
                        p.classList.add('h6')
                        p.innerHTML = caseDesignation[k].value
                        td.appendChild(p)
                    }


                }


            }else {
                td.innerHTML = facture[i].value;
            }
        }

        ligneFacture.reference = facture[0].value
        ligneFacture.intitule = designation
        ligneFacture.quantite = facture[2].value
        ligneFacture.prixUnitaire = facture[3].value
        ligneFacture.tva = facture[4].value
        ligneFacture.montantHt = facture[5].value
        ligneArray.push(ligneFacture)
        table.appendChild(tr)

        somme(ligneArray)
        for (let i = 0; i < facture.length; i++) {
            facture[i].value = ""
        }
        for (let i = 0; i <caseDesignation.length; i++) {
            caseDesignation[i].value=""
        }
        let ligneFact = document.querySelectorAll('.ligneFacture')
        let designationM = document.querySelectorAll('.designationM')
        let factM = document.querySelectorAll('.factureM')
        let valider = document.querySelector('.validerLigne')
        let supprimer = document.querySelector('.effacerLigne')
        factM[1].addEventListener('keyup',()=>{

            factM[4].value = Math.round((factM[1].value * factM[2].value)*100)/100
        })
        factM[2].addEventListener('keyup',()=>{

            factM[4].value = Math.round((factM[1].value * factM[2].value)*100)/100
        })
        for (let i = 0; i < ligneFact.length; i++) {

            ligneFact[i].addEventListener('click',()=>{

                document.querySelector('.btnModifFact').click()

                factM[0].value=ligneArray[i].reference
                factM[1].value = ligneArray[i].quantite
                factM[2].value=ligneArray[i].prixUnitaire
                factM[3].value= ligneArray[i].tva
                factM[4].value = ligneArray[i].montantHt
                designationM[0].value = ligneArray[i].intitule.designation1
                designationM[1].value = ligneArray[i].intitule.designation2
                designationM[2].value = ligneArray[i].intitule.designation3
                designationM[3].value = ligneArray[i].intitule.designation4
                designationM[4].value = ligneArray[i].intitule.designation5
                designationM[5].value = ligneArray[i].intitule.designation6
                valider.addEventListener('click',()=>{

                    while (ligneFact[i].children[1].firstChild){
                        ligneFact[i].children[1].removeChild(ligneFact[i].children[1].lastChild)
                    }
                    ligneFact[i].children[0].innerText = factM[0].value

                    for (let j = 0; j < designationM.length; j++) {

                        if (designationM[j].value !==""){
                            let p = document.createElement("p")
                            p.classList.add("h6")
                            p.innerHTML = designationM[j].value
                            ligneFact[i].children[1].appendChild(p);
                        }
                    }
                    ligneFact[i].children[2].innerText = factM[1].value
                    ligneFact[i].children[3].innerText = factM[2].value
                    ligneFact[i].children[4].innerText = factM[3].value
                    ligneFact[i].children[5].innerText = factM[4].value
                    ligneArray[i].reference = factM[0].value
                    ligneArray[i].quantite=factM[1].value
                    ligneArray[i].prixUnitaire=factM[2].value
                    ligneArray[i].tva=factM[3].value
                    ligneArray[i].montantHt = factM[4].value
                    ligneArray[i].intitule.designation1 = designationM[0].value
                    ligneArray[i].intitule.designation2 = designationM[1].value
                    ligneArray[i].intitule.designation3 = designationM[2].value
                    ligneArray[i].intitule.designation4 = designationM[3].value
                    ligneArray[i].intitule.designation5 = designationM[4].value
                    ligneArray[i].intitule.designation6 = designationM[5].value
                    somme(ligneArray)

                    document.querySelector('.closeModifFact').click()


                })
                supprimer.addEventListener('click',()=>{
                    try {
                        table.removeChild(ligneFact[i])
                    }catch (e) {
                        
                    }
                    let index = ligneArray.indexOf(ligneArray[i])
                    ligneArray.splice(0,index)
                    somme(ligneArray)
                })
            })

        }
    })

    facture[2].addEventListener('keyup',()=>{

        facture[5].value = Math.round((facture[2].value * facture[3].value)*100)/100
    })
    facture[3].addEventListener('keyup',()=>{

        facture[5].value = Math.round((facture[2].value * facture[3].value)*100)/100
    })
    let client = new Client();
    sauverClient.addEventListener('click', () => {
        infoClient.innerHTML = '';
        for (let i = 0; i < clientFacture.length; i++) {
            let div = document.createElement('div')
            div.innerHTML = clientFacture[i].value
            infoClient.appendChild(div)

        }
        client.nom = clientFacture[0].value
        client.prenom = clientFacture[1].value
        client.entreprise = clientFacture[2].value
        client.siret = clientFacture[3].value
        client.adresse = clientFacture[4].value
        client.telephone = clientFacture[5].value
        client.email = clientFacture[6].value
    })

    boutonEnvoie.addEventListener('click', () => {
        let typePaiement = [];
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
            title: titre.value,
            id: boutonEnvoie.dataset.salarie,
            condition : typePaiement
        })
        fetch('/exportFacture', {
            method: 'POST',
            body: corps
        })
            .then((reponse) => {
                return reponse.json()
            })
            .then((reponse) => {

                window.open('/uploads/factureEnt/' + reponse, '_blank')

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
    console.log(document.querySelector('#montantTTCPaiement'))
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
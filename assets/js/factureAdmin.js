

if (document.title == 'Facture Admin') {


    let typeDoc = document.querySelector('#typeDoc')

    let type = document.querySelectorAll('.type')

    typeDoc.addEventListener('change', () => {

        for (let i = 0; i < type.length; i++) {
            type[i].innerHTML = typeDoc.value

        }
    })
    function Designation(designation, ligne1, ligne2, ligne3, ligne4, ligne5, ligne6) {
        this.designation = designation;
        this.ligne1 = ligne1
        this.ligne2 = ligne2
        this.ligne3 = ligne3
        this.ligne4 = ligne4
        this.ligne5 = ligne5
        this.ligne6 = ligne6
    }
    function Ligne(ref, designer, prixUnitVente, montantHors) {
        this.ref = ref;
        this.designer = designer;
        this.nombre = 1;
        this.unitaire = 1;
        this.prixUnitVente = prixUnitVente;
        this.tva = '20%';
        this.montantHors = montantHors;
    }
    function Entreprise(nom, nomDirigeant, adresse, ville) {
        this.nom = nom;
        this.nomDirigeant = nomDirigeant;
        this.adresse = adresse;
        this.ville = ville;
    }
    function calculerMontant() {
        let montantHT = 0
        let mHT = document.querySelectorAll('.MHT');
        for (let k = 0; k < mHT.length; k++) {
            montantHT += parseFloat(mHT[k].innerHTML);
        }
        document.querySelector('.horstaxe').innerHTML = montantHT
        document.querySelector('.TVAvaleur').innerHTML = montantHT * 1.2 - montantHT
        document.querySelector('.ttc').innerHTML = montantHT * 1.2
    }

    let liste = [];
    let ajouter = document.querySelector('.ajouter')
    let retirer = document.querySelector('.retirer')
    let table = document.querySelector('.table-facture')
    let listeDesignation = document.querySelectorAll('.ligneDesignation')
    let PuVente = document.querySelector('.Punitaire')
    let montantHt = document.querySelector('.montantHt')
    let referenceFinale = 0
    let designationIntermediaire = 0
    let reference = document.querySelector('.optionRef')
    let optionOtd = document.querySelector('.optionOTD')
    let OTD = document.querySelector('.OTD')
    reference.addEventListener('change', () => {
        if (reference.value === 'OTD') {
            OTD.style.display = 'block'
            let ref = 0;
            optionOtd.addEventListener('change', () => {
                ref = JSON.parse(optionOtd.value)
                referenceFinale = ref['ref'];
                designationIntermediaire = ref['designation']
            })
        }
        else {
            OTD.style.display = 'none'
            ref = JSON.parse(reference.value)
            referenceFinale = ref['ref'];
            designationIntermediaire = ref['designation']
        }
    })

    ajouter.addEventListener('click', () => {
        let tr = document.createElement('tr');
        let tdRef = document.createElement('td');
        tdRef.classList.add('case')
        tdRef.classList.add('inRef')
        tdRef.innerHTML = referenceFinale;
        let tdDesigantion = document.createElement('td');
        tdDesigantion.innerHTML = designationIntermediaire
        tdDesigantion.classList.add('case')
        tdDesigantion.classList.add('inDesignation');
        let ulDesignation = document.createElement('ul');
        for (let i = 0; i < listeDesignation.length; i++) {
            let li = document.createElement('li')
            li.innerHTML = listeDesignation[i].value
            ulDesignation.appendChild(li);
        }


        tdDesigantion.appendChild(ulDesignation)

        let tdQuantite = document.createElement('td');
        tdQuantite.innerHTML = 1;
        let tdUnite = document.createElement('td');
        tdUnite.innerHTML = 1
        let tdPu = document.createElement('td');
        tdPu.innerHTML = PuVente.value
        let tdTva = document.createElement('td');
        tdTva.innerHTML = '20%';
        let tdMontant = document.createElement('td')
        tdMontant.classList.add('MHT')
        tdMontant.innerHTML = montantHt.value


        tr.appendChild(tdRef);
        tr.appendChild(tdDesigantion)
        tr.appendChild(tdQuantite)
        tr.appendChild(tdUnite)
        tr.appendChild(tdPu)
        tr.appendChild(tdTva)
        tr.appendChild(tdMontant)
        table.appendChild(tr)

        let Designa = new Designation(designationIntermediaire,
            listeDesignation[0].value, listeDesignation[1].value, listeDesignation[2].value, listeDesignation[3].value, listeDesignation[4].value, listeDesignation[5].value)
        let line = new Ligne(referenceFinale, Designa, PuVente.value, montantHt.value)
        let mHT = document.querySelectorAll('.MHT');

        liste.push(line)

        let fact = document.querySelectorAll('.fact')
        for (let j = 0; j < fact.length; j++) {
            fact[j].value = ''
        }
        calculerMontant()

    })
    retirer.addEventListener('click', () => {
        table.removeChild(table.lastChild)
        liste.pop();
        calculerMontant();

    })
    let bouton = document.querySelector('.btn-pdf')
    bouton.addEventListener('click', () => {
        let boite = new Entreprise(document.querySelector('#societe').value, document.querySelector('#dirigeant').value, document.querySelector('#adresse').value, document.querySelector('#ville').value,)
        let contenu = JSON.stringify({
            objet: liste,
            entreprise: boite,
            dateFact: document.querySelector('.date').value,
            numeroFact: document.querySelector('#numeroFact').value,
            HT: document.querySelector('.horstaxe').innerHTML,
            TVA: document.querySelector('.TVAvaleur').innerHTML,
            TTC: document.querySelector('.ttc').innerHTML,
            type: typeDoc.value


        })
        if (typeDoc.value != null) {


            fetch('/admin/doc/pdf', {
                method: 'POST',
                body: contenu

            })
                .then((reponse) => {
                    return reponse.json()
                })
                .then((reponse) => {
                    if (typeDoc.value == 'devis') {

                        window.open('/uploads/devisAdmin/' + reponse + '.pdf', '_blank')
                    }
                    else {
                        window.open('/uploads/factureAdmin/' + reponse + '.pdf', '_blank')
                    }

                })
        }
        else {
            alert('Choisissez le type de document produit')
        }
    })
}


if (document.title == 'Liste des factures') {

    let date = new Date();
    let mois = document.querySelector('.date')
    let liste = document.querySelector('.liste')
    let dateDevis = document.querySelector('.dateDevis')
    let listeDevis = document.querySelector('.devis')

    let suivant = document.querySelector('.suivant');
    let precedent = document.querySelector('.precedent');
    let suivantDevis = document.querySelector('.moisApres')
    let precedentDevis = document.querySelector('.moisAvant')



    document.addEventListener("DOMContentLoaded", () => {
        let dossier = '/uploads/factureAdmin/';
        let lien = '/admin/retourListe'
        let lienDevis = '/admin/listeDevis'
        let dossierDevis = 'uploads/devisAdmin/'

        facture(date, lien, dossier, liste, mois)
        facture(date, lienDevis, dossierDevis, listeDevis, dateDevis)
    })

    //Facture
    suivant.addEventListener('click', () => {
        let moisApres = new Date(date.setMonth(date.getMonth() + 1));
        let dossier = '/uploads/factureAdmin/';
        let lien = '/admin/retourListe'
        facture(moisApres, lien, dossier, liste, mois)

        while (liste.firstChild) {
            liste.removeChild(liste.lastChild)
        }


    })

    precedent.addEventListener('click', () => {
        let moisAvant = new Date(date.setMonth(date.getMonth() - 1))
        let lien = '/admin/retourListe'
        let dossier = '/uploads/factureAdmin/';
        facture(moisAvant, lien, dossier, liste, mois)
        while (liste.firstChild) {
            liste.removeChild(liste.lastChild)
        }
    })

    //Devis

    suivantDevis.addEventListener('click', () => {
        let moisApres = new Date(date.setMonth(date.getMonth() + 1));
        let lienDevis = '/admin/listeDevis'
        let dossierDevis = '/uploads/devisAdmin/'
        facture(moisApres, lienDevis, dossierDevis, listeDevis, dateDevis)

        while (listeDevis.firstChild) {
            listeDevis.removeChild(listeDevis.lastChild)
        }


    })

    precedentDevis.addEventListener('click', () => {
        let moisAvant = new Date(date.setMonth(date.getMonth() - 1))
        let lienDevis = '/admin/listeDevis'
        let dossierDevis = '/uploads/devisAdmin/'
        facture(moisAvant, lienDevis, dossierDevis, listeDevis, dateDevis)

        while (listeDevis.firstChild) {
            listeDevis.removeChild(listeDevis.lastChild)
        }
    })
    function facture(choixdate, lien, dossier, listeFacture, moisDoc) {
        moisDoc.innerHTML = choixdate.toLocaleString('fr-Fr', { month: 'long', year: 'numeric' })

        let premier = new Date(choixdate.getFullYear(), choixdate.getMonth(), 1)
        let lastDay = new Date(choixdate.getFullYear(), choixdate.getMonth() + 1, 0);
        const post = JSON.stringify({
            debut: premier.toLocaleString('fr-Fr', {
                day: 'numeric',
                month: 'numeric',
                year: 'numeric'
            }),
            fin: lastDay.toLocaleString('fr-FR', {
                day: 'numeric',
                month: 'numeric',
                year: 'numeric'
            })
        })
        fetch(lien, {
            method: 'POST',
            body: post

        })
            .then((reponse) => {
                return reponse.json();
            })
            .then((reponse) => {

                for (let i = 0; i < reponse.length; i++) {
                    let a = document.createElement("a");
                    let img = document.createElement("img");
                    let div = document.createElement("div");
                    let span = document.createElement("span")
                    span.textContent = reponse[i];
                    div.classList.add("col-sm-2");
                    a.href = dossier + reponse[i] + '.pdf';
                    a.target = "_blank"
                    img.src = '/css/css_admin/img/pdf.png'

                    a.appendChild(img)
                    a.appendChild(span)

                    div.appendChild(a)
                    listeFacture.appendChild(div);
                }

            })

    }

}
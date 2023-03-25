if (document.title == "Liste Mairie") {
    let bouton = document.querySelector('.btn-maincolor4')
    let button = document.querySelector('.btn-warning')

    bouton.addEventListener('click', () => {
        /* let adresse = "https://etablissements-publics.api.gouv.fr/v3/departements/" + departement.value + "/mairie";
         fetch(adresse, {
             method: 'GET',
 
         })
             .then((reponse) => {
                 return reponse.json()
             })
             .then((reponse) => {
                 for (const key in reponse.features) {
                     if (reponse.features.hasOwnProperty(key)) {
                         const element = reponse.features[key];
 
                         console.log(element.properties.email);
 
                     }
                 }
 
             })*/
        fetch('/admin/envoyerVille', {
            method: 'GET',

        })
            .then((reponse) => {
                return reponse.json()
            })
            .then((reponse) => {
                console.log(reponse);
            })


    })
    button.addEventListener('click', () => {
        fetch('/admin/envoyerVille', {
            method: 'GET'
        })
            .then((reponse) => {
                return reponse.json()
            })
            .then((reponse) => {
                console.log(reponse);
            })
    })

}
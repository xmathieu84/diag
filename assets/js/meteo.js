if (document.title == 'localisation intervention') {
    let ville = document.querySelector('#adresse_ville');
    let numero = document.querySelector('#adresse_numero')
    let cp = document.querySelector('#adresse_codePostal')
    let rue = document.querySelector('#adresse_nomVoie')
    let idInter = document.querySelector('.btn-maincolor').dataset.inter;
    let explication = document.querySelectorAll('.explication');





    ville.addEventListener('change', () => {
        let content = JSON.stringify({
            numeroVoie: numero.value,
            nomVoie: rue.value,
            codePostal: cp.value,
            nomVille: ville.value,
            inter: idInter


        })
        fetch('/meteo', {
            method: 'POST',
            body: content
        })
            .then(function (reponse) {
                return reponse.json()
            })
            .then(function (reponse) {
                explication[0].innerHTML = "Le risque de pluie est de " + reponse['pluie'] + "%";
                explication[1].innerHTML = "Le vent soufflera à " + reponse['vent'] + " km/h avec des rafales à " + reponse['ventRafale'] + " km/h"
                explication[2].innerHTML = "La température sera de " + reponse['temperature'] + "°"

            })
    })

}
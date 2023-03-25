if (document.title == 'Inscription consultant') {


    document.addEventListener('DOMContentLoaded', function () {

        document.getElementById('consultant_adresse_ville').setAttribute('readonly', 'readonly')

    });
    document.getElementById('consultant_adresse_ville').focus(function () {

        document.getElementById('consultant_adresse_ville').autocomplete("search", "");

    });

    // OnKeyDown Function
    document.getElementById("consultant_adresse_codePostal").addEventListener('keyup', () => {
        let zip_in = document.getElementById("consultant_adresse_codePostal").value;

        let city = document.getElementById("city");

        if ((zip_in.length == 5)) {


            // Make HTTP Request
            fetch('https://api.zippopotam.us/fr/' + zip_in, {
                method: 'GET',
                headers: new Headers(),
            })
                .then(function (response) {
                    return response.json()
                })
                .then(function (response) {
                    document.getElementById('consultant_adresse_ville').removeAttribute('readonly');

                    suggestions = [];
                    for (ville in response['places']) {
                        suggestions.push(response['places'][ville]['place name']);

                    }
                    for (const iterator of suggestions) {
                        let option = document.createElement('option');
                        option.value = iterator;
                        city.appendChild(option);
                    }




                })

        }
    });
}
if (document.title === 'Inscription') {


    document.addEventListener('DOMContentLoaded', function () {

        document.getElementById('demandeur_adresse_ville').setAttribute('readonly', 'readonly')


        document.getElementById('demandeur_adresse_ville').focus(function () {

            document.getElementById('demandeur_adresse_ville').autocomplete("search", "");

        });

        // OnKeyDown Function
        document.getElementById("demandeur_adresse_codePostal").addEventListener('keyup', () => {
            let zip_in = document.getElementById("demandeur_adresse_codePostal").value;

            let city = document.getElementById("city");

            if ((zip_in.length == 5)) {


                // Make HTTP Request
                fetch('https://api.zippopotam.us/fr/' + zip_in, {
                    method: 'GET',
                    headers: new Headers(),
                })
                    .then(function (response) {
                        return response.json()
                    })
                    .then(function (response) {
                        document.getElementById('demandeur_adresse_ville').removeAttribute('readonly');

                        suggestions = [];
                        for (ville in response['places']) {
                            suggestions.push(response['places'][ville]['place name']);

                        }
                        for (const iterator of suggestions) {
                            let option = document.createElement('option');
                            option.value = iterator;
                            city.appendChild(option);
                        }




                    })

            }
        });
    });
}

if (document.title == 'Inscription entreprise') {



    document.addEventListener('DOMContentLoaded', function () {

        document.getElementById('entreprise_adresse_ville').setAttribute('readonly', 'readonly')






        document.getElementById('entreprise_adresse_ville').focus(function () {

            document.getElementById('entreprise_adresse_ville').autocomplete("search", "");

        });




        document.getElementById("entreprise_adresse_codePostal").addEventListener('keyup', () => {
            let zip_in = document.getElementById("entreprise_adresse_codePostal").value;

            let city = document.getElementById("city");

            if ((zip_in.length == 5)) {


                // Make HTTP Request
                fetch('https://api.zippopotam.us/fr/' + zip_in, {
                    method: 'GET',
                    headers: new Headers(),
                })
                    .then(function (response) {
                        return response.json()
                    })
                    .then(function (response) {
                        document.getElementById('entreprise_adresse_ville').removeAttribute('readonly');

                        suggestions = [];
                        for (ville in response['places']) {
                            suggestions.push(response['places'][ville]['place name']);

                        }
                        for (const iterator of suggestions) {
                            let option = document.createElement('option');
                            option.value = iterator;
                            city.appendChild(option);
                        }




                    })

            }
        });
    });

}
if (document.title == 'Inscription salarie') {


    document.addEventListener('DOMContentLoaded', function () {

        document.getElementById('ajoutersalarie_adresse_ville').setAttribute('readonly', 'readonly')

    });
    document.getElementById('ajoutersalarie_adresse_ville').focus(function () {

        document.getElementById('ajoutersalarie_adresse_ville').autocomplete("search", "");

    });

    // OnKeyDown Function
    document.getElementById("ajoutersalarie_adresse_codePostal").addEventListener('keyup', () => {
        let zip_in = document.getElementById("ajoutersalarie_adresse_codePostal").value;

        let city = document.getElementById("city");

        if ((zip_in.length == 5)) {


            // Make HTTP Request
            fetch('https://api.zippopotam.us/fr/' + zip_in, {
                method: 'GET',
                headers: new Headers(),
            })
                .then(function (response) {
                    return response.json()
                })
                .then(function (response) {
                    document.getElementById('ajoutersalarie_adresse_ville').removeAttribute('readonly');

                    suggestions = [];
                    for (ville in response['places']) {
                        suggestions.push(response['places'][ville]['place name']);

                    }
                    for (const iterator of suggestions) {
                        let option = document.createElement('option');
                        option.value = iterator;
                        city.appendChild(option);
                    }




                })

        }
    });
};
if (document.title === "Demande d'intervention (phase 2)"|| document.title ==="Adresse du diagnostic") {
    document.addEventListener('DOMContentLoaded', function () {

        document.getElementById('adresse_inter_ville').setAttribute('readonly', 'readonly')

    });
    document.getElementById('adresse_inter_ville').focus(function () {

        document.getElementById('adresse_inter_ville').autocomplete("search", "");

    });

    // OnKeyDown Function
    document.getElementById("adresse_inter_codePostal").addEventListener('keyup', () => {
        let zip_in = document.getElementById("adresse_inter_codePostal").value;

        let city = document.getElementById("city");

        if ((zip_in.length == 5)) {


            // Make HTTP Request
            fetch('https://geo.api.gouv.fr/communes?codePostal=' + zip_in, {
                method: 'GET',
                headers: new Headers(),
            })
                .then(function (response) {
                    return response.json()
                })
                .then(function (response) {


                    document.getElementById('adresse_inter_ville').removeAttribute('readonly');
                    document.getElementById('adresse_inter_ville').value = null;
                    suggestions = [];
                    for (ville in response) {


                        suggestions.push(response[ville]['nom']);

                    }
                    for (const iterator of suggestions) {
                        let option = document.createElement('option');
                        option.value = iterator;
                        city.appendChild(option);
                    }
                    let inter = document.querySelector('legend').dataset.inter;
                    const requete = JSON.stringify({
                        idInter: inter,
                        departement: response[0]['codeDepartement']
                    })
                    fetch('/choixDepartement', {
                        method: 'POST',
                        body: requete

                    })




                })

        }
    });
};
if (document.title == "Modifer l'adresse") {
    document.addEventListener('DOMContentLoaded', function () {

        document.getElementById('adresse_ville').setAttribute('readonly', 'readonly')

    });
    document.getElementById('adresse_ville').focus(function () {

        document.getElementById('adresse_ville').autocomplete("search", "");

    });

    // OnKeyDown Function
    document.getElementById("adresse_code_postal").addEventListener('keyup', () => {
        let zip_in = document.getElementById("adresse_code_postal").value;

        let city = document.getElementById("city");

        if ((zip_in.length == 5)) {


            // Make HTTP Request
            fetch('https://api.zippopotam.us/fr/' + zip_in, {
                method: 'GET',
                headers: new Headers(),
            })
                .then(function (response) {
                    return response.json()
                })
                .then(function (response) {
                    document.getElementById('adresse_ville').removeAttribute('readonly');
                    document.getElementById('adresse_ville').value = null;
                    suggestions = [];
                    for (ville in response['places']) {
                        suggestions.push(response['places'][ville]['place name']);

                    }
                    for (const iterator of suggestions) {
                        let option = document.createElement('option');
                        option.value = iterator;
                        city.appendChild(option);
                    }




                })

        }
    });
};

if (document.title == 'Inscription institutionnel') {



    document.addEventListener('DOMContentLoaded', function () {

        document.getElementById('institution_adresse_ville').setAttribute('readonly', 'readonly')






        document.getElementById('institution_adresse_ville').focus(function () {

            document.getElementById('institution_adresse_ville').autocomplete("search", "");

        });




        document.getElementById("institution_adresse_code_postal").addEventListener('keyup', () => {
            let zip_in = document.getElementById("institution_adresse_code_postal").value;

            let city = document.getElementById("city");

            if ((zip_in.length == 5)) {
                while (city.firstChild){
                    city.removeChild(city.lastChild);
                }

                // Make HTTP Request
                fetch('https://api.zippopotam.us/fr/' + zip_in, {
                    method: 'GET',
                    headers: new Headers(),
                })
                    .then(function (response) {
                        return response.json()
                    })
                    .then(function (response) {
                        document.getElementById('institution_adresse_ville').removeAttribute('readonly');

                        suggestions = [];
                        for (ville in response['places']) {
                            suggestions.push(response['places'][ville]['place name']);

                        }
                        for (const iterator of suggestions) {
                            let option = document.createElement('option');
                            option.value = iterator;
                            city.appendChild(option);
                        }




                    })

            }
        });
    });

}

if (document.title == "Création d'un dossier") {



    document.addEventListener('DOMContentLoaded', function () {

        document.getElementById('dossier_adresse_ville').setAttribute('readonly', 'readonly')






        document.getElementById('dossier_adresse_ville').focus(function () {

            document.getElementById('dossier_adresse_ville').autocomplete("search", "");

        });




        document.getElementById("dossier_adresse_codePostal").addEventListener('keyup', () => {
            let zip_in = document.getElementById("dossier_adresse_codePostal").value;

            let city = document.getElementById("city");

            if ((zip_in.length == 5)) {
                while (city.firstChild){
                    city.removeChild(city.lastChild);
                }

                // Make HTTP Request
                fetch('https://api.zippopotam.us/fr/' + zip_in, {
                    method: 'GET',
                    headers: new Headers(),
                })
                    .then(function (response) {
                        return response.json()
                    })
                    .then(function (response) {
                        document.getElementById('dossier_adresse_ville').removeAttribute('readonly');

                        suggestions = [];
                        for (ville in response['places']) {
                            suggestions.push(response['places'][ville]['place name']);

                        }
                        for (const iterator of suggestions) {
                            let option = document.createElement('option');
                            option.value = iterator;
                            city.appendChild(option);
                        }




                    })

            }
        });
    });

}

if (document.title === 'Inscription Pro-Btp') {



    document.addEventListener('DOMContentLoaded', function () {

        document.getElementById('entreprise_tp_adresse_ville').setAttribute('readonly', 'readonly')






        document.getElementById('entreprise_tp_adresse_ville').focus(function () {

            document.getElementById('entreprise_tp_adresse_ville').autocomplete("search", "");

        });




        document.getElementById("entreprise_tp_adresse_codePostal").addEventListener('keyup', () => {
            let zip_in = document.getElementById("entreprise_tp_adresse_codePostal").value;

            let city = document.getElementById("city");

            if ((zip_in.length == 5)) {


                // Make HTTP Request
                fetch('https://api.zippopotam.us/fr/' + zip_in, {
                    method: 'GET',
                    headers: new Headers(),
                })
                    .then(function (response) {
                        return response.json()
                    })
                    .then(function (response) {
                        document.getElementById('entreprise_tp_adresse_ville').removeAttribute('readonly');

                        suggestions = [];
                        for (ville in response['places']) {
                            suggestions.push(response['places'][ville]['place name']);

                        }
                        for (const iterator of suggestions) {
                            let option = document.createElement('option');
                            option.value = iterator;
                            city.appendChild(option);
                        }




                    })

            }
        });
    });

}
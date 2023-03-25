if (document.title == 'Carte des interventions') {
    let inter = document.querySelector('.dateInter')
    let distanceInter = document.querySelector('.distanceInter')
    function carte(){

        let table = document.querySelector('tbody')

        let contenu = JSON.stringify({
            dateInter: inter.value,
            distance: distanceInter.value
        })
        fetch('/militaire/afficherCarte', {
            method: 'POST',
            body: contenu
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (response) {


                var script = document.createElement('script');
                script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDuoYW11lZP5K0U3kCoeP8YHhxaFHD8l7g&callback=initMap';
                script.defer = true;
                script.async = true;
                window.initMap = function () {

                    map = new google.maps.Map(document.querySelector('.carteInter'), {
                        center: {lat: response['center'][0], lng: response['center'][1]},
                        zoom: 8,
                        mapTypeControl: true,
                        scrollwheel: true,
                        zoomControl: false,


                    })
                    let LatLngArmee = new google.maps.LatLng(response['center'][0], response['center'][1])
                    let marqueurAmee = new google.maps.Marker({
                        position:LatLngArmee,
                        map:map,
                        title : 'Votre position',

                    })
                    marqueurAmee.setIcon('http://maps.google.com/mapfiles/ms/icons/green-dot.png')

                    for (let j = 0; j <= response['inter'].length; j++) {

                        var myLatlng = new google.maps.LatLng(response['inter'][j][0], response['inter'][j][1])
                        var marker = new google.maps.Marker({
                            position: myLatlng,
                            map: map,
                            title: "Drone utilisé :" + response['inter'][j][2] + " " + response['inter'][j][3] + " " + response['inter'][j][4]


                        })

                    }
                };
                document.head.appendChild(script);
                while (table.lastChild){
                    table.removeChild(table.lastChild)
                }
                for (let i = 0; i <= response['inter'].length; i++) {
                    console.log('ok')
                    let tr = document.createElement('tr')
                    let tdHD = document.createElement('td')
                    let tdHF = document.createElement('td')
                    let tdAdresse = document.createElement("td")
                    let tdOTD = document.createElement("td")
                    tdHD.innerHTML = response['inter'][i][12];
                    tdHF.innerHTML = response['inter'][i][13];
                    tdAdresse.innerHTML = response['inter'][i][8] + " " + response['inter'][i][9] + "<br>" + response['inter'][i][10] + " " + response['inter'][i][11];
                    tdOTD.innerHTML = "OTD présent : " + response['inter'][i][6] + " " + response['inter'][i][5] + "<br>" + "Téléphone :" + response['inter'][i][7];
                    tr.appendChild(tdHD)
                    tr.appendChild(tdHF)
                    tr.appendChild(tdAdresse)
                    tr.appendChild(tdOTD)
                    table.appendChild(tr)
                }
            })
    }
    document.addEventListener('DOMContentLoaded', () => {
            carte()
    })
    distanceInter.addEventListener('change',()=>{
        carte()
    });
    inter.addEventListener('change',()=>{
        carte();
    })
}
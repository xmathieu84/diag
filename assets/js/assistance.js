
if (document.title ==='Assistance au vol'){


        let interventions = document.querySelectorAll('.card')
        let mapSat =[];
        let mapExclu = [];
        let exlusion = document.querySelectorAll('.exclusion')
        let vent = document.querySelectorAll('.vent')
        let pluie = document.querySelectorAll('.pluie')
        let temp = document.querySelectorAll('.temp')
        for (let i = 0; i <interventions.length ; i++) {
            fetch("/coordInter/"+interventions[i].dataset.inter,{
                method:"GET"
            })
                .then((reponse)=>{
                    return reponse.json()
                })
                .then((reponse)=>{

                    mapExclu[i] = L.map(exlusion[i], {
                        center: [reponse.lat,reponse.lon],
                        zoom: 18,
                        attributionControl: false,
                        zoomControl: false,
                        scrollWheelZoom: false,
                        keyboard: false,
                        doubleClickZoom: false,
                        dragging: false

                    });
                    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {

                        maxZoom: 20,
                        id: 'mapbox/satellite-v9',
                        tileSize: 512,
                        zoomOffset: -1,
                        accessToken: 'pk.eyJ1IjoieG1hdGhpZXUxMyIsImEiOiJja2N1aHU2eTAyOGZ2MnJsZjk1bjR0ZHE0In0.-N1CSInz77-O6xViA43KQw'
                    }).addTo(mapExclu[i]);
                    L.circle([reponse.lat,reponse.lon],reponse.rayon).addTo(mapExclu[i]);
                    L.marker(reponse, {
                        title: "Zone d'exclusion"
                    }).addTo(mapExclu[i])



                })



        }





}
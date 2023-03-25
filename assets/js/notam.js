if (document.title ==='Notam'){
    let carte = document.querySelector('.carte')
    let carteNotam;
    let recherche = document.querySelector('#loupe')
    let adresse = document.querySelector("#rechercheAdresse")
    let array =[];
    let notams = document.querySelector('.notam')

    function notam(contenu,zoom){
        fetch('/listeNotam',{
            method:'POST',
            body : null,


        })
            .then((response)=>{
                return response.json()
            })
            .then((response)=>{

                carteNotam = L.map(carte, {
                    center: [46.227638,2.213749],
                    zoom: 6,
                    attributionControl: false,
                    zoomControl: false,
                    scrollWheelZoom: false,
                    keyboard: false,
                    doubleClickZoom: false,
                    dragging: true

                });
                L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {

                    maxZoom: 20,
                    id: 'mapbox/streets-v11',
                    tileSize: 512,
                    zoomOffset: -1,
                    accessToken: 'pk.eyJ1IjoieG1hdGhpZXUxMyIsImEiOiJja2N1aHU2eTAyOGZ2MnJsZjk1bjR0ZHE0In0.-N1CSInz77-O6xViA43KQw'
                }).addTo(carteNotam);

                for (let i = 0; i < response.zone.Situation.BordureS.Bordure.length; i++) {
                    const bordure = response.zone.Situation.BordureS.Bordure[i].Geometrie.split('\n')
                    array.push(bordure)


                }
                for (let i = 0; i < response.zone.Situation.HelistationS.Helistation.length; i++) {
                    const coord = [parseFloat(response.zone.Situation.HelistationS.Helistation[i].Latitude),parseFloat(response.zone.Situation.HelistationS.Helistation[i].Longitude)]
                    let myIcon = L.icon({
                        iconUrl: '\\css\\css_site\\img\\helliport.png',
                        iconSize: [15, 15],

                        popupAnchor: [-3, -76],

                        shadowSize: [68, 95],
                        shadowAnchor: [22, 94]
                    });

                    L.marker(coord,{icon:myIcon,title:response.zone.Situation.HelistationS.Helistation[i].Nom}).addTo(carteNotam);
                }
                for (let i = 0; i < response.zone.Situation.ObstacleS.Obstacle.length; i++) {
                    let icone;
                    let nom;
                        const coord =  [parseFloat(response.zone.Situation.ObstacleS.Obstacle[i].Latitude),parseFloat(response.zone.Situation.ObstacleS.Obstacle[i].Longitude)]
                        let type = response.zone.Situation.ObstacleS.Obstacle[i].TypeObst
                        switch (response.zone.Situation.ObstacleS.Obstacle[i].TypeObst) {
                            case 'Eolienne(s)' :
                               icone =  '\\css\\css_site\\img\\eolienne.png';
                               nom = 'Eolienne(s)';
                               break
                            case 'Mât':
                                icone = '\\css\\css_site\\img\\mat.png';
                                nom = 'Mât';
                                break
                            case "Château d'eau":
                                icone = '\\css\\css_site\\img\\chateau.png';
                                nom = "Château d'eau";
                                break;
                            case "Pylône":
                                icone = '\\css\\css_site\\img\\pylone.png';
                                nom = "Pylône";
                                break;
                            case  "Grue":
                                icone = '\\css\\css_site\\img\\grue.png';
                                nom = "Grue";
                                break;
                            case 'Câble':
                                icone = '\\css\\css_site\\img\\cable.png';
                                nom = 'Câble';
                                break;

                            case 'Cheminée':
                                icone = '\\css\\css_site\\img\\cheminee.png';
                                nom = 'Cheminée';
                                break;
                            case 'Bâtiment':
                                icone = '\\css\\css_site\\img\\batiment.png';
                                nom = 'Bâtiment';
                                break;
                                case 'Tour':
                                icone = '\\css\\css_site\\img\\tour.png';
                                nom = 'Tour';
                                break;
                            case 'Centrale thermique':
                                icone = '\\css\\css_site\\img\\centrale.png';
                                nom = 'Centrale thermique';
                                break;
                            case 'Silo':
                                icone = '\\css\\css_site\\img\\silo.png';
                                nom = 'Silo';
                                break;
                            case 'Antenne':
                                icone = '\\css\\css_site\\img\\antenne.png';
                                nom = 'Antenne';
                                break;
                            case 'Torchère':
                                icone = '\\css\\css_site\\img\\torchere.png';
                                nom = 'Torchère';
                                break;
                            case 'Derrick':
                                icone = '\\css\\css_site\\img\\derrick.png';
                                nom = 'Derrick';
                                break;
                            case 'Treillis métallique':
                                icone = '\\css\\css_site\\img\\treillis.png';
                                nom = 'Treillis métallique';
                                break;
                            case 'Terril':
                                icone = '\\css\\css_site\\img\\terril.png';
                                nom = 'Terril';
                                break;
                            case 'Phare marin':
                                icone = '\\css\\css_site\\img\\phare.png';
                                nom = 'Phare marin';
                                break;
                            case 'Pile de pont':
                                icone = '\\css\\css_site\\img\\pile.png';
                                nom = 'Pile de pont';
                                break;
                            case 'Portique':
                                icone = '\\css\\css_site\\img\\portique.png';
                                nom = 'Portique';
                                break;
                            case 'Eglise':
                                icone = '\\css\\css_site\\img\\eglise.png';
                                nom = 'Eglise';
                                break;
                        }


                    let myIcon = L.icon({
                        iconUrl: icone,
                        iconSize: [15, 15],

                        popupAnchor: [-3, -76],

                        shadowSize: [68, 95],
                        shadowAnchor: [22, 94]
                    });

                    L.marker(coord,{icon:myIcon,title:response.zone.Situation.ObstacleS.Obstacle[i].Combien +' '+type}).addTo(carteNotam);

                }
                for (const responseElement of array) {
                    let table2 = [];
                    for (const responseElementElement of responseElement) {
                        const net = responseElementElement.split(',')

                        table2.push([parseFloat(net[0]),parseFloat(net[1])])

                    }

                    let zone2 = L.polyline(table2,{color:'red'})

                    zone2.addTo(carteNotam)
                }


            })
    }
    document.addEventListener('DOMContentLoaded',()=>{
        carteNotam = L.map(carte, {
            center: [46.227638,2.213749],
            zoom: 6,
            attributionControl: false,
            zoomControl: false,
            scrollWheelZoom: false,
            keyboard: false,
            doubleClickZoom: false,
            dragging: true

        });
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {

            maxZoom: 20,
            id: 'mapbox/streets-v11',
            tileSize: 512,
            zoomOffset: -1,
            accessToken: 'pk.eyJ1IjoieG1hdGhpZXUxMyIsImEiOiJja2N1aHU2eTAyOGZ2MnJsZjk1bjR0ZHE0In0.-N1CSInz77-O6xViA43KQw'
        }).addTo(carteNotam);
    })




    recherche.addEventListener('click',()=>{
        while (carte.lastChild){
            carte.removeChild(carte.lastChild)
        }
        carteNotam.remove();
        fetch('/listeNotam',{
            method:'POST',
            body : adresse.value,


        })
            .then((response)=>{
                return response.json()
            })
            .then((response)=>{

                carteNotam = L.map(carte, {
                    center: response.coord,
                    zoom: 12,
                    attributionControl: false,
                    zoomControl: false,
                    scrollWheelZoom: false,
                    keyboard: false,
                    doubleClickZoom: false,
                    dragging: true

                });
                L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {

                    maxZoom: 16,
                    id: 'mapbox/streets-v11',
                    tileSize: 512,
                    zoomOffset: -1,
                    accessToken: 'pk.eyJ1IjoieG1hdGhpZXUxMyIsImEiOiJja2N1aHU2eTAyOGZ2MnJsZjk1bjR0ZHE0In0.-N1CSInz77-O6xViA43KQw'
                }).addTo(carteNotam);

                for (let i = 0; i < response.zone.Situation.BordureS.Bordure.length; i++) {
                    const bordure = response.zone.Situation.BordureS.Bordure[i].Geometrie.split('\n')
                    array.push(bordure)


                }
                for (let i = 0; i < response.zone.Situation.HelistationS.Helistation.length; i++) {
                    const coord = [parseFloat(response.zone.Situation.HelistationS.Helistation[i].Latitude),parseFloat(response.zone.Situation.HelistationS.Helistation[i].Longitude)]
                    let myIcon = L.icon({
                        iconUrl: '\\css\\css_site\\img\\helliport.png',
                        iconSize: [50, 50],

                        popupAnchor: [-3, -76],

                        shadowSize: [68, 95],
                        shadowAnchor: [22, 94]
                    });

                    L.marker(coord,{icon:myIcon,title:response.zone.Situation.HelistationS.Helistation[i].Nom}).addTo(carteNotam);
                }
                for (let i = 0; i < response.zone.Situation.ObstacleS.Obstacle.length; i++) {
                    let icone;
                    let test;
                        const coord =  [parseFloat(response.zone.Situation.ObstacleS.Obstacle[i].Latitude),parseFloat(response.zone.Situation.ObstacleS.Obstacle[i].Longitude)]
                        let type = response.zone.Situation.ObstacleS.Obstacle[i].TypeObst
                        switch (response.zone.Situation.ObstacleS.Obstacle[i].TypeObst) {
                            case 'Eolienne(s)' :
                               icone =  '\\css\\css_site\\img\\eolienne.png';
                               test = 'Eolienne(s)';
                               break
                            case 'Mât':
                                icone = '\\css\\css_site\\img\\mat.png';
                                test = 'Mât';
                                break
                            case "Château d'eau":
                                icone = '\\css\\css_site\\img\\chateau.png';
                                test = "Château d'eau";
                                break;
                            case "Pylône":
                                icone = '\\css\\css_site\\img\\pylone.png';
                                test = "Pylône";
                                break;
                            case  "Grue":
                                icone = '\\css\\css_site\\img\\grue.png';
                                test = "Grue";
                                break;
                            case 'Câble':
                                icone = '\\css\\css_site\\img\\cable.png';
                                test = 'Câble';
                                break;

                            case 'Cheminée':
                                icone = '\\css\\css_site\\img\\cheminee.png';
                                test = 'Cheminée';
                                break;
                            case 'Bâtiment':
                                icone = '\\css\\css_site\\img\\batiment.png';
                                test = 'Bâtiment';
                                break;
                                case 'Tour':
                                icone = '\\css\\css_site\\img\\tour.png';
                                test = 'Tour';
                                break;
                            case 'Centrale thermique':
                                icone = '\\css\\css_site\\img\\centrale.png';
                                test = 'Centrale thermique';
                                break;
                            case 'Silo':
                                icone = '\\css\\css_site\\img\\silo.png';
                                test = 'Silo';
                                break;
                            case 'Antenne':
                                icone = '\\css\\css_site\\img\\antenne.png';
                                test = 'Antenne';
                                break;
                            case 'Torchère':
                                icone = '\\css\\css_site\\img\\torchere.png';
                                test = 'Torchère';
                                break;
                            case 'Derrick':
                                icone = '\\css\\css_site\\img\\derrick.png';
                                test = 'Derrick';
                                break;
                            case 'Treillis métallique':
                                icone = '\\css\\css_site\\img\\treillis.png';
                                test = 'Treillis métallique';
                                break;
                            case 'Terril':
                                icone = '\\css\\css_site\\img\\terril.png';
                                test = 'Terril';
                                break;
                            case 'Phare marin':
                                icone = '\\css\\css_site\\img\\phare.png';
                                test = 'Phare marin';
                                break;
                            case 'Pile de pont':
                                icone = '\\css\\css_site\\img\\pile.png';
                                test = 'Pile de pont';
                                break;
                            case 'Portique':
                                icone = '\\css\\css_site\\img\\portique.png';
                                test = 'Portique';
                                break;
                            case 'Eglise':
                                icone = '\\css\\css_site\\img\\eglise.png';
                                test = 'Eglise';
                                break;
                        }


                    let myIcon = L.icon({
                        iconUrl: icone,
                        iconSize: [50, 50],

                        popupAnchor: [-3, -76],

                        shadowSize: [68, 95],
                        shadowAnchor: [22, 94]
                    });

                    L.marker(coord,{icon:myIcon,title:response.zone.Situation.ObstacleS.Obstacle[i].Combien +' '+type}).addTo(carteNotam);

                }
                for (const responseElement of array) {
                    let table2 = [];
                    for (const responseElementElement of responseElement) {
                        const net = responseElementElement.split(',')

                        table2.push([parseFloat(net[0]),parseFloat(net[1])])

                    }

                    let zone2 = L.polyline(table2,{color:'red'})

                    zone2.addTo(carteNotam)
                }



                let longNet =[];
                var re = /[\n]/;
                let re2 = /[,\n]/
                let z=0;
               /* for (const re2Element of response.interdit) {
                    let test=[]
                    z++
                    if(re2Element.length>2){

                        for (let i = 0; i < re2Element.length; i++) {

                            test.push(re2Element[i].split(','))


                        }
                        let table3=[]
                        for(testElement of test){
                            table3.push([parseFloat(testElement[0]),parseFloat(testElement[1])]);

                        }
                        let zone3 = L.polyline(table3,{color:'red'})
                        console.log(zone3);
                        zone3.addTo(carteNotam)
                    }

                }*/
                let important=[];
                let nomPer = [];
                for (let i = 0; i < response.notam.length; i++) {
                    console.log(response.notam[i].properties.minimumFL>12);
                    if (response.notam[i].properties.countryCode !=='FRA'){
                        nomPer.push(response.notam[i].properties)
                    }
                    else if (response.notam[i].properties.affectedFIR ==='FIR'){
                        nomPer.push(response.notam[i].properties)
                    }
                    else if(parseInt(response.notam[i].properties.minimumFL) > 12){
                        nomPer.push(response.notam[i].properties)
                    }
                    else {
                        important.push(response.notam[i].properties)
                    }


                }
                let zoneImportant = document.querySelector('.important')
                let zoneNonPer = document.querySelector('.nonPer')

                for (const nonPerElement of nomPer) {
                    addElementIntable(zoneNonPer,nonPerElement)
                }
                for (const IMPORTANT of important) {
                    addElementIntable(zoneImportant,IMPORTANT)
                }
    function addElementIntable(tbody,element){
        let tr = document.createElement('tr')
        let tdDate = document.createElement('td')
        tdDate.classList.add('text-dark')
        tdDate.innerText = element.effectiveStart
        let tdFin = document.createElement('td')
        tdFin.classList.add('text-dark')
        tdFin.innerText = element.effectiveEnd
        let tdLocation = document.createElement('td')
        tdLocation.classList.add('text-dark')
        tdLocation.innerText = element.location
        let tdAlt = document.createElement('td')
        tdAlt.classList.add('text-dark')
        tdAlt.innerHTML = 'Minimum : '+element.minimumFL *30+"m <br>Maximum : "+ element.maximumFL*30 + "m"
        tdAlt.style.fontSize = '0.8em'
        let tdText = document.createElement('td')
        tdText.classList.add('text-dark')
        tdText.innerText = element.text
        tdText.style.fontSize = '0.75em'
        tr.appendChild(tdDate)
        tr.appendChild(tdFin)
        tr.appendChild(tdLocation)
        tr.appendChild(tdAlt)
        tr.appendChild(tdText)
        tbody.appendChild(tr)
    }









            })
    })
}
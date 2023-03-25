import {Calendar} from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import interactionPlugin from "@fullcalendar/interaction";
import fr from "@fullcalendar/core/locales/fr";
import req from 'superagent';

if (document.title==="Choix de l'ODI"){
        let elementCalendrier = document.querySelector("#calendrier")
        let inter = document.querySelector('input[type=hidden]').value
        let remise = 1;
        let demiJ = document.querySelector('#tab02')
        let prixInter;
        let dateInter;
        let delai;
        let pack;
        document.addEventListener('DOMContentLoaded', () => {


            let calendar = new Calendar(elementCalendrier, {
                plugins: [ dayGridPlugin, timeGridPlugin, listPlugin,interactionPlugin ],
                initialView: 'dayGridMonth',
                locale: fr,
                height: 500,
                aspectRatio: 2,
                editable: false,

                views: {
                    listDay: { buttonText: "Aujourd'hui" },
                    listWeek: { buttonText: 'Semaine' },
                    listMonth: { buttonText: 'Mois' }
                },
                events: function(info, successCallback, failureCallback) {
                    req.post('/infoCaledrierOdi/'+inter)
                        .type('json')
                        .query({
                            start: info.start.valueOf(),
                            end: info.end.valueOf()
                        })
                        .end(function(err, res) {

                            if (err) {
                                failureCallback(err);
                            } else {

                                successCallback(

                                    Array.prototype.slice.call( // convert to array
                                        res.body
                                    ).map(function(eventEl) {

                                        return {
                                            title: eventEl.total,
                                            start : eventEl.date,
                                            allDay : true,
                                            color : eventEl.backgroud,
                                            backgroundColor : eventEl.backgroud,
                                            salarie : eventEl.salarie,
                                            prix : eventEl.prix,
                                            duree : eventEl.duree,
                                            total :eventEl.total,
                                            mission:eventEl.mission,
                                            dureeS : eventEl.dureeS,
                                            pack : eventEl.pack
                                        }
                                    })
                                )
                            }
                        })
                },


                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek',
                },

                timeZone: 'locale',
                allDaySlot: false,

                slotDuration: '00:30:00',
               eventClick: function(info) {
                    document.querySelector('.salarie').value = info.event._def.extendedProps.salarie
                   let zonePrixHeure = document.querySelector(".prixHeurePreci")
                   while (zonePrixHeure.firstChild){
                        zonePrixHeure.removeChild(zonePrixHeure.lastChild)
                   }
                    dateInter = new Date(info.el.fcSeg.eventRange.range.start).toLocaleDateString('fr-FR')
                    prixInter = info.event._def.extendedProps.prix;
                    delai = info.event._def.extendedProps.dureeS;
                    pack = info.event._def.extendedProps.pack
                   console.log(pack)
                    let p = document.createElement("p")
                   p.classList.add("h5","color_blue")
                   p.innerText = "Le prix proposé par l'ODI est " + prixInter+" €."
                   zonePrixHeure.appendChild(p)

                   document.querySelector('.dureeDiag').innerHTML = info.event._def.extendedProps.duree
                   document.querySelector('.salarie').value = info.event._def.extendedProps.salarie
                   document.querySelector('.dateDiag').innerText = new Date(info.el.fcSeg.eventRange.range.start).toLocaleDateString('fr-FR')
                   fetch("/dispoOdi",{
                       method : 'POST',
                       body : JSON.stringify({
                           salarie :  info.event._def.extendedProps.salarie,
                           date : new Date(info.el.fcSeg.eventRange.range.start).toLocaleDateString('fr-FR')
                       })
                   }).then((response)=> {
                       return response.json()
                   }).then((response)=>{
                       let zoneJournee = document.querySelector('#journee')
                       let zonePrix = document.querySelector("#prixJournee")

                       while (zoneJournee.firstChild){
                            zoneJournee.removeChild(zoneJournee.lastChild)
                       }
                       while (zonePrix.firstChild){
                           zonePrix.removeChild(zonePrix.lastChild)
                       }
                       if (response.journee){
                           let p = document.createElement("p")
                           p.classList.add("h4")
                           p.innerText = "Vous pouvez bénéficiez d'une remise de "+response.remise+"% en laissant le technicien choisir l'heure du rendez. Vous serez prévénu par email la veille de votre rendez de l'heure d'arrivée du technicien"
                           zoneJournee.append(p)
                           remise += response.remise/100

                       }
                       else{
                           let p = document.createElement("p")
                           p.classList.add("h4")
                           p.innerText = "Vous serez prévénu par email la veille de votre rendez de l'heure d'arrivée du technicien ."
                           zoneJournee.append(p)
                       }
                       let prix = document.createElement("p")
                       prix.classList.add("h5")
                       prix.innerText = "Le prix proposé par le technicien est : "+(prixInter/remise).toFixed(2)+ " €"
                       zonePrix.appendChild(prix)

                   })
                   document.querySelector('.btn-primary').click()
                }
            });

            calendar.render();

        })

        demiJ.addEventListener('click',()=>{
            fetch("/dispoOdi/demi",{
                method : 'post',
                body : JSON.stringify({
                    date:dateInter,
                    salarie:document.querySelector('.salarie').value
                })

            }).then((response)=>{
                return response.json()
            }).then((response)=>{
                let remiseDemi = 1
                let zoneJournee = document.querySelector('#demi')
                let zonePrix = document.querySelector("#prixDemi")
                while (zoneJournee.firstChild){
                    zoneJournee.removeChild(zoneJournee.lastChild)
                }
                while (zonePrix.firstChild){
                    zonePrix.removeChild(zonePrix.lastChild)
                }
                if (response.journee){
                    let p = document.createElement("p")
                    p.classList.add("h4")
                    p.innerText = "Vous pouvez bénéficiez d'une remise de "+response.remise+"% en choisissant un rendez vous le matin ou l'après midi. Le technicien vous préviendra par email "
                    zoneJournee.append(p)
                    remiseDemi += response.remise/100

                }
                else{
                    let p = document.createElement("p")
                    p.classList.add("h4")
                    p.innerText = "Veuillez choisir la demi-journée à laquelle vous souhaitez votre rendez-vous. Vous serez prévénu par email la veille de votre rendez de l'heure d'arrivée du technicien ."
                    zoneJournee.append(p)
                }
                let prix = document.createElement("p")

                prix.classList.add("h5")
                prix.innerText = "Le prix proposé par l'ODI est : "+(prixInter/remiseDemi).toFixed(2)+ " €"
                zonePrix.appendChild(prix)
            })
        })

    let choixDemi = document.getElementsByName("demi")
    for (let i = 0; i < choixDemi.length; i++) {
        choixDemi[i].addEventListener('change',()=>{
            fetch("/choixMoment",{
                method : 'post',
                body : JSON.stringify({
                    moment : choixDemi[i].value,
                    date : dateInter,
                    duree : delai,
                    odi : document.querySelector('.salarie').value,
                })
            }).then((response)=>{return response.json()})
                .then((response)=>{
                    if (response==="disponible"){
                        document.querySelector(".demiJ").removeAttribute('hidden')
                        document.querySelector('.resultat').innerText = "L'opérateur est disponible. Vous pouvez valider votre intervention."
                    }
                    else{
                        document.querySelector(".demiJ").setAttribute('hidden','hidden')
                        document.querySelector('.resultat').innerText = "L'opérateur n'est disponible. Veuillez modifier votre choix."
                    }
                })
        })
    }

    let heure = document.querySelector("#heure")
    heure.addEventListener('change',()=>{
        fetch("/choixHeure",{
            method:'post',
            body : JSON.stringify({
                date : dateInter,
                duree : delai,
                odi : document.querySelector('.salarie').value,
                heure : heure.value
            })
        }).then((reponse)=>{return reponse.json()})
            .then((response)=>{
                if (response==="disponible"){
                    document.querySelector(".reponseHeure").innerHTML ="L'opérateur est disponible. Vous pouvez valider votre intervention."
                    document.querySelector('.heure').removeAttribute('hidden')
                }
                else{
                    document.querySelector('.resultat').innerText = "L'opérateur n'est disponible. Veuillez modifier votre choix."
                    document.querySelector('.heure').setAttribute('hidden','hidden')
                }
            })
    })

    let valider = document.querySelectorAll('.btn-maincolor')
    let moment;
    for (const element of valider) {
        element.addEventListener('click',()=>{
            let dateRdv;
            if (element.classList.contains('journee')){
               moment = 'Dans la journee'
            }
            else if(element.classList.contains('demiJ')){
                let choixMoment = document.querySelector("input.demijournee:checked")
                if (!choixMoment){
                    alert("Veuillez choisir une periode pour votre rendez vous")
                }
                else{
                    moment = choixMoment.value

                }

            }else{
                moment = "Heure précise"
                dateRdv = heure.value
            }
            fetch("/validerDiag",{
                method : "POST",
                body : JSON.stringify({
                    intervention : inter,
                    moment : moment,
                    duree : delai,
                    odi : document.querySelector('.salarie').value,
                    date : dateInter,
                    rdv : dateRdv,
                    prix : prixInter,
                    pack : pack
                })
            }).then((response)=>{return response.json()})
                .then((response)=>{
                    document.querySelector(".closeModal").click()

                    if (response.inscrit){
                        document.querySelector(".connectz").click()
                    }else{
                        document.querySelector('.inscription').click()
                        document.querySelector("#connect").href = "/login?id="+response.inter
                        document.querySelector('#inscrit').href = "/inscription/demandeur?id="+response.inter
                    }


                })
        })
    }
}
import { Calendar } from '@fullcalendar/core';
import fr from '@fullcalendar/core/locales/fr';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from "@fullcalendar/interaction";
if (document.title === 'Calendrier') {
    let calendarEl = document.getElementById('calendrier');
    let idSalarie = document.querySelector('#idSalarie')
    let route; let routeInfo;
    if (idSalarie.dataset.role==="odi"){
        route='/calendarODI/'+idSalarie.value
        routeInfo = "/infoInterOdi"
    }
    else{
        route='/calendarOTD/'+idSalarie.value;
        routeInfo = "/recupereInterCalendar";

    }
    document.addEventListener('DOMContentLoaded', () => {


        let calendar = new Calendar(calendarEl, {
            plugins: [ dayGridPlugin, timeGridPlugin, listPlugin,interactionPlugin ],
            initialView: 'dayGridMonth',
            locale: fr,
            height: 750,
            aspectRatio: 4.5,
            editable: false,

            views: {
                listDay: { buttonText: "Aujourd'hui" },
                listWeek: { buttonText: 'Semaine' },
                listMonth: { buttonText: 'Mois' }
            },
            events : route,

            headerToolbar: {
                left: 'prev,next',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay',
            },

            timeZone: 'locale',
            allDaySlot: false,


            slotDuration: '00:30:00',
            eventClick: function(info) {
                if (info.event._def.extendedProps.recurringDef ==="absence"){
                    let confirmer = confirm("Souhaitez vous supprimer cette plage horaire ?")
                    if (confirmer){
                        fetch("/SuppimerAbsence/"+info.event._def.extendedProps.publicId,{
                            method : 'GET'
                        })
                            .then(()=>{
                                alert("Suppression effectuée")
                                info.event.remove()
                            })
                    }

                }else{
                    fetch(routeInfo,{
                        method : 'POST',
                        body :info.event._def.extendedProps.publicId
                    })
                        .then((response)=>{
                            return response.json()
                        })
                        .then((response)=>{

                            for (const responseElement of response) {
                                document.querySelector('.typeInter').innerHTML = responseElement.type
                                document.querySelector('.adresse').innerHTML = responseElement.adresse
                                document.querySelector('.detail').innerHTML = responseElement.detail
                                document.querySelector('.drone').innerHTML = responseElement.drone
                                document.querySelector('.demandeur').innerHTML = responseElement.demandeur
                                document.querySelector('.rdv').innerHTML = responseElement.rdv
                                document.querySelector('.temps').innerHTML = responseElement.temps
                                document.querySelector('.distance').innerHTML = responseElement.distance

                            }
                            document.querySelector('.btn-primary').click()
                        })
                }

            }


        });

        calendar.render();

    })
        ;


}
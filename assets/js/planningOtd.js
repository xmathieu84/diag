import { Calendar } from '@fullcalendar/core';
import fr from '@fullcalendar/core/locales/fr';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from "@fullcalendar/interaction";


if (document.title ==='Planning OTD'  || document.title ==='Planning ODI'){
    let route;
    if (document.title==="Planning OTD"){
        route="/planningAllOtd"
    }
    else {
        route="/planningAllOdi"
    }
    let calendrier = document.querySelector('#calendrier')
    document.addEventListener('DOMContentLoaded',()=>{
        let calendar = new Calendar(calendrier, {
            plugins: [ dayGridPlugin, timeGridPlugin, listPlugin,interactionPlugin ],
            initialView: 'dayGridMonth',
            locale: fr,
            height: 1200,
            aspectRatio: 2.5,
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


        });
        calendar.render();
    })


}
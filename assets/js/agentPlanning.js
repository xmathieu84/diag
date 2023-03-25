import { Calendar } from '@fullcalendar/core';
import fr from '@fullcalendar/core/locales/fr';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from "@fullcalendar/interaction";

if (document.title ==='Votre planning'){
    let calendrier = document.querySelector('#calendrier')
    let idAgent = document.querySelector("#idAgent")
    let calendar = new Calendar(calendrier, {
        plugins: [ dayGridPlugin, timeGridPlugin, listPlugin,interactionPlugin ],


        initialView: 'listWeek',
        locale: fr,
        height: 600,
        aspectRatio: 3.5,
        events : '/recupererPlanning/'+idAgent.value,
        views: {
            listDay: { buttonText: "Aujourd'hui" },
            listWeek: { buttonText: 'Semaine' },
            listMonth: { buttonText: 'Mois' }
        },

        headerToolbar: {
            start: 'prev,next',
            center: 'title',
            right: 'listDay,listWeek,listMonth'
        },

        timeZone: 'locale',




    });

    calendar.render();
}
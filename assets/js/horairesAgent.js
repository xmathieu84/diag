import { Calendar } from '@fullcalendar/core';
import fr from '@fullcalendar/core/locales/fr';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin, { Draggable  } from '@fullcalendar/interaction';


if (document.title === 'Créer planning'){
    let idAgent = document.querySelector('#idAgent');
    document.addEventListener('DOMContentLoaded', () => {
        let calendarEl = document.getElementById('planning');
        let couleur = document.querySelector('#couleur')
        let intitule = document.querySelector('#intitule')
        let ouiJour = document.querySelector('#ouiJour')
        let nonJour = document.querySelector('#nonJour')
        let action = document.querySelector('.action')
        let agent = document.querySelector('#agent')
        let alerte = document.querySelector("#alertPanel")
        let bgAlert = document.querySelector('#overlay')
        let valider = document.querySelector('.btn-outline-danger')
        let annuler = document.querySelector('.btn-outline-success')
        function dataEvent(){
            if (ouiJour.checked===true){
                action.dataset.event = '{"title":"'+intitule.value +' '+agent.value+'","duration":"24:00","id":"'+intitule.value+'","backgroundColor":"'+couleur.value+'"}'
            }
            if(nonJour.checked === true) {
                action.dataset.event = '{"title":"'+intitule.value +' '+agent.value+'","duration":"00:30","id":"'+intitule.value+'","backgroundColor":"'+couleur.value+'"}'
            }
        }
        couleur.addEventListener('change',()=>{
            action.style.backgroundColor = couleur.value

            dataEvent();
        })
        intitule.addEventListener('keyup',()=>{
            action.innerHTML = intitule.value
            dataEvent()
        })
        ouiJour.addEventListener('change',()=>{
            dataEvent()
        })
        nonJour.addEventListener('change',()=>{
            dataEvent()
        })


        let calendar = new Calendar(calendarEl, {
            plugins: [ dayGridPlugin, timeGridPlugin, listPlugin,interactionPlugin ],


            initialView: 'timeGridWeek',
            locale: fr,
            height: 600,
            aspectRatio: 3.5,
            
            events : '/recupererPlanning/'+idAgent.value,
            views : {
                timeGrid:{
                    visibleRange :{
                        start:"5:00:00",
                        end:"20:00:00"
                    },

                    businessHours: {
                        daysOfWeek: [ 1, 2, 3, 4,5 ],
                        startTime: '05:00',
                        endTime: '20:00',
                    },
                    hiddenDays :[6,0],
                    slotDuration :'00:30:00',

                },


            },
            droppable:true,
            editable: true,
            headerToolbar: {
                start: 'prev,next',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek',
            },

            timeZone: 'locale',

            allDaySlot: false,
            eventClick : (info)=>{
                alerte.style.display ='block'
                bgAlert.style.display = 'block'
                valider.addEventListener('click',()=>{
                    info.event.remove()
                    alerte.style.display ='none'
                    bgAlert.style.display = 'none'

                })
                annuler.addEventListener('click',()=>{
                    alerte.style.display ='none'
                    bgAlert.style.display = 'none'
                })
            },




        });

        calendar.render();
        new Draggable(action);

        document.querySelector('.btn-maincolor').addEventListener('click',()=>{

            fetch('/planningAgent/'+idAgent.value,{
                method:'POST',
                body:JSON.stringify(calendar.getEvents())
            })
                .then((response)=>{
                    return response.json()
                })
                .then((response)=>{
                    alert('Vos changements ont été pris en compte .')
                })




        })


    })
    ;

}
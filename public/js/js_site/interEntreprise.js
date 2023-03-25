document.addEventListener('DOMContentLoaded',()=>{
    let notifgen = document.querySelectorAll('.notifGénérale')
    let notifFin = document.querySelectorAll('.notifFin')
    let lienMenu = document.querySelectorAll('.menuPrincipal>li>a')
    console.log(window.navigator);
    fetch('/entreprise/proposition/nombre',{
        method :'GET'
    })
        .then((response)=>{
            return response.json()
        })
        .then((response)=>{

            if (response>0){
                notifgen[0].classList.add('interNotifEnt')
                notifgen[0].innerHTML = response
                notifgen[1].classList.add('interNotifEnt')
                notifgen[1].innerHTML = response
            }
            for (let i = 0; i < lienMenu.length; i++) {
                lienMenu[i].addEventListener('click',()=>{
                    if (lienMenu[i].classList.contains('interGen') && response>0 && !notifFin[0].classList.contains('interNotifEnt') ||!notifFin[1].classList.contains('interNotifEnt') ){
                        notifFin[0].classList.add('interNotifEnt')
                        notifFin[0].innerHTML = response
                        notifgen[0].classList.remove('interNotifEnt')
                        notifgen[0].innerHTML = ''
                        notifFin[1].classList.add('interNotifEnt')
                        notifFin[1].innerHTML = response
                        notifgen[1].classList.remove('interNotifEnt')
                        notifgen[1].innerHTML = ''
                    }
                    else if (notifFin[0].classList.contains('interNotifEnt')||notifFin[1].classList.contains('interNotifEnt')){

                        notifgen[0].classList.add('interNotifEnt')
                        notifgen[0].innerHTML = response
                        notifFin[0].classList.remove('interNotifEnt')
                        notifFin[0].innerHTML = ''
                        notifgen[1].classList.add('interNotifEnt')
                        notifgen[1].innerHTML = response
                        notifFin[1].classList.remove('interNotifEnt')
                        notifFin[1].innerHTML = ''

                    }
                })
            }
        })


})
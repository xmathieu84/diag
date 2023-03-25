if (document.title == "Mes Interventions") {

    let boutonDate = document.querySelectorAll('.btn-inter');
    let validerdate = document.querySelectorAll('.change')
    let modalDate = document.querySelectorAll('.dateInter')
    let date = document.querySelectorAll('.dateIntervention');
    let heure = document.querySelectorAll('.heureInter');
    let boutonDrone = document.querySelectorAll('.btn-changer');
    let modalDrone = document.querySelectorAll('.appareil')
    let choixDrone = document.querySelectorAll('.choixAppareil')
    for (let i = 0; i < boutonDate.length; i++) {

        boutonDate[i].addEventListener('click', () => {
            modalDate[i].showModal();

            validerdate[i].addEventListener('click', () => {
                let rdv = Date.parse(date[i].value + ' ' + heure[i].value);
                let corp = JSON.stringify({
                    dateRdv: rdv / 1000,
                    inter: boutonDate[i].dataset.inter
                })
                fetch('/changerDate', {
                    method: 'POST',
                    body: corp

                })
                    .then(() => {
                        document.location.reload()
                    })
            })
        })


    }
    for (let j = 0; j < boutonDrone.length; j++) {
        boutonDrone[j].addEventListener('click', () => {
            modalDrone[j].showModal();
            choixDrone[j].addEventListener('change', () => {

                let corps = JSON.stringify({
                    idDrone: choixDrone[j].value,
                    idInter: choixDrone[j].dataset.inter
                })
                fetch('/choixDroneInter', {
                    method: 'POST',
                    body: corps
                })
                    .then(() => {
                        document.location.reload();
                    })

            })
        })

    }



}
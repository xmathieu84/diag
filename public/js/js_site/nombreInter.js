document.addEventListener('DOMContentLoaded', () => {
    let interApayer = document.querySelectorAll('.Apaiement');
    let encours = document.querySelectorAll('.cours');
    let termine = document.querySelectorAll('.terminees');

    fetch('/nombreInter', {
        method: 'GET'
    })
        .then(response => {
            return response.json();
        })
        .then(response => {
            console.log(response)
            if (response.payer > 0 && interApayer.length > 0) {
                interApayer[0].innerHTML = response.payer
                interApayer[0].style.paddingLeft = '0.5em';
                interApayer[0].style.paddingRight = '0.5em';
                interApayer[1].innerHTML = response.payer;
                interApayer[1].style.paddingLeft = '0.5em';
                interApayer[1].style.paddingRight = '0.5em';
            }
            if (response.enCours > 0) {
                encours[0].innerHTML = response.enCours;
                encours[0].style.paddingLeft = '0.5em';
                encours[0].style.paddingRight = '0.5em';
                encours[1].innerHTML = response.enCours;
                encours[1].style.paddingLeft = '0.5em';
                encours[1].style.paddingRight = '0.5em';
            }
            if (response.termine > 0) {
                termine[0].innerHTML = response.termine;
                termine[0].style.paddingLeft = '0.5em';
                termine[0].style.paddingRight = '0.5em';
                termine[1].innerHTML = response.termine;
                termine[1].style.paddingLeft = '0.5em';
                termine[1].style.paddingRight = '0.5em';
            }
        })
})

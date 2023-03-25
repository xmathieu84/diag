if (document.title ==='Condition générales') {
    document.addEventListener('DOMContentLoaded', () => {
        /*let modale = document.querySelector('.modal');
        let bouton = document.querySelector('.btn-maincolor');


        bouton.addEventListener('click', () => {
            modale.style.display = 'block'

        })*/
    })
}
if (document.title == 'Mandat SEPA') {
    let suivant = document.querySelector('.btn-maincolor2');
    suivant.addEventListener('click', () => {
        document.location = '/terminer/Inscription';
    })
}
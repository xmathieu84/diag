if (document.title == 'Accueil entreprise') {
    let bouton = document.querySelector('#modal');
    let modale = document.querySelector('#modif');

    bouton.addEventListener('click', () => {
        modale.style.display = 'block'
    })

}
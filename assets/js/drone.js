if (document.title == 'Phase 5 enrigister vos drones') {
    let boite = document.querySelector("#choice")
    let form = document.querySelector('form');
    let a = document.createElement('a')
    let button = document.querySelector('.bouton')

    boite.addEventListener('change', () => {
        if (boite.checked) {
            form.style.display = 'none'
            a.classList.add('btn');
            a.classList.add('btn-maincolor2')
            a.href = '/banqueAssurance'
            a.innerHTML = 'Etape suivante';
            button.appendChild(a);
        }
        else {
            button.removeChild(a)
            form.style.display = 'block'
        }
    })
}
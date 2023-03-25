if (document.title == 'Intervention sans proposition') {
    let bouton = document.querySelectorAll('.btn-primary')
    let inputProp = document.querySelectorAll('.inputProp')

    for (let i = 0; i < bouton.length; i++) {
        bouton[i].addEventListener('click', () => {
            let contenu = JSON.stringify({
                prix: inputProp[i].value,
                salarie: inputProp[i].dataset.salarie,
                intervention: inputProp[i].dataset.inter,
                distance: inputProp[i].dataset.distance
            })
            fetch('/validerProp', {
                method: 'POST',
                body: contenu
            })
                .then(() => {
                    document.location.reload();
                })
        })

    }
}


if (document.title == 'Création zip') {
    let p = document.querySelector('p')
    let id = document.querySelector('img').dataset.rapport
    console.log(id);
    document.addEventListener('DOMContentLoaded', () => {
        fetch('/telechargerMedia', {
            method: 'POST',
            body: id,
            headers: new Headers(),
        })
            .then((response) => {

                return response.json()
            })
            .then((response) => {
                p.innerHTML = 'La finalisation est terminée. Vous allez être redirigés automatiquement.'

                p.style.animationName = 'non'
                if (response['reponse'] === 'ok') {
                    setTimeout(() => {
                        document.location.href = response['route'];
                    }, 6000);
                }
            })
    })

}
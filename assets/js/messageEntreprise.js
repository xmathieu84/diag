if (document.title == 'Lire un message') {
  document.getElementById('repondre').addEventListener('click', () => {
    document.getElementById('reponse').style.display = 'block'
  })
  document.getElementById('fermer').addEventListener('click', () => {
    document.getElementById('reponse').close()
  })
  //fonctionnalite marquer comme lu
  let adresse = document.getElementById('chemin')
  chemin.addEventListener('click', function (e) {
    e.preventDefault()
    fetch(adresse.href, {
      method: 'GET',
      headers: new Headers(),
    })
      .then(function (response) {
        return response.json()
      })
      .then(function (response) {
        JSON.stringify(response)

        if (response.reponse) {
          adresse.style.display = 'none'
        }
      })
  })
}
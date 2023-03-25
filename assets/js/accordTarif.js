if (document.title === 'Tarifs disponibles' || document.title === 'Phase 4 choix des tarifs') {

	document.addEventListener('DOMContentLoaded', () => {
		let tarifs = document.querySelectorAll('.tauxHoraire');
		let indemnite = document.querySelector('.indemnite')
		let prixMin = document.querySelectorAll('.prixMinimum')
		let zoneBouton = document.querySelector('.button')
		let a = document.createElement('a')
		a.classList.add('btn')
		a.classList.add('btn-maincolor2')
		a.setAttribute('href', '/banqueAssurance')

		indemnite.addEventListener('keyup', () => {
			fetch('/indemnite', {
				method: 'POST',
				body: indemnite.value
			})
		})

		for (let i = 0; i < tarifs.length; i++) {
			tarifs[i].addEventListener('keyup', () => {

				let corps = JSON.stringify({
					id: tarifs[i].dataset.id,
					tarif: tarifs[i].value,


				})
				fetch('/envoieTarif', {
					method: 'POST',
					body: corps
				})
				for (let j = 0; j < tarifs.length; j++) {


					if (tarifs[j].value != 0 && document.title == 'Phase 4 choix des tarifs') {

						zoneBouton.appendChild(a)
						a.innerHTML = 'Valider'
					}

				}




			})
			prixMin[i].addEventListener('keyup', () => {

				let corps = JSON.stringify({
					id: tarifs[i].dataset.id,
					prixMinimum: prixMin[i].value

				})
				fetch('/envoiePrixMin', {
					method: 'POST',
					body: corps
				})
				for (let j = 0; j < prixMin.length; j++) {


					if (prixMin[j].value != 0 && document.title == 'Phase 4 choix des tarifs') {

						zoneBouton.appendChild(a)
						a.innerHTML = 'Valider'
					}

				}



			})

		}
	})


}

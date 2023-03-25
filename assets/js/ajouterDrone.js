if (document.title == 'Liste des drones') {
    let contenu1 = document.querySelectorAll("input[type=text]")
    let contenu2 = document.querySelectorAll("input[type=radio]")
    let contenu3 = document.querySelector('select')
    let contenu4 = document.querySelectorAll("input[type=number]")
    let bouton = document.querySelector('.btn-primary')
    let form = new FormData()
    let captif=undefined;
    let deleteDrone = document.querySelectorAll('.deleteDrone')
    let adresse;
    console.log(contenu1)
    console.log(contenu2)
    console.log(contenu3)
    console.log(contenu4)
    bouton.addEventListener('click', () => {
        if (contenu2[0].checked) {
            captif = contenu2[0].value
        }
        else {
            captif = contenu2[1].value
        }
        console.log(captif)
        if (captif !=undefined && contenu1[0].value !=="" && contenu1[1].value !=="" && contenu1[2].value !==""&& contenu1[5].value!==""&& contenu3.value !=="" && contenu4[0].value!==""){
            form.append('fabriquant', contenu1[0].value)
            form.append('type', contenu1[1].value)
            form.append('numero', contenu1[2].value)
            form.append('poids', contenu4[0].value)
            form.append('trame', contenu1[3].value)
            form.append('serial',contenu1[5].value)
            form.append('marque',contenu1[4].value)
            form.append('vitesse',contenu4[1].value)
            form.append('captif', captif)
            form.append('classe', contenu3.value)

                fetch('/drone/enregistrer', {
                method: 'POST',
                body: form
            })
                .then(() => {
                    alert('Votre appareil est enregistré.')
                    document.location.reload()
                })
        }
        else{
            alert("Veuillez remplir les champs obligatoires")
        }



    })
    for (let i = 0; i < deleteDrone.length; i++) {
        deleteDrone[i].addEventListener('click',(e)=>{
            e.preventDefault();
             adresse = deleteDrone[i].href
            let confirmer = confirm("Voulez vous supprimer cet appareil ?")
            if (confirmer){
                document.location.href = adresse
            }
        })
    }
}
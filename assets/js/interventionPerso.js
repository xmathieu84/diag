

if (document.title == "Créer une intervention") {
    let listeInter = document.querySelector('.listeinter')
    let type = document.querySelector('.typeInter')
    let bouton = document.querySelector('.btn-maincolor')
    let adresse = document.querySelectorAll('.adresses')
    let precision = document.querySelector('.precision')
    let date = document.querySelector('.Date')
    let drone = document.querySelector('.drone');
    let civilite = document.querySelectorAll('.civilite')
    let typeIntemp = document.querySelectorAll('.intemp')
    let ouiIntemp = document.querySelector("#ouiIntemp")
    let nonItemp = document.querySelector('#nonIntemp')
    let dateIntemp = document.querySelector('#dateItemp')
    let autre = document.querySelector('#autre')
    let genre;
    let intemp=[];
    ouiIntemp.addEventListener('click',()=>{
        document.querySelector('.typeintemp').style.display = 'flex'
    })
    nonItemp.addEventListener('click',()=>{
        document.querySelector('.typeintemp').style.display = 'none'
    })


    listeInter.addEventListener('change', () => {

        fetch('/preciserInter', {
            method: 'POST',
            body: listeInter.value
        })
            .then((reponse) => {
                return reponse.json()
            })
            .then((reponse) => {
                while (type.firstChild) {
                    type.removeChild(type.lastChild)
                }
                for (const key in reponse) {
                    let option = document.createElement('option')

                    if (reponse.hasOwnProperty(key)) {
                        const element = reponse[key];
                        option.value = element['id']
                        option.innerText = element['nom']
                        type.appendChild(option);
                    }
                }
            })
    })


    function inter() {
        let form = new FormData();
        form.append('idDrone', drone.value)
        form.append('idListeInter', listeInter.value)
        form.append('idTypeInter', type.value)
        form.append('date', (Date.parse(date.value)) / 1000)
        form.append('precision', precision.value)
        form.append('idSalarie', bouton.dataset.salarie)

        for (let i = 0; i < adresse.length; i++) {
            form.append(adresse[i].dataset.identifiant, adresse[i].value)

        }

        if (civilite[0].checked) {
            genre = civilite[0].value
        }
        else {
            genre = civilite[1].value
        }
        form.append(civilite[0].dataset.identifiant, genre)
        form.append(civilite[2].dataset.identifiant, civilite[2].value)
        form.append(civilite[3].dataset.identifiant, civilite[3].value)
        if (ouiIntemp.checked) {
            for (let i = 0; i < typeIntemp.length; i++) {
                if (typeIntemp[i].checked) {
                    intemp.push([typeIntemp[i].value])
                }



            }
            if (autre.value !==""){
                intemp.push([autre.value])
            }

            form.append('dateIntemp', (Date.parse(dateIntemp.value) / 1000))
            form.append('intemperie', intemp)
        }

       fetch('/enregistrerInter', {
            method: 'POST',
            body: form
        })
            .then((reponse) => {
                if (reponse) {
                    document.location.href = '/mes interventions'

                }


            })
    }
    bouton.addEventListener('click', () => {
        if (listeInter.dataset.nom == 'interventions aériennes') {
            if (drone.value) {
                inter();
            }
            else {
                alert('Veuillez renseignez le type de drone utilisé')
            }
        }
        else {
            inter()
        }




    })
}
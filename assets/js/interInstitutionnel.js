if (document.title == 'Intervention institutionnel') {
    let temps = document.querySelector('#duree')
    let corps = document.querySelector('.corpsTable')

    document.addEventListener('DOMContentLoaded', () => {
        choixTemps('P7D')
    })
    temps.addEventListener('change', () => {
        while (corps.lastChild) {
            corps.removeChild(corps.lastChild)
        }
        choixTemps(temps.value)
    })

    function choixTemps(duree) {
        fetch('/institutionnel/recuperer/listeInter', {
            method: 'POST',
            body: duree
        })
            .then((reponse) => {
                return reponse.json()
            })
            .then((reponse) => {
                console.log(reponse);
                for (let i = 0; i < reponse.length; i++) {
                    while (corps.firstChild){
                        corps.removeChild(corps.lastChild)
                    }
                    const liste = reponse[i];
                    let tr = document.createElement('tr')

                    for (let j = 0; j < liste['inter'].length; j++) {

                        const element = liste['inter'][j];
                        console.log(element);
                        console.log(typeof (liste['inter'][0]));
                        let td = document.createElement('td')
                        let span = document.createElement('p')
                        span.innerHTML = element
                        td.appendChild(span)
                        tr.appendChild(td)

                    }
                    let td = document.createElement('td');

                    if (liste['result']["result"] == true) {
                        let button = document.createElement('a')

                        button.classList.add('btn')
                        button.classList.add('btn-maincolor')
                        button.classList.add('reserverOtd')
                        button.href = '/creerInterInsti/' + liste.tipi.listeInter + '/' + liste.tipi.typeInter + '/' + liste.tipi.date + '/' + liste.tipi.otd + '/' + liste.tipi.ville + '/' + liste.tipi.codeP
                        button.innerText = 'Réserver cet OTD'
                        td.appendChild(button);

                    }
                    else {
                        let span = document.createElement('span')
                        span.classList.add('h6')
                        span.innerHTML = "Cet OTD n'est plus disponible pour cette date"
                        td.appendChild(span)
                    }

                    tr.appendChild(td)
                    corps.appendChild(tr)

                }
                let reserverOtd = document.querySelectorAll(".reserverOtd")
                for (let i = 0; i < reserverOtd.length; i++) {
                    reserverOtd[i].addEventListener('click',(e)=>{
                        e.preventDefault()
                        document.querySelector('#modalInterInsti').style.display ='block'
                        document.querySelector("#continuer").addEventListener('click',()=>{
                            document.location.href = reserverOtd[i].href
                        })
                        document.querySelector('#close-modal').addEventListener('click',()=>{
                            document.querySelector("modalInterInsti").style.display="none"
                        })
                    })
                }

            })
    }

}
if (document.title == 'Nouvelle Intervention institutionnel') {

    let button = document.querySelector('.boutonRemplace')
    let realInput = document.querySelector('.custom-file-input')

    button.addEventListener('click', () => {
        realInput.click()
        realInput.addEventListener('change', () => {
            console.log(realInput.files.length);
            button.innerHTML = realInput.files.length + ' fichier(s) selectionné(s)'
        })
    })

    let radio = document.getElementsByName('inter_insti[intemp]')
    let intemp = document.querySelectorAll('.intempInsti')

    for (let i = 0; i < radio.length; i++) {
        const element = radio[i];
        element.addEventListener('change', () => {
            if (element.value == 'Oui') {
                console.log('ok');
                for (let j = 0; j < intemp.length; j++) {
                    const elementIntemp = intemp[j];
                    elementIntemp.style.display = 'block'

                }
            }
            else {
                for (let k = 0; k < intemp.length; k++) {
                    const elementNonIntemp = intemp[k];
                    elementNonIntemp.style.display = 'none'

                }
            }

        })

    }
}
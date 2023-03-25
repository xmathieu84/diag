if (document.title == 'Accueil armee'){
    let interJour = document.querySelector('.interJour');
    let interSemaine = document.querySelector('.interSemaine')
    let interMois = document.querySelector('.interMois')
    document.addEventListener('DOMContentLoaded',()=>{
        fetch("/militaire/interDujour",{
            method:'GET'
        })
            .then((reponse)=>{
                return reponse.json()
            })
            .then((reponse)=>{

                if (reponse['nbreInterjour']<=1){
                    interJour.innerText = reponse['nombreInterJour'] + ' intervention'

                }
                else{
                    interJour.innerText = reponse['nombreInterJour'] + ' interventions'
                }
                if (reponse['nbreInterSemaine']<=1){
                    interSemaine.innerText = reponse['nbreInterSemaine'] + ' intervention'

                }
                else{
                    interSemaine.innerText = reponse['nbreInterSemaine'] + ' interventions'
                }
                if (reponse['nbreInterMois']<=1){
                    interMois.innerText = reponse['nbreInterMois'] + ' intervention'

                }
                else{
                    interMois.innerText = reponse['nbreInterMois'] + ' interventions'
                }

            })


    })
}
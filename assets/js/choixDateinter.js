if(document.title ==='Choix Date'){
    let code = document.querySelector('.codeSyndic')
    let aujourd = new Date();
    let demain = new Date();
    let dateMax = new Date();

    let delai = document.querySelector('#delai')

    dateMax.setDate(aujourd.getDate()+2)
    if (delai){
        demain.setDate(aujourd.getDate()+14);
        dateMax.setDate(aujourd.getDate()+15)

    }
    else{
        demain.setDate(aujourd.getDate());
        dateMax.setDate(aujourd.getDate()+2)
    }

    let choixTypedate = document.getElementsByName("typeDate")
    let type = "dateSeule";
    for (let i = 0; i < choixTypedate.length; i++) {
        choixTypedate[i].addEventListener('change',()=>{
            switch (choixTypedate[i].value){
                case "dateSeule":
                    document.querySelector('.dateSimple').style.display="block"
                    document.querySelector('.plageDate').style.display = "none"
                    type = "dateSeule"
                    break;
                case "plageDate" :
                    document.querySelector('.dateSimple').style.display="none"
                    document.querySelector('.plageDate').style.display = "block"
                    type= "plageDate"
                    break;
                case "sansDate" :
                    document.querySelector('.dateSimple').style.display="none"
                    document.querySelector('.plageDate').style.display = "none"
                    type = "sansDate"
                    break;
            }
        })
    }
    let date = new Date(demain);
    let dateInput =document.querySelector('#dateInter');
    let dateMinimum = document.querySelector("#dateMin")
    let datemaximum = document.querySelector("#dateMax")
    dateInput.value = demain.toISOString().substring(0,10);
    dateInput.min = demain.toISOString().substring(0,10);
    dateMinimum.value = demain.toISOString().substring(0,10)
    dateMinimum.min = demain.toISOString().substring(0,10)
    datemaximum.value = dateMax.toISOString().substring(0,10)
    datemaximum.min = dateMax.toISOString().substring(0,10)


    let temps;
    let btn = document.querySelector('.btn-maincolor');

    dateMinimum.addEventListener('change',()=>{
        let nDateMaximum = new Date(new Date().setDate(new Date(dateMinimum.value).getDate()+1)).toISOString().substring(0,10);
        datemaximum.value = nDateMaximum
    })
    datemaximum.addEventListener('change',()=>{
        let nDateMinimum = new Date(new Date().setDate(new Date(datemaximum.value).getDate()-1));

        if (nDateMinimum.getTime() < new Date(dateMinimum.value).getTime()){
            dateMinimum.value = nDateMinimum.toISOString().substring(0,10)
        }
    })


   /* btn.addEventListener('click',()=>{

        if (demain.getTime() <= date.getTime()){
            let timestamp = date.getTime();

            fetch("/newInter/nbreOtd/"+btn.dataset.inter,{
                method :'POST',
                body : timestamp
            }).then((response)=>{
                return response.json()
            })
                .then((response)=>{
                    if (response.type ==='rapide'){
                        reponse = "<p class='h4'><span class='color_blue'>Diag-drone</span> a détecté  <span class='color_blue nombre'>"+response.nombre+"</span>\n" +
                            "                        <span class='color_blue'>O</span>pérateurs <span class='color_blue'>T</span>élépilotes de <span class='color_blue'>D</span>rone qui correspondent à votre demande pouvant intervenir dans un délai de 24 heures minimum</p>"
                    }
                    else{
                        reponse = "<p class='h4'><span class='color_blue'>Diag-drone</span> a détecté  <span class='color_blue nombre'>"+response.nombre+"</span>\n" +
                            "                        <span class='color_blue'>O</span>pérateurs <span class='color_blue'>T</span>élépilotes de <span class='color_blue'>D</span>rone qui correspondent à votre demande pouvant intervenir dans un délai de 6 jours minimum</p>"
                    }
                    document.querySelector('.modal-body').innerHTML = reponse
                    document.querySelector('#modalReponse').style.display = 'block'
                    document.querySelector('#modalReponse').classList.add('show')
                    document.querySelector('#modalReponse').style.paddingRight = '16px'
                })
        }
        else {
            alert('Veuillez choisir un autre jour')
        }

    })*/

    let valider = document.querySelector('.btn-maincolor2')
    btn.addEventListener('click',()=>{
        console.log(type);
        let content ;
            switch (type){
                case "dateSeule":
                    content =JSON.stringify({
                        debut : dateInput.value,
                        fin : null
                    })
                    break;

                case "plageDate":
                    content = JSON.stringify({
                        debut : dateMinimum.value,
                        fin : datemaximum.value
                    })
                    break;

                case "sansDate" : {
                        content = null
                    break;
                }
            }


           // document.querySelector('.closeF').click()
            document.querySelector(".btnModale").click()
            fetch('/validerRdvInter/'+valider.dataset.inter,{
                method:'POST',
                body : content
            })
                .then(()=>{
                    document.querySelector(".closeSpinner").click()
                    document.querySelector(".btnModaleF").click()
                   if (code){
                        //document.location.href='/demandeur/encours/'+code.value;
                    }
                    else{
                        //document.location.href='/demandeur/encours/'
                    }

                })


    })
    document.querySelector('.btn-secondary').addEventListener('click',()=>{
        document.querySelector('#modalReponse').style.display = 'none';
    })




}
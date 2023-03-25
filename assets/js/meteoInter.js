if (document.title==='Météo pour mes interventions'){
    let interventions = document.querySelectorAll('input[type=hidden]')
        let temps = document.querySelectorAll('.temp')
    let temperature = document.querySelectorAll('.tempe')
    let vent = document.querySelectorAll('.wind')

        for (let i = 0; i <interventions.length ; i++) {
            fetch('/retournMeteo/'+interventions[i].value,{
                method : 'GET'
            }).then((response)=>{
                return response.json()
            })
                .then((response)=>{

                    temps[i].innerHTML = response[0].temps
                    temperature[i].innerHTML = response[0].temp+' C°'
                    vent[i].innerHTML = 'Vent moyen : '+response[0].vent+' km/h <br> '+ 'Rafales : '+response[0].rafale +' km/h'
                })
        }




}

if (document.title ==="Demande d'intervention (phase 1)"){
    console.log('ok')
    let possible = document.querySelector('.possible')

    if (possible && possible.value ==='true'){
        let decollage = document.getElementsByName("intervention[decollage]")
        let retract = document.getElementsByName('intervention[renoncementDelaiRetract]')
        console.log(retract)
        for (let i = 0; i < decollage.length; i++) {
            decollage[i].addEventListener('change',()=>{
                if (decollage[i].value ==='Oui'){

                    if (retract.length>0){
                        for (let j = 0; j < retract.length; j++) {
                            retract[i].addEventListener('change',()=>{
                                if (retract[i].value ==="1"){
                                    document.querySelector('.modalMess').click()
                                }
                            })

                        }
                    }else {
                        document.querySelector('.modalMess').click()
                    }
                }
            })
        }
    }
}
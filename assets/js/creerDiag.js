if (document.title=='Créer un diagnostic'){
    let trueFile = document.querySelector('#docDiag')
    let falseButton = document.querySelector('.boutonRemplace')
    falseButton.addEventListener('click',()=>{
        trueFile.click()
        trueFile.addEventListener('change',()=>{
            falseButton.innerHTML = trueFile.files[0]['name'];
        })
    })
    let validite = document.querySelector('#date')
    let type = document.querySelector('.nomDiag')
    let save = document.querySelector('.btn-primary')
    let informe;
    if (document.querySelector('#non').checked == true){
        informe = false;
    }
    else {
        informe = true;
    }

    save.addEventListener('click',()=>{


        if (trueFile&&validite.value&&type.value){

            let form = new FormData();
            form.append('dateFin',validite.value)
            form.append('typeDiag',type.value)
            form.append('document',trueFile.files[0])
            form.append('informe',informe)
            form.append('idActif',document.querySelector('#idActif').value)

            fetch("/institution/saveDiag",{
                method :'POST',
                body:form
            })
                .then(()=>{
                    document.location.reload();
                })

        }
    })

let chkInforme = document.querySelectorAll('.informeDiag')
    console.log(chkInforme)
    for (let i=0;i<chkInforme.length;i++){
        chkInforme[i].addEventListener('change',()=>{

            fetch('/institution/changeInforme',{
                method:'POST',
                body:chkInforme[i].value
            })
        })

    }

}
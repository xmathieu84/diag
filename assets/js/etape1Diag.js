if (document.title==="Nouvelle demande de diagnostics"){
    let id = document.querySelector('input[type=hidden]')
    console.log(id)
    let route;
    if (id){
        route = "/saveDiag/"+id.value
        fetch("/recupDiag/"+id.value,{
            method :'get'
        })
            .then((response)=>{return response.json()})
            .then((response)=>{
                let typeDiags = document.getElementsByName('action')
                for (const typeDiag of typeDiags) {
                    if (typeDiag.value===response.type){
                        typeDiag.checked=true
                    }
                }
                let biens = document.getElementsByName('typeBien')
                for (const bien of biens) {
                    if (bien.value ==response.bienChoisi)  {
                        bien.checked =true
                    }
                }
                let div = document.createElement('div')
                div.classList.add('col-12','mt-5','mb-5')
                let p = document.createElement('p')
                p.classList.add('h5')
                p.innerText = "Sélectionnez la taille de votre bien :"
                div.appendChild(p)
                zoneTaille.appendChild(div)
                response.taille.taille.forEach(function(element,key){

                    let div = document.createElement('div')
                    div.classList.add('col-sm-4',"col-6")
                    let label = document.createElement('label')
                    label.classList.add('border-gray','text-center','zoneTypeBien')
                    label.setAttribute('for','taille'+key)
                    label.innerText = element
                    label.style.background = "none"
                    let input = document.createElement('input')
                    input.type = "radio"
                    input.value = response.taille.id[key]
                    input.name = "tailleBien"
                    input.id = "taille"+key
                    input.classList.add("inputTypeBien",'inputTaille')
                    input.setAttribute('hidden','hidden')
                    div.append(input,label)
                    zoneTaille.appendChild(div)

                    if (response.taille.id[key] == response.tailleChoisie){
                        input.checked =true
                    }
                })
                document.querySelector("#dateInterDiag").value = response.rdv
            })
    }
    else{
        route = "/saveDiag"
    }

    let valider = document.querySelector('.btn-maincolor')
    let types = document.getElementsByName("typeBien")
    let zoneTaille= document.querySelector('.zoneTaille')
    for (const type of types) {
        type.addEventListener('change',()=>{
            fetch("/recupeTaille/"+type.value,{
                method : 'GET'
            })
                .then((response)=>{
                    return response.json()
                })
                .then((response)=>{
                    while (zoneTaille.firstChild){
                        zoneTaille.removeChild(zoneTaille.lastChild)
                    }
                    let div = document.createElement('div')
                    div.classList.add('col-12','mt-5','mb-5')
                    let p = document.createElement('p')
                    p.classList.add('h5')
                    p.innerText = "Sélectionnez la taille de votre bien :"
                    div.appendChild(p)
                    zoneTaille.appendChild(div)
                    response.forEach(function(element,key){
                        let div = document.createElement('div')
                        div.classList.add('col-sm-4',"col-6")
                        let label = document.createElement('label')
                        label.classList.add('border-gray','text-center','zoneTypeBien')
                        label.setAttribute('for','taille'+key)
                        label.innerText = element.taille
                        label.style.background = "none"
                        let input = document.createElement('input')
                        input.type = "radio"
                        input.value = element.id
                        input.name = "tailleBien"
                        input.id = "taille"+key
                        input.classList.add("inputTypeBien",'inputTaille')
                        input.setAttribute('hidden','hidden')
                        div.append(input,label)
                        zoneTaille.appendChild(div)
                    })

                })
        })
    }



    valider.addEventListener('click',()=>{
        let taille = document.querySelector('.inputTaille:checked')
        let typeDiag = document.querySelector('.typeDiag:checked')
        let date = document.querySelector("#dateInterDiag")
        if (!taille||!typeDiag){
            alert("Veuillez compléter votre demande")
        }
        else{
            fetch(route,{
                method:'post',
                body : JSON.stringify({
                    tailleBien : taille.value,
                    type : typeDiag.value,
                    dateRdv : date.value
                })
            }).then((response)=>{return response.json()})
                .then((response)=>{
                    document.location.href = "/etape1-precision/"+response
                })
        }
    })
}

if (document.title==="Etape 2 du diagnostic"){
    let id = document.querySelector('input[type=hidden]')


        document.addEventListener('DOMContentLoaded',()=>{

            fetch("/recupeEtape2/"+id.value,{
                method : 'get'
            })
                .then((response)=>{return response.json()})
                .then((response)=>{
                    console.log(response)
                    let permis = document.getElementsByName("datePermis")
                    for (const permi of permis) {
                        if(permi.value === response.permis){
                            permi.checked =true
                        }
                    }
                    let gazs = document.getElementsByName("dateGaz")
                    for (const gaz of gazs) {
                        if (gaz.value===response.gaz){
                            gaz.checked =true
                        }
                    }
                    let elecs = document.getElementsByName("dateElec")
                    for (const elec of elecs) {
                        if (elec.value===response.elec){
                            elec.checked = true
                        }
                    }
                })
        })

    let valider = document.querySelector('.btn-maincolor')

    valider.addEventListener('click',()=>{
        let permisConst = document.querySelector('.permis:checked')
        let installGaz = document.querySelector('.installGaz:checked')
        let elecInstall = document.querySelector('.elecInstall:checked')
        if (!permisConst||!installGaz||!elecInstall){
            alert("Veuillez compléter votre demande")
        }
        else{
            fetch("/completeDiag/"+valider.dataset.inter,{
                method :'post',
                body : JSON.stringify({
                    amiante : permisConst.dataset.amiante,
                    plomb : permisConst.dataset.plomb,
                    gaz : installGaz.dataset.gaz,
                    elec : elecInstall.dataset.elec,
                    ageElec : elecInstall.value,
                    ageGaz : installGaz.value,
                    permis : permisConst.value
                })
            }).then((response)=>{
                return response.json()
            }).then((response)=>{

                document.location.href="/etape2-adresse/"+response
            })
        }
    })

}
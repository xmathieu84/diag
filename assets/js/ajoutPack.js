if (document.title ==='Mon abonnement'){

    let ajouter = document.querySelectorAll('.ajoutPack')
    let packEnvoie = [];
    let packCommande = [];
    let nombrePack = document.querySelectorAll('.nbrePack')
    let commander = document.querySelector('.commander')
    let fermer = document.querySelector('.fermer')
    let totalHt= document.querySelector('.ht')
    let totalTtc = document.querySelector('.ttc')
    class Pack{
        constructor(id,nombre,nom,prix) {
            this.id = id;
            this.nombre = nombre;
            this.nom = nom;
            this.prix = prix;
        }
    }
    let ttc = 0;


    for (let i = 0; i <ajouter.length ; i++) {
        ajouter[i].addEventListener('click',()=>{

            if (nombrePack[i].value!=='0' && nombrePack[i].value !==""){
                let prix = parseInt(nombrePack[i].value)*parseInt(nombrePack[i].dataset.prix)

                let pack = new Pack(nombrePack[i].dataset.id,nombrePack[i].value,nombrePack[i].dataset.nom,prix)
                packEnvoie.push(pack)
                packCommande.push(pack);
            }

            for (let j = 0; j <packEnvoie.length ; j++) {

                if (packEnvoie[j].nombre !=="") {
                    let tr = document.createElement('tr')
                    tr.classList.add('color-dark')
                    let tdNom = document.createElement('td')
                    tdNom.innerHTML = packEnvoie[j].nom
                    tr.appendChild(tdNom)
                    let tdNombre = document.createElement('td')
                    tdNombre.innerHTML = packEnvoie[j].nombre
                    tr.appendChild(tdNombre)
                    let tdPrixTTC = document.createElement('td')
                    tdPrixTTC.innerHTML = parseInt(packEnvoie[j].prix)*1.2 + ' €'
                    tr.appendChild(tdPrixTTC)
                    let tdPrixHt = document.createElement('td')
                    tdPrixHt.innerHTML =  parseInt(packEnvoie[j].prix) + ' €'
                    tr.appendChild(tdPrixHt)
                    document.querySelector("tbody").appendChild(tr)
                    ttc +=  parseInt(packEnvoie[j].prix) *1.2

                }
            }
            totalTtc.innerHTML = 'Total TTC : '+ttc+' € TTC'
            totalHt.innerHTML = 'Total HT : '+Math.round((ttc/1.2)*100)/100+' € HT'
        })


    }
    commander.addEventListener('click',()=>{

        console.log(packCommande)
        fetch('/institution/ajoutPack',{
            method:'POST',
            body : JSON.stringify({packCommande})
        })
            .then((response)=>{
                return response.json()
            })
            .then((response)=>{
                if (response.fact ===true){
                    fermer.click()

                    document.querySelector('.btnreponse').click()
                }
                else{
                    fermer.click()

                    document.querySelector('.btnreponse2').click()
                }


            })

    })
    fermer.addEventListener('click',()=>{
        packEnvoie =[];
        for (let i = 0; i < nombrePack.length; i++) {
            nombrePack[i].value ="";
        }
    })
}
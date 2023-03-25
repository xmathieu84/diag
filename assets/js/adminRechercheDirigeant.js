if (document.title ==="Info MangoPay"){
    let mail = document.querySelector('#mail')
   let tbody = document.querySelector('tbody')
    mail.addEventListener('keydown',()=>{
        if (mail.value.length >=5){
            fetch("/administrateur/rechercheDirigeant",{
                method:'POST',
                body:mail.value
            })
                .then((response)=>{
                    return response.json()
                })
                .then((response)=>{
                    while (tbody.firstChild){
                        tbody.removeChild(tbody.lastChild)
                    }
                    for (let i = 0; i < response.length; i++) {
                        let tr = document.createElement('tr')
                        let tdEntreprise = document.createElement('td')
                        tdEntreprise.innerHTML = response[i].nom + ' '+ response[i].prenom + '<br>' + response[i].entreprise + '<br>'+response[i].email
                        tr.appendChild(tdEntreprise)
                        let tdMango = document.createElement('td')
                        tdMango.innerHTML = response[i].mangoUser
                        tr.appendChild(tdMango)
                        let tdWallet = document.createElement('td')
                        tdWallet.innerHTML = response[i].wallet
                        tr.appendChild(tdWallet)
                        let tdBank = document.createElement('td')
                        tdBank.innerHTML = response[i].bank
                        tr.appendChild(tdBank)
                        let tdUbo = document.createElement('td')
                        tdUbo.innerHTML = response[i].ubo
                        tr.appendChild(tdUbo)
                        let tdResultat = document.createElement('td')
                        tdResultat.innerHTML = response[i].resultat
                        tr.appendChild(tdResultat)
                        tbody.appendChild(tr)


                    }
                })
        }
    })
}
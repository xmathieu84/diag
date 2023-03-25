let zoneLogo = document.querySelector('.logoMenu')

    fetch('/institution/menu/logo',{
        method :'GET'
    }).then((response)=>{
        return response.json()
    })
        .then((response)=>{
            console.log(response)
            if (response){
                let img = document.createElement('img')
                img.src = '/uploads/logo/'+response
                img.width = 110
                img.height = 110
                zoneLogo.appendChild(img)
            }
        })

/*let lien = document.querySelectorAll('.liensSyndic')
let code = document.querySelector('.codeSyndic')
console.log(code.value)
for (let i = 0; i < lien.length; i++) {
    console.log(lien[i].href)
    lien[i].href = lien[i].href + '/'+code.value
}*/




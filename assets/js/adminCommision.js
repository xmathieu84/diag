if (document.title==="Espace entreprise Admin"){
    let commision = document.querySelectorAll('.commission')

    for (let i = 0; i < commision.length; i++) {
        let com = commision[i];

        com.addEventListener('keyup',()=>{
            fetch("/administrateur/modiferCom/"+com.dataset.id,{
                method :'POST',
                body : com.value
            })
        })
    }
}
if (document.title =='Chat DIAG-DRONE'){
    let button = document.querySelector('.btn-maincolor')
    var strWindowFeatures = "menubar=no,location=no,resizable=no,scrollbars=yes,status=1,height=500,width=850";
    button.addEventListener('click',()=>{
        window.open('/chat/fenetreChat','chat',strWindowFeatures);
    })
}
if (document.title =='Message à DIAG-DRONE'){
    let envoyer = document.querySelector('.btn-success')
    let text = document.querySelector('textarea')
    let discussion = document.querySelector('.discussion')
    envoyer.addEventListener('click',()=>{
        let p = document.createElement('p')
        p.innerHTML = 'Moi: '+ text.value
        discussion.appendChild(p);

        fetch('/chat/envoieMessage',{
            method:'POST',
            body : text.value
        })
    })
}
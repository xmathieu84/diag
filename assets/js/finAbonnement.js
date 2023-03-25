let mail = document.querySelectorAll('.mail');



for (let i = 0; i < mail.length; i++) {
    mail[i].addEventListener('click', () => {
        let id = mail[i].getAttribute('data-identifiant');
        fetch('/admin/envoieMail', {
            method: 'POST',
            body:id
        })
        .then(function(response) {
            return response.json()
        })
        .then(function(response) {
            
            document.location.reload(true)
           
        })
    })
    
}
let fin = document.querySelectorAll('.termine')

for (let i = 0; i < fin.length; i++) {
    let identifiant = fin[i].getAttribute('data-identifiant');
    fin[i].addEventListener('click', () => {
        fetch('/admin/terminerAbonnement/',{
        method: 'POST',
        body:identifiant
        })
            .then(function(response) {
            return response.json()
        })
        .then(function(response) {
            
            document.location.reload(true)
           
        })
    
    })
}
if (document.title == 'Rappel intervention') {
    var bouton = document.querySelectorAll('.sms')

    for (let i = 0; i < bouton.length; i++) {
        
        bouton[i].addEventListener('click', () => {
            let id = bouton[i].getAttribute('data-intervention');
            fetch('/admin/intervention/inter', {
                method: 'POST',
                body:id
            })
            .then(function() {
                document.location.reload(true);
            })
                
        })
        
    }
    
}
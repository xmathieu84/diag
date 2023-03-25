if (document.title == 'Modifier mes informations') {
    let cerfa = document.querySelector("#modif_salairie_licenceDgac_fichierLicence")
    let bouton = document.querySelector('#licence');

    bouton.addEventListener('click', () => {
    
        
        cerfa.click();
        cerfa.addEventListener('change', () => {
            bouton.innerHTML = cerfa.value.replace(/^.*\\/, "");
        })
    })
}
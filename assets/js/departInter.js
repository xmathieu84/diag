if (document.title == 'finalisation') {
    let debut = document.getElementById('depart_debut')
    let fin = document.getElementById('depart_depart')
    let bouton = document.getElementById('valider');
    let dateDebut = Date.parse(debut.value)
    fin.addEventListener('change', () => {
        let dateFin = Date.parse(fin.value);
        if (dateDebut>dateFin) {
            fin.style.border = "red solid 1px"
            valider.style.display = "none"
            
        }
        else {
            fin.style.border = "none"
            valider.style.display = "block"
        }
        
    })
}

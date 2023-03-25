
if (document.title === 'Inscription entreprise') {
    let tva = document.getElementsByName("entreprise[siretTva][assujeti]")
    document.addEventListener('DOMContentLoaded', () => {
        let bouton = document.querySelector('#logo');

        let logo = document.querySelector('#entreprise_logo');
        bouton.addEventListener('click', () => {
            logo.click();
            logo.addEventListener('change', () => {

                bouton.innerHTML = logo.value.replace(/^.*\\/, "");
            })
        })

        for (let i = 0; i < tva.length; i++) {
            tva[i].addEventListener('change', () => {

                if (tva[i].value === '0') {
                    document.querySelector('.tva').style.display = 'none'
                    document.querySelector('#entreprise_siretTva_tva').removeAttribute('required')
                    document.querySelector('#entreprise_siretTva_tva').setAttribute('disabled', 'disabled')
                    document.querySelector('#entreprise_siretTva_tva').value = ""
                }
                else {
                    document.querySelector('.tva').style.display = 'block'
                    document.querySelector('#entreprise_siretTva_tva').setAttribute('required', 'required')
                    document.querySelector('#entreprise_siretTva_tva').removeAttribute('disabled')
                }
            })
        }

    })

    let siret = document.querySelector('#entreprise_siretTva_siret')


        siret.addEventListener('keyup',()=>{

            if (siret.value.length === 14){

                fetch('/entreprise/siret',{
                    method:'POST',
                    body : siret.value
                })
                    .then((response)=>{
                        return response.json()
                    })
                    .then((response)=>{

                        document.querySelector('#entreprise_adresse_numero').value= response.adresse.numero
                        document.querySelector('#entreprise_adresse_nomVoie').value= response.adresse.nomVoie
                        document.querySelector('#entreprise_adresse_codePostal').value= response.adresse.codePostal
                        document.querySelector('#entreprise_adresse_ville').value= response.adresse.ville
                        document.querySelector('#entreprise_siretTva_tva').value = response.TVA

                    })
            }
        })





}
if (document.title ==="Modifier mes informations") {
    console.log('ok');
    let bouton = document.querySelector('#logo')
    let realInput = document.querySelector('.custom-file-input')

    bouton.addEventListener('click', () => {
        realInput.click();
        realInput.addEventListener('change', () => {
            bouton.innerHTML = realInput.value.replace(/^.*\\/, "");
        })
    })
}
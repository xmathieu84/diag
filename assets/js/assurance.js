if (document.title === 'Phase 6 de votre inscription' || document.title ==='Ajouter vos informations assurantielles') {

    document.addEventListener('DOMContentLoaded', () => {

        function changeButton(idButton,realId){
            let button = document.getElementById(idButton);
            let assurance = document.getElementById(realId);

            button.addEventListener('click', () => {
                assurance.click();
                assurance.addEventListener('change', () => {
                    button.innerHTML = assurance.files[0]['name'];
                })


            })
        }

        changeButton('assu','assurance_ent_ass_pro_fichier')

    })


}
if (document.title ==="Complémentaire"){
    let realButton = document.querySelector("#rc_pro2_fichier")
    let fakeButton = document.querySelector("#assu")

    fakeButton.addEventListener('click',()=>{
        realButton.click()
        realButton.addEventListener('change',()=>{
            fakeButton.innerHTML = realButton.files[0]["name"]
        })
    })
}
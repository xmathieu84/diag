if (document.title == 'Signature Admin') {
    let test = document.getElementById('signature')
    test.addEventListener('change', () => {
        var selectedFile = signature.files[0];

        var reader = new FileReader();
        //reader.onload = function (event) { console.log(reader.result); };
        reader.readAsDataURL(selectedFile);
        reader.onload = function () {
            fetch('/signatureAdmin', {
                method: 'POST',
                body: reader.result
            })//base64encoded string
        };
    })

    /*enregister.addEventListener("click", function (e) {
        var adresse =
            fetch('/signatureAdmin', {
                method: 'POST',
                body: adresse
            })
                .then(() => {
                    document.location.reload()
                })
    }, false);*/


}
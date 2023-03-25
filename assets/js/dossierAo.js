import Dropzone from "dropzone";
if (document.title =='Dossier reponse'){
    let drop = document.querySelector('#dropzone')
    let arrayResponse = [];
    let zoneDrop = "<div class='dz-preview dz-file-preview fichier'>\n" +
        "  <div class='dz-details'>\n" +
        "\n" +
        "    <figure>\n" +
        "      <img data-dz-thumbnail src='/css/css_site/images/fichier104.png'/>\n" +
        "      <figcaption  class='dz-filename fileIndication' data-dz-name></figcaption>\n" +
        "      <figcaption class='dz-size fileIndication' data-dz-size></figcaption>\n" +
        "    </figure>\n" +
        "\n" +
        "  </div>\n" +
        "  <div class='dz-progress'><span class='dz-upload up' data-dz-uploadprogress></span></div>\n" +
        "  <span class='dz-success-mark'></span>\n" +

        "  <div class='dz-error-message mess'><span data-dz-errormessage></span></div>\n" +
        "</div>"
    let dropzone = new Dropzone(drop,{
        url : '/entreprise/dossierAo/'+drop.dataset.id,
        previewTemplate : zoneDrop,
        parallelUploads : 100,
        acceptedFiles : 'application/pdf,application/zip,image/*',
        addRemoveLinks : true,
        dictCancelUpload : "",
        dictRemoveFile : "<img src='/css/css_site/images/supprimer.png'>",
        success : (file,response)=>{
            file.previewElement.lastChild.dataset.dzRemove = "/entreprise/supprimerDossier/"+response

    },
        removedfile : (file)=>{
            fetch(file.previewElement.lastChild.dataset.dzRemove,{
                method : 'GET'
            })
                .then(()=>{
                    let _ref;
                    return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                })
        },
    });
    let effacer = document.querySelectorAll('.delete')

    for (let i = 0; i < effacer.length; i++) {
        effacer[i].addEventListener('click',()=>{
            fetch('/entreprise/dossierAo/'+effacer[i].dataset.id,{
                method : 'GET'
            })
                .then(()=>{
                    document.location.reload()
                })
        })
    }


}
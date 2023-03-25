if (document.title ==="Recherche unique d'un rapport"){
    let code = document.querySelector('#codeRapport')
    let email = document.querySelector('#mail')
    let valider = document.querySelector('.btn-maincolor')
    let zone  = document.querySelector('.zoneReponse')
    valider.addEventListener('click',()=>{
        if (email.value !=="" && code.value!==""){

            let contenu = JSON.stringify({
                mail:email.value,
                codeRapport : code.value
            })
            fetch('/media',{
                body:contenu,
                method : 'POST'
            })
                .then((response)=>{
                    return response.json()
                })
                .then((response)=>{
                    console.log(response)
                    let reponse = response[0]
                        if (reponse !=='non'){
                            /**
                             *
                             * Integration de l'archive du rapport
                             */
                            let figArchive = document.createElement('figure')
                            let figCaptionArchive = document.createElement('figcaption')
                            let imgrachive = document.createElement('img')
                            let aArchive = document.createElement("a")
                            let zoneArchive = document.querySelector('.archive')
                            imgrachive.src = "/css/css_site/img/zip.png";
                            aArchive.href = "/uploads/rapport/"+reponse.archive
                            aArchive.setAttribute('target','_blank');
                            figCaptionArchive.innerText = reponse.archive
                            figArchive.appendChild(imgrachive)
                            figArchive.appendChild(figCaptionArchive)
                            aArchive.appendChild(figArchive)
                            zoneArchive.appendChild(aArchive)

                            /**
                             * Intégration du rapport en pdf
                             */
                            let figPdf = document.createElement('figure')
                            let figCaptionPdf = document.createElement('figcaption')
                            let imgrPdf = document.createElement('img')
                            let aPdf = document.createElement("a")
                            let zonePdf = document.querySelector('.rapport')
                            imgrPdf.src = "/css/css_site/images/pdfmoyen.png";
                            aPdf.href = "/uploads/rapport/"+reponse.rapport
                            aPdf.setAttribute('target','_blank');
                            figCaptionPdf.innerText = reponse.archive
                            figPdf.appendChild(imgrPdf)
                            figPdf.appendChild(figCaptionPdf)
                            aPdf.appendChild(figPdf)
                            zonePdf.appendChild(aPdf)

                            /**
                             * Integration des photos dans un carousel
                             */
                            let photoBtnModal = document.querySelector('.photo');
                            let btnModal = document.createElement('button')
                            btnModal.classList.add('btn')
                            btnModal.classList.add('btn-maincolor2')
                            btnModal.dataset.toggle = "modal"
                            btnModal.dataset.target = "#photoModal"
                            btnModal.innerText = "Voir les photos"
                            photoBtnModal.appendChild(btnModal)

                            let zonePhoto = document.querySelector('.photoRapport')

                            for (let i = 0; i <reponse.photos.length ; i++) {
                                let div = document.createElement('div')
                                div.classList.add('carousel-item')
                                let img = document.createElement('img')
                                img.classList.add('d-block')
                                img.classList.add('w-100')
                                img.src = "/uploads/photoInter/"+reponse.photos[i]

                                if (i===0){
                                   div.classList.add('active')
                                }
                                div.appendChild(img)
                                zonePhoto.appendChild(div)

                            }

                            let videoModale = document.querySelector('.videoM');
                            let btnvideo = document.createElement('button')
                            btnvideo.classList.add('btn')
                            btnvideo.classList.add('btn-maincolor')
                            btnvideo.dataset.toggle = "modal"
                            btnvideo.dataset.target = "#videoModal"
                            btnvideo.innerText = "Voir les videos"
                            videoModale.appendChild(btnvideo)
                            if (reponse.videos[0]){
                                document.querySelector('#videoRapport1').src = "/uploads/videoRapport/"+reponse.videos[0]
                            }
                            if (reponse.videos[1]){
                                document.querySelector('#videoRapport2').src = "/uploads/videoRapport/"+reponse.videos[1]
                            }



                        }
                        else{
                            alert("Le rapport n'a pas pu être trouvé")
                        }
                })

        }
        else {
            alert('Veuillez remplir tout les champs')
        }
    })
}
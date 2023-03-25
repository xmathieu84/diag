
import jQuery from 'jquery';

if (document.title === "Appel d'offre") {
    let appelOuvert = document.getElementsByName('appel[ouvert]');
    let appelType = document.querySelector('#appel_type')
    let appel = document.querySelector('.appel')
    let fakebutton = document.querySelector('.btn-dossierAo')
    let realbutton = document.querySelector('#appel_dossier')

    fakebutton.addEventListener('click',()=>{

        realbutton.click()
        realbutton.addEventListener('change',()=>{

            fakebutton.innerHTML = realbutton.files.length + ' fichier(s) ajouté(s)'
        })
    })
    appelType.addEventListener('change',()=>{
        if (appelType.value == "appel d'offre"){
            appel.style.display = 'block'
            document.querySelector('#appel_ouvert_0+label').classList.add('required')
            document.querySelector('#appel_ouvert_1+label').classList.add('required')
            document.querySelector('.dossier').innerHTML = "d'appel d'offre"
        }

        else {
            appel.style.display ='none'
            document.querySelector('#appel_ouvert_0+label').classList.remove('required')
            document.querySelector('#appel_ouvert_1+label').classList.remove('required')
            restreint.style.display ='none'
            appelOuvert[0].checked = false
            appelOuvert[1].checked = false
        }
        if (appelType.value == "appel a concurrence"){
            document.querySelector('.dossier').innerHTML = "d'appel à concurrence"
        }
    })

    let restreint = document.querySelector('.restreint')
    for (let i = 0; i < appelOuvert.length; i++) {
        appelOuvert[i].addEventListener('change',()=>{
            if (appelOuvert[i].value =='restreint' && appelType.value == "appel d'offre"){

                restreint.style.display = 'block'
                document.querySelector('#appel_restreint_delaiDepotCandidature').setAttribute('required','required')
                document.querySelector('#appel_restreint_delaiReponseCandidature').setAttribute('required','required')


            }
            else {
                restreint.style.display ='none'
                document.querySelector('#appel_restreint_delaiDepotCandidature').removeAttribute('required')
                document.querySelector('#appel_restreint_delaiReponseCandidature').removeAttribute('required')


            }
        })
    }
    let budgetExistant = document.getElementsByName('appel[budgetExistant]')
    for (let i = 0; i < budgetExistant.length; i++) {
        budgetExistant[i].addEventListener('change',()=>{
            if (budgetExistant[i].value =='oui'){
                document.querySelector('.budget').style.display='block'
            }
            else{
                document.querySelector('.budget').style.display='none'
            }
        })
    }

    function addFormToCollection($collectionHolderClass) {
        // Get the ul that holds the collection of tags
        var $collectionHolder = $('.' + $collectionHolderClass);

        // Get the data-prototype explained earlier
        var prototype = $collectionHolder.data('prototype');

        // get the new index
        var index = $collectionHolder.data('index');

        var newForm = prototype;

        // You need this only if you didn't set 'label' => false in your tags field in TaskType
        // Replace '__name__label__' in the prototype's HTML to
        // instead be a number based on how many items we have
        // newForm = newForm.replace(/__name__label__/g, index);

        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        newForm = newForm.replace(/__name__/g, index);

        // increase the index with one for the next item
        $collectionHolder.data('index', index + 1);

        // Display the form in the page in an li, before the "Add a tag" link li
        var $newFormLi = $("<div class='col-sm-6 formContact'></div>").append(newForm);
        // Add the new form at the end of the list
        $collectionHolder.append($newFormLi)
    }
    function addTagFormDeleteLink($tagFormLi) {
        var $removeFormButton = $('<button type="button" class="btn btn-danger mt-5">Effacer</button>');
        $tagFormLi.append($removeFormButton);

        $removeFormButton.on('click', function(e) {
            // remove the li for the tag form
            $tagFormLi.remove();
        });
    }
    jQuery(document).ready(function() {
        // Get the ul that holds the collection of tags
        var $tagsCollectionHolder = $('div.contacts');
        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)


        // add a delete link to all of the existing tag form li elements
        $tagsCollectionHolder.find('div.col-sm-6').each(function() {
            addTagFormDeleteLink($(this));
        });




        $tagsCollectionHolder.data('index', $tagsCollectionHolder.find('input').length);
        let i = 0;
        $('body').on('click', '.add_item_link', function(e) {
            var $collectionHolderClass = $(e.currentTarget).data('collectionHolderClass');
            addFormToCollection($collectionHolderClass);
            $tagsCollectionHolder.find('div#appel_contacts_'+i).each(function() {
                addTagFormDeleteLink($(this));
            });
            i++



        })

    });


}


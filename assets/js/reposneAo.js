import jQuery from "jquery";

if (document.title =='Reponse appel'){
    console.log('ok')
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
        var $removeFormButton = $('<button type="button" class="btn btn-danger mt-5">Éffacer</button>');
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
            $tagsCollectionHolder.find('div#reponse_ao_contacts_'+i).each(function() {
                console.log(i)
                addTagFormDeleteLink($(this));
            });
            i++



        })

    });
    let radioPrecision = document.getElementsByName('reponse_ao[precision]')

    let precision = document.querySelector('.zonePrecision')
    for (let i = 0; i < radioPrecision.length; i++) {

        radioPrecision[i].addEventListener('change',()=>{

            if (radioPrecision[i].value =='oui'){
                precision.style.display = 'block'
                document.querySelector('#reponse_ao_precisionCom').setAttribute('required','required')
            }
            else {
                precision.style.display = 'none'
                document.querySelector('#reponse_ao_precisionCom').removeAttribute('required')
            }
        })
    }
}
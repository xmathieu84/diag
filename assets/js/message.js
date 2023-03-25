if (document.title == 'Espace administrateur Envoyer message') {
  document
    .getElementById('admin_message_selectionDestinataire')
    .addEventListener('change', () => {
      if (
        document.getElementById('admin_message_selectionDestinataire').value ==
        3
      ) {
        document.getElementById('mail').style.display = 'block'
        document
          .getElementById('destinataire')
          .classList.replace('col-lg-12', 'col-lg-6')
        document
          .getElementById('admin_message_mail')
          .setAttribute('required', true)
      }
      if (
        document.getElementById('admin_message_selectionDestinataire').value !=
        3
      ) {
        document.getElementById('mail').style.display = 'none'
        document
          .getElementById('destinataire')
          .classList.replace('col-lg-6', 'col-lg-12')
        document
          .getElementById('admin_message_mail')
          .removeAttribute('required')
      }
    })
}

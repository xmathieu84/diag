if (document.title == 'Mes tarifs') {
  var input = document.getElementsByClassName('form-control form-control-sm')

  function tarif(id, min, max) {
    document.getElementById(id).addEventListener('click', function() {
      if (!document.getElementById(id).checked) {
        for (let i = min; i <= max; i++) {
          input[i].setAttribute('readonly', 'readonly')
        }
      }
      if (document.getElementById(id).checked) {
        for (let i = min; i < max; i++) {
          input[i].removeAttribute('readonly')
        }
      }
    })
  }
  tarif('drone_drone', 171, 174)
}

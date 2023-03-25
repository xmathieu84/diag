if (document.title == 'Nos abonnements en details') {
    let type = document.querySelector('.type').dataset.abonnement
    let collaspe1 = document.querySelector('#collapse1_header > h5>a')
    let collaspe2 = document.querySelector('#collapse2_header > h5>a')
    let collaspe3 = document.querySelector('#collapse3_header > h5>a')
    let collaspe4 = document.querySelector('#collapse4_header > h5>a')


    switch (type) {
        case 'So':

            document.querySelector('#collapse1').classList.add('show')
            collaspe1.setAttribute('aria-expanded', true)

            document.location.href = '#collapse1';
            break;
        case 'Classic':
            document.querySelector('#collapse2').classList.add('show')
            collaspe2.setAttribute('aria-expanded', true)
            document.location.href = '#collapse2';
            break;
        case 'Premium':
            document.querySelector('#collapse3').classList.add('show')
            collaspe3.setAttribute('aria-expanded', true)
            document.location.href = '#collapse3';
            break;
        case 'Infinite':
            document.querySelector('#collapse4').classList.add('show')
            collaspe4.setAttribute('aria-expanded', true)
            document.location.href = '#collapse4';
            break;

        default:
            break;
    }

}
if (document.title == 'Type') {
    let type = document.querySelector('.type').dataset.type
    document.querySelector('#' + type).classList.add('show')
    document.querySelector('#' + type + '_header>h5>a').setAttribute('aria-expanded', true)
    document.location.href = '#' + type
}
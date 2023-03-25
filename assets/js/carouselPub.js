import Siema from 'siema'

if (document.title==="Diag-drone"){

   const siema =  new Siema({
        selector: '.siema',
        duration: 2,
        easing: 'cubic-bezier(.17,.67,.32,1.34)',
        perPage: 4,
        startIndex: 0,
        draggable: false,
        multipleDrag: false,
        threshold: 20,
        loop: true,
        rtl: true,
        onInit: () => {},
        onChange: () => {},
    });
    document.querySelector('.js-prev').addEventListener('click',()=>{
        siema.next()
    })
   function defilementPub(){
       document.querySelector('.js-prev').click()
   }
   window.setInterval(defilementPub,5000)

}



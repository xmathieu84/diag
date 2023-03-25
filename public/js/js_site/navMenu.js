let route = document.currentScript.getAttribute('route');

$('.menu-click').find('li.active').removeClass('active');
let $navLink = $('.menu-click').find('[href="'+ route +'"]');
$navLink.parent('li').addClass('active');
$navLink.parents('.subMenu').addClass('active-submenu');

$('.page_header ').find('.toggle_menu').hide();
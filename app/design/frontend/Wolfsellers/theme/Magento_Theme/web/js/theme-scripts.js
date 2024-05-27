require([
	'jquery',
	'slick',
	'domReady!'
], function ($) {

// header sticky
	// $(window).scroll(function () {
	// 	if ($(window).scrollTop() >= 1) {
	// 		$('body').addClass('nav-up');
	// 	}
	// 	else {
	// 		$('body').removeClass('nav-up');
	// 	}
	// });

	var x = window.matchMedia("(max-width: 768px)");
    var y = window.matchMedia("(min-width: 769px)");

	if (y.matches) {
		$('.menu-dior ul li, .menu-swarovski ul li').hover(function () {
				$(this).children('.custom-menu').toggleClass('se-ve');
				
			}, function () {
				$(this).children('.custom-menu').removeClass('se-ve');
			}
		);
	}

	if (x.matches) {
		$('.menu-dior ul li, .menu-swarovski ul li').click(function (e) { 
			$(this).children('.custom-menu, span').toggleClass('se-ve');
		});
	}

});


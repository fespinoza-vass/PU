require([
	'jquery',
	'slick',
	'domReady!'
], function ($) {

// header sticky
	$(window).scroll(function () {
		if ($(window).scrollTop() >= 1) {
			$('body').addClass('nav-up');
		}
		else {
			$('body').removeClass('nav-up');
		}
	});

});


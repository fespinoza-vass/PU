require([
	'jquery',
	'slick',
	'domReady!'
	], function($){

	//carrouseles de productos en el home
	$('.carrousel-productos ol.product-items').slick({
		dots: false,
		arrows: true,
		infinite: false,
		speed: 300,
		autoplay: false,
		slidesToShow: 5,
		slidesToScroll: 1,
		responsive: [
			{
				breakpoint: 768,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 1,
  					arrows: true,
					dots: false
				}
			}
    	]
	});

	//slider de marcas en el home
	$('.marcas-block').slick({
		dots: false,
		arrows: true,
		infinite: false,
		speed: 300,
		autoplay: false,
		slidesToShow: 7,
		slidesToScroll: 1
	});

    $('.carousel-releases, .carousel-category').slick({
        function (config, element) {
            if (!$(element).hasClass("slick-initialized")) {
                $(element).slick({
                    infinite: false,
                    slidesToShow: 5,
                    slidesToScroll: 5,
                    arrows: true,
                    dots: true,
                    responsive: [{
                        breakpoint: 1025,
                        settings: {
                            slidesToShow: 4,
                            slidesToScroll: 4
                        }
                    },
                        {
                            breakpoint: 769,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 3
                            }
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 2
                            }
                        },
                        {
                            breakpoint: 600,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 2
                            }
                        }
                    ]
                });
            }
        }
    });

});

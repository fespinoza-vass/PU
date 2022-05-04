require([
    'jquery',
    'slick',
    'domReady!'
], function ($) {

    $(window).on('load', function () {

        //carrouseles de productos en el home
        $('.carrousel-productos ol.product-items').slick({
            dots: true,
            arrows: true,
            infinite: false,
            speed: 300,
            autoplay: false,
            slidesToShow: 5,
            slidesToScroll: 1,
            responsive: [{
                breakpoint: 768,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1,
                    arrows: true,
                    dots: false
                }
            }]
        });

        //slider de marcas en el home
        $('.carrucel_mejores_marcas').slick({
            slidesToScroll: 1,
            slidesToShow: 6,
            autoplay: false,
            infinite: true,
            arrows: true,
            dots: false,
            speed: 300,
            responsive: [{
                breakpoint: 768,
                settings: {
                    slidesToScroll: 1,
                    slidesToShow: 3,
                    autoplay: true,
                    arrows: true,
                    dots: false
                }
            }]
        });

        $('.carousel-releases').slick({
            dots: true,
            arrows: true,
            infinite: false,
            speed: 300,
            autoplay: false,
            slidesToShow: 5,
            slidesToScroll: 1,
            responsive: [{
                    breakpoint: 1025,
                    settings: {
                        slidesToShow: 4
                    }
                },
                {
                    breakpoint: 769,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 666,
                    settings: {
                        slidesToShow: 2
                    }
                }
            ]
        });

        $('.carousel-category').slick({
            dots: true,
            arrows: true,
            infinite: false,
            speed: 300,
            autoplay: false,
            slidesToShow: 5,
            slidesToScroll: 1,
            responsive: [{
                    breakpoint: 1025,
                    settings: {
                        slidesToShow: 4
                    }
                },
                {
                    breakpoint: 769,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 666,
                    settings: {
                        slidesToShow: 2
                    }
                }
            ]
        });

		$('.checkout-cart-index .product-items').slick({
            dots: true,
            arrows: true,
            infinite: false,
            speed: 300,
            autoplay: false,
            slidesToShow: 4,
            slidesToScroll: 1,
            responsive: [{
                    breakpoint: 1025,
                    settings: {
                        slidesToShow: 4
                    }
                },
                {
                    breakpoint: 769,
                    settings: {
                        slidesToShow: 3,
						arrows: false,
						dots: false,
                    }
                },
                {
                    breakpoint: 666,
                    settings: {
						slidesToScroll:1,
                        slidesToShow: 2,
						autoplay: true,
						infinite: true,
						arrows: false,
						dots: false
                    }
                }
            ]
        });

    });

});

require([
	'jquery',
	'slick',
	'domReady!'
	], function($){

        $(window).on('load', function() {
             
            var optionsCarrusels = {
                centerPadding: '0',
                centerMode: true,
                dots: false,
                infinite: true,
                speed: 300,
                autoplay: false,
                autoplaySpeed: 5000,
                slidesToShow: 4,
                slidesToScroll: 1,
                responsive: [
                    {
                        breakpoint: 2025,
                        settings: {
                            centerMode: true,
                            slidesToShow: 4
                        }
                    },
                    {
                        breakpoint: 1025,
                        settings: {
                            centerMode: true,
                            slidesToShow: 3
                        }
                    },
                    {
                        breakpoint: 769,
                        settings: {
                            centerMode: true,
                            slidesToShow: 2
                        }
                    },
                    {
                        breakpoint: 666,
                        settings: {
                            centerMode: true,
                            slidesToShow: 1
                        }
                    },
                ]
            };
        
            $('.products-related .product-items').not('.slick-initialized').slick(optionsCarrusels);
            $('.products-upsell .product-items').not('.slick-initialized').slick(optionsCarrusels);
            $('.amshopby-morefrom .product-items').not('.slick-initialized').slick(optionsCarrusels);

          });

        $('.page-with-filter .product-item-info').hover(function () {
                // over
                $(this).addClass('zplus');
            }, function () {
                // out
                $(this).removeClass('zplus');
            }
        );
});	
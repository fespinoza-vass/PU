define([
    "jquery",
    "slick",
    "domReady"
], function ($) {
    "use strict";
    return function (config, element) {
        if (!$(element).hasClass("slick-initialized")) {
            $(element).slick({
                infinite: false,
                slidesToShow: 5,
                slidesToScroll: 5,
                arrows: true,
                dots: false,
                responsive: [{
                    breakpoint: 1025,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 4,
                        dots: true
                    }
                },
                    {
                        breakpoint: 769,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3,
                            dots: true
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2,
                            dots: true
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2,
                            dots: true
                        }
                    }
                ]
            });
        }
    }
});

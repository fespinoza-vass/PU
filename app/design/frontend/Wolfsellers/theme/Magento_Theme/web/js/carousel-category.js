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

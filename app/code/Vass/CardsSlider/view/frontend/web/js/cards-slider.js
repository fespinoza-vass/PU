/**
 * @copyright Copyright (c) 2024 VASS
 * @package Vass_CardsSlider
 * @author VASS Team
 */
define([
    "jquery",
    "swiper"
], function ($, Swiper) {
    'use strict';
    return function (config, element) {
        $(document).ready(function () {
            let slidesInDesktop = config.slidesInDesktop,
                slidesInTablet = config.slidesInTablet,
                slidesInMobile = config.slidesInMobile,
                swiperOptions = {
                    slidesPerView: slidesInMobile,
                    spaceBetween: 0,
                    loop: true,
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true,
                    },
                    breakpoints: {
                        1024: {
                            slidesPerView: slidesInDesktop,
                            spaceBetween: 24,
                        },
                        768: {
                            slidesPerView: slidesInTablet,
                            spaceBetween: 10,
                        }
                    }
                };

            new Swiper(element, swiperOptions);
        });
    }
});

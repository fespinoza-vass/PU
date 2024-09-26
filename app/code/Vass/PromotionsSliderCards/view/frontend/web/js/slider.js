/**
 * @copyright Copyright (c) 2024 VASS
 * @package Vass_PromotionsSliderCards
 * @author VASS Team
 */

define([
    'jquery',
    'swiper'
], function ($, Swiper) {
    'use strict';

    return function (config, element){
        $(document).ready(function () {
            let swiperOptions = {
                slidesPerView: 1,
                spaceBetween: 24,
                freeMode: false,
                a11y: false,
                loop: true,
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                breakpoints: {
                    1024: {
                        slidesPerView: 3
                    },
                    768: {
                        slidesPerView: 2
                    }
                }
            };

            new Swiper(element, swiperOptions);
        });
    }
});

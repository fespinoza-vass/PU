/**
 * @copyright Copyright (c) 2024 VASS
 * @package Vass_ProductSliderWidget
 * @author VASS Team
 */

define([
    'jquery',
    'swiper'
], function ($, Swiper) {
    'use strict';

    return function (config, element){
        $(document).ready(function () {
            let slidesInDesktop = config.slidesInDesktop,
                slidesInTablet = config.slidesInTablet,
                slidesInMobile = config.slidesInMobile,
                swiperOptions = {
                    slidesPerView: slidesInMobile,
                    slidesPerGroup: slidesInMobile,
                    spaceBetween: 16,
                    loop: true,
                    autoplay: {
                        delay: 3000,
                        disableOnInteraction: false,
                    },
                    navigation: {
                        nextEl: `#${element.id}-next`,
                        prevEl: `#${element.id}-prev`
                    },
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true,
                    },
                    breakpoints: {
                        1024: {
                            slidesPerView: slidesInDesktop,
                            slidesPerGroup: slidesInDesktop,
                            spaceBetween: 24,
                        },
                        768: {
                            slidesPerView: slidesInTablet,
                            slidesPerGroup: slidesInTablet,
                            spaceBetween: 16,
                        }
                    }
                };

            new Swiper(element, swiperOptions);
        });
    }
});

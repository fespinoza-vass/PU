/**
 * @copyright Copyright (c) 2024 VASS
 * @package Vass_CardsSlidere
 * * @author VASS Team
 */
define([
    "jquery",
    "swiper"
], function (
    $,
    Swiper
) {
    $(document).ready(function () {
        let swiperOptions = {
            slidesPerView: 1,
            loop: true,
            pagination: {
                el: ".swiper-pagination",
                clickable: true
            },
            breakpoints: {
                1024: {
                    slidesPerView: 2.5,
                    spaceBetween: 30
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 10
                }
            }
        };
        new Swiper('.card-slider', swiperOptions);
    });
});

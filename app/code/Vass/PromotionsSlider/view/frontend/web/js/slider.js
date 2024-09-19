/**
 * @copyright Copyright (c) 2024 VASS
 * @package Vass_PromotionsSlider
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
                slidesPerView: 'auto',
                spaceBetween: 24,
                loop: false
            };

            new Swiper(element, swiperOptions);
        });
    }
});

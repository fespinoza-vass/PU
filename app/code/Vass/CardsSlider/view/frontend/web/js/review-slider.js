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
            arrows: true,
            navigation: {
                nextEl: ".ba-review-next",
                prevEl: ".ba-review-prev",
            },
            scrollbar: {
                el: '.swiper-scrollbar-review',
                draggable: true,
                dragSize: 'auto',
                snapOnRelease: true,
            },
        };
        new Swiper('.ba-review-slider', swiperOptions);
    });
});

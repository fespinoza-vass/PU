require([
    'jquery',
    'domReady!',
    'dropdown'
], function ($) {

    window.onload = function(){
        if(window.innerWidth < 768) {
            $('.loginbtns').dropdown();
        }
    }

});
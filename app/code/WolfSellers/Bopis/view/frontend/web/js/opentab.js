define(['jquery', 'domReady!'], function($){
    "use strict";
    return function opentab()
    {
        $(document).ajaxComplete(function() {
            $(".tab-delivery").trigger('click');
        });
    }
});

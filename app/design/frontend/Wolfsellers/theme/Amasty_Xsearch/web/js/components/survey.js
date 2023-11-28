define([
    'jquery',
    'underscore',
    'domReady!'
], function ($, _) {
    var params =  new URL(document.location).searchParams;
    if(!_.isUndefined(params)){
        var increment_id = params.get("id");
        if(!_.isUndefined(increment_id)){
            $('input.num-order').val(increment_id);
        }
    }
});

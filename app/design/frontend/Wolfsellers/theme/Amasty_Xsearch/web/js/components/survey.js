define([
    'uiComponent',
    'jquery',
    'underscore'
], function (Component, $, _) {
    return Component.extend({

        /**
         * Get url param id value, id reference increment id order to survey
         * @returns {*}
         */
        initialize: function () {
            let params = new URL(document.location).searchParams;
            if(!_.isUndefined(params)){
                let increment_id = params.get("id");
                if(!_.isUndefined(increment_id)){
                    $('input.num-order').val(increment_id);
                }
            }
            return this._super();
        },
    });
});

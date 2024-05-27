define([
    'jquery'
], function ($) {
    'use strict';

    return function (Component) {
        return Component.extend({
            increaseQty: function (item) {
                item.form.find("[name='qty']")
                    .val(item.qty + 1)
                    .trigger('change')
                ;
            },

            decreaseQty: function (item) {
                if (item.qty <= 1) {
                    return;
                }

                item.form.find("[name='qty']")
                    .val(item.qty - 1)
                    .trigger('change')
                ;
            }
        });
    };
});

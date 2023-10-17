define([
    'ko',
    'uiComponent'
],function (
    ko,
    Component
) {
    'use strict';
    return Component.extend({
        isVisibleShipping:ko.observable(true),
        initialize: function () {
            this._super();
            this.isVisibleShipping.subscribe(function (value) {
                console.log("adios");
            }, this);
            return this;
        },
        /**
         * Listen edit event when shipping summary its finished
         * @param parent
         */
        showShippingStep: function (parent) {
            parent.isVisibleShipping(true);
        }

    });
})

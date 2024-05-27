define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/modal'
], function ($, _, uiRegistry, select, modal) {
    'use strict';

    return select.extend({

        initialize: function () {
            this._super();
            $('body').on('processStop', function () {
                var defaultValue = this.defaut || this.initialValue;
                if (this.value() == defaultValue) {
                    this.toggleFields(defaultValue);
                }
            }.bind(this));
        },

        onUpdate: function (value) {

            this.toggleFields(value);

            return this._super();
        },

        toggleFields: function (value) {
            if (value == '1') {
                uiRegistry.get('index = products_per_page').show();
            } else {
                uiRegistry.get('index = products_per_page').hide();
            }
        }
    });
});
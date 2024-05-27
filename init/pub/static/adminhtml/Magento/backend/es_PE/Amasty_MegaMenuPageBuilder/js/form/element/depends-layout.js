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
            if (value == 'slider') {
                uiRegistry.get('index = slider_items_show').show();
                uiRegistry.get('index = slider_width').show();
                uiRegistry.get('index = slider_autoplay').show();
                if (uiRegistry.get('index = slider_autoplay').value() == "1") {
                    uiRegistry.get('index = slider_autoplay_speed').show();
                }
                uiRegistry.get('index = show_pager').hide();
                uiRegistry.get('index = products_per_page').hide();
            } else {
                uiRegistry.get('index = show_pager').show();
                if (uiRegistry.get('index = show_pager').value() == "1") {
                    uiRegistry.get('index = products_per_page').show();
                }
                uiRegistry.get('index = slider_items_show').hide();
                uiRegistry.get('index = slider_width').hide();
                uiRegistry.get('index = slider_autoplay').hide();
                uiRegistry.get('index = slider_autoplay_speed').hide();
            }
        }
    });
});
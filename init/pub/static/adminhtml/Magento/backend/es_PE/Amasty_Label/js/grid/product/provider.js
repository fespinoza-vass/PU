define([
    'jquery',
    'Magento_Ui/js/grid/provider'
], function ($, provider) {
    'use strict';

    return provider.extend({

        // eslint-disable-next-line no-unused-vars
        reload: function (options) {
            if (typeof this.params.filters === 'undefined') {
                this.params.filters = {};
            }

            this.params.filters.label_id = $('[name="label_id"]').val();

            this._super({'refresh': true});
        }
    });
});

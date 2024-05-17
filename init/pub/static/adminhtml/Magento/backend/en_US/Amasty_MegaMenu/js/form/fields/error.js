define([
    'Magento_Ui/js/form/element/abstract'
], function (Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            elementTmpl: 'Amasty_MegaMenu/form/error'
        },

        /**
         * Subcategories Position Field init method
         */
        initialize: function () {
            var self = this;

            self._super();

            self.visible(!self.hidden);
        },
    });
});

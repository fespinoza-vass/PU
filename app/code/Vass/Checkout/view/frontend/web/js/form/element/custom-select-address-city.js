define([
    'ko',
    'jquery',
    'uiRegistry',
    'Magento_Checkout/js/model/quote',
    'Magento_Ui/js/form/element/select',
    'Magento_Checkout/js/model/full-screen-loader',
], function (ko, $, registry, quote, select, fullScreenLoader) {
    'use strict';

    return select.extend({
        /**
         * Initializes component.
         */
        initialize: function () {
            this._super();
            let self = this;

            $(document).on('change', 'select[name="region_id"]', function () {
                let id = $(this).attr('id');
                let shippingContainer = $('.checkout-shipping-address');
                if (shippingContainer.find(`#${id}`) && shippingContainer.is(":visible")) {
                    if (shippingContainer.find('select[name="region_id"]').is(':visible')) {
                        let regionId = registry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.region_id');
                        let regionIdValue = regionId.value();

                        if (regionIdValue) {
                            fullScreenLoader.startLoader();
                            self.filterCities(regionIdValue);
                        }
                    }
                }
            });
        },

        /**
         * Get and set cities for city select
         *
         * @param regionId
         */
        filterCities: function (regionId) {
            let self = this;
            $.ajax({
                url: '/zipcode/index/getcity',
                data: {region_id: regionId},
                success: function (data) {
                    let options = JSON.parse(data);
                    self.setOptions(options);
                    fullScreenLoader.stopLoader();
                }
            });
        }
    });
});

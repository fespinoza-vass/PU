define([
    'ko',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'mage/translate',
    'uiRegistry',
    'domReady!'
], function (ko, $, quote, $t, registry) {
    'use strict';

    return function (Component) {
        return Component.extend({
            getAddress: function () {
                return quote.shippingAddress();
            },

            filterCustomAttributes: function (attributesCodes) {
                let address = this.getAddress();
                let customAttributes = address.customAttributes;

                let attribute = customAttributes.filter(function (attribute) {
                    return attributesCodes.includes(attribute.attribute_code);
                });

                if (attribute.length > 0) {
                    return attribute[0].value ? attribute[0] : $t('None');
                }

                return $t('None');

            },

            getRegion: function () {
                let address = this.getAddress();
                let regionId = registry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.region_id');
                if (regionId) {
                    let filterRegion = regionId.options().filter(function (option) {
                        return option.value == address.regionId;
                    });

                    if (filterRegion.length) return filterRegion[0]?.label ?? $t('None');
                }

                return $t('None');
            },

            getCity: function () {
                let city = this.getAddress()?.city
                if (!city) city = this.filterCustomAttributes(['colony'])?.value
                return city ?? $t('None');
            },

            getColony: function () {
                let colony = this.filterCustomAttributes(['colony']);
                return colony?.value ?? colony;
            },

            getReference: function () {
                let reference = this.filterCustomAttributes(['referencia_envio', 'referencia']);
                return reference?.value ?? reference;
            },

            getCustomerName: function () {
                let firstname = this.getAddress().firstname;
                $('.customer-info__name span:last-of-type').text(firstname);

                return firstname
            },

            getCustomerLastName: function () {
                let lastname = this.getAddress().lastname;
                $('.customer-info__lastname span:last-of-type').text(lastname);

                return lastname;
            },

            getDocumentType: function () {
                let documentType = this.filterCustomAttributes(['identificacion_picker']);
                documentType = documentType?.label ?? 'DNI';
                $('.customer-info__document span:first-of-type').text(`${documentType}:`);

                return documentType;
            },

            getDocument: function () {
                let document = this.filterCustomAttributes(['numero_identificacion_picker']);
                $('.customer-info__document span:last-of-type').text(document?.value ?? document);

                return document?.value ?? '';
            },

        });
    }
});

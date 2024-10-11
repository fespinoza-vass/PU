define([
        'ko',
        'jquery',
        'uiComponent',
        'underscore',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/checkout-data',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/quote',
        'mage/translate',
        'mage/mage',
        'domReady!'
], function (
        ko,
        $,
        Component,
        _,
        stepNavigator,
        customerData,
        selectShippingAddressAction,
        checkoutData,
        addressList,
        quote,
        $t
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Vass_Checkout/personal-information'
            },

            elements: {
                id: '#personal-information',
                form: '#personal-information-form',
                content: '#personal-information-content',
                column: '.checkout-column.opc:first-of-type',
                container: 'checkout-block',
                login: '.form.form-login',
                shippingAddress: '#shipping-new-address-form',
            },

            moveInputs: [
                'shippingAddress.firstname',
                'shippingAddress.lastname',
                'shippingAddress.custom_attributes.identificacion_picker',
                'shippingAddress.custom_attributes.numero_identificacion_picker'
            ],

            isVisible: ko.observable(true),
            moveSuccess: ko.observable(false),
            isLoaded: ko.observable(false),
            customer: customerData.get('customer'),
            stepCode: 'personal-information',
            stepTitle: $t('My personal information'),

            /**
             *
             * @returns {*}
             */
            initialize: function () {
                let self = this;
                this._super();

                stepNavigator.registerStep(
                    this.stepCode,
                    null,
                    this.stepTitle,
                    this.isVisible,

                    _.bind(this.navigate, this),

                    15
                );

                $(document).ready(function () {
                    self.initBlock();
                });

                return this;
            },

            navigate: function () {
            },

            /**
             * Init column block
             *
             * @returns {*}
             */
            initBlock: function () {
                let self = this;
                let intervalMove = setInterval(function () {
                    if (!$(self.elements.column).find(`.${self.elements.container}`).length) {
                        $(self.elements.column).append(`<div class="${self.elements.container}"></div>`);
                    } else {
                        $(self.elements.id).appendTo($(self.elements.column).find(`.${self.elements.container}`));
                        self.moveElements();
                        $(self.elements.id).show();
                        clearInterval(intervalMove);
                    }
                }, 1500);
            },

            /**
             * Move elements
             *
             * @returns {*}
             */
            moveElements: function () {
                let self = this;
                let customer = customerData.get('customer');
                let totalAddresses = addressList().length;
                if (!customer().firstname || !totalAddresses) {
                    $(this.elements.login).prependTo($(this.elements.content));

                    this.moveInputs.forEach(function (element) {
                        $(self.elements.shippingAddress).find(`div[name='${element}']`)
                            .appendTo($(self.elements.id).find(`${self.elements.form} .fieldset`));
                    });
                }
            },

            isLoggedIn: function () {
                let totalAddresses = addressList().length;
                if (!totalAddresses) return false;

                customerData.invalidate(['customer']);
                return customerData.get('customer')()?.firstname ?? false;
            },

            editAddress: function () {
                let self = this;
                if (self.isLoaded()) {
                    self.selectNewAddress();
                } else {
                    self.isLoaded(true);
                }
            },

            getAddress: function () {
                return quote.shippingAddress();
            },

            selectNewAddress: function () {
                let addresses = addressList();
                let totalAddresses = addresses.length;
                $("#opc-new-shipping-address").show()
                $("select[name='shipping_address_id']").prop('selectedIndex', totalAddresses - 1);
                selectShippingAddressAction(addresses[totalAddresses - 1]);
                checkoutData.setSelectedShippingAddress(addresses[totalAddresses - 1].getKey());
                $('#opc-new-shipping-address').siblings('.actions-toolbar').find('.action-update').click();
            },

            getCustomerName: function () {
                return this.getAddress().firstname ?? $t('None');
            },

            getCustomerLastName: function () {
                return this.getAddress()?.lastname ?? $t('None');
            },

            getDocumentType: function () {
                let documentType = this.filterCustomAttributes(['identificacion_picker']);
                return documentType?.label ?? 'DNI';
            },

            getDocument: function () {
                let document = this.filterCustomAttributes(['numero_identificacion_picker']);
                return document?.value ?? $t('None');
            },

            filterCustomAttributes: function (attributesCodes) {
                let address = this.getAddress();
                let customAttributes = address.customAttributes;

                if (!customAttributes) {
                    return $t('None');
                }

                let attribute = customAttributes.filter(function (attribute) {
                    return attributesCodes.includes(attribute.attribute_code);
                });

                if (attribute.length > 0) {
                    return attribute[0].value ? attribute[0] : $t('None');
                }

                return $t('None');

            }
        });
    }
);

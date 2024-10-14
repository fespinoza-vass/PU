/**
 * Change shipping address list view.
 */
define([
    'ko',
    'underscore',
    'Amasty_CheckoutCore/js/model/address-form-state',
    'Amasty_CheckoutCore/js/model/shipping-registry',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-address/form-popup-state',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/checkout-data',
    'mage/translate',
    'jquery'
], function (
    ko,
    _,
    addressFormState,
    shippingRegistry,
    addressList,
    quote,
    formPopUpState,
    selectShippingAddress,
    selectBillingAddress,
    checkoutData,
    $t,
    $
) {
    'use strict';

    var newAddressOption = {
        /**
         * Get new address label
         * @returns {String}
         */
        getAddressInline: function () {
            return $t('New Address');
        },
        customerAddressId: null
    };

    function isAddressNew(address) {
        return address && (address === newAddressOption || address.getType() === 'new-customer-address');
    }

    return function (Component) {
        return Component.extend({
            defaults: {
                dropdownTemplate: 'Amasty_CheckoutCore/shipping-address/shipping-address',
                modules: {
                    shippingInformationComponent: 'index = ship-to',
                    shippingAddressComponent: '${ $.parentName }'
                }
            },

            initialize: function () {
                _.bindAll(
                    this,
                    'addressOptionsText',
                    'changeAddress',
                    'onAddressChange',
                    'updateAddress',
                    'cancelAddressEdit'
                );

                this._super();

                if (this.getVisible()) {
                    window.isShippingAddressFormOverridden(true);
                    this.shippingAddressComponent(this.overrideNewShippingAddress);
                    shippingRegistry.excludedCollectionNames.push('shipping-address-fieldset');
                }

                return this;
            },

            initObservable: function () {
                this._super()
                    .observe({
                        /**
                         * Current dropdown value
                         */
                        selectedAddress: quote.shippingAddress(),

                        /**
                         * On true shows current shipping address information as plain text
                         */
                        isAddressDetailsVisible: quote.shippingAddress() != null,

                        /**
                         * On true shows new address form if isAddressListVisible is also true
                         */
                        isAddressFormVisible: false,

                        /**
                         * On true shows dropdown and new address form
                         */
                        isAddressListVisible: !quote.shippingAddress(),

                        isLoaded: false,
                        timeOutShowFormAddress: null,
                        canShowFormAddress: true,
                        beforeValidation: true,
                    });

                this.initSubscribers();

                return this;
            },

            /**
             * Set subscribers to observables
             */
            initSubscribers: function () {
                if (this.getVisible()) {
                    quote.shippingAddress.subscribe(this.onShippingAddressUpdate, this);
                    this.selectedAddress.subscribe(this.onAddressChange, this);
                    this.isAddressFormVisible.subscribe(this.updatePopupState, this);
                    this.isAddressListVisible.subscribe(this.isShippingFormVisibleUpdate, this);
                }
            },

            /**
             * Modify new shipping address for registered customer functionality.
             *  Old behavior - popup; new - inline form
             *
             * @param {object} shippingComponent
             */
            overrideNewShippingAddress: function (shippingComponent) {
                //override popup functionality
                shippingComponent.getPopUp = shippingComponent.getPopUpOverride;
            },

            /**
             * Override for better compatibility.
             * Magento_NegotiableQuote compatibility.
             *
             * @return {string}
             */
            getTemplate: function () {
                this._super();

                return this.dropdownTemplate;
            },

            /**
             * Override to prevent not used functionality render (performance save)
             */
            initChildren: function () {
                return this;
            },

            /**
             * Override to prevent not used functionality render (performance save).
             * This list uses ship-to component instead origin
             */
            createRendererComponent: function () {},

            /**
             * Visible by default is usual variable,
             * but it can be changed to observable or computed by vendors (amazon)
             *
             * @return {boolean}
             */
            getVisible: function () {
                return ko.utils.unwrapObservable(this.visible);
            },

            /**
             * Subscriber
             * updates new shipping address core state
             * required to suppress address update before save
             */
            updatePopupState: function () {
                if (this.getVisible()) {
                    formPopUpState.isVisible(this.isAddressListVisible() && this.isAddressFormVisible());
                }
            },

            /**
             * Shipping address subscriber
             * Close form on shipping address change
             *
             * @param {object} newAddress
             */
            onShippingAddressUpdate: function (newAddress) {
                if (newAddress != null) {
                    this.isAddressDetailsVisible(true);
                    this.isAddressListVisible(false);
                }
            },

            /**
             * New Address form visibility subscriber
             */
            isShippingFormVisibleUpdate: function () {
                this.updatePopupState();

                if (this.getVisible()) {
                    addressFormState.isShippingFormVisible(
                        this.isAddressListVisible()
                    );
                }
            },

            /**
             * Options array
             */
            addressOptions: ko.pureComputed(function () {
                var addressOptions = _.clone(addressList()),
                    newAddressAdded;

                _.find(addressOptions, function (address) {
                    if (isAddressNew(address)) {
                        newAddressAdded = true;

                        return true;//break
                    }
                });

                if (!newAddressAdded) {
                    addressOptions.push(newAddressOption);
                }

                return addressOptions;
            }),

            /**
             * For html option text binding
             *
             * @param {Object} address
             * @return {string}
             */
            addressOptionsText: function (address) {
                if (address.getAddressInline) {
                    return address.getAddressInline();
                }

                if (isAddressNew(address)) {
                    return $t('New Address');
                }

                return this.getCaptionByAddressType(address);
            },

            /**
             * @param {Object} address
             * @returns {*}
             */
            getCaptionByAddressType: function (address) {
                switch (address.getType()) {
                    case 'gift-registry':
                        return $t('Recipient Address');
                    default:
                        return address.getKey();
                }
            },

            /**
             * Update address action
             */
            updateAddress: function () {
                let self = this;
                let canCloseForm = true;
                let isNewAddressSelected = false;

                if (!self.isLoaded()) {
                    let addresses = addressList();
                    let totalAddresses = addresses.length;
                    let currentAddress = this.selectedAddress()?.getKey() ?? null;
                    if (totalAddresses > 1 && currentAddress && currentAddress == 'new-customer-address') {
                        self.selectedAddress(addresses[0]);
                        selectShippingAddress(self.selectedAddress());
                        checkoutData.setSelectedShippingAddress(self.selectedAddress().getKey());
                    }
                    self.isLoaded(true);
                }

                if (this.selectedAddress() != null) {
                    if (this.isAddressFormVisible()) {
                        let inputDocument = $('.form-shipping-address input[name="custom_attributes[numero_identificacion_picker]"]');
                        if (inputDocument.siblings('.field-error').length) {
                            inputDocument.focus();
                            return;
                        }

                        isNewAddressSelected = true;
                        this.shippingAddressComponent().saveNewAddress();
                    } else {
                        canCloseForm = true;

                        if ($("#opc-new-shipping-address").is(":visible")) {
                            self.isAddressFormVisible(true);
                            canCloseForm = false;
                        } else {
                            selectShippingAddress(this.selectedAddress());
                            checkoutData.setSelectedShippingAddress(this.selectedAddress().getKey());

                            if (this.selectedAddress().getKey() === 'new-customer-address') {
                                self.isAddressFormVisible(true);
                                canCloseForm = false;
                            }
                        }
                    }
                }

                if ((this.shippingAddressComponent().source.get('params.invalid') !== true && canCloseForm === true )) {
                    if (isNewAddressSelected) self.selectNewAddressPopup()
                    this.cancelAddressEdit();
                }

                self.addDefaultCustomAttributes();
            },

            /**
             * Update dropdown selected value. Should same as current shipping address.
             */
            updateDropdownAddress: function () {
                var shippingAddress = quote.shippingAddress(),
                    list = addressList(),
                    addressIndex,
                    selectedAddress;

                selectedAddress = _.find(list, function (address) {
                    return address === shippingAddress;
                });

                if (!selectedAddress && isAddressNew(shippingAddress)) {
                    //Resolve when shipping address and selectedAddress different objects with same value.
                    addressIndex = _.findIndex(list, function (address) {
                        return isAddressNew(address);
                    });

                    if (addressIndex !== -1) {
                        list[addressIndex] = shippingAddress;
                        addressList(list);
                    }
                }

                this.selectedAddress(shippingAddress);
            },

            /**
             * Show address dropdown
             */
            changeAddress: function () {
                this.updateDropdownAddress();
                this.isAddressListVisible(true);
                this.isAddressDetailsVisible(false);
            },

            /**
             * Cancel address edit action.
             */
            cancelAddressEdit: function () {
                this.updateDropdownAddress();
                this.isAddressDetailsVisible(true);
                this.isAddressListVisible(false);
                this.isAddressFormVisible(false);

                if (!$('.form-shipping-address').is(':visible')) {
                    $('body').css('overflow', 'auto');
                }
            },

            /**
             * On dropdown value change
             *
             * @param {Object} address
             */
            onAddressChange: function (address) {
                let isNewAddress = isAddressNew(address);
                let formAddress = $('.shipping-address-form');
                this.isAddressFormVisible(isNewAddress);

                if (isNewAddress) {
                    if ($('.form-shipping-address').is(':visible')) {
                        $('body').css('overflow', 'hidden');
                        let inputDocument = $('.form-shipping-address input[name="custom_attributes[numero_identificacion_picker]"]');
                        if (inputDocument.siblings('.field-error').length) {
                            inputDocument.val('');
                        }
                    }
                }

                if (formAddress.is(':visible')) {
                    let referenceShipping = $("[name='custom_attributes[referencia_envio]']");
                    let identificationNumber = $("[name='custom_attributes[numero_identificacion_picker]']");

                    if (referenceShipping.val() === 'referencia_envio') {
                        referenceShipping.val('');
                    }

                    if (identificationNumber.val().includes('numero_identificacion_picker')) {
                        identificationNumber.val(identificationNumber.val()
                            .replace('numero_identificacion_picker', ''));
                    }
                }

                if (
                    formAddress.is(':visible')
                    && !$('select[name="custom_attributes[city]"]').val()
                ) {
                    $('select[name="region_id"]').trigger('change');
                }
            },

            /**
             * Select new address
             *
             * @param {boolean} canShowForm
             */
            selectNewAddress: function (canShowForm = true) {
                let addresses = addressList();
                let totalAddresses = addresses.length;
                $("select[name='shipping_address_id']").prop('selectedIndex', totalAddresses - 1);
                $("#opc-new-shipping-address").show()
                selectShippingAddress(addresses[totalAddresses - 1]);
                checkoutData.setSelectedShippingAddress(addresses[totalAddresses - 1].getKey());
                $('#opc-new-shipping-address').siblings('.actions-toolbar').find('.action-update').click();
            },

            /**
             * Select new address popup
             */
            selectNewAddressPopup: function () {
                let addresses = addressList();
                let totalAddresses = addresses.length;
                $("select[name='shipping_address_id']").prop('selectedIndex', totalAddresses - 1);
                selectShippingAddress(addresses[totalAddresses - 1]);
            },

            addDefaultCustomAttributes: function () {
                let newAddressEdited = this.selectedAddress();
                let customAttributes = newAddressEdited.customAttributes;
                for (let i = 0; i < customAttributes.length; i++) {
                    if (customAttributes[i].attribute_code === 'ruc' && !customAttributes[i].value) {
                        customAttributes[i].value = '00000000000';
                    } else if (customAttributes[i].attribute_code === 'razon_social' && !customAttributes[i].value) {
                        customAttributes[i].value = 'Razon Social';
                    } else if (customAttributes[i].attribute_code === 'direccion_fiscal' && !customAttributes[i].value) {
                        customAttributes[i].value = 'Direccion Fiscal';
                    }
                }

                let ruc = customAttributes.filter(function (attribute) {return attribute.attribute_code === 'ruc';});
                if (!ruc.length) {
                    newAddressEdited.customAttributes.push({ attribute_code: 'ruc', value: '00000000000' });
                }

                let nameCompany = customAttributes.filter(function (attribute) {
                    return attribute.attribute_code === 'razon_social';
                });
                if (!nameCompany.length) {
                    newAddressEdited.customAttributes.push(
                        { attribute_code: 'razon_social', value: $t('Razon Social') }
                    );
                }

                let TaxDirectory = customAttributes.filter(function (attribute) {
                    return attribute.attribute_code === 'direccion_fiscal';
                });
                if (!TaxDirectory.length) {
                    newAddressEdited.customAttributes.push(
                        { attribute_code: 'direccion_fiscal', value: $t('Direccion Fiscal') }
                    );
                }

                quote.shippingAddress(newAddressEdited);
            }
        });
    };
});

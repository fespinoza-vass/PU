/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
        'ko',
        'underscore',
        'Magento_Ui/js/form/form',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/quote'
    ],
    function (
        ko,
        _,
        Component,
        customer,
        addressList,
        quote
    ) {
        'use strict';
        var addressOptions = addressList().filter(function (address) {
                return address.getType() === 'customer-address';
            });

        var billingAddressMixin = {
            /**
             * @return {exports.initObservable}
             */
            initObservable: function (){
                if(_.isUndefined(addressOptions)){
                    this._super()
                        .observe({
                            selectedAddress: null,
                            isAddressDetailsVisible: quote.billingAddress() != null,
                            isAddressFormVisible: !customer.isLoggedIn(),
                            isAddressSameAsShipping: false,
                            saveInAddressBook: 1
                        });
                }
                this._super()
                    .observe({
                        selectedAddress: null,
                        isAddressDetailsVisible: quote.billingAddress() != null,
                        isAddressFormVisible: !customer.isLoggedIn() || !addressOptions.length,
                        isAddressSameAsShipping: false,
                        saveInAddressBook: 1
                    });

                quote.billingAddress.subscribe(function (newAddress) {

                    if (quote.isVirtual()) {
                        this.isAddressSameAsShipping(false);
                    } else {
                        //override to prevet error that not show step two
                        this.isAddressSameAsShipping(true);
                    }

                    if (newAddress != null && newAddress.saveInAddressBook !== undefined) {
                        this.saveInAddressBook(newAddress.saveInAddressBook);
                    } else {
                        this.saveInAddressBook(1);
                    }
                    this.isAddressDetailsVisible(true);
                }, this);
            return this;
            }
        }

        return function (billingAddressTarget) {
            return billingAddressTarget.extend(billingAddressMixin);
        }

});

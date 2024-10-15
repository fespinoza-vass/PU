define([
    'ko',
    'jquery',
    'mageUtils'
], function (ko, $, utils) {
    'use strict';

    return function (Shipping) {
        return Shipping.extend({

            initObservable: function () {
                this._super();

                return this;
            },
            initialize: function () {
                this._super();
                
                this.filteredRates = ko.computed(function () {
                    let rates = this.rates();
                    let aereoRate = rates.find(rate => rate.method_code === 'aereo');
                    let terrestreRate = rates.find(rate => rate.method_code === 'terrestre');
                    
                    if (aereoRate && terrestreRate) {
                        return rates.filter(rate => rate.method_code !== 'terrestre');
                    }
                    
                    return rates;
                }, this);
            }
        });
    };
});


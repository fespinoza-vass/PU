define([
    'Amasty_CheckoutCore/js/view/checkout/summary/item/details',
    'ko'
], function (Component, ko) {
    'use strict';

    return Component.extend({
        updateItemQty: function(item, change) {
            var qty = ko.isObservable(item.qty) ? item.qty() : item.qty;
            var newQty = parseFloat(qty) + change;
            // Ensure the new quantity is not less than the minimum allowed
            newQty = Math.max(newQty, this.isDecimal(item) ? 0.1 : 1);
            
            if (ko.isObservable(item.qty)) {
                item.qty(newQty);
            } else {
                item.qty = newQty;
            }
            
            this.updateItem(item);
        }
    });
});
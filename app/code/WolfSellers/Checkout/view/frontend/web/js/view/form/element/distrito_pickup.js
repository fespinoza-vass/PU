define([
    'ko',
    'Magento_Ui/js/form/element/select'
], function (
    ko,
    Select
) {
    'use strict';

    return Select.extend({
        defaults:{
            links:{
                'searchQuery':'checkout.steps.store-pickup.store-selector:searchQuery'
            }
        },
        searchQuery: ko.observable(),
        initialize:function () {
            this._super();
            this.value.subscribe(function (value) {
                if(value){
                    this.searchQuery(value);
                }
            },this);
        }
    });
})

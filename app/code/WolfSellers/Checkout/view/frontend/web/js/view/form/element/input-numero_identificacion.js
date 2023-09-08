define([
    'ko',
    "Magento_Ui/js/form/element/abstract"
], function (ko,Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            placeholder: ''
        },
        dataType: ko.observable("text"),
        initialize: function () {
            this._super();
            this.value.subscribe(function (value) {
                if(value.length > 18){
                    this.value(value.substring(0,18));
                }
            },this);
            return this;
        },
        initObservable: function(){
            this._super();
            this.observe('placeholder');
            return this;
        }
    });
});

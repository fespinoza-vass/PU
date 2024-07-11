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
            this.dataType.subscribe(function (newValue) {
                if (newValue === 'text') {
                    this.setMaxLength(12);
                } else if (newValue === 'number') {
                    this.setMaxLength(8);
                }
            },this);
            this.value.subscribe(function (value) {
                if(value.length > 8){
                    this.value(value.substring(0,8));
                }
            },this);
            return this;
        },
        /**
         * Init observable
         * @returns {*}
         */
        initObservable: function(){
            this._super();
            this.observe('placeholder');
            return this;
        },
        /**
         * Set max length
         * @param {number} length
         */
        setMaxLength: function(length) {
            this.maxLength(length);
        }
    });
});
